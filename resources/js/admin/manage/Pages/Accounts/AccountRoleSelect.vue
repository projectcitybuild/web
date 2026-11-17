<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import { Role } from '../../Data/Role'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import DataTable from '../../Components/DataTable.vue'

interface Props {
    success?: string,
    roles: Role[],
    account_role_ids: number[],
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    account_role_ids: props.account_role_ids,
})

const fields = [
    { key: 'select', label: 'Select' },
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'role_type', label: 'Role Type' },
]

function toggleRole(role: Role): void {
    form.account_role_ids = form.account_role_ids.includes(role.id)
        ? form.account_role_ids.filter(id => id !== role.id)
        : [...form.account_role_ids, role.id]
}

function submit() {
    form.put('/manage/accounts/' + props.account_id + '/roles')
}
</script>

<template>
    <ManageFormPage title="Account Roles" description="Choose the roles assigned to this website account." :framed="false">
        <template #actions>
            <FilledButton variant="primary" @click="submit" :disabled="form.processing">
                Update
            </FilledButton>
        </template>

        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="flex flex-col md:flex-row gap-2 mt-4">
            <section class="md:max-w-80">
                <Card padding="md">
                    <span class="text-sm text-muted">
                        Note: Removing all roles will automatically assign this user to the default (Member) role
                    </span>
                </Card>

                <FilledButton
                    variant="secondary"
                    @click="form.account_role_ids = []"
                    class="mt-4 w-full"
                >
                    Clear All Selections
                </FilledButton>
            </section>

            <section class="grow">
                <Card padding="none">
                    <DataTable :fields="fields" :rows="roles" :on-select="toggleRole">
                        <template #select="{ item }">
                            <input
                                v-model="form.account_role_ids"
                                :value="item.id"
                                :id="item.id"
                                type="checkbox"
                                class="w-4 h-4 text-primary bg-surface-muted border-border-strong rounded focus:ring-focus ring-offset-surface focus:ring-2"
                                @click.stop
                            />
                        </template>
                    </DataTable>
                </Card>
            </section>
        </div>
    </ManageFormPage>
</template>
