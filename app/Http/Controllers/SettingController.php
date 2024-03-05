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

    public function formatResponse($text)
    {
        $boldText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $italicText = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $boldText);
        return str_replace("\n", '<br/>', $italicText);
    }
    public function responseDecode($string)
    {
        $string = substr($string, 6);
        if ($string !== "[DONE]") {
            $decodes = json_decode($string, true);
            if ($decodes) {
                if (isset($decodes['choices'][0]['delta']['content'])) {
                    $output = $decodes['choices'][0]['delta']['content'];
                } else {
                    $output = "";
                }
            } else {
                $jsonStartPos = strpos($string, '{"content":');
                $jsonEndPos = strpos($string, ',"finish_reason"');
                $substring = substr($string, $jsonStartPos + 12, $jsonEndPos - ($jsonStartPos + 14));
                $output = $substring;
            }
            return $output;
        }
        return "";
    }

    
    public function show($id)
    {
        $log=Log::find($id);
        $log->summary=$this->formatResponse($this->responseDecode($log->summary));
        return view('log', [
            'log' => $log
        ]);
    }

    public function destroy($id)
    {
        Log::find($id)->delete();
        return redirect()->route('dashboard')->with('success', 'Log deleted successfully.');
    }
}
