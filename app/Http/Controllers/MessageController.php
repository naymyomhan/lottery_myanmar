<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageEvent;
use App\Models\Message;
use App\Models\TopUp;
use App\Models\User;
use App\Services\FCMService;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class MessageController extends Controller
{
    use ResponseTrait;

    public function messenger(Request $request)
    {
        $users=User::all();

        if($request->to){
            $user_id=$request->to;
            $target_user=User::find($user_id);
            if(!$target_user){
                return view('admin_messenger',compact('users'));
            }
            $target_user->active=false;
            $target_user->save();
        }

        if($request->to){
            return view('admin_messenger',compact('users','target_user'));
        }else{
            return view('admin_messenger',compact('users'));
        }
    }

    public function get_users(Request $request)
    {
        if($request->search){
            $users=User::orderBy('last_message_send_at','DESC')
            ->where('name', 'LIKE', "%{$request->search}%")
            ->orWhere('phone', 'LIKE', "%{$request->search}%")
            ->paginate(20);
        }else{
            $users=User::has('messages')->orderBy('last_message_send_at','DESC')->paginate(50);
        }



        foreach($users as $user){
            $user->profile_picture=env('DO_STORAGE_URL').$user->profile_picture_location;

            if($user->last_message_send_at!=null && $user->last_message_send_at!=""){
                $user->message_date=Carbon::createFromFormat('Y-m-d H:i:s',$user->last_message_send_at)->format('M d,Y');
                $user->message_time=Carbon::createFromFormat('Y-m-d H:i:s',$user->last_message_send_at)->format('H:i a');
            }else{
                $user->message_date='Jun 1, 2000';
                $user->message_time='12:00 am';
            }

            $last_message=$user->messages->last();
            if($last_message){
                $user->last_message=$last_message->message;
            }else{
                $user->last_message="No message yet";
            }

            unset($user->verify_code_send_at);
            unset($user->verify_code_send_count);
            unset($user->verify_code);
            unset($user->verify_attempt_count);
            unset($user->last_verify_attempt_at);
            unset($user->phone_verified_at);
            unset($user->profile_picture_path);
            unset($user->profile_picture_name);
            unset($user->profile_picture_location);
        }

        return $users;
    }

    //Admin Message
    public function send_message(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'user_id'=>'required|numeric',
                'message'=>'required'
            ]);

            if($validate->fails()){
                return $this->fail("validation error",400);
            }

            $new_message=Message::create([
                'admin_id'=>Auth::guard('admin')->user()->id,
                'user_id'=>$request->user_id,
                'message'=>$request->message,
            ]);

            $user=User::find($request->user_id);
            if($user){
                $user->last_message_send_at=$new_message->created_at;
                $user->save();
            }

            $new_message->name=$user->name;
            $new_message->profile_picture=env('DO_STORAGE_URL').$user->profile_picture_location;
            $new_message->active=$user->active;

            if($new_message->created_at!=null && $new_message->created_at!=""){
                $new_message->message_date=Carbon::createFromFormat('Y-m-d H:i:s',$new_message->created_at)->format('M d,Y');
                $new_message->message_time=Carbon::createFromFormat('Y-m-d H:i:s',$new_message->created_at)->format('H:i a');
            }else{
                $new_message->message_date='Jun 1, 2000';
                $new_message->message_time='12:00 am';
            }


            $data=[
                'new_message'=>$new_message,
            ];

            FCMService::userSend(env('APP_NAME') . ' CS', $request->message, null, $user->fcm_token);

            event(new PrivateMessageEvent($data));
            DB::commit();
            return $this->success("send message successful",$data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }


    public function user_messenger($token)
    {
        $bearer_token=$token;
        if(!PersonalAccessToken::findToken($token)){
            return redirect('/');
        }
        $my_token=PersonalAccessToken::findToken($token)->token;
        $token = PersonalAccessToken::where('token', $my_token)->first();
        if(!$token){
            return redirect('/');
        }
        $user = $token->tokenable;

        $messages=$user->messages;

        return view('user_messenger',compact('user','messages','bearer_token'));
    }

    public function user_send_image(Request $request,$bearer_token)
    {
        DB::beginTransaction();
        if(!PersonalAccessToken::findToken($bearer_token)){
            return back();
        }
        $my_token=PersonalAccessToken::findToken($bearer_token)->token;
        $token = PersonalAccessToken::where('token', $my_token)->first();
        if(!$token){
            return back();
        }
        $user = $token->tokenable;

        try {
            $validate = Validator::make($request->all(),
            [
                'image'=>'required'
            ]);

            if($validate->fails()){
                return back();
            }

            $image = $request->file('image');
            $image_name=$image->getClientOriginalName();
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            $upload_file_name=date('mdYHis').uniqid()."_moeZ.".$extension;
            $filePath = 'MoeZmessage/' . $upload_file_name;
            Storage::disk('do')->put($filePath, file_get_contents($image), 'public');

            $new_message=Message::create([
                'user_id'=>$user->id,
                'message'=>'Image',
                'image_path'=>'MoeZmessage',
                'image_name'=>$upload_file_name,
                'image_location'=>$filePath,
            ]);

            if($new_message){
                $user->last_message_send_at=$new_message->created_at;
                $user->active=true;
                $user->save();
            }

            $new_message->name=$user->name;
            $new_message->profile_picture=env('DO_STORAGE_URL').$user->profile_picture_location;

            $data=[
                'new_message'=>$new_message,
            ];

            event(new PrivateMessageEvent($data));
            DB::commit();
            return back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    //Admin Message (image)
    public function admin_send_image(Request $request,$user_id)
    {
        try {
            $validate = Validator::make($request->all(),
            [
                'image'=>'required'
            ]);

            if($validate->fails()){
                return back();
            }

            $image = $request->file('image');
            $image_name=$image->getClientOriginalName();
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            $upload_file_name=date('mdYHis').uniqid()."_moeZ.".$extension;
            $filePath = 'MoeZmessage/' . $upload_file_name;
            Storage::disk('do')->put($filePath, file_get_contents($image), 'public');

            $new_message=Message::create([
                'admin_id'=>Auth::guard('admin')->user()->id,
                'user_id'=>$user_id,
                'message'=>'Image',
                'image_path'=>'MoeZmessage',
                'image_name'=>$upload_file_name,
                'image_location'=>$filePath,
            ]);

            $data=[
                'new_message'=>$new_message,
            ];

            event(new PrivateMessageEvent($data));

            return back();


        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function make_as_read($user_id)
    {
        $user=User::find($user_id);
        if($user){
            $user->active=false;
            $user->save();
        }
    }


    


}