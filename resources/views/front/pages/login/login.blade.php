@extends('front.layouts.master')

@section('title', 'Login')
@section('description', 'Login to your Project City Build account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="login">
        <div class="login__left">
            <h1>Sign In to PCB</h1>

            <br>

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

                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="Email Address" value="{{ old('email') }}" />
                </div>
                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Password" />
                </div>
                <div class="form-row">
                    <label for="remember_me"><input type="checkbox" name="remember_me" id="remember_me"> Stay logged in</label>
                </div>
                <div class="form-row">
                    <button class="button button--large button--fill button--primary" type="submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
                <div class="form-row">
                    <div class="login__options">
                        <div class="login__remember"></div>
                        <div class="login__forgot">
                            <a href="{{ route('front.password-reset.create') }}">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="login__right">
            <h1>Sign Up</h1>

            <div class="login__description">
                Members gain access to personal player statistics, the forums, in-game rank synchronization and more.
            </div>

            <a class="button button--fill button--large button--accent" href="{{ route('front.register') }}">
                Create an Account
            </a>

            <!-- <div class="login__divider">or</div>

            <div class="login__social">
                <a class="login__button login__button--facebook" href="">
                    <i class="fab fa-facebook-square"></i> Sign in with Facebook
                </a>
                <a class="login__button login__button--twitter" href="">
                    <i class="fab fa-twitter-square"></i> Sign in with Twitter
                </a>
                <a class="login__button login__button--google" href="">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
                <a class="login__button login__button--discord" href="">
                    <i class="fab fa-discord"></i> Sign in with Discord
                </a>
            </div> -->

        </div>
    </div>

@endsection
