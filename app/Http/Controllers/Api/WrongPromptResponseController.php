<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WrongPromptResponse;

class WrongPromptResponseController extends Controller
{
    public function store(Request $request) {
       $data = new WrongPromptResponse();
       $data->log_id = $request->id;
       $data->asin   = $request->asin;
       if($request->product_id){
        $data->product_id = $request->product_id;
       }
       $data->save();
       return response()->json(["message" => "Data save successfully"], 200);
    }
}
