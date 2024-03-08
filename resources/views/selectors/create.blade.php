@extends('admin.layout')

@section('content')
<form class="rounded shadow-sm p-4" action="{{ route('selectors.store') }}" method="POST">

    <h2 class="text-lg font-medium text-gray-900 mb-4">
        Selectors
    </h2>

    @include('admin.partials.message')

    @csrf

    <div class="form-group mb-4">
        <label class="form-label" for="name">Name</label>
        <div class="form-control-wrap">
            <input class="form-control" id="name" name="name" type="text" />
            <small class="text-danger"></small>
        </div>
    </div>

    <div class="form-group mb-4">
        <label class="form-label" for="selector">Selector</label>
        <div class="form-control-wrap">
            <textarea class="form-control" rows="5" id="selector" name="selector"></textarea>
            <small class="text-danger"></small>
        </div>
    </div>
    <div class="form-group mb-4">
        <label class="form-label" for="type">Type</label>
        <div class="form-control-wrap">
            <select class="form-control" id="type" name="type">
                <option value="id">
                    id
                </option>
                <option value="className">
                    className
                </option>
                <option value="tag">
                    tag
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