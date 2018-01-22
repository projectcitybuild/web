@extends('layouts.master')

@section('contents')
    <div class="contents__body">
        @yield('left')
    </div>

    <div class="contents__sidebar">
        @include('components.sidebar')    
    </div>
@endsection