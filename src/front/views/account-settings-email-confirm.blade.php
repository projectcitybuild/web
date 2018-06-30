@extends('layouts.master')

@section('title', 'Change Email')
@section('description', '')

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Confirm Email Change</h1>
            <span class="header-description">
                You have requested to change your email address.<br />
                Please click the link sent to both email addresses to complete the process.
            </span>

            <p />

            <div class="form-row">
                <i class="fas {{ $changeRequest->is_previous_confirmed ? 'fa-check' : 'fa-hourglass-half' }}"></i> 
                Current email address: {{ $changeRequest->email_previous }}
            </div>

            <div class="form-row">
                <i class="fas {{ $changeRequest->is_new_confirmed ? 'fa-check' : 'fa-hourglass-half' }}"></i> 
                New email address: {{ $changeRequest->email_new }}
            </div>

        </div>
    </div>


@endsection