@include('manage.pages.activity._filter')
<table class="table table-hover">
    <thead>
    <tr>
        <th>User</th>
        <th>Action</th>
        <th>When</th>
        <th>Subject</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($activities as $activity)
        <tr>
            <td>
                @if($activity->causer)
                    <x-audit-support::model :model="$activity->causer"/>
                @else
                    {{ $activity->system_causer?->displayName() ?? 'System' }}
                @endif
            </td>
            <td>
                <strong>
                    {{ $activity->human_action }}
                </strong>
            </td>
            <td>
                    <span title="{{ $activity->created_at }}" data-bs-toggle="tooltip">
                        {{ $activity->created_at->shortRelativeDiffForHumans() }}
                    </span>
            </td>
            <td>
                <x-audit-support::model :model="$activity->subject"/>
            </td>
            <td>
                <a href="{{ route('manage.activity.show', $activity) }}">
                    <i class="fas fa-eye me-1"></i>Show
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $activities->links('vendor.pagination.bootstrap-4') }}
