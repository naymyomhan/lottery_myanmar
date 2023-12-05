<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\CashOut;
use App\Models\CashOutMethod;
use App\Models\PaymentMethod;
use App\Models\TopUp;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ResponseTrait;
    public function get_cash_in_payments()
    {
        try {
            $payment_methods = PaymentMethod::all();
            foreach ($payment_methods as $payment_method) {
                $payment_method->image = env("DO_STORAGE_URL") . $payment_method->image_location;
                unset($payment_method->image_path);
                unset($payment_method->image_name);
                unset($payment_method->image_location);
                unset($payment_method->created_at);
                unset($payment_method->updated_at);
            }

            $data = ["payment_methods" => $payment_methods];
            return $this->success("get payment methods successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }


    public function prepare_topup(Request $request)
    {
        DB::beginTransaction();
        try {
            $last_topup = Auth::user()->topups->last();

            if ($last_topup) {
                if ($last_topup->success == 0 && ($last_topup->payment_transaction_number == null || $last_topup->payment_transaction_number == "")) {
                    return $this->fail("you have an uncompleted topup", 400);
                }
            }

            $validate = Validator::make(
                $request->all(),
                [
                    'payment_method_id' => 'required',
                    'amount' => 'required',
                ]
            );

            if ($validate->fails()) {
                if (isset($validate->failed()['payment_method_id'])) {
                    return $this->fail("payment method is required", 400);
                }
                if (isset($validate->failed()['amount'])) {
                    return $this->fail("topup amount is required", 400);
                }
                return $this->fail("validation error", 400);
            }

            $payment_method = PaymentMethod::find($request->payment_method_id);
            if (!$payment_method) {
                return $this->fail('payment method not found', 404);
            }

            $payment_accounts = $payment_method->payment_accounts;
            if ($payment_accounts->count() < 1) {
                return $this->fail('payment account not found', 404);
            }

            $rand_index = rand(0, $payment_accounts->count() - 1);
            $rand_payment_account = $payment_accounts[$rand_index];


            $new_topup_prepare = TopUp::create([
                'user_id' => Auth::id(),
                'payment_method' => $payment_method->payment_name,
                'payment_account_name' => $rand_payment_account->account_name,
                'payment_account_number' => $rand_payment_account->account_number,
                'amount' => $request->amount,
            ]);

            if ($new_topup_prepare) {
                $new_topup_prepare->topup_transaction_number = "EPR" . str_pad($new_topup_prepare->id, 7, '0', STR_PAD_LEFT);
                $new_topup_prepare->save();
            }

            unset($new_topup_prepare->admin_id);

            $data = [
                "topup_info" => $new_topup_prepare,
            ];

            DB::commit();
            return $this->success('ready to request a topup', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function get_prepared_topup()
    {
        try {
            $prepared_topup = Auth::user()->topups->last();

            if (!$prepared_topup) {
                return $this->fail('there is no prepared topup', 404);
            }

            if ($prepared_topup->payment_transaction_number != null && $prepared_topup->payment_transaction_number != "") {
                return $this->fail('there is no prepared topup', 404);
            }

            unset($prepared_topup->admin_id);
            $data = [
                "topup_info" => $prepared_topup,
            ];

            return $this->success('ready to request a topup', $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function confirm_topup(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'topup_id' => 'required',
                    'payment_transaction_number' => 'required',
                ]
            );

            if ($validate->fails()) {
                if (isset($validate->failed()['topup_id'])) {
                    return $this->fail("topup transaction ID is required", 400);
                }
                if (isset($validate->failed()['payment_transaction_id'])) {
                    return $this->fail("last 6 number of payment transaction id is required", 400);
                }
                return $this->fail("validation error", 400);
            }

            $top_up = TopUp::find($request->topup_id);
            if (!$top_up) {
                return $this->fail('transaction not found', 404);
            }

            if ($top_up->success) {
                return $this->fail('this transaction is already completed', 400);
            }

            if ($top_up->payment_transaction_number != null && $top_up->payment_transaction_number != "") {
                return $this->fail('this transaction is already confirmed', 400);
            }

            $top_up->payment_transaction_number = $request->payment_transaction_number;
            $top_up->save();

            unset($top_up->admin_id);

            $data = [
                "confirmed_topup" => $top_up,
            ];

            DB::commit();
            return $this->success('top up request successful', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function get_confirmed_topup()
    {
        try {
            $last_topup = Auth::user()->topups->last();

            if (!$last_topup) {
                return $this->fail('there is no confirmed topup', 404);
            }

            if ($last_topup->success) {
                return $this->fail('there is no confirmed topup', 404);
            }

            if ($last_topup->payment_transaction_number == null || $last_topup->payment_transaction_number == "") {
                return $this->fail('there is no confirmed topup', 404);
            }

            unset($last_topup->admin_id);

            $data = [
                "confirmed_topup" => $last_topup,
            ];

            return $this->success('get last confirmed topup', $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function get_topup_history()
    {
        $topups = TopUp::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(10);

        foreach ($topups as $topup) {
        }

        // return $this->success('get topup history successful', $topups);
        return $this->success("Get topup history successful", [
            'current_page' => $topups->currentPage(),
            'last_page' => $topups->lastPage(),
            'per_page' => $topups->perPage(),
            'data' => $topups->items(),
        ]);
    }


    //Cash Out
    ///////////////////////////
    //////////////////////////////////////////////////

    public function get_cash_out_payments()
    {
        try {
            $payment_methods = CashOutMethod::all();
            foreach ($payment_methods as $payment_method) {
                $payment_method->image = env("DO_STORAGE_URL") . $payment_method->image_location;
                unset($payment_method->image_path);
                unset($payment_method->image_name);
                unset($payment_method->image_location);
                unset($payment_method->created_at);
                unset($payment_method->updated_at);
            }

            $data = ["payment_methods" => $payment_methods];
            return $this->success("get cash out methods successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function request_cash_out(Request $request)
    {
        DB::beginTransaction();
        try {
            $old_request = CashOut::where('user_id', Auth::id())->where('success', 0)->first();
            if ($old_request) {
                return $this->fail("you have an incompleted request", 400);
            }

            $validate = Validator::make(
                $request->all(),
                [
                    'cash_out_method_id' => 'required',
                    'receive_account_name' => 'required',
                    'receive_account_number' => 'required',
                    'amount' => 'required',
                ]
            );

            if ($validate->fails()) {
                if (isset($validate->failed()['cash_out_method_id'])) {
                    return $this->fail("cash_out_method_id is required", 400);
                }
                if (isset($validate->failed()['receive_account_name'])) {
                    return $this->fail("lreceive_account_name is required", 400);
                }
                if (isset($validate->failed()['receive_account_number'])) {
                    return $this->fail("receive_account_number is required", 400);
                }
                if (isset($validate->failed()['amount'])) {
                    return $this->fail("amount is required", 400);
                }
                return $this->fail("validation error", 400);
            }

            $user = Auth::user();
            $current_balance = $user->main_wallet->balance;

            if ($current_balance < $request->amount) {
                return $this->fail("you don't have enough balance", 400);
            }

            $cash_out_method = CashOutMethod::find($request->cash_out_method_id);
            if (!$cash_out_method) {
                return $this->fail("Cash out method not found", 404);
            }

            $new_cash_out_request = CashOut::create([
                'user_id' => Auth::id(),
                'cash_out_method_id' => $request->cash_out_method_id,
                'receive_account_name' => $request->receive_account_name,
                'receive_account_number' => $request->receive_account_number,
                'amount' => $request->amount,
            ]);

            $data = [
                "cash_out" => $new_cash_out_request,
            ];

            DB::commit();

            return $this->success('request cash out successful', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function get_cash_out_history()
    {
        $cash_outs = CashOut::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(10);

        foreach ($cash_outs as $cash_out) {
            $cash_out->cash_out_method = CashOutMethod::find($cash_out->cash_out_method_id)->payment_name;
            unset($cash_out->admin_id);
            unset($cash_out->cash_out_method_id);
        }
        return $this->success("Get cash out history successful", [
            'current_page' => $cash_outs->currentPage(),
            'last_page' => $cash_outs->lastPage(),
            'per_page' => $cash_outs->perPage(),
            'data' => $cash_outs->items(),
        ]);
    }
}
