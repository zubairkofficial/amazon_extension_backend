@extends('admin.layout')

@section('content')
<div class="container">
    <h1 class="my-2">Logs</h1>

    <div class="text-end">
        <a href="{{route('admin.dashboard')}}" class="btn btn-primary">Show All</a>
    </div>

    <table class="table table-hover">
        <tr>
            <td class="fw-bold">User</td>
            <td>{{ $log->user->name }}</td>
        </tr>
        <tr>
            <td class="fw-bold">ASIN</td>
            <td>{{ $log->asin }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Prompt</td>
            <td>{!! $log->prompt !!}</td>
        </tr>
        <tr>
            <td class="fw-bold">Summary</td>
            <td>{!!$log->summary !!}</td>
        </tr>
        <tr>
            <td class="fw-bold">Created At</td>
            <td>{{ $log->created_at }}</td>
        </tr>
    </table>

</div>

@endsection