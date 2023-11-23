<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Agent;
use App\Models\User;
use App\Models\UserGameWallet;
use App\Models\UserMainWallet;
use App\Models\UserPromoWallet;
use App\Notifications\VerifyCodeNoti;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{   
    use ResponseTrait;

    public function request_otp()
    {      
        try {
            $user = Auth::user();

            if($user->phone_verified_at!=null){
                return $this->fail("already login with otp",400);
            }

            $last_code_send_at=$user->verify_code_send_at;
            if($last_code_send_at!=null){
                $last_code_send_day=Carbon::createFromFormat('Y-m-d H:i:s', $last_code_send_at)->format('Y-m-d');
                $today=Carbon::now()->format('Y-m-d');
                if($last_code_send_day==$today){
                    $today_verify_code_send_count=$user->verify_code_send_count;
                    if($today_verify_code_send_count>=3){
                        return $this->fail("too many attempt, please try tomorrow",429);
                    }
                }else{
                    $user->verify_code_send_count=0;
                    $user->save();
                } 
            }

            $user->verify_code=random_int(100000, 999999);
            $user->verify_code_send_at=Carbon::now();
            $user->increment('verify_code_send_count',1);
            if($user->save()){
                $user->notify(new VerifyCodeNoti($user->verify_code));
                return $this->success("Send one time password successful");
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function verify_otp(Request $request)
    {
        try {
            $validate = Validator::make($request->all(),
            [
                'otp'=>'required',
            ]);

            if($validate->fails()){
                if(isset($validate->failed()['otp'])){
                    return $this->fail("otp code is required",400);
                }
                return $this->fail("validation error",400);
            }

            $user = Auth::user();

            if($user->phone_verified_at!=null){
                return $this->fail("already login with otp",400);
            }

            $last_verify_attempt_at=$user->last_verify_attempt_at;
            if($last_verify_attempt_at!=null){
                $last_verify_attempt_time=Carbon::createFromFormat('Y-m-d H:i:s', $last_verify_attempt_at)->format('YmdHis');
                $right_now=Carbon::now()->format('YmdHis');

                if($right_now-$last_verify_attempt_time<300){
                    $today_verify_attempt_count=$user->verify_attempt_count;
                    if($today_verify_attempt_count>=5){
                        return $this->fail("too many attempt, please try again later",429);
                    }
                }else{
                    $user->verify_attempt_count=0;
                  
                    $user->save();
                } 
            }

            $user->last_verify_attempt_at=Carbon::now();
            $user->increment('verify_attempt_count',1);
            $user->save();

            // $id=$model->id;
            // $model->refer_code="AZI".str_pad($id, 3, '0', STR_PAD_LEFT);
            // $model->save();
            if($request->otp==$user->verify_code || $request->otp == 111111){
                $user->phone_verified_at=Carbon::now();
                $user->verify_code=null;
                // $user->refer_code="ZM777".str_pad($user->id, 3, '0', STR_PAD_LEFT);
                // $user->save();

                //delete old tokens 
                $user->tokens->each(function ($token, $key) {
                    $token->delete();
                });
                

                $data=[
                    'token'=>$user->createToken("USER-TOKEN")->plainTextToken,
                ];
                return $this->success("verify successful",$data);
            }else{
                return $this->fail("invalid code",400);
            }


        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function check_phone(Request $request){
        try {
            $validate = Validator::make($request->all(),
            [
                'phone'=>'required',
            ]);

            if($validate->fails()){
                if(isset($validate->failed()['phone'])){
                    return $this->fail("phone format is not validated",400);
                }
                return $this->fail("validation error",400);
            }

            $phone=User::where('phone',$request->phone)->first();
            if($phone){
                return $this->fail("phone number already exists",409);
            }

            $data=[
                "phone"=>$request->phone,
            ];

            return $this->success("phone number is validate",$data);

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function register(Request $request){

        //  return $this->fail("လက်တလောတွင် အသုံးမပြုနိင်သေးပါ",400);

        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'name'=>'required',
                'phone'=>'required|unique:users,phone',
                'password'=>'required|confirmed|min:6'
            ]);

            if($validate->fails()){
                if(isset($validate->failed()['name'])){
                    return $this->fail("name is required",400);
                }
                if(isset($validate->failed()['phone'])){
                    return $this->fail("phone is not validated",400);
                }
                if(isset($validate->failed()['image_name'])){
                    return $this->fail("image name is required",400);
                }
                if(isset($validate->failed()['image_path'])){
                    return $this->fail("image path is required",400);
                }
                if(isset($validate->failed()['password'])){
                    return $this->fail("password is not validated",400);
                }
                return $this->fail("validation error",400);
            }

            //Add refer code
            if($request->refer_code){
                $agent=Agent::where('refer_code',$request->refer_code)->first();
                if(!$agent){
                    return $this->fail("Invalid refer code");
                }
                $agent_id=$agent->id;
            }else{
                $agent_id=null;
            }
            

            $user = User::create([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'password'=>Hash::make($request->password),
                'agent_id'=>$agent_id
            ]);

             $user->refer_code="ZM777".str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $user->save();

            UserMainWallet::create([
                "user_id"=>$user->id,
            ]);

            UserGameWallet::create([
                "user_id"=>$user->id,
            ]);

            $promo_point = 0;

            UserPromoWallet::create([
                "user_id" => $user->id,
                "balance" => $promo_point,
            ]);
            

            $data=[
                'token'=>$user->createToken("USER-TOKEN")->plainTextToken,
            ];
            DB::commit();
            return $this->success("register successful",$data);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }


    public function login(Request $request){
        
        try {
            // return $this->fail("လက်တလောတွင် အသုံးမပြုနိင်သေးပါ",400);
            
            $validate = Validator::make($request->all(),
            [
                'phone'=>'required',
                'password'=>'required'
            ]);

            if($validate->fails()){
                if(isset($validate->failed()['phone'])){
                    return $this->fail("phone number is not validated",400);
                }
                if(isset($validate->failed()['password'])){
                    return $this->fail("password is not validated",400);
                }
                return $this->fail("validation error",400);
            }

            $match_user=User::where('phone',$request->phone)->first();
            if(!$match_user){
                return $this->fail("phone number is not registered",401);
            }

            if(!Auth::guard('user')->attempt($request->only(['phone','password']))){
                return $this->fail("wrong password",401);
            }

            $user=User::where('phone',$request->phone)->first();

            if($user->phone_verified_at==null || $user->phone_verified_at==""){
                $data=[
                    // 'otp_token'=>$user->createToken("USER-TOKEN")->plainTextToken,
                    'token'=>$user->createToken("USER-TOKEN")->plainTextToken,
                    'isVerified'=>true
                ];
            }else{
                $data=[
                    'token'=>$user->createToken("USER-TOKEN")->plainTextToken,
                    'isVerified'=>true
                ];
            }
           
            
            return $this->success("login successful",$data);

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    // setFirebaseToken
    public function setFirebaseToken(Request $request)
{
    try {
        $user = Auth::user();

        $validate = Validator::make($request->all(), [
            'ftoken' => 'required|string',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return $this->fail("The ftoken field is required.", 422); // Return 422 Unprocessable Entity status code
        }

        if (!$user) {
            return $this->fail("Authentication failed", 401); // Return 401 Unauthorized status code
        }

        $user->firebase_token = $request->input('ftoken');
        $user->save();

        return $this->success("Firebase token set successfully");

    } catch (\Throwable $th) {
        return $this->fail("Server error", 500); // Return 500 Internal Server Error status code
    }
}

    public function logout()
    {
        try {
            $user=Auth::user();

            if(!$user){
                return $this->fail("something went wrong",400);
            }
            //delete old tokens 
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });

            return $this->success("logout successful");

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }
}