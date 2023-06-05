<?php

/**
 * FakeTids.su
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace App\Services\Domain;

use App\Models\Product;
use App\Models\RandomShippingAddress;
use setasign\Fpdi\Fpdi;
use App\Models\UserOrder;
use App\Models\Setting;
use App\Models\Tid;
use Illuminate\Support\Facades\Storage;
use Throwable;


class TidGenerationService
{
    public function generateTidPDF(string $orderId): bool
    {
        try {
            $order = UserOrder::with(['address'])->find($orderId);
            if (! $order instanceof UserOrder) {
                return false;
            }

            $original_name = $order->tids->tid;
            $file_loc = $order->tids->loc;
            $offset_x = 170;
            $offset_y = 30;
            if ($file_loc == 'eu') {
                $offset_x = 35;
                $offset_y = 22;
            }

            if (Storage::disk('public')->exists("tid_copy/$order->product_id/$original_name")) {
                Storage::disk('public')->delete("tid_copy/$order->product_id/$original_name");
            }

            if (Storage::disk('public')->exists("tid/$order->product_id/$original_name")) {
                Storage::disk('public')
                ->copy("tid/$order->product_id/$original_name", "tid_copy/$order->product_id/$original_name");
            }
            $path = Storage::disk('public')->path("tid_copy/$order->product_id/$original_name");
            $pdf = new Fpdi();
            $pdf->setSourceFile($path);
            $tplIdx = $pdf->importPage(1);
            $specs  = $pdf->getTemplateSize($tplIdx);
            $pdf->AddPage($specs[ 'height' ] > $specs[ 'width' ] ? 'P' : 'L');
            $pdf->useTemplate($tplIdx);

            $pdf->SetFont('arial', '', '10');
            $pdf->SetTextColor(0, 0, 0);

            setlocale(LC_ALL, 'de_DE');

            $shipping = Setting::where('key', 'like', 'shipping%')->get();

            $settings = [];

            foreach ($shipping->pluck('value', 'key')->toArray() as $key => $setting) {
                $settings[explode('.', $key)[ 1 ]] = $setting;
            }
            // sender_first_name sender_last_name
            $pdf->SetXY($offset_x, $offset_y);
            $pdf->Write(
                0,
                $this->codeToISO($order->address->sender_first_name . ' ' . $order->address->sender_last_name)
            );

            // sender_street
            $pdf->SetXY($offset_x, $offset_y + 5);
            $pdf->Write(0, $this->codeToISO($order->address->sender_street));

            // sender_zip
            $pdf->SetXY($offset_x, $offset_y + 10);
            $pdf->Write(0, $this->codeToISO($order->address->sender_zip));

            // sender_city
            $pdf->SetXY($offset_x, $offset_y + 15);
            $pdf->Write(0, $this->codeToISO($order->address->sender_city));

            // sender_country
            $pdf->SetXY($offset_x, $offset_y + 20);
            $pdf->Write(0, $this->codeToISO($order->address->sender_country));
            if (
                $order->address->recipient_first_name &&
                $order->address->recipient_last_name &&
                $order->address->recipient_street
                ) {
                // first_name last_name
                $address = $order->address;
                $pdf->SetXY($offset_x, $offset_y + 30);
                $pdf->Write(
                    0,
                    $this->codeToISO($address->recipient_first_name . ' ' . $address->recipient_last_name)
                );
                // street
                $pdf->SetXY($offset_x, $offset_y + 35);
                $pdf->Write(0, $this->codeToISO($address->recipient_street));

                // zip
                $pdf->SetXY($offset_x, $offset_y + 40);
                $pdf->Write(0, $this->codeToISO($address->recipient_zip));

                // city
                $pdf->SetXY($offset_x, $offset_y + 45);
                $pdf->Write(0, $this->codeToISO($address->recipient_city));

                // country
                $pdf->SetXY($offset_x, $offset_y + 50);
                $pdf->Write(0, $this->codeToISO($address->recipient_country));

            } else {
                // first_name last_name
                $pdf->SetXY($offset_x, $offset_y + 30);
                $pdf->Write(0, $this->codeToISO($order->address->first_name . ' ' . $order->address->last_name));

                // street
                $pdf->SetXY($offset_x, $offset_y + 35);
                $pdf->Write(0, $this->codeToISO($order->address->street));

                // zip
                $pdf->SetXY($offset_x, $offset_y + 40);
                $pdf->Write(0, $this->codeToISO($order->address->zip));

                // city
                $pdf->SetXY($offset_x, $offset_y + 45);
                $pdf->Write(0, $this->codeToISO($order->address->city));

                // country
                $pdf->SetXY($offset_x, $offset_y + 50);
                $pdf->Write(0, $this->codeToISO($order->address->country));

            }

            if (! is_null($order->products) && $order->products->name === 'LIT für Filling') {
                $pdf->image(__DIR__ . "/scan.png", 15, (($file_loc === 'eu') ? 22 : 30), 100, 36);
            }

            $path = "order/{$order->id}";
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->deleteDirectory($path);
            }
            Storage::disk("public")->makeDirectory($path);

            if (is_file(public_path("storage/order/{$order->id}/{$original_name}"))) {
                unlink(public_path("storage/order/{$order->id}/{$original_name}"));
            }

            $pdf->Output(public_path("storage/order/{$order->id}/{$original_name}"), 'F');

            if (Storage::disk('public')->exists("tid_copy/$order->product_id/$original_name")) {
                Storage::disk('public')->delete("tid_copy/$order->product_id/$original_name");
            }
            return true;
        } catch (Throwable $e) {
            throw $e;
            return false;
        }
    }

    public function generateRandomTidPDF(string $orderId, RandomShippingAddress $address): bool
    {
        try {
            $order = UserOrder::find($orderId);
            $product = Product::where('name', 'Random')->first();
            if (! $order instanceof UserOrder || is_null($address) || ! $product instanceof Product) {
                return false;
            }
            $tid = Tid::where('product_id', $product->id);

            if (isset($order->random_tid) && !empty($order->random_tid)) {
                $tid = $tid->where('tid', $order->random_tid);
            } else {
                $tid = $tid->where('used', 0);
            }

            $tid = $tid->firstOrFail();

            if (is_null($order->random_tid)) {
                $tid->update(['used' => 1]);

                $order->update([
                    'random_tid'    => $tid->tid
                ]);
            }

            $original_name = $tid->tid;
            $file_loc = $tid->loc;

            $offset_x = 170;
            $offset_y = 30;
            if ( $file_loc == 'eu' ) {
                $offset_x = 35;
                $offset_y = 22;
            }
            if (Storage::disk('public')->exists("tid_copy/$product->id/$original_name")) {
                Storage::disk('public')->delete("tid_copy/$product->id/$original_name");
            }

            if (Storage::disk('public')->exists("tid/$product->id/$original_name")) {
                Storage::disk('public')
                ->copy("tid/$product->id/$original_name", "tid_copy/$product->id/$original_name");
            }

            $path = Storage::disk('public')->path("tid_copy/$product->id/$original_name");

            $pdf = new Fpdi();
            $pdf->setSourceFile( $path );

            $tplIdx = $pdf->importPage( 1 );
            $specs  = $pdf->getTemplateSize( $tplIdx );
            $pdf->AddPage($specs[ 'height' ] > $specs[ 'width' ] ? 'P' : 'L');
            $pdf->useTemplate($tplIdx);

            $pdf->SetFont('arial', '', '10');
            $pdf->SetTextColor(0, 0, 0);

            setlocale(LC_ALL, 'de_DE');

            $shipping = Setting::where( 'key', 'like', 'shipping%' )->get();

            $settings = [];

            foreach ($shipping->pluck('value', 'key')->toArray() as $key => $setting) {
                $settings[ explode( '.', $key )[ 1 ] ] = $setting;
            }

            // sender_first_name sender_last_name
            $pdf->SetXY($offset_x, $offset_y);
            $pdf->Write(
                0,
                $this->codeToISO(($address->sender_first_name ?? '') . ' ' . ($address->sender_last_name ?? ''))
            );


            // sender_street
            $pdf->SetXY($offset_x, $offset_y + 5);
            $pdf->Write(0, $this->codeToISO($address->sender_street));

            // sender_zip
            $pdf->SetXY($offset_x, $offset_y + 10);
            $pdf->Write(0, $this->codeToISO($address->sender_zip));

            // sender_city
            $pdf->SetXY($offset_x, $offset_y + 15);
            $pdf->Write(0, $this->codeToISO($address->sender_city));

            // sender_country
            $pdf->SetXY($offset_x, $offset_y + 20);
            $pdf->Write(0, $this->codeToISO($address->sender_country));

            // first_name last_name
            $pdf->SetXY($offset_x, $offset_y + 30);
            $pdf->Write(
                0,
                $this->codeToISO(($address->recipient_first_name ?? '') . ' ' . ($address->recipient_last_name ?? ''))
            );

            // street
            $pdf->SetXY($offset_x, $offset_y + 35);
            $pdf->Write(0, $this->codeToISO($address->recipient_street));

            // zip
            $pdf->SetXY($offset_x, $offset_y + 40);
            $pdf->Write(0, $this->codeToISO($address->recipient_zip));

            // city
            $pdf->SetXY($offset_x, $offset_y + 45);
            $pdf->Write(0, $this->codeToISO($address->recipient_city));

            // country
            $pdf->SetXY($offset_x, $offset_y + 50);
            $pdf->Write(0, $this->codeToISO($address->recipient_country));

            $pdf->image(__DIR__ . "/random.png", 15, (($file_loc === 'eu') ? 22 : 30), 100, 36);


            $path = "order/{$order->id}";
            Storage::disk("public")->makeDirectory($path);

            if (is_file(public_path("storage/order/{$order->id}/{$original_name}"))) {
                unlink(public_path("storage/order/{$order->id}/{$original_name}"));
            }

            $pdf->Output(public_path("storage/order/{$order->id}/{$tid->tid}"), 'F');

            Storage::disk('public')->delete("tid_copy/$product->id/$original_name");

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function codeToISO($string)
    {
        if (str_contains($string, 'ș')) {
            $string = str_replace('ș', 's', $string);
            return iconv('UTF-8', "ISO-8859-2//IGNORE//TRANSLIT", $string);
        }

        return iconv('UTF-8', "ISO-8859-1", $string);
    }
}
