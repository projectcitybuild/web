<div class="game-ban">
    <div class="game-ban__info">
        <div class="game-ban__avatar">
            <img src="https://minotar.net/avatar/{{ $ban->bannedPlayer->uuid }}/32" alt="">
        </div>
        <div>
            <div class="game-ban__meta">
                {{ $ban->bannedPlayer->alias ?? 'No Alias' }}
                &middot; Banned {{ $ban->created_at }} BY
                {{ $ban->bannerPlayer?->alias ?? 'No Alias' }}
            </div>
            <div class="game-ban__reason">
                "{{ $ban->reason }}"
            </div>
            @if($ban->banAppeals->isEmpty() && !$ban->isActive())
                <div class="game-ban__status game-ban__status--is-ban">
                    Unbanned
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
                                @case(\App\Domains\BanAppeals\Data\BanAppealStatus::PENDING)
                                    Pending
                                    @break

                                @case(\App\Domains\BanAppeals\Data\BanAppealStatus::DENIED)
                                    Denied
                                    @break

                                @case(\App\Domains\BanAppeals\Data\BanAppealStatus::ACCEPTED_UNBAN)
                                    Unbanned
                                    @break

                                @case(\App\Domains\BanAppeals\Data\BanAppealStatus::ACCEPTED_TEMPBAN)
                                    Reduced to tempban
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
    @if($ban->isActive() && !$ban->banAppeals()->pending()->exists())
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
