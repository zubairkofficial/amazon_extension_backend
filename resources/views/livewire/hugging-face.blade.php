<div class="row" >
    <h1 class="mb-2">{{ $formType === 'add' ? 'Create' : 'Update' }} Local Model</h1>
    
    <div class="text-end">
        <a href="/admin/localmodels" class="btn btn-primary">Show All</a>
    </div>
    
    <div class="col-md-7" >
        <form method="POST" action="{{ $formType === 'update' ? "/admin/localmodels/{$model->id}" : '/admin/localmodels' }}" id="localModelForm">
            @csrf
            @if($formType === 'update')
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label for="baseUrl" class="form-label">Base URL</label>
                <input type="text" class="form-control" id="baseUrl" name="baseUrl" wire:model.defer="baseUrl" autocomplete="baseUrl">
                @error('baseUrl')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" wire:model.defer="name" autocomplete="name">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" wire:change="changeType($event.target.value)" wire:model="type">
                    <option value="">Select Type</option>
                    <option value="completions">completions</option>
                    <option value="chat-completions">chat completions</option>
                    <option value="chat-completions-with-characters">Chat completions with characters</option>
                </select>
                @error('type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            @if($type === 'completions')
                <div class="form-group mb-3">
                    <label for="max_tokens" class="form-label">Max Tokens</label>
                    <input type="number" class="form-control" id="max_tokens" name="max_tokens" wire:model.defer="max_tokens" wire:input="getJsonPreviewProperty()" autocomplete="max_tokens">
                    @error('max_tokens')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="temp" class="form-label">Temperature</label>
                    <input type="number" class="form-control" id="temp" name="temp" wire:model.defer="temp" autocomplete="temp" wire:input="getJsonPreviewProperty()">
                    @error('temp')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="top_p" class="form-label">Top_p</label>
                    <input type="number" class="form-control" id="top_p" name="top_p" wire:model.defer="top_p" autocomplete="top_p"  wire:input="getJsonPreviewProperty()">
                    @error('top_p')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="seed" class="form-label">Seed</label>
                    <input type="number" class="form-control" id="seed" name="seed" wire:model.defer="seed" autocomplete="seed" wire:input="getJsonPreviewProperty()">
                    @error('seed')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if($type === 'chat-completions')
                <div class="form-group mb-3">
                    <label for="mode" class="form-label">Mode</label>
                    <input type="text" class="form-control" id="mode" name="mode" wire:model.defer="mode" autocomplete="mode" wire:input="getJsonPreviewProperty()">
                    @error('mode')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="instruction_template" class="form-label">Instruction Template</label>
                    <input type="text" class="form-control" id="instruction_template" name="instruction_template" wire:model.defer="instruction_template" wire:input="getJsonPreviewProperty()" autocomplete="instruction_template">
                    @error('instruction_template')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if($type === 'chat-completions-with-characters')
                <div class="form-group mb-3">
                    <label for="mode" class="form-label">Mode</label>
                    <input type="text" class="form-control" id="mode" name="mode" wire:model.defer="mode" autocomplete="mode" wire:input="getJsonPreviewProperty()">
                    @error('mode')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="character" class="form-label">Character</label>
                    <input type="text" class="form-control" id="character" name="character" wire:model.defer="character" wire:input="getJsonPreviewProperty()" autocomplete="character">
                    @error('character')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if(count($models) > 0)
                <div class="form-group mb-3">
                    <label for="copyFrom" class="form-label">Prompt Copy From</label>
                    <select class="form-control" id="copyFrom" name="copyFrom" wire:change="changeCopyFrom($event.target.value)" wire:input="getJsonPreviewProperty()">
                        <option value="">Select Model</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-group mb-4">
                <label class="form-label" for="prompt">Product Compare Prompt</label>
                <div class="form-control-wrap">
                    <textarea class="form-control" rows="15" id="prompt" wire:model.defer="prompt" name="prompt"></textarea>
                    <small class="text-danger"></small>
                </div>
                <div class="mb-3">
                    <div>
                        <strong>Scrape Arguments: </strong>
                        @foreach ($scrapeArguments as $promptArgument)
                            <button type="button" class="btn border border-primary d-inline-block m-1 px-2 py-0 prompt-argument" data-add="scrape.{{ $promptArgument }}">{{ $promptArgument }}</button>
                        @endforeach
                    </div>
                    <div>
                        <strong>System Arguments: </strong>
                        @foreach ($systemArguments as $promptArgument)
                            <button type="button" class="btn border border-primary d-inline-block m-1 px-2 py-0 prompt-argument" data-add="system.{{ $promptArgument }}">{{ $promptArgument }}</button>
                        @endforeach
                    </div>
                </div>
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
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-primary">{{ $formType === 'add' ? 'Add' : 'Update' }}</button>
            </div>
        </form>
    </div>

    <div class="col-md-5 mt-4" >        
        <label for="baseUrl" class="form-label">JSON PREVIEW</label>
        <div style=" background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
            <pre>{{ $this->jsonPreview }}</pre>
        </div>
    </div>
</div>

