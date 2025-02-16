@props(['value'])

@if($value)
    <div class="badge bg-success">
        True
    </div>
@else
    <div class="badge bg-danger">
        False
    </div>
@endif
