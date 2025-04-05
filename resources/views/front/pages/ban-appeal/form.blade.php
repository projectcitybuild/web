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
                            Submit Your Appeal
                        </h2>
                        <p class="text-base text-gray-500 mt-4 leading-relaxed">
                            Please fill-in all fields with appropriate detail. Appeals with false or insufficient detail will likely be denied.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <section class="max-w-screen-lg mx-auto px-6">
            <form method="post" action="" id="form" class="flex flex-col">
                @csrf
                @include('front.components.form-error')

                @isset ($ban)
                    <h2 class="text-xl">Selected Ban</h2>

                    <div class="rounded-lg border border-gray-200 mt-3 bg-gray-50 py-3 px-4 flex flex-row items-center gap-6 overflow-x-auto mb-12">
                        <img src="https://minotar.net/avatar/{{ $ban->bannedPlayer->uuid }}/32" class="rounded-md w-12 h-12">

                        <div class="mt-1 flex flex-col gap-1 flex-grow">
                            <strong class="text-2xl">{{ $ban->bannedPlayer->alias }}</strong>
                            <span class="text-gray-500 text-xs">UUID: {{ $ban->bannedPlayer->uuid }}</span>
                            <span class="text-sm">{{ $ban->created_at->format('jS \of M, Y - h:i A') }}</span>
                        </div>
                    </div>
                @endisset

                <h2 class="text-xl">Ban Details</h2>

                <div class="flex flex-col md:flex-row gap-3 justify-between">
                    <div class="flex flex-col flex-grow md:w-0">
                        <label for="email" class="text-md font-bold mt-6">
                            Minecraft UUID
                        </label>
                        <input
                            class="
                                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                                @error('minecraft_uuid') border-red-500 @enderror
                            "
                            id="minecraft_uuid"
                            name="minecraft_uuid"
                            type="text"
                            placeholder="e.g. 069a79f4-44e9-4726-a5be-fca90e38aaf5"
                            value="{{ old('minecraft_uuid', $ban?->bannedPlayer->uuid) }}"
                        />
                        @error('minecraft_uuid')
                            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                        @enderror
                        <p class="text-sm mt-2 text-gray-500">
                            Can be looked up <a class="text-blue-500" href="https://mcuuid.net/" target="_blank">here</a>
                        </p>
                    </div>

                    <div class="flex flex-col flex-grow md:w-0">
                        <label for="email" class="text-md font-bold mt-6">
                            Date of Ban
                        </label>
                        <input
                            class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        @error('ban_date') border-red-500 @enderror
                    "
                            id="ban_date"
                            name="ban_date"
                            type="email"
                            placeholder="e.g. January 31st, 2016"
                            value="{{ old('ban_date', $ban?->created_at->format('Y-m-d H:i:s')) }}"
                        />
                        @error('ban_date')
                        <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                        @enderror
                        <p class="text-sm mt-2 text-gray-500">
                            If unknown, a rough estimate is also acceptable
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
                >{{ old('explanation', $ban?->reason) }}</textarea>

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
                    value="{{ old('email', Auth::user()?->email) }}"
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
