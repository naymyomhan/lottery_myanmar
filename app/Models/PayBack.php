<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class PayBack extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'user_id',
        'section_id',
        'winner_id',
        'amount',
    ];

    public function winner()
    {
        return $this->belongsTo(Winner::class);
    }

    public function user()
    {   
        return $this->belongsTo(User::class);
    }

    public function section()
    {   
        return $this->belongsTo(Section::class);
    }
}