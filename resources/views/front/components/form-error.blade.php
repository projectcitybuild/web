@php
    /** @deprecated */

    if(!isset($field)) {
        $bag = $errors;
    } else {
        $bag = $errors->getBag($field);
    }
@endphp

@if($bag->any())
    <div class="
        rounded-r-md
        bg-red-100 border-l-4 border-red-500
        p-4
        flex flex-row gap-2
    ">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>

        <div>
            <span class="text-red-500 font-bold block mb-2">An error occurred</span>

            @foreach($bag->all() as $error)
                <div class="text-red-500 text-sm">{!! $error !!}</div>
            @endforeach
        </div>
    </div>
@endif
