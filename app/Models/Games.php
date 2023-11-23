<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
class Games extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $table = 'games';
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

     public static function boot()
    {   
        parent::boot();
        Games::saving(function (Games $games) {
            $games->admin_id=Auth::id();
        });

        Games::updating(function (Games $games) {
            // 
        });
    }

}