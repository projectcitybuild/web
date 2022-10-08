@extends('front.templates.2-col')

@section('title', 'Status - Submit Build for Showcase')

@section('heading', 'Application Status')

@section('col-1')
    <p>
        View the contents of your submission and the review status.
        This page will automatically update when a decision has been passed.
    </p>
@endsection

@section('col-2')
    <div class="contents__section">
        <h2>Application Status</h2>

        @switch($application->status())
            @case(\Domain\BuilderRankApplications\Entities\ApplicationStatus::IN_PROGRESS)
                <div class="alert alert--info">
                    <h2><i class="fas fa-clock"></i> In review</h2>
                    Please wait while the Architect Council reviews your submission
                </div>
                @break

            @case(\Domain\BuilderRankApplications\Entities\ApplicationStatus::APPROVED)
                <div class="alert alert--success">
                    <h2><i class="fas fa-check"></i> Success!</h2>
                    Your application was approved and your build has been included in the showcase
                </div>
                @break

            @case(\Domain\BuilderRankApplications\Entities\ApplicationStatus::DENIED)
                <div class="alert alert--error">
                    <h2><i class="fas fa-times"></i> Unsuccessful</h2>
                    Sorry, your application was not approved this time.
                    <br />
                    The following reason was provided: <strong>{{ $application->denied_reason }}</strong>
                </div>
                @break
        @endswitch
    </div>
    <div class="contents__section">
        <h2>Submission</h2>
        <p>
            <strong>Build Title:</strong> {{ $application->title }}
        </p>
        <p>
            <strong>Desired Warp Name:</strong> {{ $application->name }}
        </p>
        <p>
            <strong>Build Location:</strong> x: {{ $application->location_x }}, y: {{ $application->location_y }}, z: {{ $application->location_z }}
        </p>
        <p>
            <strong>Build Direction:</strong> Pitch: {{ $application->location_pitch }}, Yaw: {{ $application->location_yaw }}
        </p>
        <p>
            <strong>World:</strong> {{ $application->location_world }}
        </p>
        <p>
            <strong>Build Description:</strong> {{ $application->description }}
        </p>
        <p>
            <strong>Build Creator(s):</strong> {{ $application->creators }}
        </p>
        <p>
            <strong>Approximate Build Date:</strong> {{ $application->built_at ?: 'Not provided' }}
        </p>
        <p>
            <strong>Applied at:</strong> {{ $application->created_at }}
        </p>
        @isset($application->closed_at)
            <p>
                <strong>Reviewed at:</strong> {{ $application->closed_at }}
            </p>
        @endisset
    </div>
@endsection
