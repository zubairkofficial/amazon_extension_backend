<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function show($id)
    {
        $log = Log::with('user')->find($id);

        if (!$log) {
            return Redirect::back()->with('success', 'Log not found.');
        }

        $log->image_match = $this->formatResponse($log->image_match);
        $log->summary = $this->formatResponse($log->summary);

        return view('admin.log', [
            'log' => $log
        ]);
    }
    public function destroy($id)
    {
        Log::find($id)->delete();
        return redirect()->route('dashboard')->with('success', 'Log deleted successfully.');
    }

    public function responseDecode($string)
    {
        $string = substr($string, 6);
        if ($string !== "[DONE]") {
            $decodes = json_decode($string, true);
            if ($decodes) {
                if (isset ($decodes['choices'][0]['delta']['content'])) {
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

    public function formatResponse($text)
    {
        $boldText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $italicText = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $boldText);
        return str_replace("\n", '<br/>', $italicText);
    }
}
