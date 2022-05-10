@if(session()->has('success'))
<div class="alert alert-success">
    <div><i class="fas fa-exclamation-check"></i> <strong>Success</strong></div>
    {{ session()->get('success') }}
</div>
@endif
