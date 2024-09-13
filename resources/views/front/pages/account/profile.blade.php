@extends('front.root-layout')

@section('title', 'Your Account')
@section('description', '')

@section('body')
    @include('front.pages.account.navbar')

    <main
        class="
            flex flex-col max-w-screen-xl
            md:flex-row md:mx-auto md:mt-8
        "
    >
        <div class="rounded-lg bg-white flex-grow p-6 m-2">
            @if(session()->has('success'))
                <x-success-alert>{{ session()->get('success') }}</x-success-alert>
            @endif

            <h1 class="text-3xl mb-3">
                Welcome back <span class="font-bold">{{ $account->username ?? "" }}</span>
            </h1>

            <div class="settings__groups">
                @foreach($account->groups as $group)
                    <span class="rounded-md bg-gray-200 py-1 px-2 text-sm">{{ $group->alias ?? Str::title($group->name) }}</span>
                @endforeach
            </div>
        </div>
    </main>
@endsection
