@extends('front.layouts.root-layout')

@section('title', 'Status - Apply for Build Rank')

@section('body')
    <x-navbar />

    <div class="max-w-screen-xl m-auto px-3 md:px-9">
        <div class="my-12">
            <h1 class="text-4xl font-bold">Build Rank Application</h1>

            <ol class="flex items-center w-full text-sm font-medium text-center text-gray-500 sm:text-base mt-6">
                <li class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                    <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                        <span class="me-2">1</span>
                        Submit Form
                    </span>
                </li>
                @if ($application->isReviewed())
                    <li class="flex md:w-full items-center sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                            <span class="me-2">2</span>
                            In Review
                        </span>
                    </li>
                    <li class="flex items-center text-blue-600">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                            <svg class="w-3.5 h-3.5 sm:w-6 sm:h-6 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            Finished
                        </span>
                    </li>
                @else
                    <li class="flex md:w-full items-center text-blue-600 sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200">
                            <svg class="w-3.5 h-3.5 sm:w-6 sm:h-6 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                            </svg>
                            In Review
                        </span>
                    </li>
                    <li class="flex items-center">
                        <span class="me-2">3</span>
                        Finished
                    </li>
                @endif
            </ol>

        </div>

        <div class="rounded-lg max-w-screen-xl m-auto mt-6 bg-gray-50 p-6">
            <h2 class="text-2xl font-bold mb-6">Submission</h2>

            <div class="text-xl mt-6">
                {{ $application->build_description }}
            </div>

            <hr class="my-6" />

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" />
                    </svg>
                    Build Location
                </div>
                <span class="text-lg">{{ $application->build_location }}</span>
            </div>

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" />
                    </svg>
                    Additional Notes
                </div>
                <span class="text-md">{{ $application->additional_notes ?? 'None Provided' }}</span>
            </div>

            <hr class="my-8" />

            <ol class="relative border-s border-gray-200">

                @if ($application->status() === \App\Domains\BuilderRankApplications\Data\ApplicationStatus::APPROVED)
                    <li class="mb-10 ms-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-green-500">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $application->closed_at }}</time>
                            <div class="text-sm font-normal text-gray-500 lex">Application was <span class="font-semibold text-green-500">Approved</span> and closed</div>
                        </div>
                    </li>
                @endif

                @if($application->status() === \App\Domains\BuilderRankApplications\Data\ApplicationStatus::DENIED)
                    <li class="mb-10 ms-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-black">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div class="p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="items-center justify-between mb-3 sm:flex">
                                <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $application->closed_at }}</time>
                                <div class="text-sm font-normal text-gray-500 lex">Application was <span class="font-semibold text-red-500">Unsuccessful</span> with the following comment</div>
                            </div>
                            <div class="p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50">
                                {{ $application->denied_reason ?? "Test" }}
                            </div>
                        </div>
                    </li>
                @endif

                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="text-gray-300">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                    </svg>
                    </span>
                    <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg sm:flex">
                        <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $application->created_at }}</time>
                        <div class="text-sm font-normal text-gray-500 lex">Application set to <span class="font-semibold text-black">In Review</span></div>
                    </div>
                </li>

                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white">
                        <img src="https://minotar.net/avatar//{{ $application->account->minecraftAccount?->first()?->currentAlias() }}/16" class="rounded-md h-6">
                    </span>
                    <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg sm:flex">
                        <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $application->created_at }}</time>
                        <div class="text-sm font-normal text-gray-500 lex">
                            <strong>{{ $application->minecraft_alias }}</strong> submitted an application (as <strong>{{ $application->current_builder_rank }}</strong>)
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>
@endsection
