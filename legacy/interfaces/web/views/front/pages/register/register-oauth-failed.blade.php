@extends('front.layouts.master')

@section('title', 'Registration Failed')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Registration Failed</h1>
            <p>
                A different account is already using this email address ({{ $email }}).<br />
                PCB requires registration with a unique email address, even when signing-up via a social media account.
            </p>

            <a class="button button--large button--primary" href="https://forums.projectcitybuild.com/login">
                <i class="fas fa-chevron-right"></i>
                Go back to Login Screen
            </a>
        </div>

    </div>

@endsection