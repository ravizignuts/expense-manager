<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ChangePassword;
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
            'firstname'      => 'required|string',
            'lastname'       => 'required|string',
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
        $request->request->add(['email_verification_token' => $token]);
        $user = User::create($request->only('firstname', 'lastname', 'email', 'phone', 'password', 'email_verification_token'));
        $is_register = 'yes';
        $user->notify(new VerifyUser($user,$is_register));
        //Mail::to($user)->queue(new WelcomeMail($user));
        return response()->json([
            'message' => 'Mail has been sent to your Email !Please veify your Account'
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
            'email'      => 'required|email|exists:users,email',
            'password'   => 'string|min:8|max:20'
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
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'user'    => $user['firstname'],
            'message' => 'User Logout'
        ]);
    }
    /**
     * API For change password
     * @param Request $request
     * @return Json data
     */
    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:8|max:16',
            'new_password' => 'required|confirmed|min:8|max:16',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return response()->json([
                'message' => 'Password is not Matched'
            ]);
        }
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json([
            'Message' => 'Password updated successfully'
        ]);
    }
    /**
     * API For send notification for chnage forget password
     * @param Request $request
     * @return Json data
     */
    public function forgotmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);
        $user->update(['email_verification_token' => $token]);
        $is_register = 'no';
        $user->notify(new VerifyUser($user,$is_register));
        return response()->json([
            'message' => 'Please Check your email reset password from the link'
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
            'email'        => 'required|email|exists:users,email',
            'new_password' => 'required|confirmed|min:8|max:16',
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
     * API For Email verification for password reset
     * @param $token
     * @return json Data
     */
    public function verifyuser($token,$is_register)
    {
        $verifyuser = User::where('email_verification_token', $token)->first();
        if (!is_null($verifyuser)) {
            if($is_register == 'yes'){
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
            }else{
                $verifyuser->is_email_verify = true;
                $verifyuser->save();
                return response()->json([
                    'message' => 'Your Token verified now you can change your password'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Your Token is Invalid'
            ]);
        }
    }
}
