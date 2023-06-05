<?php

    namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	
    use App\Models\Coupon;
    use App\Models\User;

    class UserCoupon extends Model
    {
        protected $table = 'users_coupons';

        protected $fillable = [
            'user_id', 'coupon_id'
		];
    }
