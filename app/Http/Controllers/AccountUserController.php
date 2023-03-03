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
                'error'   => 'Validation Error',
                'message' => $validator->errors(),
            ]);
        }
        $user = User::where('email',$request->email)->first();
        $account = Account::findOrFail($request->account_id);
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
    /**
     * API For Update User In Account
     * @param Request $request,$id
     * @return Json data
     */
    public function edit(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:10',
            'last_name'  => 'required|string|max:10',
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
    public function delete($id){
            $account_user = AccountUser::findOrFail($id);
            $account_user->delete();
            return response()->json([
                    'message' => 'Account User Deleted Successfully',
                    'data'    => $account_user
                ]);
    }
    /**
     * API For list User In Account
     * @param $id
     * @return Json data
     */
    public function get($id){
        $account_user = AccountUser::with('transactions')->findOrFail($id);
        return response()->json([
            'message' => 'Account User With Transaction',
            'account' =>  $account_user
        ]);
    }
    /**
     * API For list User In Account
     * @return Json data
     */
    public function list(){
        $account_users = AccountUser::get();
        return response()->json([
            'message'       => 'Account Users',
            'account_users' => $account_users
        ]);
    }
}
