<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Notifications\WelcomeMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class AuthController extends Controller
{

//=============================================User Register Code==================================================
    public function register(Request $request){
        $request->validate([
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required|unique:users|email',
            'phone_number'          => 'required|unique:users|min:10',
            'password'              => 'required|same:password_confirmation|min:8',
            'account_name'          => 'required',
            'account_number'        => 'required|min:12',
            'password_confirmation' => 'required',
        ]);

        //$account_name = $request->first_name ."". $request->last_name;
        //$account_number = fake()->numerify('############');
        $token = Str::random(64);
       $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone_number'      => $request->phone_number,
            'token'             => $token,
            'password'          => Hash::make($request->password),
        ]);

        $userAccount = Account::create([
            'account_name'      => $request->account_name,
            'account_number'    => $request->account_number,
            'email'             => $request->email,
            'is_default'        => true,
            'user_id'           => $user->id,
        ]);

        $user->notify(new WelcomeMessageNotification($user));

        return response()->json([
            'message' => 'You Are Regester Now Welcome Email Send SuccessFully',
            'userdata' => $user,
            'accountdata' => $userAccount
        ]);
    }

//=============================================User Login Code==================================================

    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        if(Auth::attempt(['email' =>$request->email,'password' => $request->password ])){
            $user = User::where('email', $request->email)->first();
            return response()->json([
                'message'   =>'You Are Login Now',
                'token'     =>$user->createToken("API TOKEN")->plainTextToken,
                'status'    =>200
            ]);
        }
        else{
            return response()->json([
                'message'   =>'email and Password Are Not Match',
                'status'    =>404
            ]);
        }
    }

//=============================================User Logout Code==================================================
    public function logout(){
        Session::flush();
        Auth::logout();
        return response()->json([
            'message'   =>'Your Are Logout Now',
            'status'    =>200
        ]);
    }

//=============================================User verify Code==================================================
    public function verifyAccount($token)
    {
        $verifyUser = User::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';

        if(!is_null($verifyUser) ){
            $user = $verifyUser->user;
                $verifyUser->status = 1;
                $verifyUser->email_verified_at = now();
                $verifyUser->token = '';
                $verifyUser->save();
                $message = "Your e-mail is verified. You can now login.";
                
                return response()->json([
                    'message'   => 'success Your Mail Is verified',
                    'status'    =>  200
                ]);
            } 
            else{
                return response()->json([
                    'message'   => 'your email is not verified',
                    'status'    => 404
                ]);
            }
    }
//=============================================User Profile Code==================================================
    public function userProfile(){
        $userdata = Auth::user();

        return response()->json([
            'message'      => 'Authenicated User Data',
            'status'       => 200,
            'data'         => $userdata
        ]);
    }

//=============================================User Profile Code==================================================

 
}
