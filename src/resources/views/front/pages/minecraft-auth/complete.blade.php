@extends('front.layouts.master')

@section('title', 'Account Authenticated')
@section('description', "")

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Account Linked</h1>

            <p>
                Your Minecraft account has been successfully linked to your PCB account. 
                Please run the <strong>/sync finish</strong> command in-game to finish the process
            </p>
        </div>

    </div>

@endsection
