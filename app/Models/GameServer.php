<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class GameServer extends Model
{
     use HasRoles;

    protected $guard_name = 'web';
    protected $table = 'game_servers';

    protected $fillable = [
        'name',
        'ads',
        'balance',
        'status',
        'app_version',
        'created_at',
        'updated_at',
    ];
    
}