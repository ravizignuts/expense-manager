<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * API for list Transaction
     * @return json Data
     */
    public function list(){
        $transactions = Transaction::get();
        return response()->json([
            'message'       => 'All Transaction',
            'Transaction'   => $transactions
        ]);
    }
    /**
     * API for add Transaction
     * @param Request $request
     * @return json Data
     */
    public function add(Request $request){
        $validator = Validator::make($request->all(),[
            'account_id'      => 'required|numeric|exists:accounts,id',
            'account_user_id' => 'required|numeric|exists:account_users,id',
            'type'            => 'required|string|in:income,expense,transfer',
            'date'            => 'required|date',
            'category'        => 'required|string|max:20',
            'amount'          => 'required|numeric|between:1,99999'
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors(),
            ]);
        }
        $transaction = Transaction::create($request->only('account_id','account_user_id','type','date','category','amount'));
        return response()->json([
            'transaction' => $transaction,
            'message'     =>'Transaction Created Successfully'
        ]);
    }
    /**
     * API for edit Transaction
     * @param Request $request,$id
     * @return json Data
     */
    public function edit(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'account_id'      => 'required|numeric|exists:accounts,id',
            'account_user_id' => 'required|numeric|exists:account_users,id',
            'type'            => 'required|in:income,expense,transfer',
            'date'            => 'required|date',
            'category'        => 'required|string|max:20',
            'amount'          => 'required|numeric|between:1,99999'
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors(),
            ]);
        }
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->only('account_id','account_user_id','type','date','category','amount'));
        return response()->json([
            'transaction' => $transaction,
            'message'     =>'Transaction Updated Successfully'
        ]);
    }
    /**
     * API for delete Transaction
     * @param $id
     * @return json Data
     */
    public function delete($id){
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return response()->json([
            'message'       => 'Transaction Deleted Successfully',
            'Transaction'   => $transaction
        ]);
    }
    /**
     * API for get Transaction
     * @param $id
     * @return json Data
     */
    public function get($id){
        $transaction = Transaction::with('account')->findOrFail($id);
        return response()->json([
            'message'       => 'Transaction Get Successfully',
            'Transaction'   => $transaction
        ]);
    }
}
