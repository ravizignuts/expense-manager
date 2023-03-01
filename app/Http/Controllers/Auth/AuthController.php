<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        $validator = Validator::make($request->all(),[
            'firstname'      => 'required|string',
            'lastname'       => 'required|string',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|max:12',
            'account_number' => 'required|min:11|max:16|unique:accounts,account_number',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        $request['password'] = Hash::make($request->password);
        $user = User::create($request->only('firstname', 'lastname', 'email', 'phone', 'password'));
        //Mail::to($user)->queue(new WelcomeMail($user));

        $request->request->add(['account_name' => $request['firstname'] ." ". $request['lastname']]);
        $request->request->add(['is_default'=>true]);
        $user->accounts()->create($request->only('account_name','account_number','is_default'));
        return response()->json([
            'success' => True,
            'user'    => $user,
            'message' => 'Mail has been sent to your Email !Please veify your Account'
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
            'email'      => 'required|email|exists:users,email',
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
            // dd($user);
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
     * @return json data
     */
    public function logout(){
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'user'    => $user,
            'message' => 'User Logout'
        ]);
    }
}
