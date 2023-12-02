<?php

namespace App\Models;

use App\Services\FCMService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Notification extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'image_path',
        'image_name',
        'image_location',
        'user_id',
        'type'
    ];

    protected $guard_name = 'web';

    protected $table = 'notification';

    public static function boot()
    {
        parent::boot();
        Notification::saving(function (Notification $notification) {
            if (is_null($notification->user_id)) {
                $notification->user_id = 0;
                $notification->type = 0;
                // FCMService::send($notification->title, $notification->message, env("DO_STORAGE_URL") . $notification->image_location);
            }
        });

        Notification::updating(function (Notification $notification) {
            if (is_null($notification->user_id)) {
                $notification->user_id = 0;
                // FCMService::send($notification->title, $notification->message, env("DO_STORAGE_URL") . $notification->image_location);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
