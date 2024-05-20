@extends('admin.layout')

@section('content')    
    @livewire('settings', ['setting' => $setting,"fastapi_url" => $fastapiUrl,"product_url" => $productUrl,
                            "scrapeArguments"=>$scrapeArguments,"systemArguments"=>$systemArguments,"local_models"=>$local_models])
@endsection
