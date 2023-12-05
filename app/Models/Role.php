<?php
// Role using UUID primary key

namespace App\Models;

use Illuminate\Support\Str;
use Sereny\NovaPermissions\Traits\SupportsRole;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
     public $guard_name = 'web';
   
    use SupportsRole; // REQUIRED TRAIT

     /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(function (Role $role) {
            if ($role->id === null) {
                $role->id = Str::uuid()->toString();
            }
        });
    }

    /**
     * Force key type as string
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Disable incrementing
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }
}