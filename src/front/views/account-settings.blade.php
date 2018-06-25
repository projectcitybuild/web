@extends('layouts.master')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="contents__body">

        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Account Security</h1>
                <span class="header-description">Email, password and login related settings</span>
            </div>
            <div class="card__body card__body--padded">
                <h3>Change Email Address</h3>

                <form method="post" action="{{ route('front.account.settings.email') }}" id="form">
                    @csrf
                    
                    @if(Session::has('success'))
                        <div class="alert alert--success">
                            <h3><i class="fas fa-exclamation-circle"></i> Success</h3>
                            {{ Session::get('success') }}
                        </div>
                        <p>
                    @endif
                    @if($errors->any())
                        <div class="alert alert--error">
                            <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                            {{ $errors->first() }}
                        </div>
                        <p>
                    @endif

                    <div class="form-row">
                        <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="New Email Address" value="{{ old('email') }}" />
                    </div>
                    <div class="form-row">
                        <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Current Password" />
                    </div>

                    <div class="form-row">
                        <button type="submit" class="g-recaptcha button button--large button--fill button--secondary">
                            <i class="fas fa-envelope"></i> Send Verification Mail
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <div class="contents__sidebar">
        <div class="card">
            <div class="card__body">

                <ul>
                    <li>
                        <i class="fas fa-lock"></i> Account Security
                    </li>
                    <li>
                        <i class="fab fa-facebook"></i> Connected Social Accounts
                    </li>
                </ul>

            </div>
        </div>
    </div>


@endsection