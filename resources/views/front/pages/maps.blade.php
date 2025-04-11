@extends('front.layouts.root-layout')

@section('title', 'Live Maps')

@push('head')
    <style>
        html, body {
            height: 100%;
            min-height: 100%;
        }
        #app {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

@section('body')
    <div class="flex flex-col bg-black size-full">
        <x-front::navbar />

        <iframe
            class="size-full flex-grow"
            src="https://maps.pcbmc.co"
            style="flex-grow: 1; border: 0">
            Your browser does not support or has disabled iFrames
        </iframe>
    </div>
@endsection
