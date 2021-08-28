@extends('v2.front.templates.master')

@section('title', 'Sign In - Project City Build')
@section('meta_title', 'Sign In - Project City Build')
@section('meta_description')
    Login to your Project City Build account to create forum posts, access personal player statistics and more @endsection

@section('body')
    <main class="page login">
        <div class="container">
            <section class="login__dialog login__sign-in">
                <h1>Sign In to PCB</h1>

                <form method="post" action="{{ route('front.login.submit') }}">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert--error">
                            <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @if(Session::get('mfa_removed', false))
                        <div class="alert alert--success">
                            <h2><i class="fas fa-check"></i> 2FA Reset</h2>
                            <p>2FA has been removed from your account, please sign in again.</p>
                        </div>
                    @endif

                    <label for="email">Email</label>
                    <input
                        class="textfield {{ $errors->any() ? 'error' : '' }}"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                    />

                    <label for="password">Password</label>
                    <input
                        class="textfield {{ $errors->any() ? 'error' : '' }}"
                        id="password"
                        name="password"
                        type="password"
                    />

                    <div class="options">
                        <label for="remember_me">
                            <input type="checkbox" name="remember_me" id="remember_me" checked> Stay logged in
                        </label>

                        <a href="{{ route('front.password-reset.create') }}">Forgot your password?</a>
                    </div>

                    <button class="button button--filled button--display button--block" type="submit">Login</button>
                </form>
            </section>

            <section class="login__register">
                <h1>Register</h1>

                <div class="register-appeal">
                    Members gain access to personal player statistics, the forums, in-game rank synchronization and more.
                </div>

                <a href="{{ route('front.register') }}" class="button button--outlined button--display button--block">Create an Account</a>
            </section>
        </div>
    </main>
@endsection
