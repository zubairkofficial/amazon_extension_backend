@extends('admin.layout')

@section('content')
    @livewireStyles
    
    @livewire('hugging-face', ['formType' => $formType, 'model' => $model ?? null])
    
    @livewireScripts

@endsection
