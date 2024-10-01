@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::navbar />

    <main class="bg-white py-20">
        <header class="max-w-screen-2xl mx-auto px-6">
            @if($errors->any())
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="-mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto mb-[60px] max-w-[650px] text-center">
                        <h2 class="font-bold text-5xl">
                            Submit Appeal
                        </h2>
                        <p class="text-base text-gray-500 mt-4 leading-relaxed">
                            Please fill out all fields honestly and with appropriate detail. Appeals with invalid, false or inadequate details will likely be denied.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <section class="max-w-screen-lg mx-auto px-6">
            <form method="post" action="" id="form" class="flex flex-col">
                @csrf
                @include('front.components.form-error')

                <h2 class="text-xl">Ban Details</h2>

                <div class="flex flex-col md:flex-row gap-3 justify-between">
                    <div class="flex flex-col flex-grow md:w-0">
                        <label for="email" class="text-md font-bold mt-6">
                            Minecraft UUID
                        </label>
                        <input
                            class="
                                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                                @error('email') border-red-500 @enderror
                            "
                            id="email"
                            name="email"
                            type="email"
                            placeholder="e.g. 069a79f4-44e9-4726-a5be-fca90e38aaf5"
                            value="{{ old('email') }}"
                        />
                        @error('email')
                            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                        @enderror
                        <p class="text-sm mt-2 text-gray-500">
                            Can be looked up <a class="text-blue-500" href="https://mcuuid.net/" target="_blank">here</a>. At the very least, provide us your current Minecraft username
                        </p>
                    </div>

                    <div class="flex flex-col flex-grow md:w-0">
                        <label for="email" class="text-md font-bold mt-6">
                            Date of Ban
                        </label>
                        <input
                            class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        @error('email') border-red-500 @enderror
                    "
                            id="email"
                            name="email"
                            type="email"
                            placeholder="e.g. January 31st, 2016"
                            value="{{ old('email') }}"
                        />
                        @error('email')
                        <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                        @enderror
                        <p class="text-sm mt-2 text-gray-500">
                            If you don't know, a rough estimate is also acceptable
                        </p>
                    </div>
                </div>

                <label for="explanation" class="text-md font-bold mt-6">
                    Reason for Ban
                </label>
                <textarea
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        @error('explanation') border-red-500 @enderror
                    "
                    name="explanation"
                    id="explanation"
                    rows="4"
                    placeholder="e.g. I ignored multiple warnings about using inappropriate language"
                >{{ old('explanation') }}</textarea>

                <h2 class="text-xl mt-12">Appeal</h2>

                <label for="email" class="text-md font-bold mt-6">
                    Your Email
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        @error('email') border-red-500 @enderror
                    "
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Enter your email address"
                    value="{{ old('email') }}"
                />
                @error('email')
                <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror
                <p class="text-sm mt-2 text-gray-500">
                    Please check this carefully, updates to your appeal will be sent to this email address.
                </p>

                <label for="explanation" class="text-md font-bold mt-6">
                    Reason to be Unbanned
                </label>
                <textarea
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        @error('explanation') border-red-500 @enderror
                    "
                    name="explanation"
                    id="explanation"
                    rows="10"
                    placeholder="Enter your appeal here"
                >{{ old('explanation') }}</textarea>

                <x-captcha class="mt-6"></x-captcha>

                <x-front::button class="mt-8" type="submit">Submit</x-button>
            </form>
        </section>
    </main>
@endsection
