@extends('admin.layout')

@section('content')

<form class="rounded shadow-sm p-4" action="/admin/settings" method="POST">

    <h2 class="text-lg font-medium text-gray-900 mb-4">
        Settings
    </h2>

    @include('admin.partials.message')

    @csrf

    <div class="form-group mb-4">
        <label class="form-label" for="model">Model Name</label>
        <div class="form-control-wrap">
            <select class="form-control" id="model" name="model">
                <option value="gpt-4-1106-preview" @if($gptKey->
                    model == 'gpt-4-1106-preview') selected @endif>
                    gpt-4-1106-preview
                </option>
                <option value="gpt-4-0125-preview" @if($gptKey->
                    model == 'gpt-4-0125-preview') selected @endif>
                    gpt-4-0125-preview
                </option>
                <option value="gpt-4-turbo-preview" @if($gptKey->
                    model == 'gpt-4-turbo-preview') selected @endif>
                    gpt-4-turbo-preview
                </option>
                <option value="gpt-4" @if($gptKey->model == 'gpt-4')
                    selected @endif>
                    gpt-4</option>
                <option value="gpt-4-0613" @if($gptKey->model ==
                    'gpt-4-0613') selected @endif>
                    gpt-4-0613</option>
                <option value="gpt-4-32k" @if($gptKey->model ==
                    'gpt-4-32k') selected @endif>
                    gpt-4-32k</option>
                <option value="gpt-4-32k-0613" @if($gptKey->model ==
                    'gpt-4-32k-0613') selected @endif>
                    gpt-4-32k-0613</option>
                <option value="gpt-3.5-turbo-0125" @if($gptKey->
                    model == 'gpt-3.5-turbo-0125') selected @endif>
                    gpt-3.5-turbo-0125</option>
                <option value="gpt-3.5-turbo" @if($gptKey->model ==
                    'gpt-3.5-turbo') selected @endif>
                    gpt-3.5-turbo</option>

            </select>
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="key">Key</label>
        <div class="form-control-wrap">
            <input class="form-control" id="key" name="key" type="text" value="{{ old('key',$gptKey->key) }}" />
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="product_prompt">Product Compare Prompt</label>
        <div class="form-control-wrap">
            <textarea class="form-control" rows="15" id="product_prompt"
                name="product_prompt">{{ $gptKey->product_prompt }}</textarea>
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
            new ArgumentsHandler('#product_prompt', '.product-prompt-argument');
        </script>
    </div>
    <div class="form-group mb-4">
        <label class="form-label" for="image_prompt">Image Compare Prompt</label>
        <div class="form-control-wrap">
            <textarea class="form-control" id="image_prompt" rows="10"
                name="image_prompt">{{ $gptKey->image_prompt }}</textarea>
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
        </div>
        <script>
            new ArgumentsHandler('#image_prompt', '.image-prompt-argument');
        </script>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="product_url">Products URL</label>
        <div class="form-control-wrap">
            <input class="form-control" id="product_url" name="product_url" value="{{ $productUrl }}">
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            Save
        </button>
    </div>

</form>

@endsection