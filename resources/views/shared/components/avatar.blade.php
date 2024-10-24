@props([
    'uuid',
])

@php
    if (! isset($uuid)) {
        $uuid = Auth::user()->minecraftAccount->first()?->uuid;
    }
@endphp

@if ($uuid === null)
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6 text-white">
        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd" />
    </svg>
@else
    <img class="w-8 h-8 rounded-full p-1" src="https://minotar.net/avatar/{{ $uuid }}">
@endif
