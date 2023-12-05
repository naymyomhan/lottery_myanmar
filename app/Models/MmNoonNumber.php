<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class MmNoonNumber extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function ledger(){
        return $this->belongsTo(Ledger::class);
    }

    public function mm_noon_bets()
    
    {
       return $this->hasMany(MmNoonBet::class, 'mm_noon_number_id', 'id');

    }
}