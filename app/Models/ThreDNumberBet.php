<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreDNumberBet extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
         use HasRoles;

    protected $guard_name = 'web';

     protected $fillable=[
        'id',
        'thre_d_numbers_id',
        'voucher_id',
        'user_id',
        'three_d_ledger_id',
        'number',
        'amount',
    ];

    public function ledger()
    {
        return $this->belongsTo(ThreeDLedger::class);
    }

     public function user(){
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(ThreeDVoucher::class);
    }

    public function thre_d_numbers()
    {
        return $this->belongsTo(ThreDNumber::class);
    }
}