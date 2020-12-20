@extends('front.layouts.master')

@section('title', 'Registration Complete')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Registration Complete</h1>
            <p>
                Thank you for registering. Your account is now active.
            </p>

            <a class="button button--large button--primary" href="https://forums.projectcitybuild.com/login">
                <i class="fas fa-chevron-right"></i>
                Go to Login Screen
            </a>
        </div>

    </div>

@endsection