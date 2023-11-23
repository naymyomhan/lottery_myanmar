<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Voucher extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'user_id',
        'section_id',
        'total_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

//number relations
    public function mm_morning_numbers(){
        return $this->hasMany(MmMorningNumber::class);
    }

    public function mm_noon_numbers()
    {
        return $this->hasMany(MmNoonNumber::class);
    }

    public function mm_after_noon_numbers()
    {
        return $this->hasMany(MmAfterNoonNumber::class);
    }

    public function mm_evening_numbers()
    {
        return $this->hasMany(MmEveningNumber::class);
    }
//number relation end


//bet relations
public function mm_morning_bets(){
    return $this->hasMany(MmMorningBet::class);
}

public function mm_noon_bets()
{
    return $this->hasMany(MmNoonBet::class);
}

public function mm_after_noon_bets()
{
    return $this->hasMany(MmAfterNoonBet::class);
}

public function mm_evening_bets()
{
    return $this->hasMany(MmEveningBet::class);
}
//number relation end


}