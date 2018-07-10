@extends('front.layouts.master')

@section('title', 'Account Security Settings')
@section('description', '')

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
                    @if($errors->email->any())
                        <div class="alert alert--error">
                            <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                            {{ $errors->email->first() }}
                        </div>
                        <p>
                    @endif

                    <div class="form-row">
                        <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="New Email Address" value="{{ old('email') }}" />
                    </div>

                    <div class="form-row">
                        <button type="submit" class="g-recaptcha button button--large button--fill button--secondary">
                            <i class="fas fa-envelope"></i> Send Verification Mail
                        </button>
                    </div>
                </form>
            </div>

            <div class="card__body card__body--padded">
                <h3>Change Password</h3>

                <form method="post" action="{{ route('front.account.settings.password') }}" id="form">
                    @csrf
                    
                    @if(Session::has('success_password'))
                        <div class="alert alert--success">
                            <h3><i class="fas fa-exclamation-circle"></i> Success</h3>
                            {{ Session::get('success_password') }}
                        </div>
                        <p>
                    @endif
                    @if($errors->password->any())
                        <div class="alert alert--error">
                            <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                            {{ $errors->password->first() }}
                        </div>
                        <p>
                    @endif

                    <div class="form-row">
                        <input class="input-text {{ $errors->has('old_password') ? 'input-text--error' : '' }}" name="old_password" type="password" placeholder="Current Password" />
                    </div>
                    <div class="form-row">
                        <input class="input-text {{$errors->has('new_password') ? 'input-text--error' : '' }}" name="new_password" type="password" placeholder="New Password" />
                    </div>
                    <div class="form-row">
                        <input class="input-text {{ $errors->has('new_password_confirm') ? 'input-text--error' : '' }}" name="new_password_confirm" type="password" placeholder="New Password (Confirm)" />
                    </div>

                    <div class="form-row">
                        <button type="submit" class="g-recaptcha button button--large button--fill button--secondary">
                            <i class="fas fa-check"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

    <div class="contents__sidebar">
        <div class="card card--no-padding">
            <div class="card__body">

                <ul class="sidemenu">
                    <li>
                        <a href="#">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-square fa-stack-2x"></i>
                                <i class="fas fa-lock fa-stack-1x fa-inverse"></i>
                            </span>
                            Account Security
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-square fa-stack-2x"></i>
                                <i class="fab fa-facebook fa-stack-1x fa-inverse"></i>
                            </span>
                            Connected Social Accounts
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </div>


@endsection