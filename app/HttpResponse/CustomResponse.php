<?php

namespace App\HttpResponse;

trait CustomResponse {
    public function success($data = null  , $message = 'request was fully successfully' , $code = 200){
        return response([
            'data' => $data,
            'message' => $message
        ] , $code);
    }

    public function error($data = null  , $message = 'request was fully failed' , $code = null){
        return response([
            'data' => $data,
            'message' => $message
        ] , $code);
    }
}
