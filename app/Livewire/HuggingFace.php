<?php

namespace App\Livewire;

use Livewire\Component;

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

    public function mount($formType, $model = null)
    {
        $this->formType = $formType;
        $this->model = $model ?? (object) [];
        $this->baseUrl = old('baseUrl') ?? $this->model->baseUrl ?? 'http://127.0.0.1:5000';
        $this->type = old('type') ?? $this->model->type ?? '';
        $this->name = old('name') ?? $this->model->name ?? '';
        $this->max_tokens = old('max_tokens') ?? $this->model->max_tokens ?? '';
        $this->temp = old('temp') ?? $this->model->temp ?? '';
        $this->top_p = old('top_p') ?? $this->model->top_p ?? '';
        $this->seed = old('seed') ?? $this->model->seed ?? '';
        $this->mode = old('mode') ?? $this->model->mode ?? '';
        $this->instruction_template = old('instruction_template') ?? $this->model->instruction_template ?? '';
        $this->character = old('character') ?? $this->model->character ?? '';
    }

    public function changeType($value) {
        $this->type = $value;
    }
    public function render()
    {
        return view('livewire.hugging-face');
    }
}
