@props(['model', 'link' => true])

@if($model->getActivitySubjectLink() && $link)
    <a href="{{ $model->getActivitySubjectLink() }}">
@endif
    {{ $model->getActivitySubjectName() ?? $model->getKey() }}
@if($model->getActivitySubjectLink() && $link)
    </a>
@endif


