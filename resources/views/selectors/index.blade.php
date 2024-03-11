@extends('admin.layout')

@section('content')
<h1 class="mb-2">selectors</h1>

<div class="text-end">
    {{-- <a href="/admin/selectors/create" class="btn btn-primary">Add selector</a> --}}
</div>

@include('admin.partials.message')

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Selector</th>
            <th>Type</th>
            <th>Status</th>
            </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($selectors) > 0)
        @foreach ($selectors as $selector)
        <tr>
            <td>{{$selector->id}}</td>
            <td>{{$selector->name}}</td>
            <td>{{$selector->selector}}</td>
            <td>{{$selector->type}}</td>
            <td>{{$selector->status}}</td>
            <td>
                {{-- <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    </button>
                    <form class="dropdown-menu"> --}}
                        <a class=" btn btn-light" href="/admin/selectors/{{ $selector->id }}/edit">Edit</a>
                        {{-- <a class="dropdown-item" href="/admin/selectors/destroy/{{ $selector->id }}">Delete</a>
                    </form>
                </div> --}}
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5">No records found...</td>
        </tr>
        @endif
    </tbody>
</table>
@endsection