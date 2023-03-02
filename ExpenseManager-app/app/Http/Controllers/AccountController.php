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
        
        $accountdata = Account::create($request->only('account_name','account_number','user_id'));

        return response()->json([
            'message'       => 'your account has been created successfully',
            'status'        => '200',
            'userId'        => $accountdata,
        ]);
    }

    //list Account 

    public function show()
    {
        $accountdata = Account::all();
        return response()->json([
            'message'       => 'Account List',
            'status'        => 200,
            'accountdata'   => $accountdata
        ]);
    }
    
    //delete Account

    public function destory($id){

        $accountdata = Account::find($id);
       
        if(is_null($accountdata)){
            return $this->DataNotFound();
        }
        else{
            $accountdata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
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
    
        return response()->json([
            'status'        => 200,
            'message'       => 'Data Updated',
            'accountdata'   => $id,
        ],200);
        }
    }

    //get Account By Id

    public function get($id)
    {
        $accountdata = Account::find($id);
        if (is_null($accountdata)) {
            return $this->DataNotFound();
        }
        else{
        return response()->json([
            'status'        => 200,
            'message'       => 'Account Data Fetched Successfully',
            'data'          => $accountdata,
        ], 200);

        }
    }

    public function listTransaction($id){
        $transaction = Account::findOrFail($id)->transactions;

        if(count($transaction) > 0){
            return response()->json([
                'message'           => 'Transaction List',
                'transactiondata'   => $transaction
            ]);
        }
        else{
            return $this->DataNotFound();
        }
       
    }
}
