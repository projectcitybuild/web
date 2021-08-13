<div class="donation-bar">
    <div class="donation-bar__outer">
        <div class="donation-bar__inner" style="width: {{ $percentage }}%; min-width: 75px">
            ${{ number_format($current, 2) }}
        </div>
    </div>
    <ul class="donation-bar__indicators">
        <li>$0</li>
        <li>$250</li>
        <li>$500</li>
        <li>$750</li>
        <li>$1000</li>
    </ul>
</div>
