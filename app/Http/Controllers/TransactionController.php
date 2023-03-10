<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseWithStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class TransactionController extends Controller
{
    use ResponseWithStatus;

    /**
     * API for list Transaction
     * @param Request $request
     * @return json Data
     */
    public function list(Request $request)
    {
        $transactions = Transaction::query();
        $per_page     = $request->per_page;
        $page_number  = $request->page_number;
        $transactions = $transactions->skip($per_page * ($page_number - 1))->take($per_page);
        return $this->listResponse('Transactions', $transactions->get());
    }
    /**
     * API for add Transaction
     * @param Request $request
     * @return json Data
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'      => 'required|numeric|exists:accounts,id',
            'account_user_id' => 'required|numeric|exists:account_users,id',
            'type'            => 'required|string|in:income,expense,transfer',
            'date'            => 'required|date',
            'category'        => 'required|string|max:20',
            'amount'          => 'required|numeric|between:1,99999'
        ]);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }
        $transaction = Transaction::create($request->only('account_id', 'account_user_id', 'type', 'date', 'category', 'amount'));
        return $this->createResponse('Transaction', $transaction);
    }
    /**
     * API for edit Transaction
     * @param Request $request,$id
     * @return json Data
     */
    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_id'      => 'required|numeric|exists:accounts,id',
            'account_user_id' => 'required|numeric|exists:account_users,id',
            'type'            => 'required|in:income,expense,transfer',
            'date'            => 'required|date',
            'category'        => 'required|string|max:20',
            'amount'          => 'required|numeric|between:1,99999'
        ]);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->only('account_id', 'account_user_id', 'type', 'date', 'category', 'amount'));
        return $this->updateResponse('Transaction', $transaction);
    }
    /**
     * API for delete Transaction
     * @param $id
     * @return json Data
     */
    public function delete($id)
    {   try{
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();
            return $this->deleteResponse('Transaction', $transaction);
        }catch(ModelNotFoundException $e){
            try{
                $transaction = Transaction::withTrashed()->findOrFail($id);
                $transaction->forceDelete();
                return response()->json(['message' => 'Transaction Deleted Successfully']);
            }catch(ModelNotFoundException $e){
                return response()->json(['message' => 'Transaction Not Found']);
            }
        }
    }
    /**
     * API for Restore Transaction
     * @param $id
     * @return json Data
     */
    public function restore($id)
    {
        $transaction = Transaction::withTrashed()->findOrFail($id);
        $transaction->restore();
        return $this->restoreResponse('Transaction', $transaction);
    }
    /**
     * API for get Transaction
     * @param $id
     * @return json Data
     */
    public function get($id)
    {
        $transaction = Transaction::with('accountUser', 'account')->findOrFail($id);
        return $this->getResponse('Transaction', $transaction);
    }
}
