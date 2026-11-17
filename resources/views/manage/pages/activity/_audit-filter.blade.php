<div class="row mb-3">
    <label for="audit_type" class="col-sm-1 col-form-label horizontal-label">Filter audits</label>
    <div class="col-sm-4">
        <select name="audit_type" id="audit_type" data-pcb-navigation-select data-pcb-search-select>
            <option value="{{ route('manage.activity.index') }}" placeholder>all</option>
            @foreach($allAuditableTypes as $auditableType)
                <option
                    value="{{ route('manage.activity.index', array_merge($activeAuditFilters, ['auditable_type' => $auditableType])) }}"
                    @selected(($activeAuditFilters['auditable_type'] ?? null) === $auditableType)
                >
                    {{ class_basename($auditableType) }}
                </option>
            @endforeach
        </select>
        <select name="audit_event" id="audit_event" data-pcb-navigation-select data-pcb-search-select>
            <option value="{{ route('manage.activity.index') }}" placeholder>all events</option>
            @foreach($allAuditEvents as $auditEvent)
                <option
                    value="{{ route('manage.activity.index', array_merge($activeAuditFilters, ['audit_event' => $auditEvent])) }}"
                    @selected(($activeAuditFilters['audit_event'] ?? null) === $auditEvent)
                >
                    {{ $auditEvent }}
                </option>
            @endforeach
        </select>
    </div>
</div>
