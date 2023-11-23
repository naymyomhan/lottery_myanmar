<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminProfileController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Agent\AgentAuthController;
use App\Http\Controllers\Api\Agent\AgentProfileController;
use App\Http\Controllers\Api\User\BetController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\SectionController;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\WalletController;
use App\Http\Controllers\Api\User\BetZNEController;
use App\Http\Controllers\Api\User\WinnerController;
use App\Http\Controllers\Api\User\AdsController;
use App\Http\Controllers\Api\User\HolidayController;
use App\Http\Controllers\Api\User\ThreeDBetController;
use App\Http\Controllers\Api\User\HistoryController;
use App\Http\Controllers\Api\User\ImageController;
use App\Http\Controllers\Api\User\NotiController;
use App\Http\Controllers\Api\User\RecommendationController;
use App\Http\Controllers\Api\User\TutorialController;
use App\Http\Controllers\Api\User\AppController;
use App\Http\Controllers\NotificaionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\MessageController;
use App\Http\Controllers\Api\User\ExchangeRateController;
use App\Http\Controllers\Api\Admin\UserPromotionController;

// Route::get('/storage_url',[SpaceController::class,'storage_url']);
// Route::post('/auth/presign',[SpaceController::class,'space_presign_url']);


//Account Creation And Login
Route::post('/user/auth/check_phone', [UserAuthController::class, 'check_phone']);
Route::post('/user/auth/register', [UserAuthController::class, 'register']);
Route::post('/user/auth/login', [UserAuthController::class, 'login']);

Route::post('/user/otp/request', [UserAuthController::class, 'request_otp'])->middleware('auth:sanctum');
Route::post('/user/otp/verify', [UserAuthController::class, 'verify_otp'])->middleware('auth:sanctum');

Route::get('/tdhistory', [HistoryController::class, 'getTwDHistory']);
Route::get('/tedhistory', [HistoryController::class, 'getTeDHistory']);
Route::get('/app', [AppController::class, 'getApps']);
Route::get('/exchage', [ExchangeRateController::class, 'getExchangeRate']);


