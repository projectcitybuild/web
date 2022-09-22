<div class="card mb-2">
    <div class="card-header">
        {{ $description }} <strong>{{ $attribute }}</strong>
    </div>
    <div class="card-body p-0">
        <div @class(['row inline-diff g-0', 'inline-diff--is-styled' => !$plain])>
            <div @class([
                'col-md-6 inline-diff__side inline-diff--is-old',
                'inline-diff--has-old-content' => !$oldIsNotInAudit
            ])>
                @if($oldIsNotInAudit)
                    <em class="text-muted">Unset</em>
                @else
                    {{ $old }}
                @endif
            </div>
            <div class="col-md-6 inline-diff__side inline-diff--is-new">
                {{ $new }}
            </div>
        </div>
    </div>
</div>
