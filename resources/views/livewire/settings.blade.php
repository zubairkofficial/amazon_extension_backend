<div>
    <form class="rounded shadow-sm p-4" action="/admin/settings" method="POST">

        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Settings
        </h2>

        @include('admin.partials.message')

        @csrf

        <div class="form-group mb-4">
            <label class="form-label" for="model_type">Model Type</label>
            <div class="form-control-wrap">
                <select class="form-control" id="model_type" name="model_type" wire:change="changeType($event.target.value)"  wire:model="model_type">
                    <option value="">Select Model Type</option>
                    <option value="local_model">Local Model</option>
                    <option value="openAI_model">Open AI Model</option>
                </select>
                <small class="text-danger"></small>
            </div>
        </div>
        
        @if($model_type=="local_model")
            <div class="form-group mb-4">
                <label class="form-label" for="local_model_id">Local Model</label>
                <div class="form-control-wrap">
                    <select class="form-control" id="local_model_id" name="local_model_id" wire:change="changeModel($event.target.value)" wire:model="local_model_id">
                    <option value="">Select Local Model</option>
                        @foreach ($local_models as $local_model)
                            <option value="{{ $local_model->id }}">{{ $local_model->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-danger"></small>
                </div>
            </div>

        <div class="form-group mb-4">
            <label class="form-label" for="prompt">Product Compare Prompt</label>
            <div class="form-control-wrap">
                <textarea class="form-control" rows="15" id="prompt" wire:model.defer="prompt"
                    name="prompt"></textarea>
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
        @endif

        @if($model_type=="openAI_model")
            <div class="form-group mb-4">
                <label class="form-label" for="model">Model Name</label>
                <div class="form-control-wrap">
                    <select class="form-control" id="model" name="model" wire:model="model">
                        <option value="gpt-4-1106-preview" >
                            gpt-4-1106-preview
                        </option>
                        <option value="gpt-4-0125-preview" f>
                            gpt-4-0125-preview
                        </option>
                        <option value="gpt-4-turbo-preview">
                            gpt-4-turbo-preview
                        </option>
                        <option value="gpt-4" >
                            gpt-4</option>
                        <option value="gpt-4-0613" >
                            gpt-4-0613</option>
                        <option value="gpt-4-32k" >
                            gpt-4-32k</option>
                        <option value="gpt-4-32k-0613">
                            gpt-4-32k-0613</option>
                        <option value="gpt-3.5-turbo-0125">
                            gpt-3.5-turbo-0125</option>
                        <option value="gpt-3.5-turbo">
                            gpt-3.5-turbo</option>

                    </select>
                    <small class="text-danger"></small>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label" for="model_temperature">Model Temperature</label>
                <div class="form-control-wrap">
                    <input class="form-control" id="model_temperature" wire:model.defer="model_temperature" name="model_temperature" type="text" pattern="^(0(\.\d+)?|1(\.0+)?)$" required />
                    <small id="model_temperature_error" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label" for="image_model">Iamge Compare Model</label>
                <div class="form-control-wrap">
                    <select class="form-control" id="image_model" name="image_model" wire:model="image_model">
                        <option value="gpt-4-vision-preview">
                            gpt-4-vision-preview
                        </option>
                        <option value="gpt-4-1106-vision-preview">
                            gpt-4-1106-vision-preview
                        </option>
                        <option value="gpt-4-1106-preview">
                            gpt-4-1106-preview
                        </option>
                        <option value="gpt-4-0125-preview">
                            gpt-4-0125-preview
                        </option>
                        <option value="gpt-4-turbo-preview">
                            gpt-4-turbo-preview
                        </option>
                        <option value="gpt-4">
                            gpt-4</option>
                        <option value="gpt-4-0613" >
                            gpt-4-0613</option>
                        <option value="gpt-4-32k" >
                            gpt-4-32k</option>
                        <option value="gpt-4-32k-0613">
                            gpt-4-32k-0613</option>
                        <option value="gpt-3.5-turbo-0125">
                            gpt-3.5-turbo-0125</option>
                        <option value="gpt-3.5-turbo" >
                            gpt-3.5-turbo</option>

                    </select>
                    <small class="text-danger"></small>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label" for="image_model_temperature">Image Model Temperature</label>
                <div class="form-control-wrap">
                    <input class="form-control" id="image_model_temperature" wire:model.defer="image_model_temperature" name="image_model_temperature" type="text" pattern="^(0(\.\d+)?|1(\.0+)?)$" required />
                    <small id="image_model_temperature_error" class="text-danger"></small>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label" for="key">Key</label>
                <div class="form-control-wrap">
                    <input class="form-control" id="key" wire:model.defer="key" name="key" type="text" />
                    <small class="text-danger"></small>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label" for="product_prompt">Product Compare Prompt</label>
                <div class="form-control-wrap">
                    <textarea class="form-control" rows="15" id="product_prompt" wire:model.defer="product_prompt"
                        name="product_prompt"></textarea>
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
                <script>
                    new ArgumentsHandler('#product_prompt', '.product-prompt-argument');
                </script>
            </div>
            <div class="form-group mb-4">
                <label class="form-label" for="image_prompt">Image Compare Prompt</label>
                <div class="form-control-wrap">
                    <textarea class="form-control" id="image_prompt" rows="10" wire:model.defer="image_prompt"
                        name="image_prompt"></textarea>
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
                    <div class="form-control-wrap">
                        <label>
                            <strong>Image Compare: </strong>
                            <input type="hidden" name="imageCompare" value="0">
                            <input type="checkbox" name="imageCompare" id="imageCompare" class="form-check-input" wire:model="imageCompare" value="1">
                        </label>
                        <small class="text-danger"></small>
                    </div>
                <script>
                    new ArgumentsHandler('#image_prompt', '.image-prompt-argument');
                </script>
            </div>
        @endif
        <div class="form-group mb-4">
            <label class="form-label" for="product_url">Products URL</label>
            <div class="form-control-wrap">
                <input class="form-control" id="product_url" wire:model.defer="product_url" name="product_url" >
                <small class="text-danger"></small>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label" for="fastapi_url">FastApi URL</label>
            <div class="form-control-wrap">
                <input class="form-control" id="fastapi_url" wire:model.defer="fastapi_url" name="fastapi_url">
                <small class="text-danger"></small>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label" for="log_delete_days">Log Delete Days</label>
            <div class="form-control-wrap">
                <input class="form-control" type="number" id="log_delete_days" wire:model.defer="log_delete_days" name="log_delete_days"
                    >
                <small class="text-danger"></small>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label" for="local_model_id">Time Zone</label>
            <div class="form-control-wrap">                
            <select name="timezone" id="timezone" class="form-control" wire:model="timezone">
                @foreach(timezone_identifiers_list() as $timezone)
                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                @endforeach
            </select>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                Save
            </button>
        </div>

    </form>
   
</div>

@push('scripts')
<script>
    class ArgumentsHandler {
            constructor(textAreaSelector, argumentButtonsSelector) {
                this.textarea = document.querySelector(textAreaSelector);
                this.argumentButtons = document.querySelectorAll(argumentButtonsSelector);

                for (const argumentButton of this.argumentButtons)
                    argumentButton.addEventListener('click', this.argumentButtonClicked.bind(this));
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
    new ArgumentsHandler('#prompt', '.prompt-argument');
</script>
<script>
    document.addEventListener('livewire:load', function () {
        function validateInputPattern(inputElement, errorElement) {
            inputElement.addEventListener('input', function () {
                if (!inputElement.checkValidity()) {
                    errorElement.textContent = 'Please enter a value between 0 and 1. Decimals are allowed.';
                } else {
                    errorElement.textContent = '';
                }
            });
        }

        var imageInput = document.getElementById('image_model_temperature');
        var imageError = document.getElementById('image_model_temperature_error');
        validateInputPattern(imageInput, imageError); 

        var modelInput = document.getElementById('model_temperature');
        var modelError = document.getElementById('model_temperature_error');
        validateInputPattern(modelInput, modelError); 
    });
</script>
@endpush
