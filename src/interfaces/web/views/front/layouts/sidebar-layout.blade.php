@extends('front.layouts.master')

@section('contents')
    <div class="contents__body">
        @yield('left')
    </div>

    <div class="contents__sidebar">
        @include('front.components.sidebar')    
    </div>
@endsection