@extends('v2.front.templates.master')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@section('body')
    <main class="page login">
        <div class="login__dialog login__dialog--is-narrow">
            <h1>Password Updated</h1>
            <p>Your password has successfully been reset. Please proceed to the login screen to complete the
                process.</p>

            <a class="button button--filled button--block" href="{{ route('front.login') }}">
                <i class="fas fa-chevron-right"></i>
                Go to Login Screen
            </a>
        </div>
    </main>
@endsection
