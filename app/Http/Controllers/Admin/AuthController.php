<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);


    }

}
