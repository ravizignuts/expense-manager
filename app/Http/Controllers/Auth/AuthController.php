<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $validator = Validator::make($request->all(), [
            'firstname'      => 'required|string',
            'lastname'       => 'required|string',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|max:12',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        $request['password'] = Hash::make($request->password);
        $token = Str::random(64);
        $request->request->add(['email_verification_token' => $token]);

        $user = User::create($request->only('firstname', 'lastname', 'email', 'phone', 'password', 'email_verification_token'));

        //Mail::to($user)->queue(new WelcomeMail($user));
        return response()->json([
            'success' => True,
            'user'    => $user,
            'message' => 'Mail has been sent to your Email !Please veify your Account'
        ]);
    }
    /**
     * API For Register User
     * @param $token
     * @return json Data
     */
    public function verifyuser($token)
    {
        $verifyuser = User::where('email_verification_token', $token)->first();
        if (!is_null($verifyuser)) {
            if ($verifyuser->is_onbord == true) {
                return response()->json([
                    'message' => 'Your email is Alreay verified'
                ]);
            } else {
                $verifyuser->is_onbord = true;
                $verifyuser->save();
                $account_name   = $verifyuser->firstname . ' ' . $verifyuser->lastname;
                $account_number = random_int(0000000000, 9999999999);
                $verifyuser->accounts()->create([
                    'account_name'   => $account_name,
                    'account_number' => $account_number,
                    'is_default'     => true
                ]);
                return response()->json([
                    'message' => 'Your email is verify You Can now Login'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Your Token is not allowed'
            ]);
        }
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
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::user()->is_onbord == true) {
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
                    'succes'  => false,
                    'message' => 'Your email is not verified'
                ]);
            }
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
    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'user'    => $user,
            'message' => 'User Logout'
        ]);
    }
}
