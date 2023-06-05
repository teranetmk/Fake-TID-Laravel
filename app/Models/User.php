<?php

namespace App\Models;

use App\Http\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'username', 'email', 'password', 'jabber_id', 'newsletter_enabled', 'balance_in_cent', 'language', 'is_super_admin',
        'referred_by', 'affiliate_id', 'is_partner'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getOpenTicketsCount()
    {
        return UserTicket::getOpenTicketsCountByUserId($this->id);
    }

    public function hasCouponUsed($coupon)
    {
        return UserCoupon::where([
                'user_id' => $this->id,
                'coupon_id' => $coupon != null ? $coupon->id : 0
            ])->get()->first() != null;
    }

    public function enabledNewsletter()
    {
        return $this->newsletter_enabled == 1;
    }

    public function redeemCoupon($coupon)
    {
        if ($coupon instanceof Coupon) {
            return $coupon->redeem($this);
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions', 'user_id', 'permission_id');
    }

    /**
     * @param $permissions
     * @return bool
     */
    public function hasAnyPermissionFromArray($permissions)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return count(array_intersect($this->getAuthUserPermissionsList(), $permissions)) > 0;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }


        //Small improvement to avoid duplicate requests
        return in_array($permission, $this->getAuthUserPermissionsList(), true);
    }

    /**
     * @return mixed
     */
    private function getAuthUserPermissionsList()
    {
        return keep('permissions_list', function () {
            return Permission::query()
                ->whereHas('user', function ($query) {
                    $query->where('user_id', $this->id);
                })
                ->pluck('permission')
                ->toArray();
        });
    }


    public function getFormattedBalance()
    {
        return number_format($this->balance_in_cent / 100, 2, ',', '.') . ' ' . Setting::getShopCurrency();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isSuperAdmin(): bool
    {
        return (bool)$this->getAttribute('ís_super_admin');
    }
    public function getTransactionCount(): bool
    {
        return \App\Models\UserTransaction::where('user_id', $this->id)->where('status','paid')->count() ?? 0;
    }
}
