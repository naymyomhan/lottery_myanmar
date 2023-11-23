<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class GameRoom extends Model
{

     use HasRoles;

    protected $guard_name = 'web';
     protected $table = 'game_rooms';

    protected $fillable = [
        'name',
        'banker',
        'buy_in_min',
        'buy_in_max',
        'bet_limit',
        'status',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}