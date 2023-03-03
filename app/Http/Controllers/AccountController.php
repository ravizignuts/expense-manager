<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * API For Add Account
     * @param Request $request
     * @return Json data
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name'    => 'required|string|max:20',
            'account_number'  => 'required|numeric|unique:accounts,account_number',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ]);
        }
        $account = Account::create($request->only('account_name', 'account_number') + ['user_id' => auth()->user()->id]);
        return response()->json([
            'message' => 'Account Created Successfully',
            'data'    => $account
        ]);
    }

    /**
     * API For edit Account
     * @param Request $request
     * @return Json data
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'           => 'required|numeric|exists:accounts,id',
            'account_name' => 'required|alpha|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'  => $validator->errors()
            ]);
        }
        $account = Account::findOrFail($request->id);
        $account->update($request->only('account_name'));
        return response()->json([
            'data'    => $account,
            'message' => 'Account Updated Successfuly'
        ]);
    }
    /**
     * API For Delete Account
     * @param $id
     * @return Json data
     */
    public function delete($id)
    {
        $account = Account::findOrFail($id);
        if ($account->is_default == true) {
            return response()->json([
                'message' => 'You can not delete Default Account'
            ]);
        } else {
            $account->delete();
            return response()->json([
                'message' => 'Account Deleted Successfully'
            ]);
        }
    }
    /**
     * API For list of accounts for the logged in user
     * @return Json data
     */
    public function list()
    {
        $accounts = Account::where('user_id', auth()->user()->id)->get();
        return response()->json([
            'Data'    => $accounts,
            'message' => 'All Account'
        ]);
    }
    /**
     * API For get Account
     * @param $id
     * @return Json data
     */
    public function get($id)
    {
        $account = Account::with('transactions')->latest()->findOrFail($id);
        return response()->json([
            'Data'    => $account,
            'message' => 'Account Get Successfully'
        ]);
    }
}
