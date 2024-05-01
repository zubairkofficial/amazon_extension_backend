<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Setting;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;

class SettingsController extends Controller
{
    public function index()
    {
        return view("admin.settings", [
            'setting' => Setting::first(),
            'user' => Auth::user(),
            'productUrl' => Option::where('key', 'product-url')->first()->value,
            'fastapiUrl' => Option::where('key', 'fastapi-url')->first()->value,
            'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
            'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $setting->model = $request->model;
        $setting->model_temperature = $request->model_temperature;
        $setting->image_model = $request->image_model;
        $setting->image_model_temperature = $request->image_model_temperature;
        $setting->key = $request->key;
        $setting->product_prompt = $request->product_prompt;
        $setting->is_image_compared = $request->has('imageCompare') ? 1 : 0;
        $setting->image_prompt = $request->image_prompt;
        $setting->log_delete_days = $request->log_delete_days;
        $setting->save();

        $option = Option::where('key', 'product-url')->first();
        $option->value = $request->product_url;
        $option->save();

        $option = Option::where('key', 'fastapi-url')->first();
        $option->value = $request->fastapi_url;
        $option->save();

        $user = Auth::user();
        return response()->redirectTo('/admin/settings')->with('success', 'Updated Successfully.');
    }
}
