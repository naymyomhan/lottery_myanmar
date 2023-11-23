<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class MmEveningNumber extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';


     protected $fillable = [
        'section_id',
    ];
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function ledger(){
        return $this->belongsTo(Ledger::class);
    }
    public function mm_evening_bets()
    {
       return $this->hasMany(MmEveningBet::class, 'mm_evening_number_id', 'id');
    }
}