@if($errors->any())
    <div class="alert alert--error">
        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
        {{ $errors->first() }}
    </div>
    <p>
@endif
