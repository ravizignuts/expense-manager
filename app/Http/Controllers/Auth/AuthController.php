<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * API For Register User
     * @param Request $request
     * @return json Data
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'firstname'      => 'required|string',
            'lastname'       => 'required|string',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'required|max:10',
            'password'       => 'required|min:8|',
            'account_number' => 'required|min:11|max:16'
        ]);

        $request['password'] = Hash::make($request->password);
        $request->request->add(['account_name' => $request['firstname'] ." ". $request['lastname']]);
        $user = User::create($request->only('firstname', 'lastname', 'email', 'phone', 'password', 'account_name', 'account_number'));
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        return response()->json([
            'success' => True,
            'data'    => $success,
            'user'    => $user
        ]);
    }
    /**
     * API For Register User
     * @param Request $request
     * @return json Data
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'string|required|email',
            'password'   => 'string|min:8|max:20'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $response = [
                'success' => true,
                'data'    => $success,
                'user'    => $user,
                'message' => 'User Login Successfully'
            ];
            return response()->json($response, 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User Credentials are Invalid'
            ]);
        }
    }
    /**
     * API for Logout user
     * @param  Request $request
     * @return json data
     */
    public function logout(Request $request){
        $user = User::findOrFail($request->id);
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'user'    => $user,
            'message' => 'User Logout'
        ]);
    }
}
