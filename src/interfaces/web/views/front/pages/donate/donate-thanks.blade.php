@extends('front.layouts.master')

@section('title', 'Account Verification')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Thank you for your donation!</h1>
            
            <div class="donate__confirmation">
                <div class="donate__confirmation__left">
                    <i class="fas fa-gift fa-4x"></i>
                </div>
                <div class="donate__confirmation__right">
                    <h3>Payment Details</h3>
                </div>
            </div>

            <a class="button button--secondary" href="{{ route('front.home') }}">
                Back to Home <i class="fas fa-chevron-right"></i>
            </a>
        </div>

    </div>

@endsection