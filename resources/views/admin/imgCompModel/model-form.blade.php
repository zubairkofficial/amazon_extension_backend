@extends('admin.layout')

@section('content')
    @livewire('image-compare', ['formType' => $formType, 'model' => $model ?? null,
     "scrapeArguments"=>$scrapeArguments,"systemArguments"=>$systemArguments,"models"=>$models])
@endsection