<?php

namespace App\Http\Controllers;

use App\Models\GptKey;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Option;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $logs = Log::all();
        return view('home', compact('logs'));
    }
    public function setting(Request $request)
    {
        $gptKey = GptKey::first();
        $user = Auth::user();
        $productUrl = Option::where('key', 'product-url')->first()->value;
        return view(
            'setting',
            compact('user', 'gptKey', 'productUrl')
        );
    }

}
