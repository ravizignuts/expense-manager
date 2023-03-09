<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponseWithStatus;
use Exception;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class AccountController extends Controller
{
    use ResponseWithStatus;
    /**
     * API For list of accounts for the logged in user
     * @return Json data
     */
    public function list()
    {
        $accounts = Account::where('user_id', auth()->user()->id)->get();
        return $this->listResponse('Account', $accounts);
    }
    /**
     * API For Add Account
     * @param Request $request
     * @return Json data
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name'    => 'required|string|max:20',
            'account_number'  => 'required|numeric|digits:10|unique:accounts,account_number',
        ]);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }
        $account = Account::create($request->only('account_name', 'account_number') + ['user_id' => auth()->user()->id]);
        return $this->createResponse('Account', $account);
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
            return $this->validationResponse($validator);
        }
        $account = Account::findOrFail($request->id);
        if ($account->is_default == true) {
            return response()->json([
                'message' => 'Default Account Can not Update'
            ]);
        } else {
            $account->update($request->only('account_name'));
            return $this->updateResponse('Account', $account);
        }
    }
    /**
     * API For Delete Account
     * @param $id
     * @return Json data
     */
    public function delete($id)
    {
        try {
            $account = Account::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            try{
                $account = Account::withTrashed()->findOrFail($id);
                $account->forceDelete();
                return response()->json(['message' => 'Account Deleted Forcefully']);
            }catch(ModelNotFoundException $e){
                return response()->json(['message' => 'Account Not Found']);
            }
        }
        if ($account->is_default == true) {
            return response()->json([
                'message' => 'You can not delete Default Account'
            ]);
        } else {
            $account->delete();
            return $this->deleteResponse('Account', $account);
        }
    }
    /**
     * API For Restore Account
     * @param $id
     * @return Json data
     */
    public function restore($id){
        $account = Account::withTrashed()->findOrFail($id);
        $account->restore();
        return $this->restoreResponse('Account',$account);
    }
    /**
     * API For get Account
     * @param $id
     * @return Json data
     */
    public function get($id)
    {
        try{
            $account = Account::with('transactions')->findOrFail($id);
        }catch(RelationNotFoundException $e){
            return response()->json(['message'=>'Relationship Not Found']);
        }catch(ModelNotFoundException $e){
            return response()->json(['message'=>'Account Not Found']);
        }
        return $this->getResponse('Account', $account);
    }
}
