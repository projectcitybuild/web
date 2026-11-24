@extends('front.layouts.root-layout')

@section('title', 'Report a Player')
@section('meta_description', 'Submit a confidential report about a player.')

@section('body')
    <x-navbar />

    <main class="bg-white py-12 text-gray-900 dark:bg-gray-950 dark:text-gray-100 md:py-20">
        <div class="mx-auto max-w-4xl space-y-8 px-4 sm:px-6">
            <header class="space-y-3">
                <h1 class="text-4xl font-bold">Player Report</h1>
                <p class="max-w-3xl leading-7 text-gray-600 dark:text-gray-300">
                    If you feel that a player has broken our rules, threatened you, or made you feel uncomfortable, please submit a report below.
                </p>
            </header>

            @if (session()->has('success'))
                <x-success-alert>{{ session('success') }}</x-success-alert>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Your reference number is <strong>#{{ session('report_id') }}</strong>.
                </p>
            @endif

            <section class="rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950/30">
                <h2 class="text-lg font-semibold">We take player reports seriously</h2>
                <p class="mt-2 leading-7 text-gray-700 dark:text-gray-300">
                    We take all reports seriously, and the consequences of a factual report are usually severe. Please be truthful and make your report in good faith, not for revenge. Players found to be intentionally making false reports may be penalised.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold">Understanding the player report process</h2>
                <ol class="list-decimal space-y-3 pl-6 leading-7 text-gray-700 dark:text-gray-300">
                    <li>
                        <strong>Submission.</strong> Complete this form to the best of your ability. Include a way for us to contact you, such as a Discord handle or email address.
                    </li>
                    <li>
                        <strong>Assignment.</strong> Your report will be assigned to a case manager, generally an Administrator. Standard reports can be seen by the staff who review reports. You can choose an administrators-only report if you do not want regular reviewers to see it. If you have a preferred staff member, include that in your details and we will try our best to accommodate your request.
                    </li>
                    <li>
                        <strong>Assessment.</strong> Your case manager will aim to contact you within 48 hours to confirm receipt and ask for any additional information. This may involve back-and-forth and can take time depending on the circumstances. Please be patient; you can contact your case manager to check on the status.
                    </li>
                    <li>
                        <strong>Decision.</strong> When the report is resolved, your case manager will notify you of the outcome.
                    </li>
                </ol>
            </section>

            <section class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold">More information</h2>
                <p class="mt-2 text-gray-700 dark:text-gray-300">
                    If you need more information, reach out to a staff member you feel comfortable discussing it with or ask on Discord in
                    <a class="text-blue-700 underline dark:text-blue-300" href="https://discord.com/invite/947PeWrAyH" target="_blank" rel="noopener noreferrer">#support</a>.
                </p>
            </section>

            <form
                method="post"
                action="{{ route('report.submit') }}"
                enctype="multipart/form-data"
                class="space-y-6"
                data-player-report-form
                data-has-errors="{{ $errors->any() ? '1' : '0' }}"
            >
                @csrf
                @include('front.components.form-error')

                <input type="text" name="details" class="hidden" tabindex="-1" autocomplete="off" />

                <div class="flex items-center justify-between gap-4" aria-live="polite">
                    <p class="font-semibold" data-step-counter>Step 1 of 4</p>
                    <div class="h-2 w-2/3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full w-1/4 rounded-full bg-blue-700 transition-all" data-step-progress></div>
                    </div>
                </div>

                <section data-report-step aria-labelledby="report-step-reporter">
                    <h2 id="report-step-reporter" class="text-2xl font-semibold">1. Your information</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Tell us who is making this report and how we can reach you.</p>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="reporter_name" class="font-semibold">Your Minecraft name</label>
                            <input
                                id="reporter_name"
                                name="reporter_name"
                                type="text"
                                maxlength="191"
                                required
                                value="{{ old('reporter_name', $playerName) }}"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                @error('reporter_name') aria-invalid="true" @enderror
                            />
                            @error('reporter_name') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="reporter_contact" class="font-semibold">Preferred contact method and details</label>
                            <input
                                id="reporter_contact"
                                name="reporter_contact"
                                type="text"
                                maxlength="191"
                                required
                                value="{{ old('reporter_contact') }}"
                                placeholder="For example, Discord: username"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                @error('reporter_contact') aria-invalid="true" @enderror
                            />
                            @error('reporter_contact') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="reporter_email" class="font-semibold">Email address <span class="font-normal text-gray-500">(optional)</span></label>
                            <input
                                id="reporter_email"
                                name="reporter_email"
                                type="email"
                                maxlength="191"
                                value="{{ old('reporter_email', $reporterEmail) }}"
                                autocomplete="email"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                @error('reporter_email') aria-invalid="true" @enderror
                            />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">If you provide an email, a case manager can send you a completion message by email.</p>
                            @error('reporter_email') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <section data-report-step aria-labelledby="report-step-player">
                    <h2 id="report-step-player" class="text-2xl font-semibold">2. Who are you reporting?</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Enter the Minecraft name of the player whose behaviour you are reporting.</p>

                    <div class="mt-5">
                        <label for="reported_player_name" class="font-semibold">Player's Minecraft name</label>
                        <input
                            id="reported_player_name"
                            name="reported_player_name"
                            type="text"
                            maxlength="191"
                            required
                            value="{{ old('reported_player_name') }}"
                            class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            @error('reported_player_name') aria-invalid="true" @enderror
                        />
                        @error('reported_player_name') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </section>

                <section data-report-step aria-labelledby="report-step-details">
                    <h2 id="report-step-details" class="text-2xl font-semibold">3. Report details</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Please include as much accurate detail as you can. You can attach screenshots or other evidence.</p>

                    <div class="mt-5 space-y-5">
                        <div>
                            <label for="subject" class="font-semibold">Subject</label>
                            <input
                                id="subject"
                                name="subject"
                                type="text"
                                maxlength="150"
                                required
                                value="{{ old('subject') }}"
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                @error('subject') aria-invalid="true" @enderror
                            />
                            @error('subject') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="report_details" class="font-semibold">Details of the report</label>
                            <textarea
                                id="report_details"
                                name="report_details"
                                rows="8"
                                minlength="10"
                                maxlength="10000"
                                required
                                class="mt-2 w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                @error('report_details') aria-invalid="true" @enderror
                            >{{ old('report_details') }}</textarea>
                            @error('report_details') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <fieldset>
                            <legend class="font-semibold">Who should be able to review this report?</legend>
                            <div class="mt-3 space-y-3">
                                <label class="flex cursor-pointer gap-3 rounded-lg border border-gray-300 p-4 dark:border-gray-700">
                                    <input type="radio" name="visibility" value="standard" required @checked(old('visibility', 'standard') === 'standard') />
                                    <span>
                                        <span class="block font-medium">Standard report</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Visible to staff members who have permission to review player reports.</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer gap-3 rounded-lg border border-gray-300 p-4 dark:border-gray-700">
                                    <input type="radio" name="visibility" value="administrators" required @checked(old('visibility') === 'administrators') />
                                    <span>
                                        <span class="block font-medium">Administrators only</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Only administrators can see or review this report.</span>
                                    </span>
                                </label>
                            </div>
                            @error('visibility') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </fieldset>

                        <div>
                            <label for="attachments" class="font-semibold">Attachments <span class="font-normal text-gray-500">(optional)</span></label>
                            <input
                                id="attachments"
                                name="attachments[]"
                                type="file"
                                multiple
                                accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="mt-2 block w-full rounded-md border border-gray-300 bg-white p-3 text-sm dark:border-gray-700 dark:bg-gray-800"
                                @error('attachments') aria-invalid="true" @enderror
                            />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Up to 5 JPG, PNG, WebP, or PDF files, 5 MB each.</p>
                            @error('attachments') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                            @error('attachments.*') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <section data-report-step aria-labelledby="report-step-review">
                    <h2 id="report-step-review" class="text-2xl font-semibold">4. Review and submit</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Check your details before sending. Your report will be stored securely and reviewed by the staff allowed by your visibility choice.
                    </p>
                    <x-captcha class="mt-6"></x-captcha>
                    @error('captcha-response') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                </section>

                <div class="flex flex-wrap justify-between gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
                    <button
                        type="button"
                        data-report-previous
                        class="rounded-md border border-gray-300 px-5 py-3 font-semibold text-gray-800 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-800"
                    >Back</button>
                    <button
                        type="button"
                        data-report-next
                        class="rounded-md bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800"
                    >Continue</button>
                    <button
                        type="submit"
                        data-report-submit
                        class="rounded-md bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800"
                    >Submit report</button>
                </div>
            </form>
        </div>
    </main>
@endsection
