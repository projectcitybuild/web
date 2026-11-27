@extends('front.layouts.root-layout')

@section('title', 'Player Ban List')
@section('meta_description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <x-front::form-page
        background="resources/images/bg_courthouse.webp"
        eyebrow="COMMUNITY SAFETY"
        title="Banned Players"
    >
        <x-slot:intro>
            If you believe you have been banned unfairly, please <a href="{{ route('front.appeal') }}" class="font-semibold text-form-accent-soft underline underline-offset-4 hover:text-white">submit an appeal</a>.
            Players looking for a second chance may also plead their case.
        </x-slot:intro>

        <x-front::form-panel title="Active bans">
            <div
                id="ban-list"
                class="mt-5"
                data-endpoint="{{ route('front.banlist') }}"
                data-initial-query="{{ $query }}"
                data-initial-results="{{ $bans->toJson() }}"
            ></div>
        </x-front::form-panel>
    </x-front::form-page>
@endsection

@push('end')
    @vite('resources/js/front/bans.ts')
@endpush
