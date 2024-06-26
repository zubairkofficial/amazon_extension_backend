<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WrongPromptResponse;

class WrongPromptResponseController extends Controller
{
    public function index()
    {
        return view('admin.WrongPromptResponse.index');
    }

    public function destroy($id)
    {
        WrongPromptResponse::find($id)->delete();
        return redirect()->route('wrong-prompt-resp')->with('success', 'Wrong Prompt Response deleted successfully.');
    }
}
