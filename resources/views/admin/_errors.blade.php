<div class="alert alert-error">
    <div><i class="fas fa-exclamation-circle"></i> <strong>There were errors submitting this form</strong></div>
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
