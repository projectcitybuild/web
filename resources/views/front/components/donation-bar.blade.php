<div>
    <div class="rounded-lg bg-white border border-gray-200 p-1">
        <div
            class="rounded-md bg-orange-400 p-3 text-xs text-center text-gray-50 font-bold"
            style="width: {{ $percentage }}%; min-width: 75px"
        >
            ${{ number_format($current, 2) }}
        </div>
    </div>
    <ul class="flex flex-row justify-between text-xs text-gray-400 mt-2 pl-2 pr-2">
        @foreach ($indicators as $indicator)
            <li>${{ $indicator }}</li>
        @endforeach
    </ul>
</div>
