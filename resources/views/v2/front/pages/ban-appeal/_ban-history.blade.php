<table class="table table--divided">
    <thead>
    <tr>
        <th>Status</th>
        <th>Reason</th>
        <th>Banned By</th>
        <th>Banned At</th>
        <th>Expires</th>
    </tr>
    </thead>
    <tbody>
        @foreach($playerBans as $ban)
            <tr class="{{ $ban->is_active ? 'warning' : '' }}">
                <td>{{ $ban->is_active ? 'Active' : 'Removed' }}</td>
                <td>{{ $ban->reason ?? 'No Reason Given' }}</td>
                <td>
                    @if($ban->staff_player_id != null && count($ban->staffPlayer->aliases) > 0)
                        <img src="https://minotar.net/avatar/{{ $ban->staffPlayer->uuid }}/16" class="banlist__head" alt="">
                        {{ $ban->staffPlayer->getBanReadableName() }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{ $ban->created_at->format('j M Y H:i') }}
                </td>
                <td>
                    {{ $ban->expires_at?->format('j M Y H:i') ?? 'Never' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
