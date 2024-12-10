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
        @foreach($banHistory as $ban)
            <tr class="{{ $ban->isActive() ? 'warning' : '' }}">
                <td>{{ $ban->isActive() ? 'Active' : 'Removed' }}</td>
                <td>{{ $ban->reason ?? 'No Reason Given' }}</td>
                <td>
                    @if($ban->banner_player_id != null && !empty($ban->bannerPlayer->alias))
                        <img src="https://minotar.net/avatar/{{ $ban->bannerPlayer->uuid }}/16" class="banlist__head" alt="">
                        {{ $ban->bannerPlayer->alias }}
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
