<form action="{{ route('front.bans.lookup') }}" method="post" id="form" class="form">
    @csrf
    @include('front.components.form-error')

    <div class="form-row">
        <label for="username">Minecraft Username</label>
        <input type="text"
               class="textfield"
               name="username"
               placeholder="Steve"
               value="{{ old('username') }}">
    </div>

    <button
        class="g-recaptcha button button--filled"
        data-sitekey="@recaptcha_key"
        data-callback="submitForm"
    >
        Start Appeal
    </button>
</form>
