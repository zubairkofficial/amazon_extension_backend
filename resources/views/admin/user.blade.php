@extends('admin.layout')

@section('content')
    <h1 class="mb-2">Users</h1>

    <div class="text-end">
        <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-primary">Edit</a>
        <a href="/admin/users" class="btn btn-primary">Show All</a>
    </div>

    @include('admin.partials.message')

    <table class="table table-hover">
        <tr>
            <td class="fw-bold">ID</td>
            <td>{{ $user->id }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Name</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Created At</td>
            <td>{{ $user->created_at }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Updated At</td>
            <td>{{ $user->updated_at }}</td>
        </tr>
    </table>

@endsection
