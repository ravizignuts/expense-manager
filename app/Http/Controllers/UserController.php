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
     * API For Get Profile
     * @param Request $request
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
        $users = User::get();
        return response()->json([
            'message' => 'User List',
            'user'    => $users
        ]);
    }
}
