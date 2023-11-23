<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\Faq;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndConditions;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BetZNEController extends Controller
{   
    use ResponseTrait;

    public function frequently_asked_question()
    {
        try {
            $faqs=Faq::all();

            $data=[
                "faq"=>$faqs,
            ];

            return $this->success("get FAQs successful",$data);
            
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function terms_and_conditions()
    {
        try {
            $tnc=TermsAndConditions::all();

            $data=[
                "faq"=>$tnc,
            ];

            return $this->success("get termas and conditions successful",$data);
            
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function privacy_policy()
    {
        try {
            $privacy_policy=PrivacyPolicy::all();

            $data=[
                "privacy_policy"=>$privacy_policy,
            ];

            return $this->success("get privacy-policy successful",$data);
            
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function contact_us()
    {
        try {
            $contact_us=ContactUs::all();

            $data=[
                "contact_us"=>$contact_us,
            ];

            return $this->success("get contacts successful",$data);
            
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }
}