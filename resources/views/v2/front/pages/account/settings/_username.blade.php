@if(Session::has('success_username'))
    <div class="alert alert--success">
        <h2><i class="fas fa-check"></i> Success</h2>
        {{ Session::get('success_username') }}
    </div>
    <p>
@endif

@if($errors->username->any())
    <div class="alert alert--error">
        <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
        @foreach($errors->username->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif

<form method="post" action="{{ route('front.account.settings.username') }}" class="settings__form">
    @csrf

    <div class="form-row">
        <label for="username">Username</label>
        <input class="textfield {{ $errors->has('username') ? 'error' : '' }}" name="username" id="username" type="text"
               placeholder="New Username" value="{{ old('username', $user->username) }}"/>
    </div>

    <button type="submit" class="g-recaptcha button button button--filled">
        Change
    </button>
</form>
