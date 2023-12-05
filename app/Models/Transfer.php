<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Transfer extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'user_id',
        'to_main',
        'to_game',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}