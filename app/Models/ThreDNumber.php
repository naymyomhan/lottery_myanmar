<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreDNumber extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
         use HasRoles;

    protected $guard_name = 'web';

    public function ledger()
    {
        return $this->belongsTo(ThreeDLedger::class);
    }

     public function bets()
{
    return $this->hasMany(ThreDNumberBet::class,'thre_d_numbers_id', 'id');
}
}