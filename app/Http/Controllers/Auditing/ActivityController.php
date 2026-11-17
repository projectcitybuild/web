<?php

namespace App\Http\Controllers\Auditing;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\ArrayDiff\WrappedArrayEntry;
use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Models\Audit;
use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Response;
use JsonException;

class ActivityController extends WebController
{
    use RendersAdminApp;

    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $activeAuditFilters = $request->validate([
            'auditable_type' => ['nullable', 'string'],
            'audit_event' => ['nullable', 'string'],
        ]);

        $audits = Audit::query()
            ->with(['auditable', 'user'])
            ->when($activeAuditFilters['auditable_type'] ?? null, fn ($query, $type) => $query->where('auditable_type', $type))
            ->when($activeAuditFilters['audit_event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->latest()
            ->orderByDesc('id')
            ->paginate(100, pageName: 'audit_page')
            ->withQueryString()
            ->through(function (Audit $audit): array {
                $subjectType = $audit->auditable
                    ? class_basename($audit->auditable)
                    : ucfirst(class_basename($audit->auditable_type ?? 'Unknown item'));

                return [
                    'id' => $audit->id,
                    'actor' => $audit->user?->username ?? 'System',
                    'action' => $audit->human_action,
                    'event' => $audit->event,
                    'subject' => $subjectType.($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                    'createdAt' => $audit->created_at->toIso8601String(),
                    'showUrl' => route('auditing.activity.audit.show', $audit),
                ];
            });
        $allAuditableTypes = Audit::query()
            ->select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(fn (string $type): array => [
                'value' => $type,
                'label' => class_basename($type),
            ])
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
        ]);
    }

    public function showAudit(Request $request, Audit $audit): Response
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
            ->map(function (Change $change, string $attribute) use ($description): array {
                return [
                    'attribute' => $attribute,
                    'description' => $description,
                    'oldValue' => $this->formatAuditValue($change->getOldValue()),
                    'newValue' => $this->formatAuditValue($change->getNewValue()),
                    'oldEntries' => $change instanceof ArrayChange
                        ? $this->formatArrayEntries($change->getOldValue())
                        : null,
                    'newEntries' => $change instanceof ArrayChange
                        ? $this->formatArrayEntries($change->getNewValue())
                        : null,
                ];
            })
            ->values();

        return $this->inertiaRender('Activity/AuditShow', [
            'audit' => [
                'id' => $audit->id,
                'actor' => $audit->user?->username ?? 'System',
                'subject' => $audit->auditable
                    ? $this->formatAuditValue($audit->auditable)
                    : [
                        'text' => ucfirst(class_basename($audit->auditable_type ?? 'Unknown item')).($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                        'href' => null,
                        'kind' => 'value',
                    ],
                'event' => $audit->event,
                'createdAt' => $audit->created_at->toIso8601String(),
                'changes' => $changes,
            ],
            'indexUrl' => route('auditing.index'),
        ]);
    }

    /**
     * @return array{text: string, href: ?string, kind: 'unset'|'null'|'boolean'|'link'|'value'}
     *
     * @throws JsonException
     */
    private function formatAuditValue(mixed $value): array
    {
        if ($value instanceof NotInAudit) {
            return ['text' => 'Unset', 'href' => null, 'kind' => 'unset'];
        }

        if ($value === null) {
            return ['text' => 'Null', 'href' => null, 'kind' => 'null'];
        }

        if (is_bool($value)) {
            return ['text' => $value ? 'True' : 'False', 'href' => null, 'kind' => 'boolean'];
        }

        if ($value instanceof LinkableAuditModel) {
            return [
                'text' => $value->getActivitySubjectName() ?? ($value instanceof Model ? class_basename($value).' #'.$value->getKey() : 'Unknown'),
                'href' => $value->getActivitySubjectLink(),
                'kind' => 'link',
            ];
        }

        if ($value instanceof Model) {
            return [
                'text' => class_basename($value).' #'.$value->getKey(),
                'href' => null,
                'kind' => 'value',
            ];
        }

        if ($value instanceof DateTimeInterface) {
            return ['text' => $value->format(DATE_ATOM), 'href' => null, 'kind' => 'value'];
        }

        if ($value instanceof BackedEnum) {
            return ['text' => (string) $value->value, 'href' => null, 'kind' => 'value'];
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if (is_scalar($value)) {
            return ['text' => (string) $value, 'href' => null, 'kind' => 'value'];
        }

        return [
            'text' => json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'href' => null,
            'kind' => 'value',
        ];
    }

    /**
     * @param  Collection<int, WrappedArrayEntry>  $entries
     * @return array<int, array{state: string, text: string}>
     *
     * @throws JsonException
     */
    private function formatArrayEntries(Collection $entries): array
    {
        return $entries
            ->map(fn (WrappedArrayEntry $entry): array => [
                'state' => $entry->getStatus()->value,
                'text' => $this->formatAuditValue($entry->unwrap())['text'],
            ])
            ->all();
    }
}
