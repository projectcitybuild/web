@extends('front.templates.master')

@section('title', 'Change Email - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        <div class="settings__content">
            <div class="settings__section status-success">
                <i class="fas fa-check-circle fa-2x"></i>
                <h2 class="settings__section-heading">Email Change Complete</h2>

                <a href="{{ route('front.account.settings') }}" class="button button--filled">Back to Account</a>
            </div>
        </div>
    </main>

    @include('front.components.footer')
@endsection
