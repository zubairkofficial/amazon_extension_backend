@extends('admin.layout')

@section('content')
<h1 class="mb-2">Users</h1>

<div class="text-end">
    <a href="/admin/users/create" class="btn btn-primary">Add User</a>
</div>

@include('admin.partials.message')

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">Actions
                    </button>
                    <form class="dropdown-menu">
                        <a class="dropdown-item" href="/admin/users/{{ $user->id }}">View</a>
                        <a class="dropdown-item" href="/admin/users/{{ $user->id }}/edit">Edit</a>

                        <button class="dropdown-item" formaction="/admin/users/{{ $user->id }}"
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