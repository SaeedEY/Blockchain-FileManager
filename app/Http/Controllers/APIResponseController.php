<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIResponseController extends Controller
{
    public function respond($data,$state=true,$msg=""){
        return response()->json([
                "state" => strval((bool)$state) ,
                "data" => $data ,
                "msg" => $msg
                ]);
    }
}
