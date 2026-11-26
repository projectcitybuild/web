@props([
    'headingId',
    'title',
    'description',
])

<section {{ $attributes->class(['form-section'])->merge(['aria-labelledby' => $headingId]) }}>
    <h2 id="{{ $headingId }}" class="form-section__title">{{ $title }}</h2>
    <p class="form-section__description">{{ $description }}</p>

    {{ $slot }}
</section>
