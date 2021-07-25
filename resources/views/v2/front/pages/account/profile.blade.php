@extends('v2.front.templates.master')

@section('title', 'Your Account - Project City Build')
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
            <div class="settings__section settings__section--is-hero">
                <h1 class="settings__section-heading">Hi {{ $account->username ?? $account->email }}</h1>
                <div class="settings__groups">
                    @foreach($account->groups as $group)
                        <span class="pill pill--is-rank-{{ $group->name }}">{{ $group->alias ?? Str::title($group->name) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection
