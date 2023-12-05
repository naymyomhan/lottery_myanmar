<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecommendationController extends Controller
{
    use ResponseTrait;

    public function recommendation(Request $request)
    {   
        DB::beginTransaction();
        try {

            $validate = Validator::make($request->all(),
            [
                'title'=>'required',
                'description'=>'required',
            ]);

            if($validate->fails()){
                if(isset($validate->failed()['title'])){
                    return $this->fail("title is required",400);
                }
                if(isset($validate->failed()['description'])){
                    return $this->fail("description is required",400);
                }
                return $this->fail("validation error",400);
            }

             $user = Auth::user();
           

            
            $new_recommendation=Recommendation::create([
                'user_id'=>$user->id,
                'title'=>$request->title,
                'description'=>$request->description,
            ]);

            DB::commit();

            return $this->success('recommendation sent successful',$new_recommendation);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }
}