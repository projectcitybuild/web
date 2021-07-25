@extends('v2.front.templates.master')

@section('title', 'Account Settings - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('v2.front.pages.account.components.account-sidebar')

        <div class="settings__content">

            <div class="settings__section" id="change-email">
                <h2 class="settings__section-heading">Change Email Address</h2>

                <p class="settings__description">You'll need to confirm this change with both your old and new email addresses.<br>If you no longer have access to your old email, please speak to a member of staff.</p>

                @include('v2.front.pages.account.settings._email')
            </div>
            <div class="settings__section" id="change-username">
                <h2 class="settings__section-heading">Change Username</h2>

                @include('v2.front.pages.account.settings._username')
            </div>
        </div>
    </main>
@endsection
