<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreeDWinner extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    //  use database table

    protected $table='thre_d_winners';

    protected $fillable=[
        'three_d_ledger_id',
        'result_id',
        'user_id',
        'bet_id',
        'type'
    ];

    public function ledger()
    {
        return $this->belongsTo(ThreeDLedger::class,"three_d_ledger_id");
    }

    public function user(){
        return $this->belongsTo(User::class,);
    }

    public function result(){
        return $this->belongsTo(ThreeDResult::class,"three_d_ledger_id");
    }

    public function pay_back()
    {
        return $this->hasOne(ThreeDLedger::class,"three_d_ledger_id");
    }

    public function thre_d_numbers()
    {
        return $this->belongsTo(ThreDNumberBet::class,'bet_id');
    }
}