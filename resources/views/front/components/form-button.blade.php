@props([
    'variant' => 'primary',
    'type' => 'button',
])

<button
    {{ $attributes->class(['form-button', 'form-button--secondary' => $variant === 'secondary'])->merge(['type' => $type]) }}
>
    {{ $slot }}
</button>
