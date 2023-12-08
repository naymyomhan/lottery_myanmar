<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CashOut;
use App\Models\MmAfterNoonBet;
use App\Models\MmEveningBet;
use App\Models\MmMorningBet;
use App\Models\MmNoonBet;
use App\Models\Notification;
use App\Models\PayBack;
use App\Models\RThreeDPayBack;
use App\Models\Section;
use App\Models\ThreDNumberBet;
use App\Models\ThreeDLedger;
use App\Models\ThreeDPayBack;
use App\Models\TopUp;
use App\Models\User;
use App\Traits\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NovaActionController extends Controller
{
    use NotificationService;
    function approve_topup($topup_id)
    {
        $topup = TopUp::find($topup_id);
        if (!$topup) {
            return redirect('/nova');
        }
        if ($topup->success == true) {
            return redirect('/nova');
        }
        if ($topup->success == false) {
            if ($topup) {
                $user = User::find($topup->user_id);
                if ($user) {
                    $topup->success = true;
                    $topup->admin_id = Auth::guard('admin')->user()->id;
                    $save = $topup->save();
                    if ($save) {
                        $user->main_wallet->increment('balance', $topup->amount);
                        //Send Data and Notification
                        $this->sendEvent($user->firebase_token, "TOPUP");
                    }
                }
            }
            return back();
        }
    }

    function topup_reject($topup_id)
    {
        $topup = TopUp::find($topup_id);
        if ($topup) {
            $user = User::find($topup->user_id);
            if ($user) {
                $topup->success = false;
                $topup->payment_transaction_number = '';
                $topup->admin_id = Auth::guard('admin')->user()->id;
                $save = $topup->save();
                //Send Data and Notification
                $this->sendEvent($user->firebase_token, "TOPUP");
            }
        }
        return back();
    }

    // topup_cancel

    function topup_cancel($topup_id)
    {
        $topup = TopUp::find($topup_id);
        if ($topup) {
            $user = User::find($topup->user_id);
            if ($user) {
                $topup->success = 2;
                $topup->admin_id = Auth::guard('admin')->user()->id;
                $topup->save();
                //Send Data and Notification
            }
        }
        return back();
    }

    public function make_as_done($cash_out_id)
    {
        try {
            $cash_out = CashOut::find($cash_out_id);
            if (!$cash_out) {
                return redirect('/nova');
            }
            if ($cash_out->success == false) {
                $user = User::find($cash_out->user_id);
                if ($user) {
                    if ($user->main_wallet->balance >= $cash_out->amount) {
                        $cash_out->success = true;
                        $cash_out->admin_id = Auth::guard('admin')->user()->id;
                        $save = $cash_out->save();
                        if ($save) {
                            $user->main_wallet->decrement('balance', $cash_out->amount);
                            //Send Data and Notification
                        }
                    }
                }
            }

            return back();
        } catch (\Throwable $th) {
            return redirect('/nova');
        }
    }

    // tpay_back_winers

    // make_as_reject
    public function make_as_reject($cash_out_id)
    {
        try {
            $cash_out = CashOut::find($cash_out_id);
            if (!$cash_out) {
                return redirect('/nova');
            }
            if ($cash_out) {
                $user = User::find($cash_out->user_id);
                if ($user) {
                    $cash_out->success = 2;
                    $cash_out->admin_id = Auth::guard('admin')->user()->id;
                    $cash_out->save();
                    //Send Data and Notification
                }
            }
            return back();
        } catch (\Throwable $th) {
            return redirect('/nova');
        }
    }



    public function tpay_back_winners($three_d_ledgers_id)
    {
        DB::beginTransaction();
        try {
            $ledger = ThreeDLedger::find($three_d_ledgers_id);
            Log::info("Ledger: $ledger");
            if ($ledger) {
                $winners = $ledger->winers;
                Log::info("winners: $winners");
                if ($winners) {
                    foreach ($winners as $winner) {
                        if ($winner->type == 0) {
                            $amount = ThreDNumberBet::find($winner->bet_id)->amount * $ledger->pay_back_multiply;

                            // Create Payback record
                            ThreeDPayBack::create([
                                'user_id' => $winner->user_id,
                                'winner_id' => $winner->id,
                                'three_d_ledger_id' => $winner->three_d_ledger_id,
                                'amount' => $amount,
                            ]);

                            // Add money to winner wallet
                            $user = User::find($winner->user_id);
                            $user->main_wallet()->increment('balance', $amount);
                            // $new_noti = Notification::create([
                            //     'user_id' => $user->id,
                            //     'title' => "ဂုဏ်ယူပါတယ်!",
                            //     'message' => "သင့် ဆုကြေးငွေ $amount Ks ကိုရရှိပါသည်",
                            //     'image_path' => "",
                            //     'image_name' => "",
                            //     'image_location' => "",
                            //     'type' => 3,
                            // ]);
                            //Send Data and Notification

                            // Log successful payment
                            Log::info("Payment successful for winner with ID: $winner->id");
                        }
                    }
                } else {
                    throw new \Exception("No winners found for ledger ID: $three_d_ledgers_id");
                }
            } else {
                throw new \Exception("Ledger not found with ID: $three_d_ledgers_id");
            }

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();

            // Log the error
            Log::error("An error occurred while processing payments: " . $th->getMessage());

            return redirect()->back();
        }
    }

    public function rtpay_back_winners($three_d_ledgers_id)
    {
        DB::beginTransaction();
        try {
            $ledger = ThreeDLedger::find($three_d_ledgers_id);
            Log::info(" R: $ledger");
            if ($ledger) {
                $winners = $ledger->winers;
                Log::info("R winners: $winners");
                if ($winners) {
                    foreach ($winners as $winner) {
                        if ($winner->type == 1) {
                            $amount = ThreDNumberBet::find($winner->bet_id)->amount * $ledger->r_pay_back_multiply;

                            // Create Payback record
                            RThreeDPayBack::create([
                                'user_id' => $winner->user_id,
                                'winner_id' => $winner->id,
                                'three_d_ledger_id' => $winner->three_d_ledger_id,
                                'amount' => $amount,
                            ]);

                            // Add money to winner wallet
                            $user = User::find($winner->user_id);
                            $user->main_wallet()->increment('balance', $amount);
                            // $new_noti = Notification::create([
                            //     'user_id' => $user->id,
                            //     'title' => "ဂုဏ်ယူပါတယ်!",
                            //     'message' => "သင့် ဆုကြေးငွေ $amount Ks ကိုရရှိပါသည်",
                            //     'image_path' => "",
                            //     'image_name' => "",
                            //     'image_location' => "",
                            //     'type' => 3,
                            // ]);
                            //Send Data and Notification

                            // Log successful payment
                            Log::info("Payment successful for winner with ID: $winner->id");
                        }
                    }
                } else {
                    throw new \Exception("No winners found for ledger ID: $three_d_ledgers_id");
                }
            } else {
                throw new \Exception("Ledger not found with ID: $three_d_ledgers_id");
            }

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();

            // Log the error
            Log::error("An error occurred while processing payments: " . $th->getMessage());

            return redirect()->back();
        }
    }




    public function pay_back_winers($section_id)
    {
        DB::beginTransaction();
        try {
            $section = Section::find($section_id);
            $winners = $section->winers;

            foreach ($winners as $winner) {

                switch ($winner->section->section_index) {
                    case 0:
                        $amount = MmMorningBet::find($winner->bet_id)->amount * $winner->section->pay_back_multiply;
                        break;
                    case 1:
                        $amount = MmNoonBet::find($winner->bet_id)->amount * $winner->section->pay_back_multiply;
                        break;
                    case 2:
                        $amount = MmAfterNoonBet::find($winner->bet_id)->amount * $winner->section->pay_back_multiply;
                        break;
                    case 3:
                        $amount = MmEveningBet::find($winner->bet_id)->amount * $winner->section->pay_back_multiply;
                        break;
                }

                //Create Pay back record
                $new_pay_back = PayBack::create([
                    'user_id' => $winner->user_id,
                    'winner_id' => $winner->id,
                    'section_id' => $winner->section_id,
                    'amount' => $amount,
                ]);

                //Add money to winner wallet
                $user = User::find($winner->user_id);
                $user->main_wallet->increment('balance', $amount);
                // $new_noti = Notification::create([
                //     'user_id' => $user->id,
                //     'title' => "ဂုဏ်ယူပါတယ်!",
                //     'message' => "သင့် ဆုကြေးငွေ $amount Ks ကိုရရှိပါသည်",
                //     'image_path' => "",
                //     'image_name' => "",
                //     'image_location' => "",
                //     'type' => 3,
                // ]);
                //Send Data and Notification
            }
            DB::commit();
            return back();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back();
        }
    }
}
