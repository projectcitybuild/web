@extends('front.templates.master')

@section('title', 'Live Maps - Project City Build')

@push('head')
    <style>
        #app {
            display: flex;
            width: 100%;
            height: 100%;
            flex-direction: column;
            background: black;
        }
    </style>
@endpush

@section('body')
    <iframe
        src="https://maps.pcbmc.co"
        style="flex-grow: 1">
    </iframe>
@endsection
