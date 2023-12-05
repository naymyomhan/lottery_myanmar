<?php

use App\Http\Controllers\NovaActionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RefController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $currentDomain = request()->getHost();
    return redirect('https://dl.' . $currentDomain);
});

Route::get('/privacy_policy', function () {
    return view('privacy_policy');
});

Route::get('/topup/approve/{topup_id}', [NovaActionController::class, 'approve_topup'])->middleware('novaauth');

Route::get('/topup/reject/{topup_id}', [NovaActionController::class, 'topup_reject'])->middleware('novaauth');
Route::get('/topup/cancel/{topup_id}', [NovaActionController::class, 'topup_cancel'])->middleware('novaauth');
// /cash_out/reject/
Route::get('/cash_out/make_as_done/{cash_out_id}', [NovaActionController::class, 'make_as_done'])->middleware('novaauth');
Route::get('/cash_out/reject/{cash_out_id}', [NovaActionController::class, 'make_as_reject'])->middleware('novaauth');

Route::get('/winers/pay_back/{section_id}', [NovaActionController::class, 'pay_back_winers'])->middleware('novaauth');

Route::get('/winers/tpay_back/{three_d_ledgers_id}', [NovaActionController::class, 'tpay_back_winners'])->middleware('novaauth');

Route::get('/winers/rtpay_back/{three_d_ledgers_id}', [NovaActionController::class, 'rtpay_back_winners'])->middleware('novaauth');


Route::get('/privacy_policy', function () {
    return view('privacy_policy');
});




Route::get('/topup/approve/{id}', [NovaActionController::class, 'approve_topup'])->middleware('novaauth');
Route::get('/messenger', [MessageController::class, 'messenger'])->middleware('novaauth');
Route::get('/messenger/users', [MessageController::class, 'get_users'])->middleware('novaauth');
Route::post('/admin/message/send', [MessageController::class, 'send_message'])->middleware('novaauth');
Route::post('messenger/image/send/{user_id}', [MessageController::class, 'admin_send_image'])->name('admin_send_image');
Route::post('messenger/user/{id}/read', [MessageController::class, 'make_as_read'])->middleware('novaauth');


Route::get('/user/messenger/{token}', [MessageController::class, 'user_messenger']);
Route::post('/user/messenger/{bearer_token}/image/send/', [MessageController::class, 'user_send_image'])->name('user_send_image');
