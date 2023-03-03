<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use App\Traits\ResponseMessage;

class UserController extends Controller
{
    use ResponseMessage;

        //User Profile Code

        public function userProfile(){
            $user = Auth::user();
            $user->accounts;
            $user->account_users;
            $user->transactions;
            
            if($user){
               return $this->success('user Profile Details',$user);
            }
            else{
                return $this->DataNotFound();
                 
            }
            
        }

    //get User Details

    public function get($id){
        $user = User::with('accounts','account_users','transactions')->find($id);

        if($user){
            return $this->success('User Details',$user);
        }
        else{
            return $this->DataNotFound();
        }
    }

    //change Password

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
        
        return $this->success('your Password Change Successfully',$user); 
    }   

    //User Logout Code

    public function logout(){
        Session::flush();
        Auth::logout();
        return response()->json([
            'message'   =>'Your Are Logout Now',
            'status'    =>200
        ]);
    }



    
}
