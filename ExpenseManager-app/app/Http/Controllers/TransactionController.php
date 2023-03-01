<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use App\Traits\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ResponseMessage;
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    } 

    public function addTransaction(Request $request){
        $request->validate([
            'type'                  => 'required',
            'category'              => 'required|alpha_dash',
            'amount'                => 'required|numeric',
            'account_id'            => 'required',
            'account_user_id'       => 'required'
        ]);
        

        $transactiondata = Transaction::create([
            'type'                  => $request->type,
            'category'              => $request->category,
            'amount'                => $request->amount,
            'account_id'            => $request->account_id,
            'account_user_id'       => $request->account_user_id
        ]);

        return response()->json([
            'message'       => 'your Transaction successfully',
            'status'        => '200',
            'userId'        => $transactiondata,
        ]);
    }

    public function listTransaction()
    {
        $transactiondata = Transaction::all();
        return response()->json([
            'message'       => 'Transaction List',
            'status'        => 200,
            'accountdata'   => $transactiondata
        ]);
    }

    public function destory($id){
        $transactiondata = Transaction::find($id);
       
        if(is_null($transactiondata)){
            $error = $this->DataNotFound();
            return $error;
        }
        else{
            $transactiondata->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Data Deleted Successfully',
            ],200);
        }
    }

    public function update(Request $request, Transaction $id){
        
        $input = $request->all();
        $validatetransactiondata = Validator::make($input, [
            'type'              => 'required',
            'category'          => 'required|alpha_dash',
            'amount'            => 'required|numeric',
            'account_user_id'   => 'required'
        ]);

        if($validatetransactiondata->fails()){
            $error = $this->ErrorResponse($validatetransactiondata);
            return $error;    
        }
        $id->type               = $input['type'];
        $id->category           = $input['category'];
        $id->amount             = $input['amount'];
        $id->account_user_id    = $input['account_user_id'];

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
        $transactiondata = Transaction::find($id);
        if (is_null($transactiondata)) {
            $error = $this->DataNotFound();
            return $error;
        }
        else{
        return response()->json([
            'status' => 200,
            'message' => 'Account Data Fetched Successfully',
            'data' => $transactiondata,
        ], 200);

        }
    }


}
