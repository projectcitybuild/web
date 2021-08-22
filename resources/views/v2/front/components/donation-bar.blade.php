<div class="donation-bar">
    <div class="donation-bar__outer">
        <div class="donation-bar__inner" style="width: {{ $percentage }}%; min-width: 75px">
            ${{ number_format($current, 2) }}
        </div>
    </div>
    <ul class="donation-bar__indicators">
        @foreach ($indicators as $indicator)
        <li>${{ $indicator }}</li>
        @endforeach
    </ul>
</div>
