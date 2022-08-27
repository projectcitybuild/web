@props(['model', 'link' => true])

@if($model instanceof \Library\Auditing\Changes\Tokens\NotInAudit)
    <em class="text-muted">Unset</em>
@else
    @if($model?->getActivitySubjectLink() && $link)
        <a href="{{ $model->getActivitySubjectLink() }}" {{ $attributes }} >
    @endif
        {{ $model?->getActivitySubjectName() ?? $model?->getKey() ?? 'Unknown' }}
    @if($model?->getActivitySubjectLink() && $link)
        </a>
    @endif
@endif
