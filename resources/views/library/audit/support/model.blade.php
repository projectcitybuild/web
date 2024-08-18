@props(['model', 'link' => true])

@if($model instanceof \App\Core\Domains\Auditing\Changes\Tokens\NotInAudit)
    <em class="text-muted">Unset</em>
@elseif($model == null)
    <span class="badge bg-secondary">Null</span>
@else
    @if($model?->getActivitySubjectLink() && $link)
        <a href="{{ $model->getActivitySubjectLink() }}" {{ $attributes }} >
            @endif
            {{ $model?->getActivitySubjectName() ?? $model?->getKey() ?? 'Unknown' }}
            @if($model?->getActivitySubjectLink() && $link)
        </a>
    @endif
@endif
