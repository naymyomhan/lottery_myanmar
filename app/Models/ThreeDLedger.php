<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreeDLedger extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    protected $table='three_d_ledgers';

    protected $fillable=[
        'target_date',
        'open_date',
        'open_at',
        'limit_date',
        'limit_time',
        'pay_back_multiply',
        'r_pay_back_multiply',
    ];

    public function pay_backs(){
        return $this->hasMany(ThreeDPayBack::class);
    }

     public function rpay_backs(){
        return $this->hasMany(RThreeDPayBack::class);
    }

    public function tdresult(){
        return $this->hasOne(ThreeDResult::class);
    }

    public function winers()
    {
        return $this->hasMany(ThreeDWinner::class);
    }

    public function bets()
{
    return $this->hasMany(ThreDNumberBet::class);
}
    public function numbers()
{
    return $this->hasMany(ThreDNumber::class);
}
}