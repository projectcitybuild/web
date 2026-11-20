<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { ref } from 'vue'
import FilledButton from '../../../../Components/FilledButton.vue'
import FormField from '../../../../Components/Forms/FormField.vue'
import FormSection from '../../../../Components/Forms/FormSection.vue'
import Input from '../../../../Components/Input.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import Select from '../../../../Components/Select.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import ConfirmDialog from '../../../../Components/ConfirmDialog.vue'
import usePermissions from '../../../../Composables/usePermissions'
import { Routes } from '../../../../Navigation/Routes'
import RoleManagementTabs from '../Roles/Partials/RoleManagementTabs.vue'
import Pill from '../../../../Components/Pill.vue'

interface RoleOption {
    id: number,
    name: string,
}

interface RoleMapping {
    id: number,
    role_id: number,
    role_name: string,
    discord_role_id: string,
    enabled: boolean,
}

interface Props {
    guild_id: string | null,
    guild_configured: boolean,
    roles: RoleOption[],
    mappings: RoleMapping[],
    success?: string,
}

const props = defineProps<AdminSharedPageProps & Props>()
const { can } = usePermissions()
const form = useForm<{ role_id: number | string, discord_role_id: string }>({
    role_id: '',
    discord_role_id: '',
})
const deleteDialog = ref<InstanceType<typeof ConfirmDialog>>()
const mappingToDelete = ref<RoleMapping | null>(null)

const availableRoles = computed(() => props.roles.filter(
    role => !props.mappings.some(mapping => mapping.role_id === role.id),
))
const deleteMessage = computed(() => {
    const mapping = mappingToDelete.value

    if (mapping === null) {
        return 'This mapping will stop managing its Discord role. The role may remain assigned to members who already have it.'
    }

    return `Delete the mapping for ${mapping.role_name}? This stops managing Discord role ${mapping.discord_role_id}. The role may remain assigned to members who already have it. Disable the mapping instead if you need the bot to remove it.`
})

function createMapping(): void {
    if (form.processing) {
        return
    }

    form.post(Routes.manage.discordRoleMappings.list, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}

function toggleMapping(mapping: RoleMapping): void {
    router.put(Routes.manage.discordRoleMappings.update(mapping.id), {
        enabled: !mapping.enabled,
    }, {
        preserveScroll: true,
    })
}

function requestDeleteMapping(mapping: RoleMapping): void {
    mappingToDelete.value = mapping
    deleteDialog.value?.open()
}

function deleteMapping(): void {
    const mapping = mappingToDelete.value

    deleteDialog.value?.close()

    if (mapping === null) {
        return
    }

    router.delete(Routes.manage.discordRoleMappings.delete(mapping.id), {
        preserveScroll: true,
        onFinish: () => {
            mappingToDelete.value = null
        },
    })
}
</script>

<template>
    <Head title="Discord Role Mappings" />

    <div class="grid gap-6">
        <PageHeader
            title="Roles"
            description="Manage roles and the access granted to their members."
        />

        <RoleManagementTabs active-tab="discord-role-mappings" />

        <SuccessAlert v-if="success" :message="success" />

        <p v-if="!guild_configured" class="rounded-lg bg-danger-soft p-4 text-sm text-danger" role="alert">
            Configure a valid DISCORD_GUILD_ID before managing role mappings.
        </p>
        <p v-else class="text-sm text-muted">
            Configured guild: <span class="font-mono">{{ guild_id }}</span>
        </p>

        <FormSection
            v-if="can('web.manage.roles.edit') && guild_configured"
            title="Add mapping"
            description="Each PCB role and Discord role can be mapped once per guild. Disabled mappings remain managed so obsolete roles can be removed."
            framed
        >
            <form class="grid gap-4 items-start sm:grid-cols-2" @submit.prevent="createMapping">
                <FormField
                    id="role_id"
                    label="PCB role"
                    :error="form.errors.role_id"
                    required
                    v-slot="field"
                >
                    <Select v-bind="field" v-model="form.role_id" placeholder="Select a role">
                        <option v-for="role in availableRoles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </Select>
                </FormField>

                <FormField
                    id="discord_role_id"
                    label="Discord role ID"
                    description="Use the role's numeric ID from Discord."
                    :error="form.errors.discord_role_id"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.discord_role_id" inputmode="numeric" />
                </FormField>

                <div class="sm:col-span-2">
                    <FilledButton
                        variant="primary"
                        type="submit"
                        :disabled="form.processing || availableRoles.length === 0"
                        :loading="form.processing"
                    >
                        Add mapping
                    </FilledButton>
                </div>
            </form>
        </FormSection>

        <section class="overflow-x-auto rounded-lg border border-border">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-muted text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">PCB role</th>
                        <th class="px-4 py-3 font-medium">Discord role ID</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th v-if="can('web.manage.roles.edit')" class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="mapping in mappings" :key="mapping.id">
                        <td class="px-4 py-3">{{ mapping.role_name }}</td>
                        <td class="px-4 py-3 font-mono">{{ mapping.discord_role_id }}</td>
                        <td class="px-4 py-3">
                            <Pill :variant="mapping.enabled ? 'success' : 'danger'">
                                {{ mapping.enabled ? 'Enabled' : 'Disabled' }}
                            </Pill>
                        </td>
                        <td v-if="can('web.manage.roles.edit')" class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <FilledButton
                                    variant="secondary"
                                    size="sm"
                                    @click="toggleMapping(mapping)"
                                >
                                    {{ mapping.enabled ? 'Disable' : 'Enable' }}
                                </FilledButton>
                                <OutlinedButton
                                    variant="danger"
                                    @click="requestDeleteMapping(mapping)"
                                >
                                    Delete
                                </OutlinedButton>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="mappings.length === 0">
                        <td
                            :colspan="can('web.manage.roles.edit') ? 4 : 3"
                            class="px-4 py-8 text-center text-muted"
                        >
                            No Discord role mappings are configured.
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <ConfirmDialog
            ref="deleteDialog"
            title="Delete Discord role mapping?"
            :message="deleteMessage"
            confirm-title="Delete mapping"
            :on-confirm="deleteMapping"
        />
    </div>
</template>
