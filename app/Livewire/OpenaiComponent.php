<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OpenAIModel;

class OpenaiComponent extends Component
{   
    public $formType;
    public $model;
    public $name;
    public $value;
    public $temp;
    public $scrapeArguments;
    public $systemArguments;
    public $openai_prompt;
    public $models;
    public $json;
    public $check=1;
 
    public function mount($formType, $model = null, $scrapeArguments, $systemArguments, $models)
    {
        $this->formType = $formType;
        $this->model = $model ?? (object) [];
        $this->name = old('name') ?? $this->model->name ?? "";
        $this->value = old('value') ?? $this->model->value ?? "";
        $this->temp = old('temp') ?? $this->model->temp ?? "";
        $this->openai_prompt = old('openai_prompt') ?? $this->model->openai_prompt ?? "";
        $this->scrapeArguments = $scrapeArguments;
        $this->systemArguments = $systemArguments;
        $this->models = $models;
        $this->json = old('json') ?? $this->model->json ?? "";
     
        if($formType!=="update"){
            $this->updateJsonPreview();
        }
        if($formType=="update"){
            $this->updateJson();
        }
    }

    public function updateJson(){        
        if($this->formType=="update"){
            $decodedJson = json_decode($this->json, true);
            if ($decodedJson) {
            
                $this->value = $decodedJson['model'] ?? null;
                $this->temp = $decodedJson['temperature'] ?? null;
                if (isset($decodedJson['messages'])) {
                    foreach ($decodedJson['messages'] as &$message) {
                        if (isset($message['role']) && $message['role'] === 'user') {
                            $this->openai_prompt= $message['content'] ;
                        }elseif (isset($message['role']) && $message['role'] === 'system') {
                            $this->openai_prompt= $message['content'] ;
                        }                        
                    }
                } 

            }
        }
    }

    
    public function getJsonPreviewProperty()
    {
        if($this->formType=="update"){
            $data = json_decode($this->json, true);
            if($this->value){
                $data['model'] = $this->value;
            }
            
            if (isset($data['messages'])) {
                foreach ($data['messages'] as &$message) {
                    if (isset($message['role']) && $message['role'] === 'user') {
                        $message['content'] = $this->openai_prompt;
                    }elseif (isset($message['role']) && $message['role'] === 'system') {
                        $message['content'] = $this->openai_prompt;
                    }                    
                }
            } 
            if($this->temp){
                $data['temperature'] = $this->temp;
            }
        }else{
            $data = [];

                if($this->value){
                    $data['model'] = $this->value;
                }
                switch ($this->check) {
                    case 1:
                        $data['messages'] = [
                            ['role' => 'system', 'content' => "You are a helpful assistant"],
                            ['role' => 'user', 'content' => $this->openai_prompt]
                        ];
                        break;
                    case 2:
                        $data['messages'] = [
                            ['role' => 'system', 'content' => $this->openai_prompt]
                        ];
                        break;
                    case 3:
                        $data['messages'] = [
                            ['role' => 'user', 'content' => $this->openai_prompt]
                        ];
                        break;
                }
                
                if($this->temp){
                    $data['temperature'] = $this->temp;
                }

            }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function changeCopyFrom($value) {
        if ($value) {
            $this->openai_prompt = OpenAIModel::find($value)->openai_prompt;
        } else {
            $this->openai_prompt = old('openai_prompt') ?? $this->model->openai_prompt ?? "";
        }
        $this->updateJsonPreview();
    }
    public function updateJsonPreview()
    {
        $this->json = $this->getJsonPreviewProperty();
    }
    
    public function render()
    {
        return view('livewire.openai-component');
    }
}
