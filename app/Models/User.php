<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Banlist;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements BannableContract , Auditable
{
   
    use Bannable;
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
     use \OwenIt\Auditing\Auditable;

    protected $guard_name = 'web';

    public function routeNotificationForVonage($notification)
    {
        return $this->phone;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'name',
        'phone',
        'password',
        'refer_code',
        'profile_picture_name',
        'profile_picture_path',
        'profile_picture_location',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    // promotionTopup
    public function promotionTopup()
    {
        return $this->hasMany(PromotionTopUps::class);
    }

    public function topups(){
        return $this->hasMany(TopUp::class);
    }

    public function cashout(){
        return $this->hasMany(CashOut::class);
    }

    public function main_wallet(){
        return $this->hasOne(UserMainWallet::class);
    }

    public function game_wallet(){
        return $this->hasOne(UserGameWallet::class);
    }

    public function transfers(){
        return $this->hasMany(Transfer::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    public function getBannedAttribute()
    {
       return $this->banned_at !== null;
    }

     public function messages()
    {
        return $this->hasMany(Message::class);
    }

     public function userPromoWallet()
    {
        return $this->hasOne(UserPromoWallet::class);
    }
}