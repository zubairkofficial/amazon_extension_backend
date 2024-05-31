<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ImageCompareModel;

class ImageCompare extends Component
{   
    public $formType;
    public $model;
    public $name;
    public $value;
    public $temp;
    public $scrapeArguments;
    public $systemArguments;
    public $imageCompare_prompt;
    public $models;
    public $json;

    public function mount($formType, $model = null, $scrapeArguments, $systemArguments, $models)
    {
        $this->formType = $formType;
        $this->model = $model ?? (object) [];
        $this->name = old('name') ?? $this->model->name ?? "";
        $this->value = old('value') ?? $this->model->value ?? "";
        $this->temp = old('temp') ?? $this->model->temp ?? "";
        $this->imageCompare_prompt = old('imageCompare_prompt') ?? $this->model->imageCompare_prompt ?? "";
        $this->scrapeArguments = $scrapeArguments;
        $this->systemArguments = $systemArguments;
        $this->models = $models;
        $this->json = old('json') ?? $this->model->json ?? "";
        if($formType!=="update"){
            $this->updateJsonPreview();
        }
    }

    public function getJsonPreviewProperty()
    {
        $data = [];
                if($this->value){
                    $data['model'] = $this->value;
                }
                $data['messages'] = [['role' => 'system', 'content' => "You are a helpful assistant."],
                                        ['role' => 'user', 'content' => $this->imageCompare_prompt]];
                if($this->temp){
                    $data['temperature'] = $this->temp;
                }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function changeCopyFrom($value) {
        if ($value) {
            $this->imageCompare_prompt = ImageCompareModel::find($value)->imageCompare_prompt;
        } else {
            $this->imageCompare_prompt = old('imageCompare_prompt') ?? $this->model->imageCompare_prompt ?? "";
        }
        $this->updateJsonPreview();
    }
    public function updateJsonPreview()
    {
        $this->json = $this->getJsonPreviewProperty();
    }
    public function render()
    {
        return view('livewire.image-compare');
    }
}
