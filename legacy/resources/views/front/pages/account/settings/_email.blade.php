@if(Session::has('success'))
    <div class="alert alert--success">
        <h2><i class="fas fa-check"></i> Success</h2>
        {{ Session::get('success') }}
    </div>
    <p>
@endif
@include('front.components.form-error', ['field' => 'email'])

<form method="post" action="{{ route('front.account.settings.email') }}" class="form">
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
