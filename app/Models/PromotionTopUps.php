<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PromotionTopUps extends Model
{
    use HasFactory;

     protected $fillable = [
        'admin_id',
        'user_id',
        'promotion_id',
        'amount',
        'refer_code',
    ];

    public function admin()
    {   
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {   
        return $this->belongsTo(User::class);
    }

     public function promotion()
    {   
        return $this->belongsTo(Promotions::class);
    }

     public static function boot()
    {   
        
        parent::boot();
        PromotionTopUps::saving(function (PromotionTopUps $promotionstopup) {
            $promotionstopup->admin_id=Auth::id();
            $promotionstopup->refer_code=User::where('id',$promotionstopup->user_id)->first()->refer_code;
            $mainWallet = $promotionstopup->user->main_wallet;

    // Increase the amount in the MainWallet
    if ($mainWallet) {
        $mainWallet->balance += $promotionstopup->amount;
        $mainWallet->save();
    }
        });

        PromotionTopUps::updating(function (PromotionTopUps $promotionstopup) {
             $promotionstopup->admin_id=Auth::id();
        });
    }
}