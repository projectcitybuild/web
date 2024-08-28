
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => '
            text-sm sm:text-lg text-gray-900
            border-gray-900 border-2 rounded-md shadow-lg
            hover:border-gray-500
            active:bg-gray-200
            px-6 py-4
            cursor-pointer
        ',
    ]) }}
>
    {{ $slot }}
</button>
