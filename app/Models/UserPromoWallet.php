<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class UserPromoWallet extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    // user_id
    protected $fillable = [
        'user_id',
        'balance',
        'on_hold_balance',
        'hold'
    ];

     public function user(){
        return $this->belongsTo(User::class);
    }

}