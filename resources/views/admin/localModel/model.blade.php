@extends('admin.layout')

@section('content')
    <h1 class="mb-2">Local Models</h1>

    <div class="text-end">
        <a href="/admin/localmodels/{{ $model->id }}/edit" class="btn btn-primary">Edit</a>
        <a href="/admin/localmodels" class="btn btn-primary">Show All</a>
    </div>

    @include('admin.partials.message')

    <table class="table table-hover">
        <tr>
            <td class="fw-bold">ID</td>
            <td>{{ $model->id }}</td>
        </tr>
        <tr>
            <td class="fw-bold">baseUrl</td>
            <td>{{ $model->baseUrl }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Name</td>
            <td>{{ $model->name }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Type</td>
            <td>{{ $model->type }}</td>
        </tr>
        @if($model->prompt)
            <tr>
                <td class="fw-bold">Prompt</td>
                <td>{{ $model->prompt }}</td>
            </tr>
        @endif
        @if($model->max_tokens)
            <tr>
                <td class="fw-bold">Max Tokens</td>
                <td>{{ $model->max_tokens }}</td>
            </tr>
        @endif
        @if($model->top_p)
            <tr>
                <td class="fw-bold">Top_p</td>
                <td>{{ $model->top_p }}</td>
            </tr>
        @endif
        @if($model->temp)
            <tr>
                <td class="fw-bold">Temperature</td>
                <td>{{ $model->temp }}</td>
            </tr>
        @endif
        @if($model->seed)
            <tr>
                <td class="fw-bold">Seed</td>
                <td>{{ $model->seed }}</td>
            </tr>
        @endif
        @if($model->mode)
            <tr>
                <td class="fw-bold">Mode</td>
                <td>{{ $model->mode }}</td>
            </tr>
        @endif
        @if($model->instruction_template)
            <tr>
                <td class="fw-bold">Instruction Template</td>
                <td>{{ $model->instruction_template }}</td>
            </tr>
        @endif
        @if($model->character)
            <tr>
                <td class="fw-bold">Character</td>
                <td>{{ $model->character }}</td>
            </tr>
        @endif
        <tr>
            <td class="fw-bold">Created At</td>
            <td>{{ $model->created_at }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Updated At</td>
            <td>{{ $model->updated_at }}</td>
        </tr>
    </table>

@endsection
