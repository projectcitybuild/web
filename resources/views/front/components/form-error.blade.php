@php
    if(!isset($field)) {
        $bag = $errors;
    } else {
        $bag = $errors->getBag($field);
    }
@endphp

@if($bag->any())
    <div class="alert alert--error">
        <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
        @foreach($bag->all() as $error)
            <div>{!! $error !!}</div>
        @endforeach
    </div>
    <p>
@endif
