<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class GameTransaction extends Model
{
     use HasRoles;

    protected $guard_name = 'web';
   protected $table = 'game_transactions';

    protected $fillable = [
        'player_id',
        'banker_id',
        'bet_amount',
        'banker_amount',
        'tax',
        'type',
        'created_at',
        'updated_at',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function banker()
    {
        return $this->belongsTo(User::class, 'banker_id');
    }
}