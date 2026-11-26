@props([
    'id',
    'name',
    'type' => 'text',
    'value' => null,
    'errorKey' => null,
])

@php
    $errorKey ??= $name;
    $hasError = $errors->has($errorKey);
@endphp

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $value }}"
    {{ $attributes->class(['form-control']) }}
    @if ($hasError) aria-invalid="true" @endif
/>
