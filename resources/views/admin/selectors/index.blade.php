@extends('admin.layout')

@section('content')
<h1 class="mb-2">Selectors</h1>

<div class="text-end">
    {{-- <a href="/admin/selectors/create" class="btn btn-primary">Add Selector</a> --}}
</div>

@include('admin.partials.message')

@livewire('selector-status-toggle')
@endsection
