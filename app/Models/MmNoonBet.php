<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class MmNoonBet extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'mm_noon_number_id',
        'voucher_id',
        'user_id',
        'section_id',
        'number',
        'amount',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }


    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function mm_noon_number()
    {
        return $this->belongsTo(MmNoonNumber::class);
    }
}