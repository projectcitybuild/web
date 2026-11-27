<?php

namespace App\Http\Controllers\Admin\Auditing;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\ArrayDiff\WrappedArrayEntry;
use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminAuditArrayEntryData;
use App\Http\Data\AdminAuditChangeData;
use App\Http\Data\AdminAuditDetailData;
use App\Http\Data\AdminAuditListEntryData;
use App\Http\Data\AdminAuditTypeData;
use App\Http\Data\AdminAuditValueData;
use App\Models\Audit;
use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Response;
use JsonException;

class ActivityController extends WebController
{
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $activeAuditFilters = $request->validate([
            'auditable_type' => ['nullable', 'string'],
            'audit_event' => ['nullable', 'string'],
        ]);

        $sortableColumns = ['createdAt' => 'created_at'];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $query = Audit::query()
            ->with(['auditable', 'user'])
            ->when($activeAuditFilters['auditable_type'] ?? null, fn ($query, $type) => $query->where('auditable_type', $type))
            ->when($activeAuditFilters['audit_event'] ?? null, fn ($query, $event) => $query->where('event', $event));

        $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

        $audits = $query->paginate(100, pageName: 'audit_page')
            ->withQueryString()
            ->through(function (Audit $audit): array {
                $subjectType = $audit->auditable
                    ? class_basename($audit->auditable)
                    : ucfirst(class_basename($audit->auditable_type ?? 'Unknown item'));

                return (new AdminAuditListEntryData(
                    id: $audit->id,
                    actor: $audit->user?->username ?? 'System',
                    action: $audit->human_action,
                    event: $audit->event,
                    subject: $subjectType.($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                    createdAt: $audit->created_at->toIso8601String(),
                    showUrl: route('auditing.activity.audit.show', $audit),
                ))->toArray();
            });

        $allAuditableTypes = Audit::query()
            ->select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(fn (string $type): array => (new AdminAuditTypeData(
                value: $type,
                label: class_basename($type),
            ))->toArray())
            ->values();

        $allAuditEvents = Audit::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return $this->inertiaRender('Activity/AuditList', [
            'audits' => $audits,
            'auditableTypes' => $allAuditableTypes,
            'auditEvents' => $allAuditEvents,
            'filters' => [
                'auditable_type' => $activeAuditFilters['auditable_type'] ?? '',
                'audit_event' => $activeAuditFilters['audit_event'] ?? '',
            ],
            'indexUrl' => route('auditing.index'),
            ...$sortState,
        ]);
    }

    public function show(Request $request, Audit $audit): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $audit->load(['auditable', 'user']);

        $description = match ($audit->event) {
            'created' => 'Set',
            'deleted' => 'Removed',
            'restored' => 'Restored',
            default => 'Updated',
        };

        $changes = $audit->processed_changes
            ->map(function (Change $change, string $attribute) use ($description): AdminAuditChangeData {
                return new AdminAuditChangeData(
                    attribute: $attribute,
                    description: $description,
                    oldValue: $this->formatAuditValue($change->getOldValue()),
                    newValue: $this->formatAuditValue($change->getNewValue()),
                    oldEntries: $change instanceof ArrayChange
                        ? $this->formatArrayEntries($change->getOldValue())
                        : null,
                    newEntries: $change instanceof ArrayChange
                        ? $this->formatArrayEntries($change->getNewValue())
                        : null,
                );
            })
            ->values();

        return $this->inertiaRender('Activity/AuditShow', [
            'audit' => new AdminAuditDetailData(
                id: $audit->id,
                actor: $audit->user?->username ?? 'System',
                subject: $audit->auditable
                    ? $this->formatAuditValue($audit->auditable)
                    : new AdminAuditValueData(
                        text: ucfirst(class_basename($audit->auditable_type ?? 'Unknown item')).($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                        href: null,
                        kind: 'value',
                    ),
                event: $audit->event,
                createdAt: $audit->created_at->toIso8601String(),
                changes: $changes->all(),
            ),
            'indexUrl' => route('auditing.index'),
        ]);
    }

    /**
     * @throws JsonException
     */
    private function formatAuditValue(mixed $value): AdminAuditValueData
    {
        if ($value instanceof NotInAudit) {
            return new AdminAuditValueData(text: 'Unset', href: null, kind: 'unset');
        }

        if ($value === null) {
            return new AdminAuditValueData(text: 'Null', href: null, kind: 'null');
        }

        if (is_bool($value)) {
            return new AdminAuditValueData(text: $value ? 'True' : 'False', href: null, kind: 'boolean');
        }

        if ($value instanceof LinkableAuditModel) {
            return new AdminAuditValueData(
                text: $value->getActivitySubjectName() ?? ($value instanceof Model ? class_basename($value).' #'.$value->getKey() : 'Unknown'),
                href: $value->getActivitySubjectLink(),
                kind: 'link',
            );
        }

        if ($value instanceof Model) {
            return new AdminAuditValueData(
                text: class_basename($value).' #'.$value->getKey(),
                href: null,
                kind: 'value',
            );
        }

        if ($value instanceof DateTimeInterface) {
            return new AdminAuditValueData(text: $value->format(DATE_ATOM), href: null, kind: 'value');
        }

        if ($value instanceof BackedEnum) {
            return new AdminAuditValueData(text: (string) $value->value, href: null, kind: 'value');
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if (is_scalar($value)) {
            return new AdminAuditValueData(text: (string) $value, href: null, kind: 'value');
        }

        return new AdminAuditValueData(
            text: json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            href: null,
            kind: 'value',
        );
    }

    /**
     * @param  list<WrappedArrayEntry>  $entries
     * @return list<AdminAuditArrayEntryData>
     *
     * @throws JsonException
     */
    private function formatArrayEntries(array $entries): array
    {
        return collect($entries)
            ->map(fn (WrappedArrayEntry $entry): AdminAuditArrayEntryData => new AdminAuditArrayEntryData(
                state: $entry->getStatus()->value,
                text: $this->formatAuditValue($entry->unwrap())->text,
            ))
            ->all();
    }
}
