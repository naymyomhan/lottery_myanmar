<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Winner extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
     use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'ledger_id',
        'section_id',
        'result_id',
        'user_id',
        'bet_id',
        'created_at'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function result(){
        return $this->belongsTo(Result::class);
    }

    public function mm_morning_bet()
    {
        return $this->belongsTo(MmMorningBet::class);
    }

    public function pay_back()
    {
        return $this->hasOne(PayBack::class);
    }
}