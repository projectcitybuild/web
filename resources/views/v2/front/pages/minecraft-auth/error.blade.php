@extends('v2.front.templates.master')

@section('title', 'Error')
@section('description', "")

@section('body')
    <div class="card">
        <div class="card__body card__body--padded">
            <h1>An Error Has Occurred</h1>

            <p>{{$message}}</p>
        </div>

    </div>
@endsection
