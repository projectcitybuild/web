@extends('admin.layouts.admin')

@section('title', 'Rank Application: ' . $application->account->username)

@section('body')
    <div class="row">
        <div class="col-md-6">
            @include('admin._errors')

            @if ($application->status == \App\Domains\BuilderRankApplications\Data\ApplicationStatus::IN_PROGRESS->value)
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
            @if ($application->status == \App\Domains\BuilderRankApplications\Data\ApplicationStatus::DENIED->value)
                <div class="card border-danger mb-3">
                    <div class="card-header">
                        Application Status
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-times"></i> Application Denied</h5>
                        <p class="card-text">
                            This application was denied for the following reason:<br/>
                            <strong>{{ $application->denied_reason }}</strong>
                            <br/><br/>
                            Closed <strong>{{ $application->closed_at }}</strong>
                        </p>
                    </div>
                </div>
            @endif
            @if ($application->status == \App\Domains\BuilderRankApplications\Data\ApplicationStatus::APPROVED->value)
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
                            Minecraft username
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->minecraft_alias }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Current build rank
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->current_builder_rank }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build location
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->build_location }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Build description
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->build_description }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Additional notes
                        </dt>
                        <dd class="col-md-9">
                            {{ $application->additional_notes ?: 'None' }}
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
            <div class="card">
                <div class="card-header">
                    Close Application
                </div>

                @if ($application->status == \App\Domains\BuilderRankApplications\Data\ApplicationStatus::IN_PROGRESS->value)
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                <i class="fas fa-check"></i> Approve and Rank Up
                            </button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#denyModal">
                                <i class="fas fa-times"></i> Deny and Close
                            </button>
                        </li>
                    </ul>
                @else
                    <div class="card-body">
                        No further action can be taken
                    </div>
                @endif

                <div class="card-footer">
                    <strong>Applications cannot be re-opened after approving or denying.</strong><br/>
                    Please finalise the decision before pressing a button!
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('front.panel.builder-ranks.approve', $application->getKey()) }}" method="post">
        @csrf
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Promote this user to:
                        <select class="form-select" name="promote_group">
                            @foreach ($buildGroups as $group)
                                <option value="{{ $group->getKey() }}">{{ ucfirst($group->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Approve and Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('front.panel.builder-ranks.deny', $application->getKey()) }}" method="post">
        @csrf
        <div class="modal fade" id="denyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deny Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row gy-3">
                            <span>Please enter a denial reason. <strong>This will be publicly visible to the applicant!</strong></span>

                            <div class="mb-3">
                                <textarea
                                        class="form-control"
                                        name="deny_reason"
                                        rows="5"
                                        placeholder="We don't accept dirt huts..."
                                >{{ old('deny_reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Deny and Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
