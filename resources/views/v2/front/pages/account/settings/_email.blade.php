@if(Session::has('success'))
    <div class="alert alert--success">
        <h2><i class="fas fa-check"></i> Success</h2>
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

<form method="post" action="{{ route('front.account.settings.email') }}" class="settings__form">
    @csrf

    <div class="form-row">
        <label for="email">Email</label>
        <input class="textfield {{ $errors->has('email') ? 'error' : '' }}" name="email" type="email" id="email"
               placeholder="New Email Address" value="{{ old('email', $user->email) }}"/>
    </div>

    <button type="submit" class="g-recaptcha button button--filled">
        Change
    </button>
</form>
