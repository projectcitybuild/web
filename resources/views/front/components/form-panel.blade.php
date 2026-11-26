@props([
    'title' => null,
    'titleClass' => null,
    'variant' => 'default',
])

<section {{ $attributes->class([
    'form-panel',
    'form-panel--warning' => $variant === 'warning',
    'form-panel--success' => $variant === 'success',
    'form-panel--danger' => $variant === 'danger',
    'form-panel--pending' => $variant === 'pending',
]) }}>
    @if ($title)
        <h2 @class(['form-panel__title', $titleClass])>{{ $title }}</h2>
    @endif

    {{ $slot }}
</section>
