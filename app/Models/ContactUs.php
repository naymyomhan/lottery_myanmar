<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
// use Spatie\Permission\Traits\HasRoles;
class ContactUs extends Model implements Auditable
{
    use HasFactory;
     use \OwenIt\Auditing\Auditable;
    //  use HasRoles;
    protected $guard_name = 'web';
}