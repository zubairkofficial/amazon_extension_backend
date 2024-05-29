@extends('admin.layout')

@section('content')
<h1 class="mb-2">Local Models</h1>

<div class="text-end">
    <a href="/admin/openaimodels/create" class="btn btn-primary">Add Model</a>
</div>

@include('admin.partials.message')

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Value</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($models as $key => $model)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$model->name}}</td>
            <td>{{$model->value}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    </button>
                    <form class="dropdown-menu">
                        <a class="dropdown-item" href="/admin/openaimodels/{{ $model->id }}">View</a>
                        <a class="dropdown-item" href="/admin/openaimodels/{{ $model->id }}/edit">Edit</a>

                        <button class="dropdown-item" formaction="/admin/openaimodels/{{ $model->id }}"
                            formmethod="POST">Delete</button>
                        @method('delete')
                        @csrf
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection