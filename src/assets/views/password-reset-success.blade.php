@extends('layouts.master')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Password Reset</h1>
            <p>Your password has successfully been reset. Please proceed to the login screen to complete the process.</p>

            <a class="button button--large button--primary" href="http://forums.projectcitybuild.com/login">
                <i class="fas fa-chevron-right"></i>
                Go to Login Screen
            </a>
        </div>

    </div>

@endsection