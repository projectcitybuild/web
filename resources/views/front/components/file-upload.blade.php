@props([
    'id',
    'name',
    'label',
    'accept' => null,
    'multiple' => false,
    'optional' => false,
    'hint' => null,
    'errorKey' => null,
])

@php
    $errorKey ??= explode('[', $name, 2)[0];
    $hasError = $errors->has($errorKey) || $errors->has("{$errorKey}.*");
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-2']) }}>
    <label for="{{ $id }}" class="font-semibold">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-gray-500">(optional)</span>
        @endif
    </label>

    <div
        data-file-dropzone
        class="flex flex-col gap-3 rounded-lg border border-dashed border-gray-600 bg-gray-900 p-4 transition-colors data-[dragging=true]:border-blue-400 data-[dragging=true]:bg-blue-950/40"
    >
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="file"
            @if ($multiple) multiple @endif
            @if ($accept) accept="{{ $accept }}" @endif
            data-file-dropzone-input
            class="block w-full cursor-pointer text-sm text-gray-300 file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-blue-700 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-800"
            @if ($hasError) aria-invalid="true" @endif
        />
        <p data-file-dropzone-file-names aria-live="polite" class="hidden break-all text-sm text-gray-300"></p>
        @if ($hint)
            <p class="text-sm text-gray-400">{{ $hint }}</p>
        @endif

        @error($errorKey)
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
        @error("{$errorKey}.*")
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror

    </div>
</div>
