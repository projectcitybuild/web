<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import ListPage from '../../../../Components/ListPage.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import Select from '../../../../Components/Select.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import { Icons } from '../../../../Navigation/Icons'
import { Routes } from '../../../../Navigation/Routes'
import usePermissions from '../../../../Composables/usePermissions'
import CopyIconButton from '../../../../Components/CopyIconButton.vue'
import Pill from '../../../../Components/Pill.vue'

interface PassportClient {
    id: number,
    name: string,
    grant_types: string[],
    redirect_uris: string[],
    confidential: boolean,
    revoked: boolean,
}

interface Props {
    clients: PassportClient[],
    created_client_secret?: string | null,
}

const props = defineProps<AdminSharedPageProps & Props>()
const { can } = usePermissions()
const statusFilter = ref('active')

const visibleClients = computed(() => {
    if (statusFilter.value === 'all') {
        return props.clients
    }

    const showRevoked = statusFilter.value === 'revoked'

    return props.clients.filter(client => client.revoked === showRevoked)
})

const activeFilterCount = computed(() => statusFilter.value === 'active' ? 0 : 1)
const emptyMessage = computed(() => {
    if (props.clients.length === 0) {
        return 'No Passport clients have been created.'
    }

    return statusFilter.value === 'revoked'
        ? 'No revoked Passport clients to display.'
        : 'No active Passport clients to display.'
})

function clearFilters(): void {
    statusFilter.value = 'active'
}
</script>

<template>
    <Head title="OAuth Clients" />

    <div class="space-y-6">
        <PageHeader title="OAuth Clients" description="Manage OAuth clients and revoke credentials that are no longer in use.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.server_tokens.edit')"
                    :href="Routes.manage.passportClients.create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" />
                    Create Client
                </LinkButton>
            </template>
        </PageHeader>

        <SuccessAlert v-if="success" :message="success" />

        <section
            v-if="created_client_secret"
            class="rounded-lg border border-danger bg-surface p-5 sm:p-6"
            aria-labelledby="created-client-secret-title"
        >
            <h2 id="created-client-secret-title" class="font-semibold text-foreground">
                Copy the client secret now
            </h2>
            <p class="mt-1 text-sm text-muted">
                Passport will not show this secret again. Store it in a trusted secret manager.
            </p>
            <div class="mt-3 flex items-start gap-2">
                <code class="min-w-0 flex-1 select-all break-all rounded-md bg-surface-muted p-3 font-mono text-sm text-foreground">
                    {{ created_client_secret }}
                </code>
                <CopyIconButton :text="created_client_secret ?? null" label="client secret" />
            </div>
        </section>

        <ListPage
            :count="visibleClients.length"
            count-label="clients"
            :filter-count="activeFilterCount"
        >
            <template #filters>
                <div class="grid gap-4 sm:grid-cols-2">
                    <Select v-model="statusFilter" label="Client status">
                        <option value="active">Active clients</option>
                        <option value="revoked">Revoked clients</option>
                        <option value="all">All clients</option>
                    </Select>
                </div>
                <div class="mt-4 flex justify-end">
                    <OutlinedButton
                        variant="secondary"
                        :disabled="activeFilterCount === 0"
                        @click="clearFilters"
                    >
                        Clear filters
                    </OutlinedButton>
                </div>
            </template>

            <section class="overflow-x-auto rounded-lg border border-border">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-muted text-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Grant types</th>
                            <th class="px-4 py-3 font-medium">Redirect URIs</th>
                            <th class="px-4 py-3 font-medium">Secret Type</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th v-if="can('web.manage.server_tokens.edit')" class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="client in visibleClients" :key="client.id">
                            <td class="px-4 py-3">
                                <span class="font-medium text-foreground">{{ client.name }}</span>
                                <span class="mt-1 block font-mono text-xs text-muted">ID: {{ client.id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <Pill v-for="grant in client.grant_types" :key="grant" class="mb-1">
                                    {{ grant }}
                                </Pill>
                            </td>
                            <td class="px-4 py-3">
                                <ul v-if="client.redirect_uris.length" class="grid gap-1">
                                    <li v-for="redirectUri in client.redirect_uris" :key="redirectUri" class="break-all font-mono text-xs">
                                        {{ redirectUri }}
                                    </li>
                                </ul>
                                <span v-else class="text-muted">None</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ client.confidential ? 'Confidential' : 'Public' }}
                            </td>
                            <td class="px-4 py-3">
                                <Pill
                                    :variant="client.revoked ? 'danger' : 'success'"
                                >
                                    {{ client.revoked ? 'Revoked' : 'Active' }}
                                </Pill>
                            </td>
                            <td v-if="can('web.manage.server_tokens.edit')" class="px-4 py-3">
                                <LinkButton
                                    v-if="!client.revoked"
                                    :href="Routes.manage.passportClients.edit(client.id)"
                                    variant="outline"
                                    size="sm"
                                >
                                    <SvgIcon :svg="Icons.pencil" />
                                    Edit
                                </LinkButton>
                            </td>
                        </tr>
                        <tr v-if="visibleClients.length === 0">
                            <td
                                :colspan="can('web.manage.server_tokens.edit') ? 6 : 5"
                                class="px-4 py-8 text-center text-muted"
                            >
                                {{ emptyMessage }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </ListPage>
    </div>
</template>
