<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LocalModel;

class HuggingFace extends Component
{
    public $formType;
    public $model;
    public $type;
    public $name;
    public $max_tokens;
    public $temp;
    public $top_p;
    public $seed;
    public $mode;
    public $instruction_template;
    public $character;
    public $baseUrl;
    public $scrapeArguments;
    public $systemArguments;
    public $prompt;
    public $models;
    public $json;
    public $showCurl = false;

    public function mount($formType, $model = null, $scrapeArguments, $systemArguments, $models)
    {
        $this->formType = $formType;
        $this->model = $model ?? (object) [];
        $this->baseUrl = old('baseUrl') ?? $this->model->baseUrl ?? 'http://127.0.0.1:5000';
        $this->type = old('type') ?? $this->model->type ?? "";
        $this->name = old('name') ?? $this->model->name ?? "";
        $this->max_tokens = old('max_tokens') ?? $this->model->max_tokens ?? "";
        $this->temp = old('temp') ?? $this->model->temp ?? "";
        $this->top_p = old('top_p') ?? $this->model->top_p ?? "";
        $this->seed = old('seed') ?? $this->model->seed ?? "";
        $this->mode = old('mode') ?? $this->model->mode ?? "";
        $this->instruction_template = old('instruction_template') ?? $this->model->instruction_template ?? "";
        $this->character = old('character') ?? $this->model->character ?? "";
        $this->prompt = old('prompt') ?? $this->model->prompt ?? "";
        $this->scrapeArguments = $scrapeArguments;
        $this->systemArguments = $systemArguments;
        $this->models = $models;
        $this->json = old('json') ?? $this->model->json ?? "";
        if($formType!=="update"){
            $this->updateJsonPreview();
        }
    }

    public function changeType($value) {
        $this->type = $value;
        $this->updateJsonPreview();
    }

    public function getJsonPreviewProperty()
    {
        $data = [];

        if ($this->type) {
            if ($this->type === 'completions') {
                $data['prompt'] = $this->prompt;
                $data['max_tokens'] = $this->max_tokens;
                $data['temperature'] = $this->temp;
                $data['top_p'] = $this->top_p;
                $data['seed'] = $this->seed;
            } elseif ($this->type === 'chat-completions') {
                $data['messages'] = [['role' => 'user', 'content' => $this->prompt]];
                $data['mode'] = $this->mode;
                $data['instruction_template'] = $this->instruction_template;
            } elseif ($this->type === 'chat-completions-with-characters') {
                $data['messages'] = [['role' => 'user', 'content' => $this->prompt]];
                $data['mode'] = $this->mode;
                $data['character'] = $this->character;
            }
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function updateJsonPreview()
    {
        $this->json = $this->getJsonPreviewProperty();
    }

    public function changeCopyFrom($value) {
        if ($value) {
            $this->prompt = LocalModel::find($value)->prompt;
        } else {
            $this->prompt = old('prompt') ?? $this->model->prompt ?? "";
        }
        $this->updateJsonPreview();
    }

    public function toggleCurlVisibility()
    {
        $this->showCurl = !$this->showCurl;
    }

    public function getCurlCommandProperty()
    {
        $escapedJson = $this->json;

        $command = "";

        if ($this->type === 'completions') {
            $command = <<<CURL
                        curl {$this->baseUrl}/v1/completions \\
                        -H "Content-Type: application/json" \\
                        -d '{$escapedJson}'
                        CURL;
        } elseif ($this->type === 'chat-completions') {
            $command = <<<CURL
                    curl {$this->baseUrl}/v1/chat/completions \\
                    -H "Content-Type: application/json" \\
                    -d '{$escapedJson}'
                    CURL;
        } elseif ($this->type === 'chat-completions-with-characters') {
            $command = <<<CURL
                    curl {$this->baseUrl}/v1/chat/completions \\
                    -H "Content-Type: application/json" \\
                    -d '{$escapedJson}'
                CURL;
        }

        return $command;
    }


    public function render()
    {
        return view('livewire.hugging-face');
    }
}