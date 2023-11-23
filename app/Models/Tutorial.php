<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Tutorial extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $guard_name = 'web';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function boot()
    {   
        parent::boot();
        Tutorial::saving(function (Tutorial $tutorial) {
            $tutorial->admin_id=Auth::id();
            $tutorial->video_name=str_replace("Videos","",$tutorial->video_location);
            $tutorial->video_path="Videos";

            $tutorial->image_name=str_replace("Tutorials","",$tutorial->image_location);
            $tutorial->image_path="Tutorials";
        });
    }
}