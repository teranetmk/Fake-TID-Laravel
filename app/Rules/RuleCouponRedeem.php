<?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\Rule;

    use App\Models\Coupon;
    
    class RuleCouponRedeem implements Rule
    {
        private $message;

        public function __construct($message = null)
        {
            $this->message = $message;
        }

        public function passes($attribute, $value)
        {
            $coupon = Coupon::where('code', $value)->get()->first();

            if($coupon != null) {
                if($coupon->canRedeem()) {
                    return true;
                } else {
                    $this->message = __('frontend/user.coupon_redeem.error2');
                }
            }

            return false;
        }

        public function message()
        {
            return $this->message ?? __('frontend/user.coupon_redeem.error1');
        }
    }
