
<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => '
            text-sm sm:text-lg text-gray-50
            bg-gray-900 rounded-md shadow-lg
            hover:bg-gray-800
            active:bg-gray-700
            px-6 py-4
            cursor-pointer
            flex justify-center items-center gap-2
        ',
    ]) }}
>
    {{ $slot }}
</button>
