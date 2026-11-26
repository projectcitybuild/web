@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::form-page
        title="Appeal a Ban"
        description="If you have been wrongfully banned, let us know so we can investigate. If you are seeking a second chance, you may plead your case for consideration."
    >
        @if ($errors->any())
            <x-front::form-panel title="We couldn't submit your appeal" variant="danger">
                <p class="mt-2 text-gray-300">{{ $errors->first() }}</p>
            </x-front::form-panel>
        @endif

        <div class="grid gap-6 md:grid-cols-[1fr_auto_1fr]">
            <x-front::form-panel class="flex h-full flex-col">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-form-accent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                </svg>
                <h2 class="mt-4 text-2xl font-bold">Select from list</h2>
                <p class="mt-3 grow leading-7 text-gray-300">
                    Auto-fill some appeal details by selecting a ban. If logged in, you will also see active bans linked to your account.
                </p>
                <a class="form-button mt-8 w-fit" href="{{ route('front.appeal.search') }}">
                    Browse bans
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </x-front::form-panel>

            <div class="self-center text-sm font-bold uppercase tracking-widest text-gray-400">or</div>

            <x-front::form-panel class="flex h-full flex-col">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-form-accent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>
                <h2 class="mt-4 text-2xl font-bold">Fill out the form</h2>
                <p class="mt-3 grow leading-7 text-gray-300">
                    No login is required, but you will need to provide the ban details manually.
                </p>
                <a class="form-button mt-8 w-fit" href="{{ route('front.appeal.form') }}">
                    Write an appeal
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </x-front::form-panel>
        </div>
    </x-front::form-page>
@endsection
