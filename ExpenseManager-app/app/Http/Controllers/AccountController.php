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
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function addAccount(Request $request){
        $request->validate([
            'account_name'      => 'required',
            'account_number'    => 'required|min:12|unique:accounts',
            'user_id'           => 'required'
        ]);
        

        $accountdata = Account::create([
            'account_name'      => $request->account_name,
            'account_number'    => $request->account_number,
            'user_id'           => $request->user_id
        ]);

        return response()->json([
            'message'       => 'your account has been created successfully',
            'status'        => '200',
            'userId'        => $accountdata,
        ]);
    }

    public function show()
    {
        $accountdata = Account::all();
        return response()->json([
            'message'       => 'Account List',
            'status'        => 200,
            'accountdata'   => $accountdata
        ]);
    }
    
    public function destory($id){

        $accountdata = Account::find($id);
       
        if(is_null($accountdata)){
            $error = $this->DataNotFound();
            return $error;
        }
        else{
            $accountdata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
        }
    }

    public function update(Request $request, Account $id){
        
        $input = $request->all();
        $validateaccountdata = Validator::make($input, [
            'account_name'      => 'required',
            'account_number'    => 'required|min:10|max:12',
            //'user_id'           => 'required',
        ]);

        if($validateaccountdata->fails()){
            $error = $this->ErrorResponse($validateaccountdata);
            return $error;    
        }
        $id->account_name = $input['account_name'];
        $id->account_number = $input['account_number'];
        //$account->user_id = $input['user_id'];
        $id->save();
        
        return response()->json([
            'status' => 200,
            'message' => 'Data Updated',
            'accountdata' => $id,
        ],200);

    }

    public function index($id)
    {
        $accountdata = Account::find($id);
        if (is_null($accountdata)) {
            $error = $this->DataNotFound();
            return $error;
        }
        else{
        return response()->json([
            'status' => 200,
            'message' => 'Account Data Fetched Successfully',
            'data' => $accountdata,
        ], 200);

        }
    }


}
