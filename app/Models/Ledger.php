<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Ledger extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    protected $casts = [
        'target_date' => 'datetime',
        'start_date' => 'datetime',
    ];



    // target_date

    protected $fillable = [
        'datetime',
        'start_date',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function mm_morning_numbers()
    {
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
}
