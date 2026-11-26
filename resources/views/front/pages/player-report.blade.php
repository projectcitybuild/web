@extends('front.layouts.root-layout')

@section('title', 'Report a Player')
@section('meta_description', 'Submit a confidential report about a player.')

@section('body')
    <x-navbar />

    <main class="relative isolate overflow-hidden bg-gray-950 py-12 text-white md:py-20">
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-[url('/resources/images/world_survival.png')] bg-cover bg-center bg-fixed opacity-50"></div>
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-b from-gray-950/65 via-gray-950/85 to-gray-950"></div>
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-br from-emerald-500/10 via-transparent to-cyan-500/10"></div>

        <div class="mx-auto max-w-5xl space-y-8 px-4 sm:px-6">
            <header class="max-w-3xl space-y-5 py-4 md:py-8">
                <p class="inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-emerald-300/10 px-4 py-2 text-sm font-semibold tracking-wide text-emerald-100 backdrop-blur">
                    <span aria-hidden="true" class="size-2 rounded-full bg-emerald-300"></span>
                    COMMUNITY SAFETY
                </p>
                <h1 class="text-5xl font-black tracking-tight sm:text-6xl">Player Report</h1>
                <p class="max-w-3xl text-lg leading-8 text-gray-200">
                    If you feel that a player has broken our rules, threatened you, or made you feel uncomfortable, please submit a report below.
                </p>
            </header>

            @if (session()->has('success'))
                <x-success-alert>{{ session('success') }}</x-success-alert>
                <p class="text-sm text-gray-200">
                    Your reference number is <strong>#{{ session('report_id') }}</strong>.
                </p>
            @endif

            <section class="rounded-2xl border border-amber-300/30 bg-amber-950/45 p-6 shadow-xl shadow-black/10 backdrop-blur-md sm:p-7">
                <h2 class="text-lg font-semibold text-amber-100">We take player reports seriously</h2>
                <p class="mt-2 leading-7 text-amber-50/85">
                    The consequences of a factual report are usually severe. Please be truthful and make your report in good faith, not for revenge. Players found to be intentionally making false reports may be penalised.
                </p>
            </section>

            <section class="rounded-2xl border border-white/10 bg-gray-950/55 p-6 backdrop-blur-md sm:p-7">
                <h2 class="text-xl font-semibold">Understanding the player report process</h2>
                <ol class="mt-5 text-gray-200">
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-emerald-300 sm:text-5xl">01</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Submission</strong>
                            Complete this form to the best of your ability. Include a way for us to contact you, such as a Discord handle or email address.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-emerald-300 sm:text-5xl">02</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Assignment</strong>
                            Your report will be assigned to a case manager who will oversee the investigation. Standard reports can be seen by all staff. You can choose an administrators-only report if you do not want non-administrators to see it. If you have a preferred staff member, include that in your details and we will try our best to accommodate your request.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-emerald-300 sm:text-5xl">03</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Assessment</strong>
                            Your case manager will aim to contact you within 48 hours to confirm receipt and ask for any additional information. This may involve back-and-forth and can take time depending on the circumstances. Please be patient; you can contact your case manager to check on the status.
                        </p>
                    </li>
                    <li class="grid grid-cols-[3.5rem_1fr] gap-4 border-b border-white/10 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:grid-cols-[4.5rem_1fr] sm:gap-6">
                        <span aria-hidden="true" class="text-4xl font-black leading-none tracking-tight text-emerald-300 sm:text-5xl">04</span>
                        <p class="leading-7">
                            <strong class="block text-lg text-white">Decision</strong>
                            When the report is resolved, your case manager will notify you of the outcome.
                        </p>
                    </li>
                </ol>
            </section>

            <section class="rounded-2xl border border-white/10 bg-gray-950/55 p-6 backdrop-blur-md sm:p-7">
                <h2 class="font-semibold">Need assistance?</h2>
                <p class="mt-2 text-gray-200">
                    If you require help or information, reach out to a staff member you feel comfortable discussing it with, or ask on Discord in the
                    <a class="font-semibold text-emerald-200 underline decoration-emerald-300/50 underline-offset-4 hover:text-emerald-100" href="https://discord.com/invite/947PeWrAyH" target="_blank" rel="noopener noreferrer">#support</a>
                    channel.
                </p>
            </section>

            <form
                method="post"
                action="{{ route('report.submit') }}"
                enctype="multipart/form-data"
                class="space-y-6 rounded-3xl border border-white/15 bg-gray-950/75 p-5 shadow-2xl shadow-black/25 backdrop-blur-lg sm:p-8"
                data-player-report-form
                data-has-errors="{{ $errors->any() ? '1' : '0' }}"
            >
                @csrf
                @include('front.components.form-error')

                <input type="text" name="details" class="hidden" tabindex="-1" autocomplete="off" />

                <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-5" aria-live="polite">
                    <p class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold text-gray-100" data-step-counter>Step 1 of 3</p>
                    <div class="h-2 w-2/3 overflow-hidden rounded-full bg-white/15">
                        <div class="h-full w-1/3 rounded-full bg-emerald-400 transition-all" data-step-progress></div>
                    </div>
                </div>

                <section class="rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-7" data-report-step aria-labelledby="report-step-reporter">
                    <h2 id="report-step-reporter" class="text-2xl font-semibold">1. Your information</h2>
                    <p class="mt-2 text-gray-300">Tell us who is making this report and how we can reach you.</p>

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
                                class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
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
                                class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
                                @error('reporter_contact') aria-invalid="true" @enderror
                            />
                            @error('reporter_contact') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="reporter_email" class="font-semibold">Email address <span class="font-normal text-gray-400">(optional)</span></label>
                            <input
                                id="reporter_email"
                                name="reporter_email"
                                type="email"
                                maxlength="191"
                                value="{{ old('reporter_email', $reporterEmail) }}"
                                autocomplete="email"
                                class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
                                @error('reporter_email') aria-invalid="true" @enderror
                            />
                            <p class="mt-2 text-sm text-gray-400">If you provide an email, a case manager can send you a completion message by email.</p>
                            @error('reporter_email') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-7" data-report-step aria-labelledby="report-step-player">
                    <h2 id="report-step-player" class="text-2xl font-semibold">2. Who are you reporting?</h2>
                    <p class="mt-2 text-gray-300">Enter the Minecraft name of the player whose behaviour you are reporting.</p>

                    <div class="mt-5">
                        <label for="reported_player_name" class="font-semibold">Player's Minecraft name</label>
                        <input
                            id="reported_player_name"
                            name="reported_player_name"
                            type="text"
                            maxlength="191"
                            required
                            value="{{ old('reported_player_name') }}"
                            class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
                            @error('reported_player_name') aria-invalid="true" @enderror
                        />
                        @error('reported_player_name') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </section>

                <section class="rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-7" data-report-step aria-labelledby="report-step-details">
                    <h2 id="report-step-details" class="text-2xl font-semibold">3. Report details and review</h2>
                    <p class="mt-2 text-gray-300">Please include as much accurate detail as you can. You can attach screenshots or other evidence.</p>

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
                                class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
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
                                class="mt-2 w-full rounded-lg border border-white/15 bg-gray-950/70 px-4 py-3 text-white placeholder:text-gray-500 focus:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-300/30"
                                @error('report_details') aria-invalid="true" @enderror
                            >{{ old('report_details') }}</textarea>
                            @error('report_details') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <fieldset>
                            <legend class="font-semibold">Who should be able to review this report?</legend>
                            <div class="mt-3 space-y-3">
                                <label class="flex cursor-pointer gap-3 rounded-lg border border-white/15 bg-gray-950/40 p-4 transition-colors hover:border-emerald-300/50 hover:bg-emerald-300/5">
                                    <input type="radio" name="visibility" value="standard" required @checked(old('visibility', 'standard') === 'standard') />
                                    <span>
                                        <span class="block font-medium">Standard report</span>
                                        <span class="text-sm text-gray-300">Visible to staff members who have permission to review player reports.</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer gap-3 rounded-lg border border-white/15 bg-gray-950/40 p-4 transition-colors hover:border-emerald-300/50 hover:bg-emerald-300/5">
                                    <input type="radio" name="visibility" value="administrators" required @checked(old('visibility') === 'administrators') />
                                    <span>
                                        <span class="block font-medium">Administrators only</span>
                                        <span class="text-sm text-gray-300">Only administrators can see or review this report.</span>
                                    </span>
                                </label>
                            </div>
                            @error('visibility') <span class="mt-2 block text-sm text-red-600">{{ $message }}</span> @enderror
                        </fieldset>

                        <x-front::file-upload
                            id="attachments"
                            name="attachments[]"
                            label="Attachments"
                            :optional="true"
                            :multiple="true"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
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
                </section>

                <div class="flex flex-wrap justify-between gap-3 border-t border-white/10 pt-5">
                    <button
                        type="button"
                        data-report-previous
                        class="rounded-lg border border-white/20 px-5 py-3 font-semibold text-white transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    >Back</button>
                    <button
                        type="button"
                        data-report-next
                        class="rounded-lg bg-emerald-400 px-5 py-3 font-bold text-gray-950 transition-colors hover:bg-emerald-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    >Continue</button>
                    <button
                        type="submit"
                        data-report-submit
                        class="rounded-lg bg-emerald-400 px-5 py-3 font-bold text-gray-950 transition-colors hover:bg-emerald-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300"
                    >Submit report</button>
                </div>
            </form>
        </div>
    </main>
    <x-front::footer />
@endsection
