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
            <td style="white-space: pre-wrap">{{ $log->user->name }}</td>
        </tr>
        <tr>
            <td class="fw-bold">ASIN</td>
            <td style="white-space: pre-wrap">{{ $log->asin }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Prompt</td>
            <td style="white-space: pre-wrap">{!! $log->prompt !!}</td>
        </tr>
        <tr>
            <td class="fw-bold">Product Image</td>
            <td style="white-space: pre-wrap">{!! $log->image_match !!}</td>
        </tr>
        <tr>
            <td class="fw-bold">Summary</td>
            <td style="white-space: pre-wrap">{!! $log->fullsummary ? $log->fullsummary :$log->summary !!}</td>
        </tr>
        <tr>
            <td class="fw-bold">Execution Time</td>
            <td style="white-space: pre-wrap">{{ isset($log->execution_time) ? number_format($log->execution_time, 2, '.', ',') . ' sec' : '' }}</td>
        </tr>
        <tr>
            <td class="fw-bold">CreatedDate</td>
            <td ><p class="text-wrap">{{ \Carbon\Carbon::Parse($log->created_at)->isoFormat("MMM Do YYYY H:m:s") }}</p></td>
        </tr>
    </table>

</div>

@endsection