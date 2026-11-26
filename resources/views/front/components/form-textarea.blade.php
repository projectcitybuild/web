@props([
    'id',
    'name',
    'errorKey' => null,
])

@php
    $errorKey ??= $name;
    $hasError = $errors->has($errorKey);
@endphp

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes->class(['form-control']) }}
    @if ($hasError) aria-invalid="true" @endif
>{{ $slot }}</textarea>
