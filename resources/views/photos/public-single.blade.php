@extends('layouts.app')

@section('content')
    <div id="app">
        <div class="row">
            <div class="col-12">
                <div id="img-title">
                    <h1 id="img-title-text" class="text-center">{{$name}}</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-9 col-lg-10">
                <div class="img-area " style="text-align: center">
                    <img src="{{$src}}" alt="{{$name}}" class="responsive-image">
                </div>
                <h2>Description</h2>
                {{$description}}
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <div id="keyword-div">
                    <h2>Keywords</h2>
                    <div id="keyword-div">
                        @foreach($keywords as $keyword)
                            <p><button class="btn btn-sm btn-light">{{$keyword->name}}</button></p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection