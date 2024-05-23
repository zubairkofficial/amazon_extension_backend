@extends('admin.layout')

@section('content')

<form class="rounded shadow-sm p-4" action="/admin/settings" method="POST">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Settings</h2>

    @include('admin.partials.message')
    @csrf
    <div class="form-group mb-4">
        <label class="form-label" for="model_type">Model Type</label>
        <div class="form-control-wrap">
            <select class="form-control" id="model_type" name="model_type">
                <option value="">Select Model Type</option>
                <option value="local_model" @if($setting->model_type == 'local_model') selected @endif>Local Model</option>
                <option value="openAI_model" @if($setting->model_type == 'openAI_model') selected @endif>Open AI Model</option>
            </select>
            <small class="text-danger"></small>
        </div>
    </div>
    
    <div id="local_model_section" style="display: none;">
        <div class="form-group mb-4">
            <label class="form-label" for="local_model_id">Local Model</label>
            <div class="form-control-wrap">
                <select class="form-control" id="local_model_id" name="local_model_id">
                    <option value="">Select Local Model</option>
                    @foreach ($local_models as $local_model)
                        <option 
                            @if($setting->local_model_id == $local_model->id) selected @endif  
                            value="{{ $local_model->id }}"
                            data-prompt="{{ $local_model->prompt }}"
                        >
                            {{ $local_model->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="prompt">Product Compare Prompt</label>
            <div class="form-control-wrap">
                <textarea class="form-control" rows="15" id="prompt" name="prompt">{{ $setting->local_model?->prompt }}</textarea>
                <small class="text-danger"></small>
            </div>
            <div class="mb-3">
                <div>
                    <strong>Scrape Arguments: </strong>
                    @foreach ($scrapeArguments as $promptArgument)
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 prompt-argument"
                        data-add="scrape.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endforeach
                </div>
                <div>
                    <strong>System Arguments: </strong>
                    @foreach ($systemArguments as $promptArgument)
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 prompt-argument"
                        data-add="system.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endforeach
                </div>
            </div>
        </div>     
    </div>

    <div id="openAI_model_section" style="display: none;">
        <div class="form-group mb-4">
            <label class="form-label" for="model">Model Name</label>
            <div class="form-control-wrap">
                <select class="form-control" id="model" name="model">
                    <option value="gpt-4-1106-preview" @if($setting->model == 'gpt-4-1106-preview') selected @endif>gpt-4-1106-preview</option>
                    <option value="gpt-4-0125-preview" @if($setting->model == 'gpt-4-0125-preview') selected @endif>gpt-4-0125-preview</option>
                    <option value="gpt-4-turbo-preview" @if($setting->model == 'gpt-4-turbo-preview') selected @endif>gpt-4-turbo-preview</option>
                    <option value="gpt-4" @if($setting->model == 'gpt-4') selected @endif>gpt-4</option>
                    <option value="gpt-4-0613" @if($setting->model == 'gpt-4-0613') selected @endif>gpt-4-0613</option>
                    <option value="gpt-4-32k" @if($setting->model == 'gpt-4-32k') selected @endif>gpt-4-32k</option>
                    <option value="gpt-4-32k-0613" @if($setting->model == 'gpt-4-32k-0613') selected @endif>gpt-4-32k-0613</option>
                    <option value="gpt-3.5-turbo-0125" @if($setting->model == 'gpt-3.5-turbo-0125') selected @endif>gpt-3.5-turbo-0125</option>
                    <option value="gpt-3.5-turbo" @if($setting->model == 'gpt-3.5-turbo') selected @endif>gpt-3.5-turbo</option>
                </select>
                <small class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="model_temperature">Model Temperature</label>
            <div class="form-control-wrap">
                <input class="form-control" id="model_temperature" name="model_temperature" type="text" pattern="^(0(\.\d+)?|1(\.0+)?)$" value="{{ old('model_temperature',$setting->model_temperature) }}" required />
                <small id="model_temperature_error" class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="image_model">Image Compare Model</label>
            <div class="form-control-wrap">
                <select class="form-control" id="image_model" name="image_model">
                    <option value="gpt-4-vision-preview" @if($setting->image_model == 'gpt-4-vision-preview') selected @endif>gpt-4-vision-preview</option>
                    <option value="gpt-4-1106-vision-preview" @if($setting->image_model == 'gpt-4-1106-vision-preview') selected @endif>gpt-4-1106-vision-preview</option>
                    <option value="gpt-4-1106-preview" @if($setting->image_model == 'gpt-4-1106-preview') selected @endif>gpt-4-1106-preview</option>
                    <option value="gpt-4-0125-preview" @if($setting->image_model == 'gpt-4-0125-preview') selected @endif>gpt-4-0125-preview</option>
                    <option value="gpt-4-turbo-preview" @if($setting->image_model == 'gpt-4-turbo-preview') selected @endif>gpt-4-turbo-preview</option>
                    <option value="gpt-4" @if($setting->image_model == 'gpt-4') selected @endif>gpt-4</option>
                    <option value="gpt-4-0613" @if($setting->image_model == 'gpt-4-0613') selected @endif>gpt-4-0613</option>
                    <option value="gpt-4-32k" @if($setting->image_model == 'gpt-4-32k') selected @endif>gpt-4-32k</option>
                    <option value="gpt-4-32k-0613" @if($setting->image_model == 'gpt-4-32k-0613') selected @endif>gpt-4-32k-0613</option>
                    <option value="gpt-3.5-turbo-0125" @if($setting->image_model == 'gpt-3.5-turbo-0125') selected @endif>gpt-3.5-turbo-0125</option>
                    <option value="gpt-3.5-turbo" @if($setting->image_model == 'gpt-3.5-turbo') selected @endif>gpt-3.5-turbo</option>
                </select>
                <small class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="image_model_temperature">Image Model Temperature</label>
            <div class="form-control-wrap">
                <input class="form-control" id="image_model_temperature" name="image_model_temperature" type="text" pattern="^(0(\.\d+)?|1(\.0+)?)$" value="{{ old('image_model_temperature',$setting->image_model_temperature) }}" required />
                <small id="image_model_temperature_error" class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="key">Key</label>
            <div class="form-control-wrap">
                <input class="form-control" id="key" name="key" type="text" value="{{ old('key',$setting->key) }}" />
                <small class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="product_prompt">Product Compare Prompt</label>
            <div class="form-control-wrap">
                <textarea class="form-control" rows="15" id="product_prompt" name="product_prompt">{{ $setting->product_prompt }}</textarea>
                <small class="text-danger"></small>
            </div>
            <div class="mb-3">
                <div>
                    <strong>Scrape Arguments: </strong>
                    @foreach ($scrapeArguments as $promptArgument)
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 product-prompt-argument"
                        data-add="scrape.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endforeach
                </div>
                <div>
                    <strong>System Arguments: </strong>
                    @foreach ($systemArguments as $promptArgument)
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 product-prompt-argument"
                        data-add="system.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="image_prompt">Image Compare Prompt</label>
            <div class="form-control-wrap">
                <textarea class="form-control" id="image_prompt" rows="10" name="image_prompt">{{ $setting->image_prompt }}</textarea>
                <small class="text-danger"></small>
            </div>
            <div class="mb-3">
                <div>
                    <strong>Scrape Arguments: </strong>
                    @foreach ($scrapeArguments as $promptArgument)
                    @if($promptArgument==='image')
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 image-prompt-argument"
                        data-add="scrape.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endif
                    @endforeach
                </div>
                <div>
                    <strong>System Arguments: </strong>
                    @foreach ($systemArguments as $promptArgument)
                    @if($promptArgument=='image')
                    <button type="button"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 image-prompt-argument"
                        data-add="system.{{ $promptArgument }}">{{ $promptArgument }}</button>
                    @endif
                    @endforeach
                </div>
                <div>
                    <strong>Image Compare: </strong>
                    <input type="checkbox" id="imageCompareCheckbox" name="imageCompare"
                        class="btn border border-primary d-inline-block m-1 px-2 py-0 product-prompt-argument" {{
                        $setting->is_image_compared ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="product_url">Products URL</label>
        <div class="form-control-wrap">
            <input class="form-control" id="product_url" name="product_url" value="{{ $productUrl }}">
            <small class="text-danger"></small>
        </div>
    </div>
    <div class="form-group mb-4">
        <label class="form-label" for="fastapi_url">FastApi URL</label>
        <div class="form-control-wrap">
            <input class="form-control" id="fastapi_url" name="fastapi_url" value="{{ $fastapiUrl }}">
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="log_delete_days">Log Delete Days</label>
        <div class="form-control-wrap">
            <input class="form-control" type="number" id="log_delete_days" name="log_delete_days"
                value="{{ $setting->log_delete_days }}">
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="local_model_id">Time Zone</label>
        <div class="form-control-wrap">                
        <select name="timezone" id="timezone" class="form-control">
            @foreach(timezone_identifiers_list() as $timezone)
                <option @if($setting->timezone == $timezone) selected @endif value="{{ $timezone }}">{{ $timezone }}</option>
            @endforeach
        </select>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<script>
class ArgumentsHandler {
    constructor(textAreaSelector, argumentButtonsSelector) {
        this.textarea = document.querySelector(textAreaSelector);
        this.argumentButtons = document.querySelectorAll(argumentButtonsSelector);

        for (const argumentButton of this.argumentButtons) {
            argumentButton.addEventListener('click', this.argumentButtonClicked.bind(this));
        }
    }

    argumentButtonClicked(e) {
        const toAdd = `{ ${e.target.dataset.add} }`;
        this.substitute(toAdd);
    }

    substitute(text) {
        const { selectionStart, selectionEnd } = this.textarea;
        this.textarea.value = this.textarea.value.substr(0, selectionStart) + text + this.textarea.value.substr(selectionEnd);
        this.textarea.focus();
        this.textarea.selectionStart = this.textarea.selectionEnd = selectionStart + text.length;
    }
}

document.addEventListener('DOMContentLoaded', (event) => {
    const modelTypeSelect = document.getElementById('model_type');
    const localModelSection = document.getElementById('local_model_section');
    const openAIModelSection = document.getElementById('openAI_model_section');
    const localModelSelect = document.getElementById('local_model_id');
    const promptTextarea = document.getElementById('prompt');

    function toggleSections() {
        const selectedModelType = modelTypeSelect.value;
        if (selectedModelType === 'local_model') {
            localModelSection.style.display = 'block';
            openAIModelSection.style.display = 'none';
        } else if (selectedModelType === 'openAI_model') {
            localModelSection.style.display = 'none';
            openAIModelSection.style.display = 'block';
        } else {
            localModelSection.style.display = 'none';
            openAIModelSection.style.display = 'none';
        }
    }

    localModelSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const prompt = selectedOption.getAttribute('data-prompt');
        promptTextarea.value = prompt || '';
    });

    modelTypeSelect.addEventListener('change', toggleSections);

    toggleSections(); // Initial call to set the correct state based on the pre-selected value.

    const imageInput = document.getElementById('image_model_temperature');
    const imageError = document.getElementById('image_model_temperature_error');
    const modelInput = document.getElementById('model_temperature');
    const modelError = document.getElementById('model_temperature_error');

    function validateInputPattern(inputElement, errorElement) {
        inputElement.addEventListener('input', function () {
            if (!inputElement.checkValidity()) {
                errorElement.textContent = 'Please enter a value between 0 and 1. Decimals are allowed.';
            } else {
                errorElement.textContent = '';
            }
        });
    }

    validateInputPattern(imageInput, imageError); 
    validateInputPattern(modelInput, modelError); 

    new ArgumentsHandler('#prompt', '.prompt-argument');
    new ArgumentsHandler('#product_prompt', '.product-prompt-argument');
    new ArgumentsHandler('#image_prompt', '.image-prompt-argument');
});
</script>

@endsection
