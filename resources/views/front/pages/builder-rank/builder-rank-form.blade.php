@extends('front.layouts.root-layout')

@section('title', 'Apply for Build Rank')
@section('description', 'Use the below form to apply for the next higher builder rank')

@section('body')
    <x-navbar />

    <div class="max-w-screen-xl m-auto px-3 md:px-9">
        <div class="my-12">
            <h1 class="text-4xl font-bold">Apply For Build Rank</h1>
            <div class="text-gray-500 mt-3">
                Use the form to apply for the next-higher builder rank, or a build rank if you don't have one.
            </div>
            <div class="text-gray-900 mt-3">
                For more information about our build ranks,
                <a
                    class="text-blue-500"
                    href="https://forums.projectcitybuild.com/t/introducing-the-architect-council/35984"
                    target="_blank"
                >
                    read here
                </a>
            </div>
        </div>

        <div class="rounded-lg max-w-screen-xl m-auto mt-6 bg-gray-50 p-6">
            <h2 class="text-2xl font-bold mb-6">Application Process</h2>

            <ol class="relative border-s border-gray-200 dark:border-gray-700">
                <li class="mb-10 ms-6">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400">Step 1</time>
                    <h3 class="text-lg font-semibold text-gray-900">Submit the form below</h3>
                    <p class="text-base font-normal text-gray-500 text-sm">Warning: If you have previously submitted an application within the last <strong>7 days</strong>, your application will automatically be denied.</p>
                </li>
                <li class="mb-10 ms-6">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400">Step 2</time>
                    <h3 class="text-lg font-semibold text-gray-900">Await review by the Architects Council</h3>
                    <p class="text-base font-normal text-gray-500 text-sm">This may take more than a few days depending on backlog and other factors.</p>
                </li>
                <li class="ms-6">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400">Step 3</time>
                    <h3 class="text-lg font-semibold text-gray-900">Receive results</h3>
                    <p class="text-base font-normal text-gray-500 text-sm">
                        Upon success you will notified and automatically receive a rank up.<br />
                        If unsuccessful, you will receive feedback on the build and you can apply again in a week.
                    </p>
                </li>
            </ol>
        </div>

        <div class="rounded-lg max-w-screen-xl m-auto mt-6 bg-gray-50 p-6 mb-6">
            <h2 class="text-2xl font-bold mb-3">Application Form</h2>

            <form
                method="post"
                action="{{ route('front.rank-up.submit') }}"
                class="flex flex-col"
            >
                @csrf

                @error('error')
                    <x-validation-error class="mt-6">{!! $message !!}</x-validation-error>
                @enderror

                <label for="minecraft_username" class="text-md font-bold mt-6">
                    Minecraft username
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('minecraft_username') border-red-500 @enderror
                    "
                    name="minecraft_username"
                    id="minecraft_username"
                    type="text"
                    value="{{ old('minecraft_username', $minecraftUsername ?? '') }}"
                />
                @error('minecraft_username')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="current_builder_rank" class="text-md font-bold mt-6">
                    Current builder rank
                </label>
                <select
                    name="current_builder_rank"
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('current_builder_rank') border-red-500 @enderror
                    "
                >
                    @foreach (\App\Domains\BuilderRankApplications\Data\BuilderRank::cases() as $rank)
                        <option value="{{ $rank->value }}">{{ $rank->humanReadable() }}</option>
                    @endforeach
                </select>
                @error('current_builder_rank')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="build_location" class="text-md font-bold mt-6">
                    Build location (XYZ co-ordinates and world)
                </label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                        @error('build_location') border-red-500 @enderror
                    "
                    name="build_location"
                    id="build_location"
                    type="text"
                    placeholder="x: 150, y: -10, z: 300 in Creative"
                    value="{{ old('build_location') }}"
                />
                @error('build_location')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="build_description" class="text-md font-bold mt-6">
                    Description
                </label>
                <textarea
                    class="
                    rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                    @error('build_description') border-red-500 @enderror
                "
                    name="build_description"
                    id="build_description"
                    rows="5"
                    placeholder="A huge pirate ship battle, 2 pirate factions meet to engage in a war."
                >{{ old('build_description') }}</textarea>
                @error('build_description')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <label for="additional_notes" class="text-md font-bold mt-6">
                    Additional notes (optional)
                </label>
                <textarea
                    class="
                    rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2 w-full
                    @error('additional_notes') border-red-500 @enderror
                "
                    name="additional_notes"
                    id="additional_notes"
                    rows="5"
                    placeholder="The pirate ships also have interiors, so please be sure to check them too"
                >{{ old('additional_notes') }}</textarea>
                @error('additional_notes')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror

                <x-front::button type="submit" class="mt-12">
                    Submit
                </x-front::button>
            </form>
        </div>
    </div>
@endsection
