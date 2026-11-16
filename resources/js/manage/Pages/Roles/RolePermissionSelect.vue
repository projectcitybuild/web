<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Card from '../../Components/Card.vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import FormErrorSummary from '../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../Components/Forms/FormField.vue'
import FormNotice from '../../Components/Forms/FormNotice.vue'
import FormSection from '../../Components/Forms/FormSection.vue'
import { useUnsavedChangesWarning } from '../../Components/Forms/useUnsavedChangesWarning'
import FilledButton from '../../Components/FilledButton.vue'
import { Permission } from '../../Data/Permission'
import Input from '../../../shared/Components/Input.vue'
import Table from '../../../shared/Components/Table.vue'
import TableCell from '../../../shared/Components/TableCell.vue'
import TableHead from '../../../shared/Components/TableHead.vue'
import TableRow from '../../../shared/Components/TableRow.vue'

interface Props {
    success?: string,
    permissions: Permission[],
    role_permission_ids: number[],
    role_id: number,
}

interface PermissionGroup {
    name: string,
    permissions: Permission[],
}

const props = defineProps<Props>()
const form = useForm({
    role_permission_ids: props.role_permission_ids,
})
const search = ref('')

const filteredPermissions = computed(() => {
    const query = search.value.trim().toLowerCase()

    return props.permissions.filter(permission => permission.name.toLowerCase().includes(query))
})
const permissionGroups = computed<PermissionGroup[]>(() => {
    const groups = new Map<string, Permission[]>()

    for (const permission of filteredPermissions.value) {
        const segments = permission.name.split('.')
        const category = segments[0] === 'web' && segments[1] === 'manage'
            ? segments[2] ?? 'other'
            : 'other'
        const permissions = groups.get(category) ?? []

        permissions.push(permission)
        groups.set(category, permissions)
    }

    return Array.from(groups, ([category, permissions]) => ({
        name: category
            .replace(/_/g, ' ')
            .replace(/\b\w/g, character => character.toUpperCase()),
        permissions,
    }))
})
const selectedCount = computed(() => form.role_permission_ids.length)
const allSelected = computed(() => props.permissions.length > 0
    && selectedCount.value === props.permissions.length)

useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        form.put(`/manage/roles/${props.role_id}/permissions`)
    }
}

function selectAll(): void {
    form.role_permission_ids = props.permissions.map(permission => permission.id)
}

function deselectAll(): void {
    form.role_permission_ids = []
}
</script>

<template>
    <ManageFormPage
        title="Role Permissions"
        description="Choose the permissions granted by this role."
        :framed="false"
    >
        <template #actions>
            <FilledButton
                variant="primary"
                :loading="form.processing"
                :disabled="!form.isDirty"
                @click="submit"
            >
                Save permissions
            </FilledButton>
        </template>

        <div class="grid gap-6 xl:grid-cols-[minmax(16rem,0.7fr)_minmax(0,1.8fr)]">
            <aside class="grid content-start gap-4">
                <Card padding="md">
                    <p class="text-sm text-muted">
                        Roles do not inherit permissions assigned to other roles.
                    </p>
                    <p class="mt-3 text-sm font-medium text-foreground" aria-live="polite">
                        {{ selectedCount }} of {{ permissions.length }} permissions selected
                    </p>
                </Card>

                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                    <FilledButton
                        variant="secondary"
                        type="button"
                        :disabled="allSelected"
                        @click="selectAll"
                    >
                        Select all permissions
                    </FilledButton>
                    <FilledButton
                        variant="secondary"
                        type="button"
                        :disabled="selectedCount === 0"
                        @click="deselectAll"
                    >
                        Deselect all permissions
                    </FilledButton>
                </div>
            </aside>

            <section class="grid min-w-0 gap-4">
                <FormErrorSummary :errors="form.errors" />
                <FormNotice v-if="success || form.recentlySuccessful" type="success">
                    {{ success || 'Permissions saved successfully.' }}
                </FormNotice>

                <FormSection
                    title="Available permissions"
                    description="Filter by permission name, then select the permissions this role should grant."
                    framed
                >
                    <FormField id="permission-search" label="Filter permissions">
                        <Input
                            id="permission-search"
                            v-model="search"
                            type="search"
                            placeholder="Search permissions..."
                        />
                    </FormField>

                    <p v-if="filteredPermissions.length === 0" class="rounded-md bg-surface-muted p-4 text-sm text-muted">
                        No permissions match this search.
                    </p>

                    <div v-for="group in permissionGroups" :key="group.name" class="grid gap-3">
                        <h3 class="text-sm font-semibold text-foreground">{{ group.name }}</h3>
                        <Card padding="none">
                            <Table>
                                <template #header>
                                    <TableRow :hoverable="false">
                                        <TableHead align="center">Select</TableHead>
                                        <TableHead>Permission</TableHead>
                                    </TableRow>
                                </template>
                                <TableRow v-for="permission in group.permissions" :key="permission.id">
                                    <TableCell align="center">
                                        <input
                                            v-model="form.role_permission_ids"
                                            :value="permission.id"
                                            :id="`permission-${permission.id}`"
                                            type="checkbox"
                                            class="size-4 rounded border-border-strong accent-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                        >
                                    </TableCell>
                                    <TableCell>
                                        <label :for="`permission-${permission.id}`" class="cursor-pointer break-all text-foreground">
                                            {{ permission.name }}
                                        </label>
                                    </TableCell>
                                </TableRow>
                            </Table>
                        </Card>
                    </div>
                </FormSection>
            </section>
        </div>
    </ManageFormPage>
</template>
