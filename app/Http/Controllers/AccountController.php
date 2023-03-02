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
            'account_name' => 'required|string',
            'account_number'  => 'required|min:10|max:10|unique:accounts,account_number',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
        $user = Auth::user();
        $request->request->add(['user_id' => $user->id]);
        $account = Account::create($request->only('user_id', 'account_name', 'account_number'));
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
            'id'           => 'required|exists:accounts,id',
            'account_name' => 'required|string|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message'  => $validator->errors()
            ]);
        }
        $account = Account::findOrFail($request->id);
        if ($account->user_id == Auth::user()->id) {
            $account->update($request->only('account_name'));
            return response()->json([
                'success' => true,
                'data'    => $account,
                'message' => 'Account Updated Successfuly'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You can not make changes in this account'
            ]);
        }
    }
    /**
     * API For Delete Account
     * @param $id
     * @return Json data
     */
    public function delete($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();
        return response()->json([
            'message' => 'Account Deleted Successfully'
        ]);
    }
    /**
     * API For list of accounts for the logged in user
     * @return Json data
     */
    public function list()
    {
        $user_id = auth()->user()->id;
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
        $account = Account::with('accountUsers')->findOrFail($id);
        return response()->json([
            'Data'    => $account,
            'message' => 'All Account'
        ]);
    }
}
