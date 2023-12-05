<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppData extends Model
{
    use HasFactory;

    protected $fillable = ['lasted_version', 'version_code', 'in_maintenance', 'message', 'has_update', 'link'];
}
