@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="app">
            <photo-search></photo-search>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/photoSearch.js"></script>

@endsection