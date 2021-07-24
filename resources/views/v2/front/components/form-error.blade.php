@if($errors->any())
    <div class="alert alert--error">
        <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
    <p>
@endif
