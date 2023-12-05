<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class Recommendation extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guard_name = 'web';
     protected $table = 'recommendation';

     use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    public static function boot()
    {   
        
        parent::boot();
        Recommendation::saving(function (Recommendation $recommendation) {
            $recommendation->user_id=Auth::id();
        });

        Recommendation::updating(function (Recommendation $recommendation) {
            //
        });
    }
}