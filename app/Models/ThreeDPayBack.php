<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreeDPayBack extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $table='three_d_pay_backs';

    protected $fillable=[
        'user_id',
        'three_d_ledger_id',
        'winner_id',
        'amount',
    ];

    public function winner()
    {
        return $this->belongsTo(ThreDWinner::class,"three_d_ledger_id");
    }

    public function user()
    {   
        return $this->belongsTo(User::class,"user_id");
    }

}