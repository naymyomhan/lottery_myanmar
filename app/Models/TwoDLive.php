<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class TwoDLive extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guard_name = 'web';
    protected $table = '2dlive_model';

    protected $fillable = ['set', 'value', 'time', 'twod', 'created_at'];
}
