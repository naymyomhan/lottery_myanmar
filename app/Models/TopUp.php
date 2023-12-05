<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class TopUp extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

     use HasRoles;

    protected $guard_name = 'web';

    protected $table = 'topups';


    protected $fillable=[
        'user_id',
        'payment_method',
        'payment_account_name',
        'payment_account_number',
        'amount',
        'success',
        'topup_transaction_number' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}