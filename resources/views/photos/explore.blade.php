@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="app">
            <photo-explore></photo-explore>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/photoExplore.js"></script>
@endsection