<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * API For Add Account
     * @param Request $request
     * @return Json data
     */
    public function profile(){
        $user  = Auth::user();
        return response()->json([
            'message' => 'User Profile',
            'User'    => $user
        ]);
    }
    /**
     * API For list user
     * @return Json data
     */
    public function list(){
        $users = User::get();
        return response()->json([
            'message' => 'User List',
            'user'    => $users
        ]);
    }
    /**
     * API For change password
     * @param Request $request
     * @return Json data
     */
    public function change(Request $request){
        $validator = Validator::make($request->all(),[
            'old_password' => 'required|min:8|max:16',
            'new_password' => 'required|confirmed|min:8|max:16',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        if( ! Hash::check($request->old_password,Auth::user()->password)){
            return response()->json([
                'success' => false,
                'message' => 'Password is not Matched'
            ]);
        }
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json([
            'success' => true,
            'Message' => 'Password updated successfully'
        ]);
    }
    /**
     * API For forget password
     * @param Request $request
     * @return Json data
     */
    public function forget(){

    }
    /**
     * API For reset password
     * @param Request $request
     * @return Json data
     */
    public function reset(){

    }
}
