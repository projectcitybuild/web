@extends('front.layouts.root-layout')

@section('title', 'Live Maps')

@push('head')
    <style>
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
            style="flex-grow: 1">
        </iframe>
    </div>
@endsection
