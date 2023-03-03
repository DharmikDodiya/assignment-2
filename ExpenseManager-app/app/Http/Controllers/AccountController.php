<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    use ResponseMessage;
    
    //create Account

    public function create(Request $request){
        $request->validate([
            'account_name'      => 'required|max:30',
            'account_number'    => 'required|min:10|max:12|unique:accounts,account_number',
            'user_id'           => 'required|exists:users,id'
        ]);
        
        $account = Account::create($request->only('account_name','account_number','user_id'));

        return $this->success('Account Created Successfuly',$account);
    }

    //list Account 

    public function list()
    {
        $accountlist = Account::all();
        if(count($accountlist) > 0){
            return $this->success('Account List',$accountlist);
        }
        return $this->DataNotFound();
    }
    
    //delete Account

    public function destory($id){

        $accountdata = Account::find($id);
       
        if(is_null($accountdata)){
            return $this->DataNotFound();
        }
        else{
            $accountdata->delete();
            return $this->deleteMessage('Account Deleted Successfuly');
        }
    }

    //update Account
    public function update(Request $request, Account $id){
        
        $input = $request->all();
        $validateaccountdata = Validator::make($input, [
            'account_name'      => 'required|max:30',
            'account_number'    => 'required|min:10|max:12|unique:accounts,account_number',
        ]);

        if($validateaccountdata->fails()){
            return $this->ErrorResponse($validateaccountdata);  
        }
        else{

        $id->update($request->only('account_name','account_number'));
    
            return $this->success('Updated Data',$id);
        }
    }

    //get Account By Id

    public function get($id)
    {
        $accountdata = Account::with('user','accountUsers','transactions')->find($id);
        if (is_null($accountdata)) {
            return $this->DataNotFound();
        }
        return $this->success('Account Details',$accountdata);
    }

    
}
