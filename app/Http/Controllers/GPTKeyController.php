<?php

namespace App\Http\Controllers;

use App\Models\GptKey;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GPTKeyController extends Controller
{
    public function update(Request $request)
    {
        $gptKey = GptKey::first();
        $gptKey->model = $request->model;
        $gptKey->key = $request->key;
        $gptKey->prompt = $request->prompt; 
        $gptKey->save();

        $option = Option::where('key', 'product-url')->first();
        $option->value = $request->product_url;
        $option->save();

        $user = Auth::user();
        return redirect('setting');
    }
}