<form method="post" action="{{ route('front.account.settings.email') }}" class="settings__form">
    @csrf

    @if(Session::has('success'))
        <div class="alert alert--success">
            <h3><i class="fas fa-exclamation-circle"></i> Success</h3>
            {{ Session::get('success') }}
        </div>
        <p>
    @endif
    @if($errors->email->any())
        <div class="alert alert--error">
            <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
            @foreach($errors->email->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <label for="email">Email</label>
    <input class="textfield {{ $errors->has('email') ? 'error' : '' }}" name="email" type="email" id="email"
           placeholder="New Email Address" value="{{ old('email', $user->email) }}"/>

    <button type="submit" class="g-recaptcha button button--filled">
        Change
    </button>
</form>
