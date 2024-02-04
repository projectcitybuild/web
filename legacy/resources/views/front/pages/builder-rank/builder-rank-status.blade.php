@extends('front.templates.2-col')

@section('title', 'Status - Apply for Build Rank')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

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
                    Your application was approved and your rank has been updated
                </div>
                @break

            @case(\Domain\BuilderRankApplications\Entities\ApplicationStatus::DENIED)
                <div class="alert alert--error">
                    <h2><i class="fas fa-times"></i> Unsuccessful</h2>
                    Sorry, your application was not approved this time.
                    <br />
                    The follow reason was provided: <strong>{{ $application->denied_reason }}</strong>
                </div>
                @break
        @endswitch
    </div>
    <div class="contents__section">
        <h2>Submission</h2>
        <p>
            <strong>Minecraft username:</strong> {{ $application->minecraft_alias }}
        </p>
        <p>
            <strong>Current builder rank:</strong> {{ $application->current_builder_rank }}
        </p>
        <p>
            <strong>Build location:</strong> {{ $application->build_location }}
        </p>
        <p>
            <strong>Build description:</strong> {{ $application->build_description }}
        </p>
        <p>
            <strong>Additional notes:</strong> {{ $application->additional_notes ?: 'None' }}
        </p>
        <p>
            <strong>Created at:</strong> {{ $application->created_at }}
        </p>
        @isset($application->closed_at)
            <p>
                <strong>Reviewed at:</strong> {{ $application->closed_at }}
            </p>
        @endisset
    </div>
@endsection
