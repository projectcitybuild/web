@extends('v2.front.templates.master')

@section('title', 'Your Account - Account Settings - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('v2.front.pages.account.components.account-sidebar')

        <div class="settings__content">

            <div class="settings__section">
                <h2>Change Email Address</h2>

                <p class="settings__description">You'll need to confirm this change with both your old and new email addresses.<br>If you no longer have access to your old email, please speak to a member of staff.</p>

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
                            @foreach($errors->email->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <label for="email">Email</label>
                    <input class="textfield {{ $errors->has('email') ? 'error' : '' }}" name="email" type="email" id="email"
                           placeholder="New Email Address" value="{{ old('email', $user->email) }}"/>

                    <div class="form-row">
                        <button type="submit" class="g-recaptcha button button--large button--fill button--secondary">
                            <i class="fas fa-envelope"></i> Send Verification Mail
                        </button>
                    </div>
                </form>
            </div>
            <div class="settings__section">
                <h2>Change Username</h2>
            </div>
            <div class="settings__section">
                <h2>Change Password</h2>
            </div>
        </div>
    </main>
@endsection