Route::get('/topupgrade', [MessageController::class, 'topUpGrade']);
// Route::group(['middleware' => ['auth:sanctum','otp']], function() {
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/set/firebase/token', [UserAuthController::class, 'setFirebaseToken']);
    Route::post('/upload-profile-picture', [ImageController::class, 'upload']);

    //Profile or Wallet
    Route::get('/user/wallet', [WalletController::class, 'get_user_wallet']);
    Route::post('/user/wallet/transfer_to/game_wallet', [WalletController::class, 'transfer_to_game_wallet']);
    Route::post('/user/wallet/transfer_to/main_wallet', [WalletController::class, 'transfer_to_main_wallet']);
    Route::get('/user/wallet/transfer_history', [WalletController::class, 'transfer_history']);
    Route::get('/user/wallet/wallethistory', [WalletController::class, 'getWalletHistroy']);

    //Notificaton
    Route::get('/user/notifications', [NotiController::class, 'userNotis']);
    Route::get('/notification', [NotiController::class, 'allNotis']);

    //CashIn and CacheOut
    //TopUp
    Route::get('/user/topup/payment_methods', [PaymentController::class, 'get_cash_in_payments']);

    Route::post('/user/topup/prepare', [PaymentController::class, 'prepare_topup']);
    Route::get('/user/topup/prepared', [PaymentController::class, 'get_prepared_topup']);

    Route::post('/user/topup/confirm', [PaymentController::class, 'confirm_topup']);
    Route::get('/user/topup/confirmed', [PaymentController::class, 'get_confirmed_topup']);

    Route::get('/user/topups', [PaymentController::class, 'get_topup_history']);

    //Cach Out
    Route::get('/user/cash_out/cash_out_methods', [PaymentController::class, 'get_cash_out_payments']);
    Route::post('/user/cash_out/request', [PaymentController::class, 'request_cash_out']);
    Route::get('/user/cash_outs', [PaymentController::class, 'get_cash_out_history']);

    //Bet
    Route::get('/sections', [SectionController::class, 'get_sections']);
    Route::get('/section/{section_id}/numbers', [SectionController::class, 'get_numbers']);

    Route::post('/section/{section_id}/bet', [BetController::class, 'bet']);
    Route::get('/bet/histories', [BetController::class, 'get_bet_histories']);
    Route::get('/bet/history/{voucher_id}', [BetController::class, 'get_bet_history_detail']);

    //Winner
    Route::get('/winners', [WinnerController::class, 'get_winners']);
    Route::get('section/{section_id}/winners', [WinnerController::class, 'get_winners_by_section']);

    // 3D Bet
    Route::get('/tdlottery', [ThreeDBetController::class, 'get_tdlottery']);
    Route::get('/tdlottery/{ledger_id}/numbers', [ThreeDBetController::class, 'get_numbers']);

    Route::post('/tdlottery/{ledger_id}/bet', [ThreeDBetController::class, 'bet']);
    Route::get('/tdlottery/bet/histories', [ThreeDBetController::class, 'get_bet_histories']);
    Route::get('/tdlottery/bet/history/{voucher_id}', [ThreeDBetController::class, 'get_bet_history_detail']);

    // 3D Winner
    Route::get('/tdwinners', [WinnerController::class, 'get_tdwinners']);
    // Route::get('section/{section_id}/winners',[WinnerController::class,'get_winners_by_section']);


    // - FAQ,T&Q,P&P,Contact
    Route::get('/frequently_asked_question', [BetZNEController::class, 'frequently_asked_question']);
    Route::get('/terms_and_conditions', [BetZNEController::class, 'terms_and_conditions']);
    Route::get('/privacy_policy', [BetZNEController::class, 'privacy_policy']);
    Route::get('/contact_us', [BetZNEController::class, 'contact_us']);
    Route::get('/tutorials', [TutorialController::class, 'get_tutorials']);
    // get Ads
    Route::get('/ads', [AdsController::class, 'getAds']);
    // get holidays
    Route::get('/holidays', [HolidayController::class, 'getHolidays']);
    // Recommendation
    Route::post('/user/recommendation', [RecommendationController::class, 'recommendation']);

    //Message
    Route::post('/message/send', [MessageController::class, 'send_message']);
    Route::get('/messages/get', [MessageController::class, 'get_messages']);
});


//Admin
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'admins']], function () {
    Route::get('/admin/anlys', [AdminProfileController::class, 'anlys']);
    Route::get('/admin/profile', [AdminProfileController::class, 'profile']);
    Route::get('/admin/user/list', [AdminUserController::class, 'userlist']);
    Route::post('/admin/user/check', [AdminUserController::class, 'checkuser']);
    Route::get('/admin/promotionlist', [UserPromotionController::class, 'promotionlist']);
    Route::get('/admin/promotion/topuplist', [UserPromotionController::class, 'ptopuplist']);
    Route::post('/admin/promotion/topup/create', [UserPromotionController::class, 'user_promotion_topup_create']);
});

//Agent

Route::post('/agent/login', [AgentAuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'agent']], function () {
    Route::get('/agent/profile', [AgentProfileController::class, 'profile']);
});

// API V2 ( post method )

