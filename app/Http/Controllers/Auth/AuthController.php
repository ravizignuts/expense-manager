<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Notifications\ChangePassword;
use App\Notifications\ForgotPassword;
use App\Notifications\VerifyUser;
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
            'firstname'      => 'required|string|max:10',
            'lastname'       => 'required|string|max:10',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|max:12',
        ]);
        if ($validator->fails()) {
            $response = [
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        $request['password'] = Hash::make($request->password);
        $token = Str::random(64);
        $user = User::create($request->only('firstname', 'lastname', 'email', 'phone', 'password', 'email_verification_token') + ['email_verification_token' => $token]);
        // Mail::to($user)->send(new WelcomeMail($user));
        // $user->notify(new VerifyUser($user));
        return response()->json([
            'message' => 'User Created Successfully !Please veify your Account',
            'token'   => $token
        ]);
    }
    /**
     * API For Login User
     * @param Request $request
     * @return json Data
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email|exists:users,email',
            'password'    => 'string|min:8|max:20'
        ], [
            'email.email'  => 'Please Enter Email in email format like abc@xyz.com',
            'email.exists' => 'Entered Email is not exists'
        ]);
        if ($validator->fails()) {
            $response = [
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
                    'data'    => $success,
                    'message' => 'User Login Successfully'
                ];
                return response()->json($response, 200);
            } else {
                return response()->json([
                    'message' => 'Your email is not verified'
                ]);
            }
        } else {
            return response()->json([
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
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'user'    => $user['firstname'],
            'message' => 'User Logout'
        ]);
    }
    /**
     * API For Email verification user Registration
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
                $verifyuser->email_verification_token = null;
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
                'message' => 'Your Token is Invalid'
            ]);
        }
    }
    /**
     * API For send mail for forgot password
     * @param $token
     * @return json Data
     */
    public function sendForgotpasswordMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email|exists:users,email'
        ], [
            'email.email'  => 'Your email is not exist'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = User::where('email', $request->email)->first();
        PasswordResetToken::where('email', $request->email)->delete();
        $token = mt_rand(100000, 999999);
        $createtoken = PasswordResetToken::create([
            'email'      => $request->email,
            'token'      => $token,
            'created_at' => now()
        ]);
        $user->notify(new ForgotPassword($createtoken));
        return response()->json([
            'message' => '! Please Checke Mail'
        ]);
    }
    /**
     * API For Verify Password Reset Token
     * @param $token
     * @return json Data
     */
    public function verifyPasswordResetToken(Request $request)
    {
        $validator = validator($request->all(), [
            'token'       => 'required|exists:password_reset_tokens',
            'email'       => 'required|email|exists:password_reset_tokens,email',
        ], [
            'email.exists' => 'Your email is not registered for password reset'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $passwordReset = PasswordResetToken::where('token', $request->token)->where('email', $request->email)->first();
        $user = User::where('email', $passwordReset->email)->first();
        // $passwordReset->delete();
        $user->is_email_verify = true;
        $user->save();
        return response()->json([
            'message' => 'Your Token verified now you can change your password'
        ]);
    }
    /**
     * API For reset password
     * @param Request $request
     * @return Json data
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                     => 'required|email|exists:users,email',
            'new_password'              => 'required|confirmed|min:8|max:16',
            'new_password_confirmation' => 'required|min:8|max:16'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if ($user->is_email_verify == true) {
            $user->is_email_verify = false;
            $user->email_verification_token = null;
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'message' => 'Your Password is change now you can login with new password'
            ]);
        } else {
            return response()->json([
                'message' => 'Your Email is not verified for reset password'
            ]);
        }
    }
    /**
     * API For change password
     * @param Request $request
     * @return Json data
     */
    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'              => 'required|min:8|max:16',
            'new_password'              => 'required|confirmed|min:8|max:16',
            'new_password_confirmation' => 'required|min:8|max:16'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = auth()->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Old Password is not Matched'
            ]);
        }
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json([
            'Message' => 'Password Changed successfully'
        ]);
    }
}
