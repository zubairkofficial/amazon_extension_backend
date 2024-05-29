@extends('admin.layout')

@section('content')
<h1 class="mb-2">{{ $formType === 'add' ? 'Create' : 'Update' }} Model</h1>

<div class="text-end">
    <a href="/admin/openaimodels" class="btn btn-primary">Show All</a>
</div>

<form method="POST" action="{{ $formType === 'update' ? " /admin/openaimodels/{$model->id}" : '/admin/openaimodels' }}">
    @csrf
    @if($formType === 'update')
    @method('PUT')
    @endif
    <div class="form-group mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $model->name ?? '' }}"
            autocomplete="name">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="value" class="form-label">Value</label>
        <input type="text" class="form-control" id="value" name="value"  value="{{ old('value') ?? $model->value ?? '' }}"
            autocomplete="value">
        @error('value')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="text-end">
        <button class="btn btn-primary">{{ $formType === 'add' ? 'Add' : 'Update' }}</button>
    </div>
</form>
@endsection