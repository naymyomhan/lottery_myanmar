<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;
class ExchangeRate extends Model implements Auditable
{
    use HasFactory;
     use \OwenIt\Auditing\Auditable;

     protected $table = 'exchange_rate';

     public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

     public static function boot()
    {   
        parent::boot();
        ExchangeRate::saving(function (ExchangeRate $exchangeRate) {
            $exchangeRate->admin_id=Auth::id();
        });

        ExchangeRate::updating(function (ExchangeRate $exchangeRate) {
            // 
        });
    }
}