<div class="game-ban">
    <div class="game-ban__info">
        <div class="game-ban__avatar">
            <img src="https://minotar.net/avatar/{{ $ban->bannedPlayer->uuid }}/32" alt="">
        </div>
        <div>
            <div class="game-ban__meta">
                {{ $ban->bannedPlayer->getBanReadableName() ?? 'No Alias' }}
                &middot; Banned {{ $ban->created_at }} BY
                {{ $ban->staffPlayer->getBanReadableName() ?? 'No Alias' }}
            </div>
            <div class="game-ban__reason">
                "{{ $ban->reason }}"
            </div>
            @if($ban->banAppeals->isEmpty() && !$ban->is_active)
                <div class="game-ban__status game-ban__status--is-ban">
                    <i class="fas fa-check is-unbanned"></i> Unbanned
                </div>
            @endif
        </div>
    </div>
    @if($ban->banAppeals->isNotEmpty())
        <ul class="game-ban__appeal-history">
            @foreach($ban->banAppeals as $i => $appeal)
                <li>
                    <a href="{{ route('front.appeal.show', $appeal) }}">
                        <div class="game-ban__status">
                            Appeal #{{ $i+1 }} -
                            @switch($appeal->status)
                                @case(\Domain\BanAppeals\Entities\BanAppealStatus::PENDING)
                                    <i class="fas fa-hourglass-half is-pending"></i> Pending
                                    @break

                                @case(\Domain\BanAppeals\Entities\BanAppealStatus::DENIED)
                                    <i class="fas fa-times is-denied"></i> Denied
                                    @break

                                @case(\Domain\BanAppeals\Entities\BanAppealStatus::ACCEPTED_UNBAN)
                                    <i class="fas fa-check is-unbanned"></i> Unbanned
                                    @break

                                @case(\Domain\BanAppeals\Entities\BanAppealStatus::ACCEPTED_TEMPBAN)
                                    <i class="fas fa-clock is-tempbanned"></i> Reduced to tempban
                                    @break
                            @endswitch
                        </div>
                        <div>
                            Submitted {{ $appeal->created_at->diffForHumans() }}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
    @if($ban->is_active && !$ban->banAppeals()->pending()->exists())
        <div class="game-ban__actions">
            <a class="button button--filled button--is-small" href="{{ route('front.appeal.create', $ban) }}">
                Appeal
                @if($ban->banAppeals->isNotEmpty())
                    Again
                @endif
            </a>
        </div>
    @endif
</div>
