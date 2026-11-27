@extends('front.layouts.root-layout')

@section('title', 'Report a Player')
@section('meta_description', 'Submit a confidential report about a player.')

@section('body')
    <x-front::form-page
        background="resources/images/bg_survival.webp"
        eyebrow="COMMUNITY SAFETY"
        title="Player Report"
        description="If you feel that a player has broken our rules, threatened you, or made you feel uncomfortable, please submit a report below."
    >
            @if (session()->has('success'))
                <x-success-alert>{{ session('success') }}</x-success-alert>
                <p class="text-sm text-gray-200">
                    Your reference number is <strong>#{{ session('report_id') }}</strong>.
                </p>
            @endif

            <x-front::form-panel title="We take player reports seriously" variant="warning">
                <p class="mt-2 leading-7 text-amber-50/85">
                    The consequences of a factual report are usually severe. Please be truthful and make your report in good faith, not for revenge. Players found to be intentionally making false reports may be penalised.
                </p>
            </x-front::form-panel>

            <x-front::form-panel title="Understanding the player report process" title-class="form-panel__title--large">
                <ol class="mt-5 text-gray-200">
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-form-accent sm:text-5xl">01</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Submission</strong>
                            Complete this form to the best of your ability. Include a way for us to contact you, such as a Discord handle or email address.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-form-accent sm:text-5xl">02</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Assignment</strong>
                            Your report will be assigned to a case manager who will oversee the investigation. Standard reports can be seen by all staff. You can choose an administrators-only report if you do not want non-administrators to see it. If you have a preferred staff member, include that in your details and we will try our best to accommodate your request.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-form-accent sm:text-5xl">03</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Assessment</strong>
                            Your case manager will aim to contact you within 48 hours to confirm receipt and ask for any additional information. This may involve back-and-forth and can take time depending on the circumstances. Please be patient; you can contact your case manager to check on the status.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-form-accent sm:text-5xl">04</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Decision</strong>
                            When the report is resolved, your case manager will notify you of the outcome.
                        </p>
                    </li>
                </ol>
            </x-front::form-panel>

            <x-front::form-panel title="Need assistance?">
                <p class="mt-2 text-gray-200">
                    If you require help or information, reach out to a staff member you feel comfortable discussing it with, or ask on Discord in the
                    <a class="font-semibold text-form-accent-soft underline decoration-form-accent/50 underline-offset-4 hover:text-white" href="https://discord.com/invite/947PeWrAyH" target="_blank" rel="noopener noreferrer">#support</a>
                    channel.
                </p>
            </x-front::form-panel>

            <x-front::form-shell
                method="post"
                action="{{ route('report.submit') }}"
                enctype="multipart/form-data"
                data-player-report-form
                data-has-errors="{{ $errors->any() ? '1' : '0' }}"
            >
                @csrf
                @include('front.components.form-error')

                <input type="text" name="details" class="hidden" tabindex="-1" autocomplete="off" />

                <x-front::form-progress :total="3" />

                <x-front::form-section
                    heading-id="report-step-reporter"
                    title="1. Your information"
                    description="Tell us who is making this report and how we can reach you."
                    data-report-step
                >

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-front::form-field for="reporter_name" label="Your Minecraft name">
                            <x-front::form-input
                                id="reporter_name"
                                name="reporter_name"
                                maxlength="191"
                                required
                                :value="old('reporter_name', $playerName)"
                            />
                        </x-front::form-field>

                        <x-front::form-field for="reporter_contact" label="Preferred contact method and details">
                            <x-front::form-input
                                id="reporter_contact"
                                name="reporter_contact"
                                maxlength="191"
                                required
                                placeholder="For example, Discord: username"
                                :value="old('reporter_contact')"
                            />
                        </x-front::form-field>

                        <x-front::form-field
                            for="reporter_email"
                            label="Email address"
                            :optional="true"
                            hint="If you provide an email, a case manager can send you a completion message by email."
                            class="sm:col-span-2"
                        >
                            <x-front::form-input
                                id="reporter_email"
                                name="reporter_email"
                                type="email"
                                maxlength="191"
                                :value="old('reporter_email', $reporterEmail)"
                                autocomplete="email"
                            />
                        </x-front::form-field>
                    </div>
                </x-front::form-section>

                <x-front::form-section
                    heading-id="report-step-player"
                    title="2. Who are you reporting?"
                    description="Enter the Minecraft name of the player whose behaviour you are reporting."
                    data-report-step
                >

                    <div class="mt-5">
                        <x-front::form-field for="reported_player_name" label="Player's Minecraft name">
                            <x-front::form-input
                            id="reported_player_name"
                            name="reported_player_name"
                            maxlength="191"
                            required
                            :value="old('reported_player_name')"
                        />
                        </x-front::form-field>
                    </div>
                </x-front::form-section>

                <x-front::form-section
                    heading-id="report-step-details"
                    title="3. Report details and review"
                    description="Please include as much accurate detail as you can. You can attach screenshots or other evidence."
                    data-report-step
                >

                    <div class="mt-5 space-y-5">
                        <x-front::form-field for="subject" label="Subject">
                            <x-front::form-input
                                id="subject"
                                name="subject"
                                maxlength="150"
                                required
                                :value="old('subject')"
                            />
                        </x-front::form-field>

                        <x-front::form-field for="report_details" label="Details of the report">
                            <x-front::form-textarea
                                id="report_details"
                                name="report_details"
                                rows="8"
                                minlength="10"
                                maxlength="10000"
                                required
                            >{{ old('report_details') }}</x-front::form-textarea>
                        </x-front::form-field>

                        <fieldset class="space-y-3">
                            <legend class="font-semibold">Who should be able to review this report?</legend>
                            <x-front::form-choice
                                name="visibility"
                                value="standard"
                                title="Standard report"
                                description="Visible to staff members who have permission to review player reports."
                                :checked="old('visibility', 'standard') === 'standard'"
                                required
                            />
                            <x-front::form-choice
                                name="visibility"
                                value="administrators"
                                title="Administrators only"
                                description="Only administrators can see or review this report."
                                :checked="old('visibility') === 'administrators'"
                                required
                            />
                            @error('visibility') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </fieldset>

                        <x-front::file-upload
                            id="attachments"
                            name="attachments[]"
                            label="Attachments"
                            :optional="true"
                            :multiple="true"
                            accept=".jpg,.jpeg,.png,.webp"
                            hint="Up to 5 JPG/PNG/WebP files. Max 5 MB each."
                            error-key="attachments"
                        />

                        <div>
                            <p class="text-gray-300">
                                Check your details before sending. Your report will be stored securely and reviewed by the staff allowed by your visibility choice.
                            </p>
                            <x-captcha class="mt-6"></x-captcha>
                            @error('captcha-response') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </x-front::form-section>

                <x-front::form-actions>
                    <x-front::form-button
                        type="button"
                        variant="secondary"
                        data-report-previous
                    >Back</x-front::form-button>
                    <x-front::form-button
                        type="button"
                        data-report-next
                    >Continue</x-front::form-button>
                    <x-front::form-button
                        type="submit"
                        data-report-submit
                    >Submit report</x-front::form-button>
                </x-front::form-actions>
            </x-front::form-shell>
    </x-front::form-page>
@endsection
