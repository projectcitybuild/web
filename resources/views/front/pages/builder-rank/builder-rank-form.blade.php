@extends('front.layouts.root-layout')

@section('title', 'Apply for Build Rank')
@section('description', 'Use the below form to apply for the next higher builder rank')

@section('body')
    <x-front::form-page
        title="Apply for Build Rank"
        description="Show the Architects Council your latest work and apply for your next-higher build rank."
    >
        <x-front::form-panel title="Application process">
            <ol class="form-process mt-5">
                <li class="form-process__step">
                    <span class="form-process__number">Step 1</span>
                    <h3 class="form-process__title">Submit the form below</h3>
                    <p class="form-process__description">
                        If you submitted an application within the last <strong>7 days</strong>, it will automatically be denied.
                    </p>
                </li>
                <li class="form-process__step">
                    <span class="form-process__number">Step 2</span>
                    <h3 class="form-process__title">Await review</h3>
                    <p class="form-process__description">
                        The Architects Council will review your application. This may take a few days depending on the backlog.
                    </p>
                </li>
                <li class="form-process__step">
                    <span class="form-process__number">Step 3</span>
                    <h3 class="form-process__title">Receive results</h3>
                    <p class="form-process__description">
                        Successful applicants receive a rank-up. If unsuccessful, you will receive feedback and may apply again in a week.
                    </p>
                </li>
            </ol>
            <p class="mt-5 text-sm text-gray-300">
                For more information about our build ranks,
                <a
                    class="font-semibold text-form-accent-soft underline decoration-form-accent/50 underline-offset-4 hover:text-white"
                    href="https://forums.projectcitybuild.com/t/introducing-the-architect-council/35984"
                    target="_blank"
                    rel="noopener noreferrer"
                >read the rank guide</a>.
            </p>
        </x-front::form-panel>

        <x-front::form-shell
            method="post"
            action="{{ route('front.rank-up.submit') }}"
        >
            @csrf

            @error('error')
                <x-validation-error>{{ $message }}</x-validation-error>
            @enderror

            <x-front::form-section
                heading-id="builder-rank-application"
                title="Application form"
                description="Provide a location and a clear description so the council can review your build."
            >
                <div class="mt-5 grid gap-5">
                    <x-front::form-field for="minecraft_username" label="Minecraft username">
                        <x-front::form-input
                            id="minecraft_username"
                            name="minecraft_username"
                            :value="old('minecraft_username', $minecraftUsername ?? '')"
                        />
                    </x-front::form-field>

                    <x-front::form-field for="current_builder_rank" label="Current builder rank">
                        <select
                            id="current_builder_rank"
                            name="current_builder_rank"
                            class="form-control"
                            @if ($errors->has('current_builder_rank')) aria-invalid="true" @endif
                        >
                            @foreach (\App\Domains\BuilderRankApplications\Data\BuilderRank::cases() as $rank)
                                <option value="{{ $rank->value }}" @selected(old('current_builder_rank') === $rank->value)>
                                    {{ $rank->humanReadable() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('current_builder_rank'))
                            <span class="form-field__error">{{ $errors->first('current_builder_rank') }}</span>
                        @endif
                    </x-front::form-field>

                    <x-front::form-field
                        for="build_location"
                        label="Build location (XYZ coordinates and world)"
                        hint="Include the coordinates and the world name."
                    >
                        <x-front::form-input
                            id="build_location"
                            name="build_location"
                            placeholder="x: 150, y: -10, z: 300 in Creative"
                            :value="old('build_location')"
                        />
                    </x-front::form-field>

                    <x-front::form-field for="build_description" label="Description">
                        <x-front::form-textarea
                            id="build_description"
                            name="build_description"
                            rows="5"
                            placeholder="A huge pirate ship battle, 2 pirate factions meet to engage in a war."
                        >{{ old('build_description') }}</x-front::form-textarea>
                    </x-front::form-field>

                    <x-front::form-field
                        for="additional_notes"
                        label="Additional notes"
                        :optional="true"
                    >
                        <x-front::form-textarea
                            id="additional_notes"
                            name="additional_notes"
                            rows="5"
                            placeholder="The pirate ships also have interiors, so please be sure to check them too"
                        >{{ old('additional_notes') }}</x-front::form-textarea>
                    </x-front::form-field>
                </div>
            </x-front::form-section>

            <x-front::form-actions class="justify-end">
                <x-front::form-button type="submit">Submit application</x-front::form-button>
            </x-front::form-actions>
        </x-front::form-shell>
    </x-front::form-page>
@endsection
