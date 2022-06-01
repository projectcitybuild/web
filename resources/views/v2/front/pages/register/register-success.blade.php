@extends('v2.front.templates.master')

@section('title', 'Account Verification')
@section('description', "")

@section('body')
    <main class="page login">
        <section class="login__dialog login__confirm login__dialog--is-narrow">
            <h1>Verify your email</h1>
            <p>
                To finish registering, please confirm your email address. If it doesn't arrive in a few minutes, try checking spam.
            </p>
            <p>
                If you need help, please contact a member of PCB staff on Discord or in-game.
            </p>
        </section>
    </main>
@endsection
