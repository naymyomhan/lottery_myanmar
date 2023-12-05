<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Promotions extends Model
{
    use HasFactory;

    public function admin()
    {   
        return $this->belongsTo(Admin::class);
    }

     public static function boot()
    {   
        
        parent::boot();
        Promotions::saving(function (Promotions $promotions) {
            $promotions->admin_id=Auth::id();
        });

        Promotions::updating(function (Promotions $promotions) {
             $promotions->admin_id=Auth::id();
        });
    }

        public function promotionTopup()
    {
        return $this->hasMany(PromotionTopUps::class,'promotion_id', 'id');
    }
}