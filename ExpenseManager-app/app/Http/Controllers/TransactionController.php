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
            'category'              => 'required|alpha_dash|max:50',
            'amount'                => 'required|numeric',
            'account_id'            => 'required|exists:accounts,id',
            'account_user_id'       => 'required|exists:account_users,id'
        ]);
        
        $transaction = Transaction::create($request->only('type','category','amount','account_id','account_user_id'));

        return $this->success('transaction create Successfully',$transaction);
    
    }

    //list Transaction

    public function list()
    {
        $transactionlist = Transaction::all();
        if(count($transactionlist) > 0){
            return $this->success('transaction List',$transactionlist);
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
            return $this->deleteMessage('transaction Deleted Successfully');
        }
    }

    //update Transaction

    public function update(Request $request, Transaction $id){
   
        $input = $request->all();
        $validatetransactiondata = Validator::make($input, [
            'type'              => 'required|in:expense,income',
            'category'          => 'required|alpha_dash|max:50',
            'amount'            => 'required|numeric',
            'account_id'        => 'required|exists:accounts,id',
            'account_user_id'   => 'required|exists:account_users,id'
        ]);
        
        if($validatetransactiondata->fails()){
            return $this->ErrorResponse($validatetransactiondata);
        }

        
        $id->update($request->only('type','category','amount','account_id','account_user_id'));
        
        return $this->success('transaction updated Successfully',$id);
    
    }

    //get Transaction By Id

    public function get($id)
    {
        $transaction = Transaction::with('user','account','accountUser')->find($id);
        if (is_null($transaction)) {
            return $this->DataNotFound();
        }
        return $this->success('Transaction Details',$transaction);
    }
}
