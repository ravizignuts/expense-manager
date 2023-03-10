<?php

namespace App\Http\Controllers;

use App\Models\AccountUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * API For Get Profile
     * @return Json data
     */
    public function profile(){
        $user  = auth()->user();
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
        $users = User::where('type','user')->get();
        return response()->json([
            'message' => 'User List',
            'user'    => $users
        ]);
    }
    /**
     * API For Delete User
     * @param $id
     * @return Json data
     */
    public function delete($id){
        $user = User::findOrFail($id);
        $account_user = AccountUser::where('email',$user->email)->first();
        $user->delete();
        $account_user->forceDelete();
        return response()->json([
            'message' => 'User Deleted successfully',
            'data'    => $user
        ]);
    }
}
