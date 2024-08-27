<div {{ $attributes->merge([
    'class' => '
        rounded-r-md
        bg-red-100 border-l-4 border-red-500
        p-4
        flex flex-row gap-2
    '])
}}>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-red-500">
        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
    </svg>

    <div class="text-red-500">
        <span class="font-bold block mb-2">An error occurred</span>
        <div class="text-sm">{{ $slot }}</div>
    </div>
</div>
