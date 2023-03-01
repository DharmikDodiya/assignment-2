<?php

namespace App\Http\Controllers;
use App\Models\AccountUser;
use App\Traits\ResponseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountUserController extends Controller
{
    use ResponseMessage;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function addAccountUser(Request $request){
        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|unique:account_users',
            'account_id'        => 'required'
        ]);
        

        $accountuserdata = AccountUser::create([
            'first_name'      => $request->first_name,
            'last_name'    => $request->last_name,
            'email'           => $request->email,
            'account_id'        => $request->account_id
        ]);

        return response()->json([
            'message'       => 'your account has been created successfully',
            'status'        => '200',
            'userId'        => $accountuserdata,
        ]);
    }

    public function accountUserShow()
    {
        $accountuserdata = AccountUser::all();
        return response()->json([
            'message'       => 'AccountUser List',
            'status'        => 200,
            'accountdata'   => $accountuserdata
        ]);
    }

    public function destory($id){
        $accountuserdata = AccountUser::find($id);
       
        if(is_null($accountuserdata)){
            $error = $this->DataNotFound();
            return $error;
        }
        else{
            $accountuserdata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
        }
    }

    public function update(Request $request, AccountUser $id){
        
        $input = $request->all();
        $validateaccountdata = Validator::make($input, [
            'first_name'        => 'required|alpha',
            'last_name'         => 'required|alpha',
            'email'             => 'required',
        ]);

        if($validateaccountdata->fails()){
            $error = $this->ErrorResponse($validateaccountdata);
            return $error;    
        }
        $id->first_name     = $input['first_name'];
        $id->last_name      = $input['last_name'];
        $id->email          = $input['email'];

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
        $accountuserdata = AccountUser::find($id);
        if (is_null($accountuserdata)) {
            $error = $this->DataNotFound();
            return $error;
        }
        else{
        return response()->json([
            'status' => 200,
            'message' => 'Account Data Fetched Successfully',
            'data' => $accountuserdata,
        ], 200);

        }
    }


}
