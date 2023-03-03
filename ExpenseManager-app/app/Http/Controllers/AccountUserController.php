<?php

namespace App\Http\Controllers;
use App\Models\AccountUser;
use App\Models\User;
use App\Traits\ResponseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountUserController extends Controller
{
    use ResponseMessage;

    //create AccountUser

    public function create(Request $request){
        $request->validate([
            'email'             => 'required|exists:users,email|unique:account_users,email',
            'account_id'        => 'required|exists:accounts,id'
        ]);
        
        $userdata = User::where('email',$request->email)->first();
     
        $accountuser = AccountUser::create($userdata->only(
            ['first_name','last_name']) 
            +[
                'email' => $request->email ,
                'account_id' => $request->account_id
            ]);

       return $this->success('transaction created Successfuly',$accountuser);
    }

    //list AccountUser

    public function list()
    {
        $accountusers = AccountUser::all();
        if(count($accountusers) > 0){
        return $this->success('AccountUser List',$accountusers);
        }
        return $this->DataNotFound();
    }

    //delete AccountUser

    public function destory($id){
        $accountuserdata = AccountUser::find($id);
       
        if(is_null($accountuserdata)){
            return $this->DataNotFound();
            
        }
        else{
            $accountuserdata->delete();
            return $this->deleteMessage('AccountUser Deleted Successful');
        }
    }

    //update AccountUser

    public function update(Request $request, AccountUser $id){
        
        $input = $request->all();
        $validateaccountdata = Validator::make($input, [
            'first_name'        => 'required|alpha|max:30',
            'last_name'         => 'required|alpha|max:30',
            'email'             => 'required|unique:account_users,email',
        ]);

        if($validateaccountdata->fails()){
            return $this->ErrorResponse($validateaccountdata);   
        }

        $id->update($request->only('first_name','last_name','email'));
        return $this->success('Updated Data',$id);

    }

    //get AccountUser By Id

    public function get($id)
    {
        $accountuserdata = AccountUser::with('user','account','transactions')->find($id);
        if (is_null($accountuserdata)) {
            return $this->DataNotFound();
        }
        
        return $this->success('AccountUser Details',$accountuserdata);
    }


}
