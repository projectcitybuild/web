@extends('layouts.master')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="card">
        <div class="card__body">
            <h1>Create an Account</h1>

            <form method="post" action="{{ route('front.register.submit') }}">
                @csrf
                
                @if($errors->any())
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        {{ $errors->first() }}
                    </div>
                    <p>
                @endif

                <div class="form-row">
                    <label>Email Address</label>
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="email" type="email" placeholder="Email Address" value="{{ old('email') }}" />
                </div>
                <div class="form-row">
                    <label>Password</label>
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password" type="password" placeholder="Password" />
                </div>
                <div class="form-row">
                    <label>Password</label>
                    <input class="input-text {{ $errors->any() ? 'input-text--error' : '' }}" name="password_confirm" type="password" placeholder="Password (Confirm)" />
                </div>

                <div>
                    <button class="button button--large button--fill button--primary" type="submit">
                        <i class="fas fa-chevron-right"></i> Proceed
                    </button>
                </div>

                <hr />
                
                <div>
                    <a class="login__button login__button--facebook" href="#">
                        <i class="fab fa-facebook-square"></i> Facebook
                    </a>
                    <a class="login__button login__button--twitter" href="#">
                        <i class="fab fa-twitter-square"></i> Twitter
                    </a>
                    <a class="login__button login__button--google" href="#">
                        <i class="fab fa-google"></i> Google
                    </a>
                </div>
            </form>
        </div>

    </div>

@endsection