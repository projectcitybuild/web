@extends('admin.layouts.admin')

@section('title', 'Builder Rank Applications')

@section('body')
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Reviewed?</th>
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
                    <td>
                        <x-bs.fa-boolean :data="$application->isReviewed()" false-class="text-muted" />
                    </td>
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
                        <a href="{{ route('front.panel.builder-ranks.show', $application->getKey()) }}">
                            {{ $application->isReviewed() ? 'View' : 'Review' }}
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
