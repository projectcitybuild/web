@extends('v2.front.templates.master')

@section('title', 'Re-enter Password')
@section('description', '')

@section('body')
    <main class="page login">
            <section class="login__dialog login__reauth">
                <h1>Reauthenticate</h1>
                @if($errors->any())
                    <div class="alert alert--error">
                        <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                <p class="form__description">Please re-enter your password to continue</p>
                <form action="{{ route('password.confirm') }}" method="post" class="form">
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
