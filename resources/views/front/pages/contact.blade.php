@extends('front.layouts.root-layout')

@section('title', 'Contact Us')
@section('description', 'General inquiries and questions')

@section('body')
    <x-navbar />

    <div class="max-w-screen-xl m-auto px-3 md:px-9">
        <div class="my-12">
            <h1 class="text-4xl font-bold">Contact Us</h1>
            <div class="text-gray-500 mt-3">
                For general inquiries to the administration team. Where appropriate, replies will be sent via email to the given email address.
            </div>
        </div>

        <div class="rounded-lg max-w-screen-xl m-auto bg-gray-50 p-6 mb-6">
            @if(session()->has('success'))
                <x-success-alert class="mb-6">{{ session()->get('success') }}</x-success-alert>
            @endif

            <form
                method="post"
                action="{{ route('front.contact.submit') }}"
                class="flex flex-col"
            >
                @csrf

                @error('error')
                    <x-validation-error>{!! $message !!}</x-validation-error>
                @enderror

                <label for="name" class="text-md font-bold">
                    Name
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('name') border-red-500 @enderror
                    "
                    name="name"
                    id="name"
                    type="text"
                    value="{{ old('name') }}"
                />
                <div class="text-xs text-gray-500 mt-2">
                    This can be a Minecraft username, Discord name or real name
                </div>
                @error('name')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="current_builder_rank" class="text-md font-bold mt-6">
                    Email address<span class="text-red-500">*</span>
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('email') border-red-500 @enderror
                    "
                    name="email"
                    id="email"
                    type="email"
                    value="{{ old('email') }}"
                />
                <div class="text-xs text-gray-500 mt-2">
                    Please ensure this is correct or we may not be able to contact you
                </div>
                @error('email')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="current_builder_rank" class="text-md font-bold mt-6">
                    Subject<span class="text-red-500">*</span>
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('subject') border-red-500 @enderror
                    "
                    name="subject"
                    id="subject"
                    type="text"
                    value="{{ old('subject') }}"
                />
                @error('subject')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="build_description" class="text-md font-bold mt-6">
                    Inquiry<span class="text-red-500">*</span>
                </label>
                <div class="text-gray-500 text-sm text-light mt-2">
                    (max 2000 characters)
                </div>
                <textarea
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('inquiry') border-red-500 @enderror
                    "
                    name="inquiry"
                    id="inquiry"
                    rows="12"
                >{{ old('inquiry') }}</textarea>
                @error('inquiry')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <x-captcha class="mt-6"></x-captcha>
                @error('captcha-response')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <x-front::button type="submit" class="mt-12">
                    Send
                </x-front::button>
            </form>
        </div>
    </div>
@endsection
