<?php

    namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	
    use App\Models\Setting;

    class DeliveryMethod extends Model
    {
        protected $table = 'delivery_methods';

        protected $fillable = [
            'price', 'name'
		];
		
		public function getFormattedPrice() {
            return $this->getFormattedPriceWithoutCurrency() . ' ' . Setting::getShopCurrency();
		}
		
		public function getFormattedPriceWithoutCurrency() {
            return number_format($this->price / 100, 2, ',', '.');
        }
    }
