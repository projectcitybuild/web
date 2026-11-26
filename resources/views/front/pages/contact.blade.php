@extends('front.layouts.root-layout')

@section('title', 'Contact Us')
@section('description', 'General inquiries and questions')

@section('body')
    <x-front::form-page
        eyebrow="GET IN TOUCH"
        title="Contact Us"
        description="For general inquiries to the administration team. Where appropriate, replies will be sent via email to the provided email address."
    >
        @if (session()->has('success'))
            <x-front::form-panel title="Message sent" variant="success">
                <p class="mt-2 text-gray-300">{{ session('success') }}</p>
            </x-front::form-panel>
        @endif

        <x-front::form-shell
            method="post"
            action="{{ route('front.contact.submit') }}"
        >
            @csrf

            @error('error')
                <x-validation-error>{{ $message }}</x-validation-error>
            @enderror

            <input type="text" name="details" class="hidden" tabindex="-1" autocomplete="off" />

            <x-front::form-section
                heading-id="contact-inquiry"
                title="Send an inquiry"
                description="Include enough detail for our administration team to understand and respond to your question."
            >
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-front::form-field
                        for="name"
                        label="Name"
                        hint="This can be a Minecraft username, Discord name, or real name."
                    >
                        <x-front::form-input
                            id="name"
                            name="name"
                            :value="old('name')"
                        />
                    </x-front::form-field>

                    <x-front::form-field
                        for="email"
                        label="Email address"
                        :required-label="true"
                        hint="Please ensure this is correct or we may not be able to contact you."
                    >
                        <x-front::form-input
                            id="email"
                            name="email"
                            type="email"
                            :value="old('email')"
                        />
                    </x-front::form-field>

                    <x-front::form-field for="subject" label="Subject" :required-label="true" class="sm:col-span-2">
                        <x-front::form-input
                            id="subject"
                            name="subject"
                            :value="old('subject')"
                        />
                    </x-front::form-field>

                    <x-front::form-field
                        for="inquiry"
                        label="Inquiry"
                        :required-label="true"
                        hint="Maximum 2,000 characters."
                        class="sm:col-span-2"
                    >
                        <x-front::form-textarea
                            id="inquiry"
                            name="inquiry"
                            rows="8"
                            maxlength="2000"
                        >{{ old('inquiry') }}</x-front::form-textarea>
                    </x-front::form-field>
                </div>
            </x-front::form-section>

            <div>
                <x-captcha class="mt-2"></x-captcha>
                @error('captcha-response')
                    <span class="form-field__error">{{ $message }}</span>
                @enderror
            </div>

            <x-front::form-actions class="justify-end">
                <x-front::form-button type="submit">Send inquiry</x-front::form-button>
            </x-front::form-actions>
        </x-front::form-shell>
    </x-front::form-page>
@endsection
