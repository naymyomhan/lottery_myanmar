<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgentAuthController extends Controller
{   
    use ResponseTrait;

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

            if(!Auth::guard('agent')->attempt($request->only(['email','password']))){
                return $this->fail("Email or password does not match",401);
            }

            $user=Agent::where('email',$request->email)->first();
            $data=[
                'token'=>$user->createToken("agent_token",["agent"])->plainTextToken,
            ];
            return $this->success("Agent loged in successfully",$data);

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage(),500);
        }
    }
}
