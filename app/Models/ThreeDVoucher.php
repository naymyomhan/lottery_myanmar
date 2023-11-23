<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreeDVoucher extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $table='three_d_vouchers';

    protected $fillable=[
        'user_id',
        'three_d_ledger_id',
        'total_amount',
        'open_at',
        'bet_id'
    ];

    // three_d_bets

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function three_d_bets()
    {
        return $this->belongsTo(ThreDNumberBet::class);
    }

    public function three_d_ledger()
    {
        return $this->belongsTo(ThreeDLedger::class);
    }

    public function pay_back()
    {
        return $this->hasOne(ThreeDPayBack::class);
    }
}