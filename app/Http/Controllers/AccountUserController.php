<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\AccountUser;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseWithStatus;

class AccountUserController extends Controller
{
    use ResponseWithStatus;
    /**
     * API For list User In Account
     * @return Json data
     */
    public function list(){
        $account_users = AccountUser::get();
        return $this->listResponse('Account User',$account_users);
    }
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
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }
        $account_user = AccountUser::where('email',$request->email)->where('account_id',$request->account_id)->first();
        if($account_user){
            return response()->json([
                'message' => 'User Already Added'
            ]);
        }
        $user = User::where('email',$request->email)->first();
        $account = Account::findOrFail($request->account_id);
            $account = $account->accountUsers()->create([
                'first_name' => $user->firstname,
                'last_name'  => $user->lastname,
                'email'      => $user->email
            ]);
            return $this->createResponse('Account User',$account);
    }
    /**
     * API For Update User In Account
     * @param Request $request,$id
     * @return Json data
     */
    public function edit(Request $request,$id){
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|alpha|max:10',
            'last_name'  => 'required|alpha|max:10',
            'email'     => 'required|exists:users,email'
        ]);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }
        $account_user = AccountUser::findOrFail($id);
        $account_user->update($request->only('first_name','last_name','email'));
        return $this->updateResponse('Account User',$account_user);
    }
    /**
     * API For delete user account
     * @param $id
     * @return Json data
     */
    public function delete($id){
            $account_user = AccountUser::findOrFail($id);
            $account_user->delete();
            return $this->deleteResponse('Account User',$account_user);
    }
    /**
     * API For list User In Account
     * @param $id
     * @return Json data
     */
    public function get($id){
        $account_user = AccountUser::with('transactions','account')->findOrFail($id);
        return $this->getResponse('Account User',$account_user);
    }

}
