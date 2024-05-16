@extends('admin.layout')

@section('content')
<h1 class="mb-2">Local Models</h1>

<div class="text-end">
    <a href="/admin/localmodels/create" class="btn btn-primary">Add Model</a>
</div>

@include('admin.partials.message')

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>baseUrl</th>
            <th>Name</th>
            <th>Type</th>
            <th>Max Tokens</th>
            <th>Temperature</th>
            <th>Top_p</th>
            <th>Seed</th>
            <th>Mode</th>
            <th>Instruction Template</th>
            <th>Character</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($models as $key => $model)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$model->baseUrl}}</td>
            <td>{{$model->name}}</td>
            <td>{{$model->type}}</td>
            <td>{{$model->max_tokens ?? "N/A"}}</td>
            <td>{{$model->temp ?? "N/A"}}</td>
            <td>{{$model->top_p ?? "N/A"}}</td>
            <td>{{$model->seed ?? "N/A"}}</td>
            <td>{{$model->mode ?? "N/A"}}</td>
            <td>{{$model->instruction_template ?? "N/A"}}</td>
            <td>{{$model->character ?? "N/A"}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    </button>
                    <form class="dropdown-menu">
                        <a class="dropdown-item" href="/admin/localmodels/{{ $model->id }}">View</a>
                        <a class="dropdown-item" href="/admin/localmodels/{{ $model->id }}/edit">Edit</a>

                        <button class="dropdown-item" formaction="/admin/localmodels/{{ $model->id }}"
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