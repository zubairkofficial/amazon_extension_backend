<div>
    <h1 class="mb-2">{{ $formType === 'add' ? 'Create' : 'Update' }} Local Model</h1>

    <div class="text-end">
        <a href="/admin/localmodels" class="btn btn-primary">Show All</a>
    </div>

    <form method="POST" action="{{ $formType === 'update' ? " /admin/localmodels/{$model->id}" : '/admin/localmodels' }}">
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
                <option>Select Type</option>
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
                <input type="number" class="form-control" id="max_tokens" name="max_tokens" wire:model.defer="max_tokens" autocomplete="max_tokens">
                @error('max_tokens')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="temp" class="form-label">Temperature</label>
                <input type="number" class="form-control" id="temp" name="temp" wire:model.defer="temp" autocomplete="temp">
                @error('temp')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="top_p" class="form-label">Top_p</label>
                <input type="number" class="form-control" id="top_p" name="top_p" wire:model.defer="top_p" autocomplete="top_p">
                @error('top_p')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="seed" class="form-label">Seed</label>
                <input type="number" class="form-control" id="seed" name="seed" wire:model.defer="seed" autocomplete="seed">
                @error('seed')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        @if($type === 'chat-completions')
            <div class="form-group mb-3">
                <label for="mode" class="form-label">Mode</label>
                <input type="text" class="form-control" id="mode" name="mode" wire:model.defer="mode" autocomplete="mode">
                @error('mode')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="instruction_template" class="form-label">Instruction Template</label>
                <input type="text" class="form-control" id="instruction_template" name="instruction_template" wire:model.defer="instruction_template" autocomplete="instruction_template">
                @error('instruction_template')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        @if($type === 'chat-completions-with-characters')
            <div class="form-group mb-3">
                <label for="mode" class="form-label">Mode</label>
                <input type="text" class="form-control" id="mode" name="mode" wire:model.defer="mode" autocomplete="mode">
                @error('mode')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="character" class="form-label">Character</label>
                <input type="text" class="form-control" id="character" name="character" wire:model.defer="character" autocomplete="character">
                @error('character')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div class="text-end">
            <button class="btn btn-primary">{{ $formType === 'add' ? 'Add' : 'Update' }}</button>
        </div>
    </form>
</div>