<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cog\Laravel\Ban\Traits\Bannable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class BanList extends Model implements Auditable
{
    use HasFactory;
    use Bannable;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $table = 'bans';

      protected $fillable = [
        'bannable_id',
        'created_by_id',
        'comment',
        'expired_at',
        'created_at'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class,'created_by_id');
    }

     public function user()
    {
        return $this->belongsTo(User::class,'bannable_id');
    }

    public function getBannedAttribute()
   {
        return $this->isBanned();
   }

    public function getBannedUntilAttribute()
   {
        $ban = $this->bans()->first();
        if($ban){
            return $ban->expired_at;
        }
        return null;
    }

    public function getBannedReasonAttribute()
    {
        $ban = $this->bans()->first();
        if($ban){
            return $ban->comment;
        }
        return null;
}
}