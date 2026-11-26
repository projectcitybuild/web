@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('meta_description', 'Use the below form to submit a ban appeal')

@push('meta')
    @isset ($ban)
        <meta name="robots" content="noindex">
    @endisset
@endpush

@section('body')
    <x-front::form-page
        eyebrow="BAN APPEALS"
        title="Submit Your Appeal"
        description="Please fill in the details as accurately as you can. Appeals with false or insufficient detail may be denied."
    >
        <x-front::form-shell
            method="post"
            action="{{ route('front.appeal.form.submit') }}"
            id="form"
            class="mx-auto max-w-3xl"
        >
            @csrf
            @include('front.components.form-error')

            <input type="text" name="details" class="hidden" tabindex="-1" autocomplete="off" />

            @isset ($ban)
                <x-front::form-panel title="Selected Ban">
                    <div class="form-record mt-4">
                        <img src="https://minotar.net/helm/{{ $ban->bannedPlayer->uuid }}/32" alt="" class="form-record__avatar">
                        <div class="form-record__content">
                            <strong class="form-record__title">{{ $ban->bannedPlayer->alias }}</strong>
                            <span class="form-record__meta">UUID: {{ $ban->bannedPlayer->uuid }}</span>
                            <span class="form-record__meta">{{ $ban->created_at->format('jS \of M, Y - h:i A') }}</span>
                        </div>
                    </div>
                </x-front::form-panel>

                <input name="ban_id" type="hidden" value="{{ $ban->id }}" />
            @endisset

            <x-front::form-section
                heading-id="appeal-ban-details"
                title="Ban Details"
                description="Tell us which ban you are appealing. If you do not know a detail, provide your best estimate."
            >
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <x-front::form-field
                        for="minecraft_uuid"
                        label="Minecraft UUID"
                    >
                        <x-front::form-input
                            id="minecraft_uuid"
                            name="minecraft_uuid"
                            placeholder="e.g. 069a79f4-44e9-4726-a5be-fca90e38aaf5"
                            :value="old('minecraft_uuid', $ban?->bannedPlayer->uuid)"
                        />
                        <p class="form-field__hint">
                            Can be looked up <a class="text-form-accent-soft underline underline-offset-4" href="https://mcuuid.net/" target="_blank" rel="noopener noreferrer">here</a>.
                        </p>
                    </x-front::form-field>

                    <x-front::form-field
                        for="date_of_ban"
                        label="Date of Ban"
                        hint="If unknown, a rough estimate is acceptable."
                    >
                        <x-front::form-input
                            id="date_of_ban"
                            name="date_of_ban"
                            placeholder="e.g. January 31st, 2016"
                            :value="old('date_of_ban', $ban?->created_at->format('Y-m-d H:i:s'))"
                        />
                    </x-front::form-field>

                    <x-front::form-field
                        for="ban_reason"
                        label="Reason for Ban"
                        class="md:col-span-2"
                    >
                        <x-front::form-textarea
                            id="ban_reason"
                            name="ban_reason"
                            rows="4"
                            placeholder="Enter the ban message, or the reason you were likely banned"
                        >{{ old('ban_reason', $ban?->reason) }}</x-front::form-textarea>
                    </x-front::form-field>
                </div>
            </x-front::form-section>

            <x-front::form-section
                heading-id="appeal-details"
                title="Your Appeal"
                description="Explain what happened and why you believe your ban should be reviewed."
            >
                <div class="mt-5 space-y-5">
                    <x-front::form-field
                        for="email"
                        label="Your Email"
                        hint="Please check this carefully; updates to your appeal will be sent to this address."
                    >
                        <x-front::form-input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="Enter your email address"
                            :value="old('email', Auth::user()?->email)"
                        />
                    </x-front::form-field>

                    <x-front::form-field for="unban_reason" label="Why should you be unbanned?">
                        <x-front::form-textarea
                            id="unban_reason"
                            name="unban_reason"
                            rows="8"
                            placeholder="Enter your appeal here"
                        >{{ old('unban_reason') }}</x-front::form-textarea>
                    </x-front::form-field>
                </div>
            </x-front::form-section>

            <div>
                <x-captcha></x-captcha>
                @error('captcha-response')
                    <span class="form-field__error">{{ $message }}</span>
                @enderror
            </div>

            <x-front::form-actions class="justify-end">
                <x-front::form-button type="submit">Submit Appeal</x-front::form-button>
            </x-front::form-actions>
        </x-front::form-shell>
    </x-front::form-page>
@endsection
