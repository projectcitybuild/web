<nav class="side-menu">
    <ul>
        <li @if(\Route::currentRouteName() == 'front.account.profile') class="side-menu--active" @endif >
            <a href="{{ route('front.account.profile') }}">
                    <span class="fa-stack">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-user fa-stack-1x fa-inverse"></i>
                    </span>
                Profile
            </a>
        </li>
        <li @if(\Route::currentRouteName() == 'front.account.settings') class="side-menu--active" @endif>
            <a href="{{ route('front.account.settings') }}">
                    <span class="fa-stack">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-pencil-alt fa-stack-1x fa-inverse"></i>
                    </span>
                Edit Profile
            </a>
        </li>
        <li @if(\Route::currentRouteName() == 'front.account.security') class="side-menu--active" @endif>
            <a href="{{ route('front.account.security') }}">
                    <span class="fa-stack">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-lock fa-stack-1x fa-inverse"></i>
                    </span>
                Security
            </a>
        </li>
        <li @if(\Route::currentRouteName() == 'front.account.donations') class="side-menu--active" @endif>
            <a href="{{ route('front.account.donations') }}">
                    <span class="fa-stack">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-star fa-stack-1x fa-inverse"></i>
                    </span>
                Donations
            </a>
        </li>
        <li @if(\Route::currentRouteName() == 'front.account.games') class="side-menu--active" @endif>
            <a href="{{ route('front.account.games') }}">
                    <span class="fa-stack">
                        <i class="fas fa-square fa-stack-2x"></i>
                        <i class="fas fa-gamepad fa-stack-1x fa-inverse"></i>
                    </span>
                Game Accounts
            </a>
        </li>
    </ul>
</nav>
