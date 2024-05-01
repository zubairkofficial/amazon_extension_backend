@extends('admin.layout')

@section('content')
<form class="rounded shadow-sm p-4" action="{{ " /admin/selectors/update/{$selector->id}" }}" method="POST">

    <h2 class="text-lg font-medium text-gray-900 mb-4">
        Settings
    </h2>

    @include('admin.partials.message')

    @csrf

    <div class="form-group mb-4">
        <label class="form-label" for="name">Name</label>
        <div class="form-control-wrap">
            <input class="form-control" id="name" name="name" type="text" value="{{ old('name',$selector->name) }}"
                readonly />
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="selector">Selector</label>
        <div class="form-control-wrap">
            <textarea class="form-control" rows="5" id="selector" name="selector">{{ $selector->selector }}</textarea>
            <small class="text-danger"></small>
        </div>
    </div>
    <div class="form-group mb-4">
        <label class="form-label" for="type">Type</label>
        <div class="form-control-wrap">
            <select class="form-control" id="type" name="type">
                <option value="id" @if($selector->
                    type == 'id') selected @endif>
                    id
                </option>
                <option value="className" @if($selector->
                    type == 'className') selected @endif>
                    className
                </option>
                <option value="tag" @if($selector->
                    type == 'tag') selected @endif>
                    tag
                </option>
            </select>
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="status">Status</label>
        <div class="form-control-wrap">
            <select class="form-control" id="status" name="status">
                <option value="enable" @if($selector->
                    status == 'enable') selected @endif>
                    enable
                </option>
                <option value="disable" @if($selector->
                    status == 'disable') selected @endif>
                    disable
                </option>
            </select>
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
