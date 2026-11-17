<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Badge from '../../../Components/Badge.vue'

interface AuditValue {
    text: string,
    href: string | null,
    kind: 'unset' | 'null' | 'boolean' | 'link' | 'value',
}

interface ArrayEntry {
    state: string,
    text: string,
}

interface AuditChangeRecord {
    attribute: string,
    description: string,
    oldValue: AuditValue,
    newValue: AuditValue,
    oldEntries: ArrayEntry[] | null,
    newEntries: ArrayEntry[] | null,
}

const props = defineProps<{
    change: AuditChangeRecord,
}>()

const sides = computed(() => [
    { label: 'Before', value: props.change.oldValue, entries: props.change.oldEntries },
    { label: 'After', value: props.change.newValue, entries: props.change.newEntries },
])
</script>

<template>
    <article class="overflow-hidden rounded-lg border border-border bg-surface">
        <h3 class="border-b border-border bg-surface-muted px-4 py-3 text-sm font-semibold text-foreground">
            {{ change.description }} <span class="break-all">{{ change.attribute }}</span>
        </h3>

        <div class="grid divide-y divide-border md:grid-cols-2 md:divide-x md:divide-y-0">
            <section
                v-for="side in sides"
                :key="side.label"
                class="min-w-0 p-4"
                :aria-label="side.label"
            >
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-muted">{{ side.label }}</h4>

                <ul v-if="side.entries !== null" class="space-y-1.5">
                    <li
                        v-for="(entry, index) in side.entries"
                        :key="`${entry.state}-${index}`"
                        class="rounded-md px-2.5 py-2 text-sm"
                        :class="entry.state === 'added'
                            ? 'bg-success-soft text-success'
                            : entry.state === 'removed'
                                ? 'bg-danger-soft text-danger line-through'
                                : 'bg-surface-muted text-foreground'"
                    >
                        {{ entry.text }}
                    </li>
                    <li v-if="side.entries.length === 0" class="text-sm italic text-subtle">Empty</li>
                </ul>

                <Badge v-else-if="side.value.kind === 'unset'" variant="default">
                    {{ side.value.text }}
                </Badge>
                <Badge v-else-if="side.value.kind === 'null'" variant="default">
                    {{ side.value.text }}
                </Badge>
                <Badge
                    v-else-if="side.value.kind === 'boolean'"
                    :variant="side.value.text === 'True' ? 'success' : 'danger'"
                >
                    {{ side.value.text }}
                </Badge>
                <Link
                    v-else-if="side.value.href"
                    :href="side.value.href"
                    class="break-words text-sm font-medium text-primary-active underline decoration-border underline-offset-4 hover:text-primary"
                >
                    {{ side.value.text }}
                </Link>
                <p v-else class="whitespace-pre-wrap break-words text-sm text-foreground">
                    {{ side.value.text }}
                </p>
            </section>
        </div>
    </article>
</template>
