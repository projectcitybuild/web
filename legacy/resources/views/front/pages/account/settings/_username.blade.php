@if(Session::has('success_username'))
    <div class="alert alert--success">
        <h2><i class="fas fa-check"></i> Success</h2>
        {{ Session::get('success_username') }}
    </div>
    <p>
@endif

@include('front.components.form-error', ['field' => 'username'])

<form method="post" action="{{ route('front.account.settings.username') }}" class="form">
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
