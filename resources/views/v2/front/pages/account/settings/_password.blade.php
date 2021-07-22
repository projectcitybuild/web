<form method="post" action="{{ route('front.account.settings.password') }}" class="settings__form">
    @csrf

    @if(Session::has('success_password'))
        <div class="alert alert--success">
            <h2><i class="fas fa-exclamation-circle"></i> Success</h2>
            {{ Session::get('success_password') }}
        </div>
        <p>
    @endif
    @if($errors->password->any())
        <div class="alert alert--error">
            <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
            @foreach($errors->password->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
        <p>
            @endif

            <label for="old_password">Current Password</label>
            <input class="textfield {{ $errors->has('old_password') ? 'input-text--error' : '' }}" name="old_password"
                   id="old_password" type="password" placeholder="Current Password"/>
            <label for="new_password">New Password</label>
            <input class="textfield {{$errors->has('new_password') ? 'input-text--error' : '' }}" name="new_password"
                   id="new_password" type="password" placeholder="New Password"/>
            <label for="new_password_confirm">Confirm New Password</label>
            <input class="textfield {{ $errors->has('new_password_confirm') ? 'input-text--error' : '' }}"
                   id="new_password_confirm" name="new_password_confirm" type="password" placeholder="New Password (Confirm)"/>

            <button type="submit" class="button button--filled">
                Change
            </button>
</form>
