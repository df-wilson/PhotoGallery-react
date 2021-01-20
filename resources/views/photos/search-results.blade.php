@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="app">
            <photo-home keywordid="{{$keywordId}}" text="{{$text}}" publicphotos="{{$publicPhotos}}" privatephotos="{{$privatePhotos}}" fromdate="{{$fromDate}}" todate="{{$toDate}}"></photo-home>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="/js/photoHome.js"></script>
@endsection