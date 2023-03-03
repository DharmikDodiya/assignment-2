<?php


namespace App\Traits;


trait ResponseMessage{
    public function ErrorResponse($validateUser){
        return response()->json([
            'status' => 403,
            'message' => 'validation error',
            'errors' => $validateUser->errors()
        ], 403);
    }

    public function DataNotFound(){
        return response()->json([
            'status' => 401,
            'message' => 'Data Not Found'
        ],401);
    }

    public function success($message,$data){
        return response()->json([
            'status'    => 200,
            'success'   => $message,
            'data'      => $data
        ]);
    }

    public function deleteMessage($message){
        return response()->json([
            'status'        => 200,
            'success'       => $message
        ]);
    }
}
?>