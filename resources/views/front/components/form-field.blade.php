@props([
    'for',
    'label',
    'optional' => false,
    'requiredLabel' => false,
    'hint' => null,
    'errorKey' => null,
])

@php
    $errorKey ??= $for;
    $hintId = "{$for}-hint";
    $errorId = "{$for}-error";
@endphp

<div {{ $attributes->class(['form-field']) }}>
    <label for="{{ $for }}" class="form-field__label">
        {{ $label }}
        @if ($optional)
            <span class="form-field__optional">(optional)</span>
        @endif
        @if ($requiredLabel)
            <span class="form-field__required" aria-hidden="true">*</span>
            <span class="sr-only">(required)</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint)
        <p id="{{ $hintId }}" class="form-field__hint">{{ $hint }}</p>
    @endif

    @if ($errors->has($errorKey))
        <span id="{{ $errorId }}" class="form-field__error">{{ $errors->first($errorKey) }}</span>
    @endif
</div>
