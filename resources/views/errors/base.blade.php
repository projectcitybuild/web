@extends('front.layouts.root-layout')

<div
    class="
        bg-gradient-to-br from-[#684ca0] to-[#1c4ca0]
        text-white min-h-screen flex items-center
    "
>
    <div class="container mx-auto p-4 flex flex-wrap items-center">
        <div class="w-full md:w-5/12 text-center p-4">
            <div class="text-[160px] font-display">@yield('code')</div>
        </div>
        <div class="w-full md:w-7/12 text-center md:text-left p-4">
            <div class="text-xl md:text-4xl font-medium mb-4">
                @yield('title')
            </div>
            <div class="text-lg text-gray-300 mb-9">
                @yield('description')
            </div>

            <x-button href="{{ route('front.home') }}" class="inline-flex">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Go Home
            </x-button>
        </div>
    </div>
</div>
