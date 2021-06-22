@extends('front.layouts.master')

@section('title', 'Re-enter Password')
@section('description', '')

@section('contents')
    <div class="contents__body twofa">
        <div class="card card--divided card--narrow card--centered">
            <div class="card__body card__body--padded">
                <h1>Reauthenticate</h1>
                <p>Please re-enter your password to continue</p>
                @include('front.components.form-error')
                <form action="{{ route('password.confirm') }}" method="post">
                    @csrf
                    <div class="form-row">
                        <label for="password">Enter Password</label>
                        <input type="password" class="input-text" id="password" name="password">
                    </div>

                    <button type="submit" class="button button--primary button--fill button--large">Confirm</button>
                </form>
            </div>
        </div>
    </div>
@endsection
