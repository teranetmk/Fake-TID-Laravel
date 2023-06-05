<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'permission',
        'parent_id'
    ];

    public function user()
    {
        return $this->hasOne(UserPermission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function getById($permission_id) {
        return self::where('id', $permission_id)->first();
    }

    public static function getByString($permission_string) {
        return self::where('permission', $permission_string)->first();
    }

    public static function get($permission_id) {
        return self::getById($permission_id);
    }

    public static function getByPermission($permission_string) {
        return self::getByString($permission_string);
    }

    public function childs(): HasMany
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
