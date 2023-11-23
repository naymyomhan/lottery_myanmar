<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    use ResponseTrait;
    //

    public function login(Request $request){
        try {
            $validateRequest = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validateRequest->fails()){
                // return $this->fail($validator->errors());
                return $this->fail("Validation fail");
            }

            if(!Auth::guard('admin')->attempt($request->only(['email','password']))){
                return $this->fail("Email or password does not match",401);
            }

            $admin=Admin::where('email',$request->email)->first();
            $data=[
                'token'=>$admin->createToken("admin_token",["admin"])->plainTextToken,
            ];
            return $this->success("Admin loged in successfully",$data);

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(),500);
        }
    }
}