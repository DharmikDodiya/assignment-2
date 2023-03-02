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
     
        $accountuserdata = AccountUser::create($userdata->only(
            ['first_name','last_name']) 
            +[
                'email' => $request->email ,
                'account_id' => $request->account_id
            ]);

        return response()->json([
            'message'       => 'your account has been created successfully',
            'status'        => '200',
            'userId'        => $accountuserdata,
        ]);
    }

    //list AccountUser

    public function show()
    {
        $accountuserdata = AccountUser::all();
        return response()->json([
            'message'       => 'AccountUser List',
            'status'        => 200,
            'accountdata'   => $accountuserdata
        ]);
    }

    //delete AccountUser

    public function destory($id){
        $accountuserdata = AccountUser::find($id);
       
        if(is_null($accountuserdata)){
            return $this->DataNotFound();
            
        }
        else{
            $accountuserdata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
        }
    }

    //update AccountUser

    public function update(Request $request, AccountUser $id){
        
        $input = $request->all();
        $validateaccountdata = Validator::make($input, [
            'first_name'        => 'required|alpha|max:30',
            'last_name'         => 'required|alpha|max:30',
            'email'             => 'required',
        ]);

        if($validateaccountdata->fails()){
            return $this->ErrorResponse($validateaccountdata);   
        }

        $id->update($request->only('first_name','last_name','email'));
        
        return response()->json([
            'status'        => 200,
            'message'       => 'Data Updated',
            'accountdata'   => $id,
        ],200);

    }

    //get AccountUser By Id

    public function get($id)
    {
        $accountuserdata = AccountUser::find($id);
        if (is_null($accountuserdata)) {
            return $this->DataNotFound();
        }
        else{
        return response()->json([
            'status'        => 200,
            'message'       => 'Account Data Fetched Successfully',
            'data'          => $accountuserdata,
        ], 200);

        }
    }


}
