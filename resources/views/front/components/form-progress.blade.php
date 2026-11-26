@props([
    'step' => 1,
    'total',
])

<div class="form-progress" aria-live="polite">
    <p class="form-progress__counter" data-step-counter>Step {{ $step }} of {{ $total }}</p>
    <div class="form-progress__track">
        <div class="form-progress__bar" style="width: {{ 100 / $total }}%" data-step-progress></div>
    </div>
</div>
