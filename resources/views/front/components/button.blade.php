@props([
    'variant' => 'filled',
    'type' => 'button',
    'size' => 'lg',
    'href',
])

@php
    $class = ['cursor-pointer', 'flex', 'justify-center', 'items-center', 'gap-2'];

    if ($variant == 'filled') {
        array_push($class, 'text-gray-50', 'bg-gray-900', 'rounded-md', 'shadow-lg', 'hover:bg-gray-800', 'active:bg-gray-700');
    } else if ($variant == 'outlined') {
        array_push($class, 'text-gray-900', 'border-gray-900', 'border-2', 'rounded-md', 'shadow-lg', 'hover:border-gray-500', 'active:bg-gray-200');
    }

    if ($size == 'lg') {
        array_push($class, 'py-4', 'px-6', 'text-sm', 'sm:text-lg', );
    } else {
        array_push($class, 'py-3', 'px-4', 'text-sm', 'sm:text-md', );
    }
@endphp

@if (empty($href))
    <button
        {{ $attributes->merge([
            'type' => $type,
            'class' => implode(separator: ' ', array: $class),
        ]) }}
    >
        {{ $slot }}
    </button>
@else
    <a
        href="{{ $href }}"
        {{ $attributes->merge([
            'class' => implode(separator: ' ', array: $class),
        ]) }}
    >
        {{ $slot }}
    </a>
@endif
