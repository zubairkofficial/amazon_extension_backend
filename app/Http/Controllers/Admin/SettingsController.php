<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\LocalModel;
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
            'local_models' => LocalModel::all(),
            'productUrl' => Option::where('key', 'product-url')->first()->value,
            'fastapiUrl' => Option::where('key', 'fastapi-url')->first()->value,
            'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
            'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
        ]);
    }

    public function update(Request $request)
    { 
        $setting = Setting::first();
        if ($request->filled('model_type')) {
            $setting->model_type = $request->model_type;
        }
        if ($request->filled('local_model_id')) {
            $setting->local_model_id = $request->local_model_id;
        }
        if ($request->filled('model')) {
            $setting->model = $request->model;
        }
        if ($request->filled('model_temperature')) {
            $setting->model_temperature = $request->model_temperature;
        }
        if ($request->filled('image_model')) {
            $setting->image_model = $request->image_model;
        }
        if ($request->filled('image_model_temperature')) {
            $setting->image_model_temperature = $request->image_model_temperature;
        }
        if ($request->filled('key')) {
            $setting->key = $request->key;
        }
        if ($request->filled('product_prompt')) {
            $setting->product_prompt = $request->product_prompt;
        }
        if ($request->filled('imageCompare')) {
            $setting->is_image_compared = $request->imageCompare;
        }
        if ($request->filled('image_prompt')) {
            $setting->image_prompt = $request->image_prompt;
        }
        if ($request->filled('log_delete_days')) {
            $setting->log_delete_days = $request->log_delete_days;
        }
        if ($request->filled('timezone')) {
            $setting->timezone = $request->timezone;
        }
        
        $setting->save();
        
        if ($request->filled('product_url')) {
            $option = Option::where('key', 'product-url')->first();
            if ($option) {
                $option->value = $request->product_url;
                $option->save();
            }
        }
        if ($request->filled('fastapi_url')) {
            $option = Option::where('key', 'fastapi-url')->first();
            if ($option) {
                $option->value = $request->fastapi_url;
                $option->save();
            }
        }
        if ($request->filled('prompt')) {
            $local_model = LocalModel::where('id', $request->local_model_id)->first();
            if ($local_model) {
                $local_model->prompt = $request->prompt;
                $local_model->save();
            }
        }

        
        $user = Auth::user();
        return response()->redirectTo('/admin/settings')->with('success', 'Updated Successfully.');
    }
}
