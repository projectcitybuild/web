<h2>Audit records</h2>
@include('admin.manage.pages.activity._audit-filter')
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
    @foreach($audits as $audit)
        <tr>
            <td>
                @if($audit->user)
                    <x-audit-support::model :model="$audit->user"/>
                @else
                    System
                @endif
            </td>
            <td>
                <strong>{{ $audit->human_action }}</strong>
            </td>
            <td>
                <span title="{{ $audit->created_at }}" data-bs-toggle="tooltip">
                    {{ $audit->created_at->shortRelativeDiffForHumans() }}
                </span>
            </td>
            <td>
                @if($audit->auditable)
                    <x-audit-support::model :model="$audit->auditable"/>
                @else
                    {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                @endif
            </td>
            <td>
                <a href="{{ route('auditing.activity.audit.show', $audit) }}">Show</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $audits->links('vendor.pagination.bootstrap-4') }}
