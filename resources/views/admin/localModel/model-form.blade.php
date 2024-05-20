@extends('admin.layout')

@section('content')  
    @livewire('hugging-face', ['formType' => $formType, 'model' => $model ?? null, "scrapeArguments"=>$scrapeArguments,"systemArguments"=>$systemArguments,])
@endsection
