<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class CashOut extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';
    

    protected $fillable=[
        'user_id',
        'admin_id',
        'cash_out_method_id',
        'receive_account_name',
        'receive_account_number',
        'amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function cash_out_method(){
        return $this->belongsTo(CashOutMethod::class);
    }
}