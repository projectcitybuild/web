<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, watch } from 'vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ShowingCount from '../../Components/ShowingCount.vue'
import ListPage from '../../Components/ListPage.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import AuditListTable from './Partials/AuditListTable.vue'

interface AuditRecord {
    id: number,
    actor: string,
    action: string,
    event: string,
    subject: string,
    createdAt: string,
    showUrl: string,
}

interface PaginationLink {
    url: string | null,
    label: string,
    active: boolean,
}

interface PaginatedAudits {
    data: AuditRecord[],
    current_page: number,
    from: number | null,
    last_page: number,
    links: PaginationLink[],
    to: number | null,
    total: number,
}

interface AuditType {
    value: string,
    label: string,
}

interface Props {
    audits: PaginatedAudits,
    auditableTypes: AuditType[],
    auditEvents: string[],
    filters: {
        auditable_type: string,
        audit_event: string,
    },
    indexUrl: string,
}

const props = defineProps<Props>()

const filters = reactive({ ...props.filters })

const activeFilterCount = computed(() => [
    filters.auditable_type,
    filters.audit_event,
].filter(Boolean).length)

watch(() => props.filters, (currentFilters) => {
    filters.auditable_type = currentFilters.auditable_type
    filters.audit_event = currentFilters.audit_event
}, { deep: true })

function visitFilteredResults() {
    const query = Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value !== '')
    )

    router.get(props.indexUrl, query, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    })
}

function clearFilters() {
    filters.auditable_type = ''
    filters.audit_event = ''
    visitFilteredResults()
}

function paginationLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;/g, '').trim()
}
</script>

<template>
    <Head title="Audit Logs" />

    <div class="space-y-6">
        <PageHeader
            title="Audit Logs"
            description="Review administrative and system changes across the platform."
        />

        <ListPage :filter-count="activeFilterCount">
            <template #filters>
                <form class="space-y-4" @submit.prevent="visitFilteredResults">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label for="auditable-type" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Subject type
                            <select
                                id="auditable-type"
                                v-model="filters.auditable_type"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option value="">All subject types</option>
                                <option v-for="type in auditableTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </label>

                        <label for="audit-event" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Event
                            <select
                                id="audit-event"
                                v-model="filters.audit_event"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option value="">All events</option>
                                <option v-for="event in auditEvents" :key="event" :value="event">
                                    {{ event }}
                                </option>
                            </select>
                        </label>
                    </div>

                    <div class="flex flex-wrap justify-end gap-2">
                        <OutlinedButton
                            variant="secondary"
                            :disabled="activeFilterCount === 0"
                            @click="clearFilters"
                        >
                            Clear filters
                        </OutlinedButton>
                        <OutlinedButton variant="secondary" type="submit">
                            Apply filters
                        </OutlinedButton>
                    </div>
                </form>
            </template>

            <AuditListTable :audits="audits.data" />

            <template #footer>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <ShowingCount :from="audits.from" :to="audits.to" :total="audits.total" />

                    <nav
                        v-if="audits.last_page > 1"
                        aria-label="Audit log pages"
                        class="flex flex-wrap items-center justify-end gap-1"
                    >
                        <template v-for="(link, index) in audits.links" :key="`${index}-${link.label}`">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :aria-current="link.active ? 'page' : undefined"
                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                :class="link.active
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-border bg-surface text-foreground hover:bg-surface-hover'"
                            >
                                {{ paginationLabel(link.label) }}
                            </Link>
                            <span
                                v-else
                                aria-disabled="true"
                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border border-border bg-surface-muted px-3 text-sm text-subtle"
                            >
                                {{ paginationLabel(link.label) }}
                            </span>
                        </template>
                    </nav>
                </div>
            </template>
        </ListPage>
    </div>
</template>
