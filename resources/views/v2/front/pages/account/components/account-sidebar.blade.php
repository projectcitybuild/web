<div class="card card--no-padding">
    <div class="card__body">

        <ul class="sidemenu">
            <li @if(\Route::currentRouteName() == 'front.account.settings') class="sidemenu--active" @endif>
                <a href="{{ route('front.account.settings') }}">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-user-circle fa-stack-1x fa-inverse"></i>
                    </span>
                    Account Settings
                </a>
            </li>
            <li @if(\Route::currentRouteName() == 'front.account.security') class="sidemenu--active" @endif>
                <a href="{{ route('front.account.security') }}">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-lock fa-stack-1x fa-inverse"></i>
                    </span>
                    Security
                </a>
            </li>
            <li @if(\Route::currentRouteName() == 'front.account.donations') class="sidemenu--active" @endif>
                <a href="{{ route('front.account.donations') }}">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-star fa-stack-1x fa-inverse"></i>
                    </span>
                    Donations
                </a>
            </li>
            <li @if(\Route::currentRouteName() == 'front.account.games') class="sidemenu--active" @endif>
                <a href="{{ route('front.account.games') }}">
                    <span class="fa-stack fa-2x">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-gamepad fa-stack-1x fa-inverse"></i>
                    </span>
                    Game Accounts
                </a>
            </li>
        </ul>

    </div>
</div>
