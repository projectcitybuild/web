@extends('front.layouts.root-layout')

@php
    use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
@endphp

@section('title', 'Status - Apply for Build Rank')
@section('meta_description', 'Check the review status of your Project City Build builder rank application.')

@section('body')
    <x-front::form-page
        background="resources/images/bg_survival.webp"
        title="Build Rank Application"
        description="Track the review of your build rank application."
    >
        <x-front::form-panel title="Application progress">
            <ol class="form-status-flow mt-5">
                <li class="form-status-flow__step form-status-flow__step--complete">01 · Submitted</li>
                @if ($application->isReviewed())
                    <li class="form-status-flow__step form-status-flow__step--complete">02 · In Review</li>
                    <li class="form-status-flow__step form-status-flow__step--complete">03 · Finished</li>
                @else
                    <li class="form-status-flow__step form-status-flow__step--complete">02 · In Review</li>
                    <li class="form-status-flow__step">03 · Awaiting result</li>
                @endif
            </ol>
        </x-front::form-panel>

        <x-front::form-panel title="Your submission">
            <p class="mt-4 whitespace-pre-wrap text-lg leading-7 text-gray-100">{{ $application->build_description }}</p>
            <dl class="form-detail-list mt-5">
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Build Location</dt>
                    <dd class="form-detail-list__value">{{ $application->build_location }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Additional Notes</dt>
                    <dd class="form-detail-list__value">{{ $application->additional_notes ?? 'None Provided' }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Current Rank</dt>
                    <dd class="form-detail-list__value">{{ $application->current_builder_rank }}</dd>
                </div>
            </dl>
        </x-front::form-panel>

        <x-front::form-panel title="Review updates">
            <div class="mt-5 space-y-3">
                @if ($application->status === ApplicationStatus::APPROVED)
                    <div class="form-status-event border-emerald-300/30 bg-emerald-950/25">
                        <p class="font-semibold text-emerald-200">Application approved and closed</p>
                        <p class="form-status-event__meta">{{ $application->closed_at }}</p>
                    </div>
                @endif

                @if ($application->status === ApplicationStatus::DENIED)
                    <div class="form-status-event border-red-300/30 bg-red-950/25">
                        <p class="font-semibold text-red-200">Application unsuccessful</p>
                        <p class="mt-2 text-sm text-gray-300">The council provided the following feedback:</p>
                        <p class="mt-3 rounded-lg border border-white/10 bg-black/10 p-4 text-sm italic text-gray-200">
                            {{ $application->denied_reason ?? 'Test' }}
                        </p>
                        <p class="form-status-event__meta">{{ $application->closed_at }}</p>
                    </div>
                @endif

                <div class="form-status-event">
                    <p class="font-semibold text-white">Application set to In Review</p>
                    <p class="form-status-event__meta">{{ $application->created_at }}</p>
                </div>

                <div class="form-status-event">
                    <p class="font-semibold text-white">
                        {{ $application->minecraft_alias }} submitted an application as {{ $application->current_builder_rank }}
                    </p>
                    <p class="form-status-event__meta">{{ $application->created_at }}</p>
                </div>
            </div>
        </x-front::form-panel>
    </x-front::form-page>
@endsection
