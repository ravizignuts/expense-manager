<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
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
        // dd($account);
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
     * @param Request $request
     * @return Json data
     */
    public function editUser(){

    }
    /**
     * API For delete account
     * @param Request $request
     * @return Json data
     */
    public function deleteUser(){

    }
    public function get(){

    }
    /**
     * API For Update User In Account
     * @param Request $request
     * @return Json data
     */
    public function list(){

    }
}
