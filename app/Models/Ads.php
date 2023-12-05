<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use VanOns\Laraberg\Traits\RendersContent;
class Ads extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use RendersContent;
     use HasRoles;

    protected $guard_name = 'web';
    protected $contentColumn = 'description';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function boot()
    {   
        
        parent::boot();
        Ads::saving(function (Ads $ads) {
            $ads->admin_id=Auth::id();
        });

        Ads::updating(function (Ads $ads) {
            //
        });
    }

    
}