@extends('layouts.master')

@section('title', 'Login')
@section('description', 'Login to your Project City Build account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="login">
        <div class="login__left">
            <h1>Sign In to PCB</h1>

            <form method="post" action="">
                <div class="form-row">
                    <input class="input-text" name="email" type="email" placeholder="Email Address" />
                </div>
                <div class="form-row">
                    <input class="input-text" name="password" type="password" placeholder="Password" />
                </div>
                <div class="form-row">
                    <button class="button button--large button--fill button--primary" type="submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
                <div class="form-row">
                    <div class="login__options">
                        <div class="login__remember">
                            <input name="remember" type="checkbox" id="inputRemember" checked />
                            <label for="inputRemember">Remember Me</label>
                        </div>
                        <div class="login__forgot">
                            <a href="#">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="login__right">
            <h1>Sign Up</h1>

            <div class="login__description">
                Members gain access to personal player statistics, the ability to post in the forums and more.
            </div>

            <div class="login__social">
                <a class="login__button login__button--facebook" href="#">
                    <i class="fab fa-facebook-square"></i> Sign in with Facebook
                </a>
                <a class="login__button login__button--twitter" href="#">
                    <i class="fab fa-twitter-square"></i> Sign in with Twitter
                </a>
                <a class="login__button login__button--google" href="#">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
            </div>

            <div class="login__divider">or</div>
            
            <a class="button button--fill button--large button--secondary" href="#">
                Create an Account
            </a>
            
        </div>
    </div>

@endsection