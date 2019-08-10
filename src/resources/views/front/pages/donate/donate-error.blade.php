@extends('front.layouts.master')

@section('title', 'Donation Error')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>There was a problem processing your donation</h1>

            <p>{{ $message }}</p>

            <a class="button button--secondary" href="{{ route('front.donate') }}">
                <i class="fas fa-chevron-left"></i> Try again
            </a>
        </div>

    </div>

@endsection
