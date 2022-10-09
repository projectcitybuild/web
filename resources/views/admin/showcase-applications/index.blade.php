@extends('admin.layouts.admin')

@section('title', 'Showcase Applications')

@section('body')
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Status</th>
                <th>Account</th>
                <th>Build Title</th>
                <th>Created At</th>
                <th>Reviewed At</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($applications as $application)
                <tr class="{{ !$application->isReviewed()  ? 'table-warning' : '' }}">
                    <td>{{ \Domain\BuilderRankApplications\Entities\ApplicationStatus::from($application->status)->humanReadable() }}</td>
                    <td>
                        <a href="{{ route('front.panel.accounts.show', $application->account->getKey()) }}">
                            {{ $application->account->username ?: 'Undefined' }}
                        </a>
                    </td>
                    <td>{{ $application->title }}</td>
                    <td>{{ $application->created_at }}</td>
                    <td>{{ $application->closed_at ?: '(Not Reviewed)' }}</td>
                    <td>
                        @if ($application->isReviewed())
                            <a href="{{ route('front.panel.showcase-apps.show', $application->getKey()) }}">
                                View
                            </a>
                        @else
                            <a href="{{ route('front.panel.showcase-apps.show', $application->getKey()) }}" class="btn btn-primary btn-sm">
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
