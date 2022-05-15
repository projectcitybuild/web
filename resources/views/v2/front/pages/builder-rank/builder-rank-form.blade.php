@extends('v2.front.templates.master')

@section('title', 'Builder Rank Application')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('body')
    <main class="page login">
        <div class="container">
            <div class="login__dialog login__register-form">
                <h1>Builder Rank Application</h1>

                Use the below form to apply for a builder rank, or the next higher builder rank.
                <br/><br/>

                Application Process

                <ol>
                    <li>Submit an Application using the below form</li>
                    <li>Applications are examined by the Architects Council</li>
                    <li>If your application is successful, you will receive a rank up and be notified</li>
                    <li>Should your application be unsuccessful, you may request feedback on the build</li>
                </ol>

                <strong>
                    PLEASE NOTE: If you have previously submitted an application within 21 days your application will
                    automatically be denied.<br/>
                    You must wait 21 days between applications
                </strong>

                @auth
                    <form method="post" action="{{ route('front.rank-up.submit') }}" id="form" class="form">
                        @csrf

                        @include('v2.front.components.form-error')

                        <div class="form-row">
                            <label for="minecraft_username">Minecraft username</label>
                            <input
                                class="textfield {{ $errors->any() ? 'error' : '' }}"
                                name="minecraft_username"
                                id="minecraft_username"
                                type="text"
                                value="{{ old('minecraft_username') }}"
                            />
                        </div>
                        <div class="form-row">
                            <label for="current_builder_rank">Current builder rank</label>
                            <select name="current_builder_rank" class="textfield {{ $errors->any() ? 'error' : '' }}">
                                @foreach (\Domain\BuilderRankApplications\Entities\BuilderRank::cases() as $rank)
                                    <option value="{{ $rank->value }}">{{ $rank->humanReadable() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label for="username">Build location</label>
                            XYZ co-ordinates and world
                            <input
                                class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                                name="build_location"
                                id="build_location"
                                type="text"
                                placeholder="150, -10, 300 in Creative"
                                value="{{ old('build_location') }}"
                            />
                        </div>
                        <div class="form-row">
                            <label for="build_description">Description</label>
                            E.g. A huge pirate ship battle, 2 pirate factions meet to engage in a war.
                            <textarea
                                class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                                name="build_description"
                                id="build_description"
                                rows="5"
                            >{{ old('build_description') }}</textarea>
                        </div>
                        <div class="form-row">
                            <label for="additional_notes">Additional notes (optional)</label>
                            E.g. The pirate ships also have interiors, so please be sure to check them too
                            <textarea
                                class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                                name="additional_notes"
                                id="additional_notes"
                                rows="5"
                            >{{ old('additional_notes') }}</textarea>
                        </div>

                        <button
                            class="g-recaptcha button button--filled button--block"
                            data-sitekey="@recaptcha_key"
                            data-callback="submitForm"
                        >
                            <i class="fas fa-check"></i> Submit
                        </button>
                    </form>
                @elseauth
                    <p/>
                    <div class="alert alert--error">
                        <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                        You must be <a href="{{ route('front.login') }}">logged-in</a> to submit a builder rank
                        application
                    </div>
                @endauth
            </div>
        </div>
    </main>
@endsection
