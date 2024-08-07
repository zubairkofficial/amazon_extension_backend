<div>
    <h1 class="mb-2">{{ $formType === 'add' ? 'Create' : 'Update' }} Open AI Model</h1>
    
    <div class="text-end">
        <a href="/admin/imgCompModels" class="btn btn-primary">Show All</a>
    </div>
    
    <form method="POST" class="row" style="--bs-gutter-x: 1.5rem!important;" action="{{ $formType === 'update' ? "/admin/imgCompModels/{$model->id}" : '/admin/imgCompModels' }}" id="localModelForm">

    <div class="col-md-7">
            @csrf
            @if($formType === 'update')
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" wire:model.defer="name" autocomplete="name">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
                <div class="form-group mb-3">
                    <label for="value" class="form-label">Value</label>
                    <input type="text" class="form-control" id="value" name="value" wire:model.defer="value" wire:input="updateJsonPreview" autocomplete="value">
                    @error('value')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="temp" class="form-label">Temperature</label>
                    <input type="number" class="form-control" id="temp" name="temp" wire:model.defer="temp" autocomplete="temp" wire:input="updateJsonPreview" step="any">
                    @error('temp')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

            @if(count($models) > 0)
                <div class="form-group mb-3">
                    <label for="copyFrom" class="form-label">Prompt Copy From</label>
                    <select class="form-control" id="copyFrom" name="copyFrom" wire:change="changeCopyFrom($event.target.value)" wire:input="updateJsonPreview">
                        <option value="">Select Model</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group mb-4">
                <label class="form-label" for="imageCompare_prompt">Image Compare Prompt</label>
                <div class="form-control-wrap">
                    <textarea class="form-control" rows="15" id="imageCompare_prompt" wire:model.defer="imageCompare_prompt" name="imageCompare_prompt" wire:input="updateJsonPreview"></textarea>
                    @error('imageCompare_prompt')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
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
                    new ArgumentsHandler('#imageCompare_prompt', '.image-prompt-argument');
                </script>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">{{ $formType === 'add' ? 'Add' : 'Update' }}</button>
            </div>
        </div>
    
        <div class="col-md-5 mt-4">
            <label for="json" class="form-label">JSON Preview</label>
            <pre>
                <textarea class="form-control" id="json" name="json" rows="15" wire:model.live="json"></textarea>
            </pre>
                @error('json')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>
    </form>
</div>
