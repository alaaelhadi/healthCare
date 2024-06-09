<?php
namespace App\Http\Controllers\Api;

trait ApiResponseTrait
{
    public function apiResponse($message = null, $status = null, $data = null){
        $array = [
            'message' => $message,
            'status' => $status,
            'data' => $data
        ];
        return response()->json($array);
    }
}
?>