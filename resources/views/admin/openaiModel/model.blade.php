@extends('admin.layout')

@section('content')
    <h1 class="mb-2">Local Models</h1>

    <div class="text-end">
        <a href="/admin/openaimodels/{{ $model->id }}/edit" class="btn btn-primary">Edit</a>
        <a href="/admin/openaimodels" class="btn btn-primary">Show All</a>
    </div>

    @include('admin.partials.message')

    <table class="table table-hover">
        <tr>
            <td class="fw-bold">Name</td>
            <td>{{ $model->name }}</td>
        </tr>
        <tr>
            <td class="fw-bold">value</td>
            <td>{{ $model->value }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Temperature</td>
            <td>{{ $model->temp }}</td>
        </tr>
    </table>

@endsection
