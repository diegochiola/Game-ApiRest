<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests ,DispatchesJobs;
    public function sendResponse($result, $message){
        $response = [
            'success' => true, 
            'data' => $result, 
            'message' => $message, 
        ];
        return response()->json($response, 200);    
    }
    public function sendError($message, $errorMessages = []){
        $response = [
            'success' => false, 
            'message' => $message, 
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, 401);
    }
}
