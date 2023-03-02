<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\AccountUser;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountUserController extends Controller
{
    /**
     * API For Add User In Account
     * @param Request $request
     * @return Json data
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_id' => 'required|numeric|exists:accounts,id',
            'email'   => 'required|email|exists:users,email'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
        $user = User::where('email',$request->email)->first();
        $account = Account::findOrFail($request->account_id);
        if($account->user_id == Auth::user()->id){
            $account = $account->accountUsers()->create([
                'first_name' => $user->firstname,
                'last_name'  => $user->lastname,
                'email'      => $user->email
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User Added Successfully',
                'data'    => $account
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'you cant not Add inthis account',
            ]);
        }
    }
    /**
     * API For Update User In Account
     * @param Request $request,$id
     * @return Json data
     */
    public function edit(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:20',
            'last_name'  => 'required|string|max:20',
            'email'     => 'required|exists:users,email'
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>$validator->errors()
            ]);
        }
        $user = AccountUser::findOrFail($id);
        $user->update($request->only('first_name','last_name','email'));
        return response()->json([
            'message' => 'Account User Updated Succesfully'
        ]);
    }
    /**
     * API For delete user account
     * @param Request $request
     * @return Json data
     */
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'account_id' => 'required|numeric|exists:accounts,id',
            'email'      => 'required|email|exists:account_users,email'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
        $account = Account::findOrFail($request->account_id);
        if($account->user_id == Auth::user()->id){
            $accountuser = AccountUser::where('account_id',$request->account_id)->where('email',$request->email)->first();
            $accountuser->delete();
            return response()->json([
                    'Success' => true,
                    'message' => 'Deleted Record',
                    'data'    => $accountuser
                ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'You can not make changes in this account'
            ]);
        }
    }
    /**
     * API For list User In Account
     * @param $id
     * @return Json data
     */
    public function get($account_id){
        $account = AccountUser::with('transactions')->where('account_id',$account_id)->get();
        return response()->json([
            'message' => 'Account Transaction',
            'account' => $account
        ]);
    }
    /**
     * API For list User In Account
     * @return Json data
     */
    public function list(){
        $accounts = AccountUser::get();
        return response()->json([
            'message'       => 'Account Users',
            'account users' => $accounts
        ]);
    }
}
