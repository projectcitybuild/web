@props(['value'])

@if($value)
    <div class="badge bg-success">
        <i class="fas fa-check-circle me-1"></i>
        True
    </div>
@else
    <div class="badge bg-danger">
        <i class="fas fa-times-circle me-1"></i>
        False
    </div>
@endif
