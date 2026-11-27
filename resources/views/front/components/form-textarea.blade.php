@props([
    'id',
    'name',
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

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes->except('aria-describedby')->class(['form-control']) }}
    @if ($hasError) aria-invalid="true" @endif
    @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
>{{ $slot }}</textarea>
