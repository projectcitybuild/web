<div class="row">
    <div class="col-md-8">
        @forelse($audit->processed_changes as $attribute => $change)
            <x-audit::attribute-diff
                :attribute="$attribute"
                :change="$change"
                :description="match ($audit->event) {
                    'created' => 'Set',
                    'deleted' => 'Removed',
                    'restored' => 'Restored',
                    default => 'Updated',
                }"
            />
        @empty
            <div class="text-center text-muted">
                <em>No changes recorded</em>
            </div>
        @endforelse
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Metadata</div>
            <dl class="kv">
                <div class="row g-0">
                    <dt class="col-md-3">Made by</dt>
                    <dd class="col-md-9">
                        @if($audit->user)
                            <x-audit-support::model :model="$audit->user"/>
                        @else
                            System
                        @endif
                    </dd>
                </div>
                <div class="row g-0">
                    <dt class="col-md-3">Subject</dt>
                    <dd class="col-md-9">
                        @if($audit->auditable)
                            <x-audit-support::model :model="$audit->auditable"/>
                        @else
                            {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                        @endif
                    </dd>
                </div>
                <div class="row g-0">
                    <dt class="col-md-3">Event</dt>
                    <dd class="col-md-9">{{ $audit->event }}</dd>
                </div>
                <div class="row g-0">
                    <dt class="col-md-3">Date</dt>
                    <dd class="col-md-9">{{ $audit->created_at }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
