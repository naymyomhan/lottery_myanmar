<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Traits\CheckAuthTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentProfileController extends Controller
{   
    use ResponseTrait;

    private function checkIfAgent(){
        if(!Auth::user()->refer_code){
            return $this->fail("Unauth");
        }
    }
    

    public function profile(){

        return Auth::user();

        
        // if(Auth::user()->refer_code){
        //     return Auth::user();
        // }
        // else{
        //     return $this->fail("Unauth");
        // }
        // try {
        //     return Auth::guard('agent');
        // } catch (\Throwable $th) {
        //     return $this->fail($th->getMessage(),500);
        // }
    }


}
