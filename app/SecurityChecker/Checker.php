<?php

namespace App\SecurityChecker;


trait Checker
{
    public function isParamsFoundInRequest(){
        if (request()->query()){
            return true;
        }
        return false ;
    }

    public function isExtraFoundInBody(array $allowed){
        if (request()->except($allowed)){
            return true;
        }

        return false;
    }

    public function ExtraResponse ($message = 'You Are Trying To Pass Unknown Variables For This Api' , $code = 422) {
        return response([
            'message' => $message
        ] , $code);
    }

    public function CheckerResponse($message = 'Query Params Not allowed For This Api' , $code = 422){
        return response([
           'message' => $message
        ] , $code);
    }
}
