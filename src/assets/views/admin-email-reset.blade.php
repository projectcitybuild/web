@if($errors->any())
    <strong>{{ $errors->first() }}</strong>
@endif

@if(Session::has('success'))
    {{ Session::get('success') }}
@endif


<form method="POST" action="{{ route('temp-email-save') }}">
    @csrf

    <input type="email" name="old_email" placeholder="Current email address" />
    <input type="email" name="new_email" placeholder="New email address" />

    <input type="submit" value="Sync" />

</form>