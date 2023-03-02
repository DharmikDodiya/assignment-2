<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use App\Traits\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ResponseMessage;
  
    //create Transaction

    public function create(Request $request){
        $request->validate([
            'type'                  => 'required|in:income,expense',
            'category'              => 'required|alpha_dash',
            'amount'                => 'required|numeric',
            'account_id'            => 'required|exists:accounts,id',
            'account_user_id'       => 'required|exists:account_users,id'
        ]);
        
        $transactiondata = Transaction::create($request->only('type','category','amount','account_id','account_user_id'));

        return response()->json([
            'message'       => 'your Transaction successfully',
            'status'        => '200',
            'userId'        => $transactiondata,
        ]);
    
    }

    //list Transaction

    public function show()
    {
        $transactiondata = Transaction::all();
        if(count($transactiondata) > 0){
            return response()->json([
                'message'           => 'Transaction List',
                'status'            => 200,
                'transactiondata'   => $transactiondata
            ]);
        }
        else{
            return $this->DataNotFound();
        }
    }

    //delete Transaction

    public function destory($id){
        $transactiondata = Transaction::find($id);
       
        if(is_null($transactiondata)){
            return $this->DataNotFound();
        }
        else{
            $transactiondata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
        }
    }

    //update Transaction

    public function update(Request $request, Transaction $id){
   
        $input = $request->all();
        $validatetransactiondata = Validator::make($input, [
            'type'              => 'required|in:expense,income',
            'category'          => 'required|alpha_dash',
            'amount'            => 'required|numeric',
            'account_id'        => 'required|exists:accounts,id',
            'account_user_id'   => 'required|exists:account_users,id'
        ]);
        
        if($validatetransactiondata->fails()){
            return $this->ErrorResponse($validatetransactiondata);
        }

        
        $id->update($request->only('type','category','amount','account_id','account_user_id'));
        
        return response()->json([
            'status'        => 200,
            'message'       => 'Data Updated',
            'accountdata'   => $id,
        ],200);

    
    }

    //get Transaction By Id

    public function get($id)
    {
        $transactiondata = Transaction::find($id);
        if (is_null($transactiondata)) {
            return $this->DataNotFound();
        }
        else{
        return response()->json([
            'status'        => 200,
            'message'       => 'Account Data Fetched Successfully',
            'data'          => $transactiondata,
        ], 200);

        }
    }
}
