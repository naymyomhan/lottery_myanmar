<?php

namespace App\Models;

use App\Services\FCMService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Result extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
         use HasRoles;

    protected $guard_name = 'web';

    protected $fillable=[
        'ledger_id',
        'section_id',
        'number_id',
        'number',
        'set',
        'val',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function ledger(){
        return $this->belongsTo(Ledger::class);
    }

    public static function boot()
{
    parent::boot();

    Result::saving(function (Result $result) {
        // add ledger_id
        $result->ledger_id = $result->section->ledger_id;

        // add number id
        switch ($result->section->section_index) {
            case 0:
                $morningNumber = MmMorningNumber::where('section_id', $result->section->id)
                    ->where('number', $result->number)
                    ->first();
                if ($morningNumber) {
                    TwoDHistory::create([
                        'set' => $result->set,
                        'value' => $result->val,
                        'open_time' => '10:45:00',
                        'twod' => $result->number
                    ]);
                    $result->number_id = $morningNumber->id;
                    FCMService::send("2D Result", "10:45 result number is $result->number", null);
                } 
                break;
            case 1:
                $noonNumber = MmNoonNumber::where('section_id', $result->section->id)
                    ->where('number', $result->number)
                    ->first();
                if ($noonNumber) {
                    TwoDHistory::create([
                        'set' => $result->set,
                        'value' => $result->val,
                        'open_time' => '12:01:00',
                        'twod' => $result->number
                        
                    ]);
                    $result->number_id = $noonNumber->id;
                    FCMService::send("2D Result", "12:01 result number is $result->number", null);
                }
                break;
            case 2:
                $afterNoonNumber = MmAfterNoonNumber::where('section_id', $result->section->id)
                    ->where('number', $result->number)
                    ->first();
                if ($afterNoonNumber) {
                    TwoDHistory::create([
                        'set' => $result->set,
                        'value' => $result->val,
                        'open_time' => '14:45:00',
                        'twod' => $result->number
                    ]);
                    $result->number_id = $afterNoonNumber->id;
                    FCMService::send("2D Result", "2:45 result number is $result->number", null);
                }
                break;
            case 3:
                $eveningNumber = MmEveningNumber::where('section_id', $result->section->id)
                    ->where('number', $result->number)
                    ->first();
                if ($eveningNumber) {
                    TwoDHistory::create([
                        'set' => $result->set,
                        'value' => $result->val,
                        'open_time' => '16:30:00',
                        'twod' => $result->number
                    ]);
                    $result->number_id = $eveningNumber->id;
                    FCMService::send("2D Result", "4:30 result number is $result->number", null);
                }
                break;
        }
    });
}





    
}