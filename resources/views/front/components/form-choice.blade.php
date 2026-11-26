@props([
    'name',
    'value',
    'title',
    'description',
    'checked' => false,
    'required' => false,
])

<label class="form-choice">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        @if ($required) required @endif
        @checked($checked)
        {{ $attributes }}
    />
    <span>
        <span class="form-choice__title">{{ $title }}</span>
        <span class="form-choice__description">{{ $description }}</span>
    </span>
</label>
