@props([
    'variant' => 'filled',
    'scheme' => 'secondary',
    'type' => 'button',
    'size' => 'lg',
    'href',
])

@php
    $class = [
        'cursor-pointer',
        'flex',
        'justify-center',
        'items-center',
        'gap-2',
        'rounded-md',
        'shadow-lg',
    ];

    if ($variant == 'filled') {
        if ($scheme == 'primary') {
            $class = [
                ...$class,
                'text-gray-900',
                'bg-amber-500',
                'hover:bg-amber-400',
                'active:bg-amber-300',
            ];
        } else {
            $class = [
                ...$class,
                'text-gray-50',
                'bg-gray-900',
                'hover:bg-gray-800',
                'active:bg-gray-700',
            ];
        }
    } else if ($variant == 'outlined') {
        $class = [
            ...$class,
            'text-gray-900',
            'border-gray-900',
            'border-2',
            'hover:border-gray-500',
            'active:bg-gray-200',
        ];
    }

    if ($size == 'lg') {
        $class = [
            ...$class,
            'py-4',
            'px-6',
            'text-sm',
            'sm:text-lg',
        ];
    } else {
        $class = [
            ...$class,
            'py-3',
            'px-4',
            'text-sm',
            'sm:text-md',
        ];
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
