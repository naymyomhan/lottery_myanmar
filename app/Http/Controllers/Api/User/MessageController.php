<?php

namespace App\Http\Controllers\Api\User;

use App\Events\PrivateMessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\TopUp;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    use ResponseTrait;

    public function send_message(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'message'=>'required'
            ]);

            if($validate->fails()){
                return $this->fail("validation error",400);
            }

            $new_message=Message::create([
                'user_id'=>Auth::id(),
                'message'=>$request->message,
            ]);

            $user=User::find(Auth::id());
            if($new_message){
                $user->last_message_send_at=$new_message->created_at;
                $user->active=true;
                $user->save();
            }

            $new_message->name=$user->name;
            $new_message->profile_picture=env('DO_STORAGE_URL').$user->profile_picture_location;
            $new_message->active=$user->active;

            if($new_message->created_at!=null && $new_message->created_at != ""){
                $new_message->message_date=Carbon::createFromFormat('Y-m-d H:i:s',$new_message->created_at)->format('M d,Y');
                $new_message->message_time=Carbon::createFromFormat('Y-m-d H:i:s',$new_message->created_at)->format('H:i a');
            }else{
                $new_message->message_date='Jun 1, 2000';
                $new_message->message_time='12:00 am';
            }


            $data=[
                'new_message'=>$new_message,
            ];

            event(new PrivateMessageEvent($data));
            DB::commit();
            return $this->success('send message successful',$data);


        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function get_messages()
    {
        try {
            $messages=Auth::user()->messages;

            $data=[
                'messages'=>$messages
            ];
            return $this->success('get messages successful',$data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }


      public function topUpGrade(Request $request){
      $thirtyDaysAgo = Carbon::now()->subDays(30);

// Query the database to get the users with the most successful top-ups in the last 30 days, along with the total amount
$topUsers = TopUp::where('created_at', '>=', $thirtyDaysAgo)
    ->where('success', 1) // Filter successful top-ups only
    ->select('user_id', \DB::raw('COUNT(*) as num_topups'), \DB::raw('SUM(amount) as total_amount'))
    ->groupBy('user_id')
    ->orderBy('total_amount', 'desc') // Order by the total amount in descending order
    ->get();

// Check if there are users with successful top-ups in the last 30 days
if ($topUsers->count() > 0) {
    // Iterate through each top user to fetch the user name and refer_code
    foreach ($topUsers as $topUser) {
        $user = User::find($topUser->user_id);
        if ($user) {
            $topUser->user_name = $user->name;
            $topUser->refer_code = $user->refer_code;
        }
    }

    // Return the response as JSON
    return response()->json($topUsers);
} else {
    // Return an empty response with a 404 status code (Not Found) if no successful top-ups were found in the last 30 days.
    return response()->json(['message' => 'No successful top-ups found in the last 30 days.'], 404);
}
      }
}