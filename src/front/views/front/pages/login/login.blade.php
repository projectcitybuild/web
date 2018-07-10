@extends('front.layouts.master')

@section('title', 'Login')
@section('description', 'Login to your Project City Build account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="login">
        <div class="login__left">
            <h1>Sign In to PCB</h1>

            <small>
                <i class="fas fa-exclamation-circle"></i> 
                Notice: Anyone with a forum account before we moved to Discourse <a href="{{ route('front.password-reset') }}">must reset their password first</a>!
                <p />
                See <a href="https://forums.projectcitybuild.com/t/welcome-to-the-new-forums/32708/1" target="_blank">this post</a> for more details.
            </small>
            <br>

            <form method="post" action="{{ route('front.login.submit') }}">
                @csrf
                
                @if($errors->any())
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        {{ $errors->first() }}
                    </div>
                    <p>
                @endif

                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="Email Address" value="{{ old('email') }}" />
                </div>
                <div class="form-row">
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Password" />
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
                            <a href="{{ route('front.password-reset') }}">Forgot your password?</a>
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
            
            <a class="button button--fill button--large button--secondary" href="{{ route('front.register') }}">
                Create an Account
            </a>
            
            <div class="login__divider">or</div>

            <div class="login__social">
                <a class="login__button login__button--facebook" href="{{ route('front.login.facebook') }}">
                    <i class="fab fa-facebook-square"></i> Sign in with Facebook
                </a>
                <a class="login__button login__button--twitter" href="{{ route('front.login.twitter') }}">
                    <i class="fab fa-twitter-square"></i> Sign in with Twitter
                </a>
                <a class="login__button login__button--google" href="{{ route('front.login.google') }}">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
            </div>

        </div>
    </div>

@endsection