<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Tid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * @var array
     */
    protected $create_tids = [];

    /**
     * @var array
     */
    protected $errorMessage = [];


    /**
     * @param string $original_name
     * @return string|string[]
     */
    public function getTid( string $original_name )
    {
        return pathinfo($original_name, PATHINFO_FILENAME);
    }


    /**
     * @param $id
     * @param $original_name
     */
    public function deleteTidFile( $id, $original_name )
    {
        if ( Storage::disk( 'public' )->exists( "tid/$id/$original_name" ) )
            Storage::disk( 'public' )->delete( "tid/$id/$original_name" );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadTidFile( Request $request )
    {
        $product = Product::find( $request->product_id );

        foreach ( $request->file( 'tid_file' ) as $file ) {
            $original_name = $file->getClientOriginalName();

            if ( Tid::where( 'tid', $original_name )->exists() ) {
                $this->errorMessage[] = $original_name;
                continue;
            }

            $this->create_tids[] = [
                'tid' => $original_name,
                'loc' => $request->file_loc
            ];

            $this->deleteTidFile( $product->id, $original_name );
            $file->move( "storage/tid/$product->id", $original_name );

        }

        if ( $this->create_tids )
            $product->tids()->createMany( $this->create_tids );

        if ( $this->errorMessage ) {
            $errorMessage = 'Already exists: <br>' . implode( "<br/>", $this->errorMessage ) . '<br/> All other uploaded';
            return redirect()->back()->with( 'errorMessage', $errorMessage );
        }

        return redirect()->back()->with( 'successMessage', 'TID files has been successfully added' );
    }

}
