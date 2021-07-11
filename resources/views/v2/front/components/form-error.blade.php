@if($errors->any())
    <div class="alert alert--error">
        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
    <p>
@endif
