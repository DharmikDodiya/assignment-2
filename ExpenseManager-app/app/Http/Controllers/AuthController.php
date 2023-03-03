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
use App\Traits\ResponseMessage;
//use Illuminate\Auth\Events\PasswordReset as EventsPasswordReset;

class AuthController extends Controller
{
    use ResponseMessage;
    
//=============================================User Register Code==================================================
    public function register(Request $request){
        $request->validate([
            'first_name'            => 'required|max:30',
            'last_name'             => 'required|max:30',
            'email'                 => 'required|unique:users,email|email',
            'phone_number'          => 'required|unique:users,phone_number|min:10|max:10',
            'password'              => 'required|same:password_confirmation|min:8',
            'account_name'          => 'required|max:30',
            'account_number'        => 'required|min:10|max:12|unique:accounts,account_number',
            'password_confirmation' => 'required',
        ]);
        
        $user = User::create($request->only(['first_name','last_name','email','phone_number'])
        +[
            'token'     => Str::random(64),
            'password'  => Hash::make($request->password),
        ]
        );
        
        $request['user_id'] = $user->id;
        $request['is_default'] = true;

        $userAccount = Account::create($request->only(['account_name','account_number','is_default','user_id']));
   
        $user->notify(new WelcomeMessageNotification($user));

        return response()->json([
            'message'     => 'You Are Regester Now Welcome Email Send SuccessFully',
            'userdata'    => $user,
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
                'token'     => $user->createToken("API TOKEN")->plainTextToken,
                'status'    => 200
            ]);
        }
        else{
            return response()->json([
                'message'   =>'email and Password Are Not Match',
                'status'    => 404
            ]);
        }
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


//=============================================User Forgetpassword Code==================================================


    public function forgetPassword(Request $request){
        try{    
            $request->validate([
                'email'            => 'required|exists:users,email',
            ]);
            $user = User::where('email',$request->email)->get();

            if(count($user) > 0){
                $token = Str::random(64);
                $domain = URL::to('/');
                $url = $domain.'/api/resetPassword?token='.$token."&email=".$request->email;

                $data['url']    = $url;
                $data['email']  = $request->email;
                $data['title']  = 'Password Reset';
                $data['body']   = 'please Click To below Link To Reset password';

                Mail::send('forgetPasswordMail',['data'=>$data],function($message) use ($data){
                    $message ->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email'         =>$request->email,
                        'token'         => $token,
                        'created_at'    => $datetime 
                    ]
                );
                return response()->json([
                    'message'       => 'send mail please check your mail',
                    'status'        => 200,
                    'token'         => $token
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
            
            return response()->json([
                'message'   => 'not found',
                'error'     => $e->getMessage(),
            ]);
        }
    }
    //resetPassword Message

    public function resetPasswordMessage(Request $request){
        $resetdata = PasswordReset::where('token',$request->token)->first();
        $resetdata = PasswordReset::all();
       
      
        if(isset($request->token) && count($resetdata) > 0){
            $user = User::where('email',$request->email)->first();

            
            return response()->json([
                'message'       => 'now you can change the password use this token',
                'status'        => 200,
                'token'         => $request->token
            ]);
        }
        else{
            
            return response()->json([
                'message'       => 'you can not change the password',
                'status'        => 404
            ]);
        }
    }


//=============================================User reset Password Code==================================================

    public function resetPassword(Request $request){
        $request->validate([
            'password'  => 'required|same:password_confirmation',
            'token'     => 'required',
            'email'     => 'required|exists:users,email'
        ]);
        $count = PasswordReset::where('token',$request->token)->where('email',$request->email)->first();
        //dd($count);
        //$email = $count->email;
        //dd($count->email);

        if($count){
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



   
 
}
