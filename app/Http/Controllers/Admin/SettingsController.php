<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\GptKey;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;

class SettingsController extends Controller
{
    public function index()
    {
        return view("admin.settings", [
            'gptKey' => GptKey::first(),
            'user' => Auth::user(),
            'productUrl' => Option::where('key', 'product-url')->first()->value,
            'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
            'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
        ]);
    }

    public function update(Request $request)
    {
        $gptKey = GptKey::first();
        $gptKey->model = $request->model;
        $gptKey->key = $request->key;
        $gptKey->product_prompt = $request->product_prompt;
        $gptKey->is_image_compared = $request->has('imageCompare') ? 1 : 0;
        $gptKey->image_prompt = $request->image_prompt;
        $gptKey->log_delete_days = $request->log_delete_days;
        $gptKey->save();

        $option = Option::where('key', 'product-url')->first();
        $option->value = $request->product_url;
        $option->save();

        $user = Auth::user();
        return response()->redirectTo('/admin/settings')->with('success', 'Updated Successfully.');
    }
}
