@extends('front.layouts.master')

@section('title', 'Error')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>An Error Has Occurred</h1>

            <p>{{$message}}</p>
        </div>

    </div>

@endsection
