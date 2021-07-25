@props(['data', 'trueClass' => 'text-success', 'falseClass' => 'text-danger'])

@if($data)
    <span class="{{ $trueClass }}"><i class="fas fa-check fa-fw"></i></span>
@else
    <span class="{{ $falseClass }}"><i class="fas fa-times fa-fw"></i></span>
@endif
