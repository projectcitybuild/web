@extends('v2.front.templates.2-col')

@section('title', 'Apply for Build Rank')
@section('description', 'Use the below form to apply for the next higher builder rank')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('heading', 'Apply For Build Rank')

@section('col-1')
    <p>
        Use the form to apply for the next-higher builder rank, or a build rank if you don't have one.
    </p>
    <p>
        For more information about our build ranks,
        <a class="alternative" href="https://forums.projectcitybuild.com/t/introducing-the-architect-council/35984" target="_blank">
            read here <i class="fas fa-external-link"></i>
        </a>
    </p>
    <blockquote>
        <h3><i class="fas fa-warning"></i> WARNING</h3>
        If you have previously submitted an application within <strong>21 days</strong>, your application will
        be denied.<br/><br />
        You must wait at least 21 days between applications
    </blockquote>
@endsection

@section('col-2')
    <div class="contents__section">
        <h2>Application Process</h2>
        <ol>
            <li>Submit an Application using the below form</li>
            <li>Applications are examined by the Architects Council</li>
            <li>If your application is successful, you will receive a rank up and be notified</li>
            <li>Should your application be unsuccessful, you may request feedback on the build</li>
        </ol>
    </div>
    <div class="contents__section">
        <h2>Application Form</h2>

        @if ($applicationInProgress)
            <div class="alert alert--error">
                <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                You already have an <a href="{{ route('front.rank-up.status', $applicationInProgress) }}">application in progress</a>.
            </div>
        @else
            @guest
                <p/>
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    You must be <a href="{{ route('front.login') }}">logged-in</a> to submit a builder rank
                    application
                </div>
            @elseguest
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
                            value="{{ old('minecraft_username', $minecraftUsername ?? '') }}"
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
                        <label for="build_location">Build location (XYZ co-ordinates and world)</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="build_location"
                            id="build_location"
                            type="text"
                            placeholder="eg. x: 150, y: -10, z: 300 in Creative"
                            value="{{ old('build_location') }}"
                        />
                    </div>
                    <div class="form-row">
                        <label for="build_description">Description</label>
                        <textarea
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="build_description"
                            id="build_description"
                            rows="5"
                            placeholder="e.g. A huge pirate ship battle, 2 pirate factions meet to engage in a war."
                        >{{ old('build_description') }}</textarea>
                    </div>
                    <div class="form-row">
                        <label for="additional_notes">Additional notes (optional)</label>
                        <textarea
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="additional_notes"
                            id="additional_notes"
                            rows="5"
                            placeholder="e.g. The pirate ships also have interiors, so please be sure to check them too"
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
            @endguest
        @endif
    </div>
@endsection
