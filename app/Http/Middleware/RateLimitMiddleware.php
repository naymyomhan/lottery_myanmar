<?php

namespace App\Http\Middleware;

use App\Models\UserDeviceId;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $routeAction = request()->route()->uri();
        // return $this->fail($routeAction);

        //Check if request come from browser
        $userAgent = $request->header('User-Agent');
        $isFromBrowser = stripos($userAgent, 'mozilla') !== false || stripos($userAgent, 'chrome') !== false;
        if ($isFromBrowser) {
            return "Request is coming from a web browser.";
        }


        //for mobile devices
        $device_id = $request->header('SEC-MM-SECTION');
        $version = $request->header('SEC-MM-CODE');

        $user = Auth::user();

        if ($user) {
            $createDate = $user->created_at->format('Ymd');
            $cutoffDate = '20231025';

            if ($createDate > $cutoffDate) {
                //user create before 10-25
                $user_device_id = UserDeviceId::where('device_id', $device_id)->first();
                if (!$user_device_id) {
                    return $this->fail("တစ်စုံတစ်ခုမှားနေပါသည်", 500);
                }
            }
        }

        if ($version != '1007') {
            return $this->fail("တစ်စုံတစ်ခုမှားနေပါသည်", 400);
        }

        return $next($request);
    }
}