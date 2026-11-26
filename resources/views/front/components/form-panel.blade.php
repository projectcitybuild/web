@props([
    'title' => null,
    'titleClass' => null,
    'variant' => 'default',
])

<section {{ $attributes->class(['form-panel', 'form-panel--warning' => $variant === 'warning']) }}>
    @if ($title)
        <h2 @class(['form-panel__title', $titleClass])>{{ $title }}</h2>
    @endif

    {{ $slot }}
</section>
