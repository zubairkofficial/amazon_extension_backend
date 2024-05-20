<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LocalModel;

class Settings extends Component
{
    public $model_type;
    public $local_model_id;
    public $model;
    public $model_temperature;
    public $image_model;
    public $image_model_temperature;
    public $key;
    public $product_prompt;
    public $image_prompt;
    public $imageCompare = false;
    public $product_url;
    public $fastapi_url;
    public $log_delete_days;
    public $scrapeArguments;
    public $systemArguments;
    public $local_models;
    public $timezone;
    public $prompt;

    public function mount($setting,$fastapi_url,$product_url,$scrapeArguments,$systemArguments,$local_models)
    {
        $this->model_type = old('model_type', $setting->model_type);
        $this->local_model_id = old('local_model_id', $setting->local_model_id);
        $this->prompt = $setting->local_model->prompt;
        $this->model = old('model', $setting->model);
        $this->model_temperature = old('model_temperature', $setting->model_temperature);
        $this->image_model = old('image_model', $setting->image_model);
        $this->image_model_temperature = old('image_model_temperature', $setting->image_model_temperature);
        $this->key = old('key', $setting->key);
        $this->product_prompt = old('product_prompt', $setting->product_prompt);
        $this->image_prompt = old('image_prompt', $setting->image_prompt);
        $this->imageCompare = $setting->is_image_compared ? true : false;
        $this->product_url = old('product_url', $product_url);
        $this->fastapi_url = old('fastapi_url', $fastapi_url);
        $this->log_delete_days = old('log_delete_days', $setting->log_delete_days);
        $this->scrapeArguments = $scrapeArguments;
        $this->systemArguments = $systemArguments;
        $this->local_models = $local_models;
        $this->timezone =  old('log_delete_days', $setting->timezone);
    }

    public function changeType($value) {
        $this->model_type = $value;
    }

    public function changeModel($value) {
        $this->prompt = LocalModel::find($value)->prompt;
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
