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
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'account_name' => 'required|string',
            'account_number'  => 'required|min:11|max:16|unique:accounts,account_number',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
        $user = Auth::user();
        $request->request->add(['user_id'=>$user->id]);
        $account = Account::create($request->only('user_id','account_name','account_number'));
        return response()->json([
            'message' => true,
            'data'    => $account
        ]);
    }
    /**
     * API For Add Account
     * @param Request $request
     * @return Json data
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_id' => 'required|numeric|exists:accounts,id',
            'emails'     => 'required|array',
            'emails.*'   => 'required|email|exists:users,email|unique:account_users,email'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }
        // $emails = $request->emails;
        // $account = Account::findOrFail($request->account_id);
        // foreach($emails as $email){
        //         $user = User::where('email',$email)->first();
        //         $account = Account::findOrFail($request->account_id);
        //         $account = $account->accountUsers()->updateOrCreate([
        //             'first_name' => $user->firstname,
        //             'last_name'  => $user->lastname,
        //             'email'      => $user->email
        //         ]);
        // }
        $user = User::where('email',$request->email)->first();
        $account = Account::findOrFail($request->account_id);
        $account = $account->accountUsers()->create([
            'first_name' => $user->firstname,
            'last_name'  => $user->lastname,
            'email'      => $user->email
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'User Added Successfully',
        //     'data'    => $account

        // ]);
    }
    /**
     * API For Add Account
     * @param Request $request
     * @return Json data
     */
    public function edit(Request $request)
    {
    }
    /**
     * API For Add Account
     * @param $id
     * @return Json data
     */
    public function delete($id)
    {
    }
    /**
     * API For Add Account
     * @param $id
     * @return Json data
     */
    public function view($id)
    {
    $account = Account::with('accountUsers')->findOrFail($id)->get();
    return response()->json([
        'Data'    => $account,
        'message' =>'All Account'
    ]);
    }
    /**
     * API For Add Account
     * @return Json data
     */
    public function list()
    {
    $account = Account::where('user_id',Auth::user()->id)->get();
    return response()->json([
        'Data'    => $account,
        'message' =>'All Account'
    ]);
    }
}
