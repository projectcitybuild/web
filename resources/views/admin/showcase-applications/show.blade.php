@extends('admin.layouts.admin')

@section('title', 'Showcase Application: ' . $application->title)

@section('body')
    <div class="row">
        <div class="col-md-6">
            @include('admin._errors')

            @if ($application->status == \Domain\BuilderRankApplications\Entities\ApplicationStatus::IN_PROGRESS->value)
                <div class="card border-warning mb-3">
                    <div class="card-header">
                        Application Status
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clock"></i> Awaiting Decision</h5>
                        <p class="card-text">
                            Please discuss the submission in Discord.<br/>
                            Once a decision has been reached, click the approve or deny button to complete this
                            application.
                            <br/><br/>
                            Opened <strong>{{ $application->created_at->diffForHumans() }}</strong>
                        </p>
                    </div>
                </div>
            @endif
            @if ($application->status == \Domain\BuilderRankApplications\Entities\ApplicationStatus::DENIED->value)
                <div class="card border-danger mb-3">
                    <div class="card-header">
                        Application Status
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-times"></i> Application Denied</h5>
                        <p class="card-text">
                            This application was denied for the following reason:<br />
                            <strong>{{ $application->denied_reason }}</strong>
                            <br/><br/>
                            Closed <strong>{{ $application->closed_at }}</strong>
                        </p>
                    </div>
                </div>
            @endif
            @if ($application->status == \Domain\BuilderRankApplications\Entities\ApplicationStatus::APPROVED->value)
                <div class="card border-success mb-3">
                    <div class="card-header">
                        Application Status
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-check"></i> Application Approved</h5>
                        <p class="card-text">
                            This application was approved
                            <br/><br/>
                            Closed <strong>{{ $application->closed_at }}</strong>
                        </p>
                    </div>
                </div>
            @endif

            <div class="card card-default">
                <div class="card-header d-flex justify-content-between">
                    Submission
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Account
                        </dt>
                        <dd class="col-md-9">
                            <a href="{{ route('front.panel.accounts.show', $application->account) }}">{{ $application->account->username }}</a>
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build Title
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->title }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Desired Warp Name
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->name }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build Creators
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->creators }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build Description
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->description }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build Location
                        </dt>
                        <dd class="col-md-9">
                            x: {{ $application->location_x }}, y: {{ $application->location_y }}, z: {{ $application->location_x }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build Direction
                        </dt>
                        <dd class="col-md-9">
                            pitch: {{ $application->location_pitch }}, yaw: {{ $application->location_yaw }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            World
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->location_world }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Approximate Build Date
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->built_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Created at
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->created_at }}
                        </dd>
                    </div>
                    @if ($application->updated_at != $application->created_at)
                        <div class="row g-0">
                            <dt class="col-md-3">
                                Updated at
                            </dt>
                            <dd class="col-md-9">
                                {{ $application->updated_at }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="col-md-6">
            @include('admin.showcase-applications.status._' . $application->status->slug())

            @if($application->status == \Domain\ShowcaseApplications\Entities\ApplicationStatus::PENDING)
                <div class="card mt-2">
                    <div class="card-header">
                        Decide Appeal
                    </div>
                    <div class="card-body border-bottom">
                        <i class="fas fa-exclamation-triangle text-danger"></i> The player <strong>will be notified of this decision immediately with the below message</strong>.
                    </div>
                    <div class="card-body">
                        <form action="{{ route('front.panel.showcase-apps.update', $application) }}" method="post">
                            @csrf
                            @include('admin._errors')
                            @method('PUT')
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="decision_note" class="form-label">Decision Message</label>
                                    <textarea
                                        class="form-control"
                                        name="decision_note"
                                        id="decision_note"
                                        rows="5"
                                    >{{ old('deny_reason', $application->decision_note) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Decision</label>
                                    @foreach(\Domain\ShowcaseApplications\Entities\ApplicationStatus::decisionCases() as $status)
                                        <div class="form-check ">
                                            <input
                                                class="form-check-input"
                                                type="radio" name="status"
                                                name="status"
                                                value="{{ $status->value }}"
                                                id="status{{ $status->value }}"
                                                @checked(old('status', $application->status) == $status)>
                                            <label class="form-check-label" for="status{{ $status->value }}">
                                                {{ $status->humanReadableAction() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
