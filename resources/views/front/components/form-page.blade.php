@props([
    'background',
    'eyebrow' => null,
    'title',
    'description' => null,
])

<x-navbar />

<main class="form-page">
    <div aria-hidden="true" class="form-page__background" style="background-image: url('{{ Vite::asset($background) }}')"></div>
    <div aria-hidden="true" class="form-page__overlay"></div>
    <div aria-hidden="true" class="form-page__glow"></div>

    <div class="form-page__container">
        <header class="form-page__header">
            @if ($eyebrow)
                <p class="form-page__eyebrow">
                    <span aria-hidden="true" class="form-page__eyebrow-indicator"></span>
                    {{ $eyebrow }}
                </p>
            @endif
            <h1 class="form-page__title">{{ $title }}</h1>
            <p class="form-page__description">
                @isset($intro)
                    {{ $intro }}
                @else
                    {{ $description }}
                @endisset
            </p>
        </header>

        {{ $slot }}
    </div>
</main>

<x-front::footer />
