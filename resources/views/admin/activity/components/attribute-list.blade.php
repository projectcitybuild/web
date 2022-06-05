@props(['changes'])

<div class="card">
    <div class="card-header">
        Attributes
    </div>
    <dl class="kv">
        @foreach($changes as $attribute => $value)
            <div class="row g-0">
                <dt class="col-md-3">
                    {{ $attribute }}
                </dt>
                <dd class="col-md-9">
                    {{ $value }}
                </dd>
            </div>
        @endforeach
    </dl>
</div>
