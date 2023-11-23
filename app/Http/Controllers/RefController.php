<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class RefController extends Controller
{
     use ResponseTrait;

    public function checkRef(Request $request)
    {
          $users = User::all();

    foreach ($users as $user) {
        if (empty($user->refer_code)) {
            $referCode = "ZM777" . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $user->refer_code = $referCode;
            $user->save();
        }
    }
    }
}