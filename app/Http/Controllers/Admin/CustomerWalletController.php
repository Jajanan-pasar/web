<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\Validator;
use App\CPU\CustomerManager;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use App\Model\AddFundBonusCategories;
use Illuminate\Support\Facades\Mail;
use App\Model\WalletTransaction;
use Carbon\Carbon;

class CustomerWalletController extends Controller
{

    public function add_fund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'=>'exists:users,id',
            'amount'=>'numeric|min:.01|max:10000000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_transaction = CustomerManager::create_wallet_transaction($request->customer_id, $request->amount, 'add_fund_by_admin',$request->referance);


        if($wallet_transaction)
        {

            try{
                Mail::to($wallet_transaction->user->email)->send(new \App\Mail\AddFundToWallet($wallet_transaction));
            }catch(\Exception $ex)
            {
                info($ex);
            }

            return response()->json([], 200);
        }

        return response()->json(['errors'=>[
            'message'=> translate('failed_to_create_transaction')
        ]], 200);
    }

    public function report(Request $request)
    {
        $customer_status = BusinessSetting::where('type','wallet_status')->first()->value; //customer disable check

        $data = WalletTransaction::selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
        ->when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when($request->transaction_type, function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when($request->customer_id, function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->get();

        $transactions = WalletTransaction::
        when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when($request->transaction_type, function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when($request->customer_id, function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->latest()
        ->paginate(Helpers::pagination_limit());

        return view('admin-views.customer.wallet.report', compact('data','transactions', 'customer_status'));
    }

    public function bonus_setup(Request $request)
    {
        $data = AddFundBonusCategories::when($request->has('search'), function($query) use($request){
                    $key = explode(' ', $request['search']);
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('title', 'like', "%{$value}%");
                        }
                    });
                })->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit());

        return view('admin-views.customer.wallet.wallet-bonus-setup', compact('data'));
    }

    public function bonus_setup_store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'bonus_type' => 'required',
            'bonus_amount' => 'required|numeric|min:.01',
            'min_add_money_amount' => 'required|numeric|min:.01',
            'start_date_time' => 'required',
        ]);

        AddFundBonusCategories::insert([
            'title' => $request->title,
            'description' => $request->description,
            'bonus_type' => $request->bonus_type,
            'bonus_amount' => $request->bonus_amount,
            'min_add_money_amount' => $request->min_add_money_amount,
            'max_bonus_amount' => $request->max_bonus_amount,
            'start_date_time' => $request->start_date_time,
            'end_date_time' => $request->end_date_time,
            'created_at' => Carbon::now()
        ]);

        Toastr::success(translate('wallet_Bonus_Added_Successfully'));
        return back();
    }

    public function bonus_setup_status(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required',
        ]);

        AddFundBonusCategories::where('id', $request->id)->update([
            'is_active' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => translate('status_Update_Successfully'),
            ]);
        }

        Toastr::success(translate('status_Update_Successfully'));
        return back();
    }

    public function bonus_setup_delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        AddFundBonusCategories::where('id', $request->id)->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => translate('bonus_removed_Successfully'),
            ]);
        }

        Toastr::success(translate('bonus_removed_Successfully'));
        return back();
    }

    public function bonus_setup_edit(Request $request)
    {
        $data = AddFundBonusCategories::where('id', $request->id)->first();

        return view('admin-views.customer.wallet.wallet-bonus-edit', compact('data'));
    }

    public function bonus_setup_update(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'bonus_type' => 'required',
            'bonus_amount' => 'required|numeric|min:.01',
            'min_add_money_amount' => 'required|numeric|min:.01',
            'start_date_time' => 'required',
        ]);

        AddFundBonusCategories::where('id', $request->id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'bonus_type' => $request->bonus_type,
            'bonus_amount' => $request->bonus_amount,
            'min_add_money_amount' => $request->min_add_money_amount,
            'max_bonus_amount' => $request->max_bonus_amount,
            'start_date_time' => $request->start_date_time,
            'end_date_time' => $request->end_date_time,
        ]);

        Toastr::success(translate('wallet_Bonus_update_Successfully'));
        return back();
    }

}
