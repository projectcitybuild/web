@if($errors->any())
<div class="alert alert-danger">
    <div><i class="fas fa-exclamation-circle"></i> <strong>There were errors submitting this form</strong></div>
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif
