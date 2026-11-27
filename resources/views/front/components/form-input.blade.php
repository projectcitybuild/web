@props([
    'id',
    'name',
    'type' => 'text',
    'value' => null,
    'errorKey' => null,
])

@aware(['for' => null, 'hint' => null])

@php
    $errorKey ??= $name;
    $hasError = $errors->has($errorKey);
    $describedBy = array_filter([
        $attributes->get('aria-describedby'),
        $hint ? ($for ?? $id).'-hint' : null,
        $hasError ? ($for ?? $id).'-error' : null,
    ]);
@endphp

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $value }}"
    {{ $attributes->except('aria-describedby')->class(['form-control']) }}
    @if ($hasError) aria-invalid="true" @endif
    @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
/>
