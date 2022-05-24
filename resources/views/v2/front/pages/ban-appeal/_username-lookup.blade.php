<form action="" id="form" class="form">
    @csrf
    @include('v2.front.components.form-error')

    <div class="form-row">
        <label for="username">Minecraft Username</label>
        <input type="text"
               class="textfield"
               name="username"
               placeholder="Steve">
    </div>

    <button
        class="g-recaptcha button button--filled"
        data-sitekey="@recaptcha_key"
        data-callback="submitForm"
    >
        Start Appeal
    </button>
</form>
