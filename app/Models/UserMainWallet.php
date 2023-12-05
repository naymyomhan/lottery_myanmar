<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class UserMainWallet extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'user_id',
        'balance',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function topups(){
        return $this->hasMany(TopUp::class,'user_id', 'user_id');
    }
}