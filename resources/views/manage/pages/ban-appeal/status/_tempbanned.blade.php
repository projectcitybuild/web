<div class="card card-default border-warning">
    <div class="card-body bg-warning">
        <h5 class="card-title mb-0"><i class="fas fa-hourglass-half me-2"></i> Banned for {{ $banAppeal->getDecisionTempbanDuration() }}</h5>
    </div>
    @include('manage.pages.ban-appeal.status._decision-kv')
</div>
