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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Exception;


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

        if(Auth::attempt(['email' =>$request->email,'password' => $request->password , 'status' => 1])){
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
                    'message'   => 'your email is already verified',
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


    public function forgetPassword(Request $request){
        try{    
            $user = User::where('email',$request->email)->get();

            if(count($user) > 0){
                $token = Str::random(64);
                $domain = URL::to('/');
                $url = $domain.'/api/resetPassword?token='.$token."&email=".$request->email;

                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = 'Password Reset';
                $data['body'] = 'please Click To below Link To Reset password';

                Mail::send('forgetPasswordMail',['data'=>$data],function($message) use ($data){
                    $message ->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email'=>$request->email,
                        'token' => $token,
                        'created_at' => $datetime 
                    ]
                );
                return response()->json([
                    'message'       => 'sending mail check your mail',
                    'status'        => 200
                ]);
            }
            else{
                return response()->json([
                    'message'       => 'email Is Not Exixt',
                    'status'        => 402
                ]);
            }
            
        }
        catch(Exception $e){
            //return back()->with('error',$e->getMessage());
            return response()->json([
                'message'   => 'not found',
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function resetPasswordMessage(Request $request){
        $resetdata = PasswordReset::where('token',$request->token)->first();
        $resetdata = PasswordReset::all();
       
      // dd($resetdata['created_at']);
        if(isset($request->token) && count($resetdata) > 0){
            $user = User::where('email',$request->email)->first();

            //return view('auth.resetPassword',['user' => $user]);
            return response()->json([
                'message'       => 'now you can change the password use this token',
                'status'        => 200,
                'token'         => $request->token
            ]);
        }
        else{
            //return view('404');
            return response()->json([
                'message'       => 'you can not change the password',
                'status'        => 404
            ]);
        }
    }

    public function resetPassword(Request $request){
        $request->validate([
            'password'  => 'required|same:password_confirmation',
            'token'     => 'required'
        ]);
        $count = PasswordReset::where('token',$request->token)->where('email',$request->email)->get();

        if(count($count) > 0){
            $user = User::where('email',$request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);

            return response()->json([
                'message'       => 'your Password Change Successfully',
                'status'        => 200
            ]);
        }
        else{
            return response()->json([
                'message'       => 'your token is not match',
                'status'        => 404
            ]);
        }
    }

    public function changePassword(Request $request){
        $request->validate([
            'current_password'          => 'required|current_password',
            'password'                  => 'required|min:8'
        ]);

        $id = Auth::user();
        $user = User::find($id)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'message'       => 'your password  Change',
            'status'        => '402',
            'data'          => $user
        ]);    
    }   
 
}
