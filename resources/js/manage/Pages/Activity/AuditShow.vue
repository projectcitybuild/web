<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { format } from '../../Utilities/DateFormatter'
import Badge from '../../Components/Badge.vue'
import Card from '../../Components/Card.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'
import AuditChange from './Partials/AuditChange.vue'

interface AuditValue {
    text: string,
    href: string | null,
    kind: 'unset' | 'null' | 'boolean' | 'link' | 'value',
}

interface AuditChangeRecord {
    attribute: string,
    description: string,
    oldValue: AuditValue,
    newValue: AuditValue,
    oldEntries: Array<{ state: string, text: string }> | null,
    newEntries: Array<{ state: string, text: string }> | null,
}

interface Props {
    audit: {
        id: number,
        actor: string,
        subject: AuditValue,
        event: string,
        createdAt: string,
        changes: AuditChangeRecord[],
    },
    indexUrl: string,
}

defineProps<Props>()
</script>

<template>
    <Head :title="`Audit #${audit.id}`" />

    <div class="space-y-6">
        <PageHeader
            :title="`Audit #${audit.id}`"
            :description="`Review the recorded changes for ${audit.subject.text}.`"
        >
            <template #actions>
                <LinkButton :href="indexUrl" variant="outline">
                    Back to audit logs
                </LinkButton>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section aria-labelledby="audit-changes-heading" class="space-y-4">
                <h2 id="audit-changes-heading" class="text-base font-semibold text-foreground">Changes</h2>

                <div v-if="audit.changes.length > 0" class="space-y-3">
                    <AuditChange
                        v-for="change in audit.changes"
                        :key="change.attribute"
                        :change="change"
                    />
                </div>
                <Card v-else padding="md">
                    <p class="text-sm text-muted">No changes were recorded for this audit entry.</p>
                </Card>
            </section>

            <aside aria-labelledby="audit-metadata-heading">
                <Card padding="md">
                    <h2 id="audit-metadata-heading" class="text-base font-semibold text-foreground">Audit details</h2>

                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-muted">Made by</dt>
                            <dd class="mt-1 break-words font-medium text-foreground">{{ audit.actor }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted">Subject</dt>
                            <dd class="mt-1 break-words font-medium text-foreground">
                                <Link
                                    v-if="audit.subject.href"
                                    :href="audit.subject.href"
                                    class="underline decoration-border underline-offset-4 hover:text-primary-active focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                >
                                    {{ audit.subject.text }}
                                </Link>
                                <span v-else>{{ audit.subject.text }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-muted">Event</dt>
                            <dd class="mt-1">
                                <Badge>{{ audit.event }}</Badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-muted">Date</dt>
                            <dd class="mt-1 break-words font-medium text-foreground">
                                <time :datetime="audit.createdAt">{{ format(audit.createdAt) }}</time>
                            </dd>
                        </div>
                    </dl>
                </Card>
            </aside>
        </div>
    </div>
</template>