Route::prefix('apiv2')->group(function () {
    Route::post('/user/auth/check_phone', [UserAuthController::class, 'check_phone']);
    Route::post('/user/auth/register', [UserAuthController::class, 'register']);
    Route::post('/user/auth/login', [UserAuthController::class, 'login']);

    Route::post('/user/otp/request', [UserAuthController::class, 'request_otp'])->middleware('auth:sanctum');
    Route::post('/user/otp/verify', [UserAuthController::class, 'verify_otp'])->middleware('auth:sanctum');

    Route::post('/tdhistory', [HistoryController::class, 'getTwDHistory']);
    Route::post('/tedhistory', [HistoryController::class, 'getTeDHistory']);
    Route::post('/app', [AppController::class, 'getApps']);
    Route::post('/exchage', [ExchangeRateController::class, 'getExchangeRate']);

    Route::post('/topupgrade', [MessageController::class, 'topUpGrade']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/set/firebase/token', [UserAuthController::class, 'setFirebaseToken']);
         Route::post('/upload-profile-picture', [ImageController::class, 'upload']);

        // Profile or Wallet
        Route::post('/user/wallet', [WalletController::class, 'get_user_wallet']);
        Route::post('/user/wallet/transfer_to/game_wallet', [WalletController::class, 'transfer_to_game_wallet']);
        Route::post('/user/wallet/transfer_to/main_wallet', [WalletController::class, 'transfer_to_main_wallet']);
        Route::post('/user/wallet/transfer_history', [WalletController::class, 'transfer_history']);

        //Notificaton
        Route::post('/user/notifications', [NotiController::class, 'userNotis']);
        Route::post('/notification', [NotiController::class, 'allNotis']);

        // CashIn and CacheOut
        // TopUp
        Route::post('/user/topup/payment_methods', [PaymentController::class, 'get_cash_in_payments']);

        Route::post('/user/topup/prepare', [PaymentController::class, 'prepare_topup']);
        Route::post('/user/topup/prepared', [PaymentController::class, 'get_prepared_topup']);

        Route::post('/user/topup/confirm', [PaymentController::class, 'confirm_topup']);
        Route::post('/user/topup/confirmed', [PaymentController::class, 'get_confirmed_topup']);

        Route::post('/user/topups', [PaymentController::class, 'get_topup_history']);



        //Cach Out
        Route::post('/user/cash_out/cash_out_methods', [PaymentController::class, 'get_cash_out_payments']);
        Route::post('/user/cash_out/request', [PaymentController::class, 'request_cash_out']);
        Route::post('/user/cash_outs', [PaymentController::class, 'get_cash_out_history']);

        //Bet
        Route::post('/sections', [SectionController::class, 'get_sections']);
        Route::post('/section/{section_id}/numbers', [SectionController::class, 'get_numbers']);

        Route::post('/section/{section_id}/bet', [BetController::class, 'bet']);
        Route::post('/bet/histories', [BetController::class, 'get_bet_histories']);
        Route::post('/bet/history/{voucher_id}', [BetController::class, 'get_bet_history_detail']);

        //Winner
        Route::post('/winners', [WinnerController::class, 'get_winners']);
        Route::post('section/{section_id}/winners', [WinnerController::class, 'get_winners_by_section']);

        // 3D Bet
        Route::post('/tdlottery', [ThreeDBetController::class, 'get_tdlottery']);
        Route::post('/tdlottery/{ledger_id}/numbers', [ThreeDBetController::class, 'get_numbers']);

        Route::post('/tdlottery/{ledger_id}/bet', [ThreeDBetController::class, 'bet']);
        Route::post('/tdlottery/bet/histories', [ThreeDBetController::class, 'get_bet_histories']);
        Route::post('/tdlottery/bet/history/{voucher_id}', [ThreeDBetController::class, 'get_bet_history_detail']);

        // 3D Winner
        Route::post('/tdwinners', [WinnerController::class, 'get_tdwinners']);

        // - FAQ, T&Q, P&P, Contact
        Route::post('/frequently_asked_question', [BetZNEController::class, 'frequently_asked_question']);
        Route::post('/terms_and_conditions', [BetZNEController::class, 'terms_and_conditions']);
        Route::post('/privacy_policy', [BetZNEController::class, 'privacy_policy']);
        Route::post('/contact_us', [BetZNEController::class, 'contact_us']);
        Route::post('/tutorials', [TutorialController::class, 'get_tutorials']);

        // get Ads
        Route::post('/ads', [AdsController::class, 'getAds']);
        // get holidays
        Route::post('/holidays', [HolidayController::class, 'getHolidays']);
        // Recommendation
        Route::post('/user/recommendation', [RecommendationController::class, 'recommendation']);

        //Message
        Route::post('/message/send', [MessageController::class, 'send_message']);
        Route::post('/messages/get', [MessageController::class, 'get_messages']);


        // Message
        Route::post('/message/send', [MessageController::class, 'send_message']);
        Route::post('/messages/get', [MessageController::class, 'get_messages']);
    });
});


// Primary API

Route::post('/notification',[NotificaionController::class,'server_notification']);