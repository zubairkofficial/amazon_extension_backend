@extends('admin.layout')

@section('content')
    @livewire('openai-component', ['formType' => $formType, 'model' => $model ?? null,
     "scrapeArguments"=>$scrapeArguments,"systemArguments"=>$systemArguments,"models"=>$models])
@endsection