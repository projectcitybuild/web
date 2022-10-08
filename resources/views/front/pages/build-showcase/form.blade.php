@extends('front.templates.2-col')

@section('title', 'Submit Build for Showcase')
@section('description', 'Use the below form to submit a build to be showcased')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('heading', 'Submit Build For PCB Showcase')

@section('col-1')
    <p>
        The showcase is a curation of high-quality builds created on our server.
        Any member may submit a build, there's no limit to how many builds you can submit, and plenty of rewards for those who are successful.
    </p>
    <p>
        For more information,
        <a class="alternative" href="TODO" target="_blank">
            continue reading here <i class="fas fa-external-link"></i>
        </a>
    </p>
@endsection

@section('col-2')
    <div class="contents__section">
        <h2>Application Process</h2>
        <ol>
            <li>Submit your build using the below form</li>
            <li>Builds are examined by the Architect Council</li>
            <li>If your application is successful, you will receive a notification and rewards, and your build will be showcased</li>
            <li>If your application be unsuccessful, you may request feedback on the build</li>
        </ol>
    </div>
    <div class="contents__section">
        <h2>Application Form</h2>

        @if ($applicationInProgress)
            <div class="alert alert--error">
                <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                You already have an <a href="{{ route('front.showcase.status', $applicationInProgress) }}">application in progress</a>.
            </div>
        @else
            @guest
                <p/>
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    You must be <a href="{{ route('front.login') }}">logged-in</a> to submit a build
                </div>
            @endguest
            @auth
                <form method="post" action="{{ route('front.showcase.apply.submit') }}" id="form" class="form">
                    @csrf
                    @include('front.components.form-error')

                    <div class="form-row">
                        <label for="title">Build Name</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="title"
                            id="title"
                            type="text"
                            placeholder="Monarch Mall"
                            value="{{ old('title') }}"
                        />
                    </div>
                    <div class="form-row">
                        <label for="name">Desired Warp Name</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="name"
                            id="name"
                            type="text"
                            placeholder="monarch_mall"
                            value="{{ old('name') }}"
                        />
                        Lowercase. No spaces or symbols permitted
                    </div>
                    <div class="form-row">
                        <label>Build coordinates</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="location_x"
                            id="location_x"
                            type="text"
                            placeholder="x"
                            value="{{ old('location_x') }}"
                        />
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="location_y"
                            id="location_y"
                            type="text"
                            placeholder="y"
                            value="{{ old('location_y') }}"
                        />
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="location_z"
                            id="location_z"
                            type="text"
                            placeholder="z"
                            value="{{ old('location_z') }}"
                        />
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="location_pitch"
                            id="location_pitch"
                            type="text"
                            placeholder="pitch"
                            value="{{ old('location_pitch') }}"
                        />
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="location_yaw"
                            id="location_yaw"
                            type="text"
                            placeholder="yaw"
                            value="{{ old('location_yaw') }}"
                        />

                        Please check these in-game, and be precise.
                        The system will automatically create a warp point at this location if your application is successful.

                        <a href="TODO">Where to find these coordinates <i class="fas fa-external-link"></i></a>
                    </div>
                    <div class="form-row">
                        <label for="location_world">World</label>
                        <select name="location_world" class="textfield {{ $errors->any() ? 'error' : '' }}">
                            <option value="creative">Creative</option>
                            <option value="survival">Survival</option>
                            <option value="monarch">Monarch</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="description">Build Description</label>
                        <textarea
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="description"
                            id="description"
                            rows="5"
                            placeholder="e.g. A huge pirate ship battle, 2 pirate factions meet to engage in a war."
                        >{{ old('description') }}</textarea>
                    </div>
                    <div class="form-row">
                        <label for="creators">Build Creator(s)</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="creators"
                            id="creators"
                            type="text"
                            placeholder="Notch"
                            value="{{ old('creators') ?: $minecraftUsername }}"
                        />
                    </div>
                    <div class="form-row">
                        <label for="built_at">Approximate Build Date</label>
                        <input
                            class="textfield {{ $errors->any() ? 'input-text--error' : '' }}"
                            name="built_at"
                            id="built_at"
                            type="text"
                            value="{{ old('built_at') ?: now() }}"
                        />
                    </div>

                    <button
                        class="g-recaptcha button button--filled button--block"
                        data-sitekey="@recaptcha_key"
                        data-callback="submitForm"
                    >
                        <i class="fas fa-check"></i> Submit
                    </button>
                </form>
            @endauth
        @endif
    </div>
@endsection
