@props([
    'variant' => 'filled',
    'type' => 'button',
    'href',
])

@php
    if ($variant == 'filled') {
        $class = 'text-gray-50 bg-gray-900 rounded-md shadow-lg hover:bg-gray-800 active:bg-gray-700';
    } else if ($variant == 'outlined') {
        $class = 'text-gray-900 border-gray-900 border-2 rounded-md shadow-lg hover:border-gray-500 active:bg-gray-200';
    }
    $class = 'text-sm sm:text-lg px-6 py-4 cursor-pointer flex justify-center items-center gap-2 ' . $class;
@endphp

@if (empty($href))
    <button
        {{ $attributes->merge([
            'type' => $type,
            'class' => $class,
        ]) }}
    >
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@endif
