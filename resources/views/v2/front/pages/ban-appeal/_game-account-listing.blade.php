<div class="game-account game-account--filled">
    <div class="game-account__avatar">
        <img src="https://minotar.net/avatar/{{ $mcAccount->uuid }}/64" alt="">
    </div>
    <div class="game-account__details">
        <div class="game-account__game">
            Minecraft
            &middot;
            @isset($mcAccount->last_synced_at)
                <span>Seen {{ $mcAccount->last_synced_at->diffForHumans() }}</span>
            @else
                <span>Never seen</span>
            @endif
        </div>
        <div class="game-account__alias">
            @if($mcAccount->aliases()->count() == 0)
                <em>No alias</em>
            @else
                {{ $mcAccount->aliases->last()->alias }}
            @endempty
        </div>
        <div class="game-account__id">{{ $mcAccount->uuid }}</div>
        @if($mcAccount->gameBans()->active()->exists())
            @if($mcAccount->banAppeals()->exists())
                <div class="game-account__status game-account__status--is-bad">
                    <i class="fas fa-hourglass-half"></i> Appealing
                </div>
                <div class="game-account__actions">
                    <a class="button button--filled button--is-small"
                       href="{{ route('front.appeal.show', $mcAccount->banAppeals()->first()) }}">View</a>
                </div>
            @else
                <div class="game-account__status game-account__status--is-bad">
                    <i class="fas fa-gavel"></i> Banned
                </div>
                <div class="game-account__actions">
                    <a class="button button--filled button--is-small"
                       href="{{ route('front.appeal.create', $mcAccount->gameBans()->active()->first()) }}">Appeal</a>
                </div>
            @endif
        @else
            <div class="game-account__status game-account__status--is-ok">
                <i class="fas fa-check"></i> Not Banned
            </div>
        @endif
    </div>
</div>
