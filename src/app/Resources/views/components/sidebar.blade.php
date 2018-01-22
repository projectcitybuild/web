<div class="donate-panel card primary">
    <div class="donate-panel__padding">
        <h3 class="donate-panel__heading">Help Keep Us Online</h3>
        <div class="progress progress--accent">
            <div class="progress__bar">
                <div class="progress__bar__fill" style="width: {{ $donations['percentage'] ?: 3 }}%"></div>
            </div>
            <div class="progress__markers">
                <span>0</span>
                <span>250</span>
                <span>500</span>
                <span>750</span>
                <span>1000</span>
            </div>
        </div>
    </div>

    <div class="stats donate-panel__stats">
        <div class="stats__item">
            <span class="stats__item__figure">${{ $donations['total'] ?: 0 }}</span>
            <span class="stats__item__heading">Funds Raised</span>
        </div>
        <div class="stats__item">
            <span class="stats__item__figure">{{ $donations['remainingDays'] ?: '?' }}</span>
            <span class="stats__item__heading">Remaining Days</span>
        </div>
    </div>

    <div class="donate-panel__padding">
        <a class="button large secondary" href="http://projectcitybuild.com/forums/index.php?topic=4124.0">
            Donate
        </a>
        <small>Donators receive a colored name, a reserved server slot and more!</small>
    </div>
</div>

<a class="button secondary sidebar-btn" href="http://projectcitybuild.com/forums/index.php?topic=6790.0">
    <div class="sidebar-btn__icon"><i class="fa fa-fw fa-gift"></i></div>
    <div class="sidebar-btn__text">
        <span class="sidebar-btn__heading">Vote For Us</span>
        <small>Vote to receive daily in-game prizes</small>
    </div>
</a>
<a class="button secondary sidebar-btn" href="https://wiki.projectcitybuild.com/">
    <div class="sidebar-btn__icon">
        <i class="fab fa-fw fa-wikipedia-w"></i>
    </div>
    <div class="sidebar-btn__text">
        <span class="sidebar-btn__heading">Community Wiki</span>
        <small>History, towns and more</small>
    </div>
</a>

<div class="panel discord-panel">
    <iframe src="https://discordapp.com/widget?id=161649330799902720&theme=light" 
        width="100%" 
        height="500" 
        allowtransparency="true" 
        frameborder="0">
    </iframe>
</div>