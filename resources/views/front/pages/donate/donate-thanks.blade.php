@extends('front.layouts.root-layout')

@section('title', 'Thank you!')
@section('meta_description', 'Thank you for supporting Project City Build and helping keep our Minecraft community running.')

@section('body')
    <x-front::navbar />

    <section class="bg-white py-20 text-gray-900">
        <div class="max-w-screen-2xl mx-auto px-6">
            <h1 class="text-5xl tracking-tight font-bold">Thank You!</h1>

            <p class="mt-3 text-lg">
                Your payment is being processed by Stripe.
            </p>
            <p class="mt-1 text-gray-500 text-sm">
                Please note that this can take time depending on payment method and provider - this is not under our control.
            </p>
            <p class="mt-6">
                On payment completion, you will be notified by email and your donation perks will automatically be assigned.
            </p>

            <div class="flex flex-row gap-2 mt-12">
                <x-button href="{{ route('front.home') }}">
                    Go Home
                </x-button>

                <x-button variant="outlined" href="{{ route('front.account.donations') }}" class="text-gray-900 border-gray-900">
                    See your donations
                </x-button>
            </div>
        </div>
    </section>
@endsection
