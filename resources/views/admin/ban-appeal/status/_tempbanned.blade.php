<div class="card card-default border-warning">
    <div class="card-header">Appeal Status</div>
    <div class="card-body">
        <h5 class="card-title mb-0"><i class="fas fa-hourglass-half text-warning me-2"></i> Banned for {{ $banAppeal->getDecisionTempbanDuration() }}</h5>
    </div>
    @include('admin.ban-appeal.status._decision-kv')
</div>
