@extends('front.templates.master')

@section('title', 'Re-enter Password')
@section('description', '')

@section('body')
    <main class="page login">
        <section class="login__dialog login__reauth login__dialog--is-narrow">
            <h1>Reauthenticate</h1>

            @include('front.components.form-error')

            <p class="form__description">Please re-enter your password to continue</p>
            <form action="{{ route('front.password.confirm.submit') }}" method="post" class="form">
                @csrf
                <div class="form-row">
                    <label for="password">Enter Password</label>
                    <input type="password" class="textfield" id="password" name="password">
                </div>

                <button type="submit" class="button button--filled">Confirm</button>
            </form>
        </section>
    </main>
@endsection