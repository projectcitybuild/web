@extends('admin.layouts.admin')

@section('title', 'Builder Rank Applications')

@section('body')
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Status</th>
                <th>Account</th>
                <th>Minecraft Username</th>
                <th>Current Rank</th>
                <th>Created At</th>
                <th>Reviewed At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($applications as $application)
                <tr class="{{ !$application->isReviewed()  ? 'table-warning' : '' }}">
                    <td>{{ \App\Domains\BuilderRankApplications\Data\ApplicationStatus::from($application->status)->humanReadable() }}</td>
                    <td>
                        <a href="{{ route('front.panel.accounts.show', $application->account->getKey()) }}">
                            {{ $application->account->username ?: 'Undefined' }}
                        </a>
                    </td>
                    <td>{{ $application->minecraft_alias }}</td>
                    <td>{{ $application->current_builder_rank }}</td>
                    <td>{{ $application->created_at }}</td>
                    <td>{{ $application->closed_at ?: '(Not Reviewed)' }}</td>
                    <td>
                        @if ($application->isReviewed())
                            <a href="{{ route('front.panel.builder-ranks.show', $application->getKey()) }}">
                                View
                            </a>
                        @else
                            <a href="{{ route('front.panel.builder-ranks.show', $application->getKey()) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $applications->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection
