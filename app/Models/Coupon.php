<?php

    namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	
    use App\Models\Setting;
    use App\Models\User;
    use App\Models\UserCoupon;

    class Coupon extends Model
    {
        protected $table = 'coupons';

        protected $fillable = [
            'amount', 'code', 'max_usable', 'used'
		];

		public function hasUserUsed($user) {
			return UserCoupon::hasUserUsedCoupon($user, $this);
		}

		public function canRedeem() {
			return $this->max_usable > $this->used || $this->max_usable == -1;
		}

		public function getUsageCount() {
			return $this->max_usable - $this->used;
		}

		public function isUnlimited() {
			return $this->max_usable == -1;
		}

		public function redeem($user) {
			$user = User::where('id', $user != null ? $user->id : 0)->get()->first();

			if($user != null) {
				if($this->canRedeem()) {
					$newBalance = $user->balance_in_cent + $this->amount;

					$user->update([
						'balance_in_cent' => $newBalance
					]);

					$this->update([
						'used' => $this->used + 1
					]);

					UserCoupon::create([
						'user_id' => $user->id,
						'coupon_id' => $this->id
					]);
		
					return true;
				}
			}

			return false;
		}
		
		public function getFormattedAmount() {
            return $this->getFormattedAmountWithoutCurrency() . ' ' . Setting::getShopCurrency();
		}
		
		public function getFormattedAmountWithoutCurrency() {
            return number_format($this->amount / 100, 2, ',', '.');
        }
    }
