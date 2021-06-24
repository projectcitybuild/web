@extends('v2.front.templates.master')

@section('title', 'Sign In - Project City Build')
@section('meta_title', 'Sign In - Project City Build')
@section('meta_description')
    Login to your Project City Build account to create forum posts, access personal player statistics and more @endsection

@section('body')
    <main class="page login">
        <div class="container">
            <section class="login__sign-in">
                <h1>Sign In to PCB</h1>

                <form method="post" action="{{ route('front.login.submit') }}">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert--error">
                            <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @if(Session::get('mfa_removed', false))
                        <div class="alert alert--success">
                            <h3><i class="fas fa-check"></i> 2FA Reset</h3>
                            <p>2FA has been removed from your account, please sign in again.</p>
                        </div>
                    @endif

                    <label for="email">
                        <span>Email</span>
                        <input
                            class="textfield {{ $errors->any() ? 'error' : '' }}"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                        />
                    </label>
                    <label for="password">
                        <span>Password</span>
                        <input
                            class="textfield {{ $errors->any() ? 'error' : '' }}"
                            name="password"
                            type="password"
                        />
                    </label>

                    <div class="options">
                        <label for="remember_me">
                            <input type="checkbox" name="remember_me" id="remember_me" checked> Stay logged in
                        </label>

                        <a href="{{ route('front.password-reset.create') }}">Forgot your password?</a>
                    </div>

                    <button class="button" type="submit">Login</button>
                </form>
            </section>

            <section class="login__register">
                <h1>Register</h1>

                <div class="register-appeal">
                    Members gain access to personal player statistics, the forums, in-game rank synchronization and more.
                </div>

                <a href="#" class="button">Create an Account</a>
            </section>
        </div>
    </main>
@endsection
