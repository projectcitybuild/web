@extends('front.layouts.master')

@section('title', 'Create Account')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Create an Account</h1>

            <p>
                There is currently no PCB account registered to this social account. 
                Would you like to create a PCB account with these details?
            </p>
            <p>
                If you wish to associate this social account with an existing PCB account, please log-in to 
                PCB first then log-in to your social account from the settings page.
            </p>

            <div class="register__social-details">

                <h2>{{ $social['name'] }}</h2>
                {{ $social['email'] }}<br><br>
                
                <a href="{{ $url }}" class="button button--large button--fill button--primary">
                    <i class="fas fa-check"></i> Create & Proceed
                </a>
            </div>

        </div>

    </div>

@endsection