<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class ThreeDResult extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $table='three_d_results';

    protected $fillable=[
        'three_d_ledger_id',
        'number_id',
        'number','created_at'
    ];

    public function ledger(){
        return $this->belongsTo(ThreeDLedger::class);
    }

     public static function boot()
    {   
        parent::boot();
        ThreeDResult::saving(function (ThreeDResult $result) {
         $result->number_id = ThreDNumber::where('three_d_ledger_id', $result->three_d_ledger_id)
    ->where('number', $result->number)
    ->first()
    ->id;
   $date = date('Y-m-d');

ThreeDHistory::create([
    'date' => $date,
    'number' => $result->number
]);
                   
        });
    }


    
}