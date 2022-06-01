@extends('v2.front.templates.master')

@section('title', 'Registration Verification')
@section('description', "")

@section('body')
    <main class="page login">
        <section class="login__dialog login__confirm login__dialog--is-narrow">
            <h1>Registration Complete</h1>
            <p>Thanks for confirming your email. Your account is now active.</p>

            <a href="{{ route('front.login') }}" class="button button--filled">Go to login</a>
        </section>
    </main>
@endsection
