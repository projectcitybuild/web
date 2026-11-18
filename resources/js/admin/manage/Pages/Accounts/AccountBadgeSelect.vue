<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import type { Badge } from '../../Data/Badge'
import BooleanCheck from '../../Components/BooleanCheck.vue'
import FilledButton from '../../Components/FilledButton.vue'
import DataTable from '../../Components/DataTable.vue'

interface Props {
    success?: string,
    badges: Badge[],
    account_badge_ids: number[],
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    account_badge_ids: props.account_badge_ids,
})

const fields = [
    { key: 'select', label: 'Select' },
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
    { key: 'list_hidden', label: 'Hidden in List?' },
]

function submit() {
    form.put('/manage/accounts/' + props.account_id + '/badges')
}

function toggleBadge(badge: Badge): void {
    form.account_badge_ids = form.account_badge_ids.includes(badge.id)
        ? form.account_badge_ids.filter(id => id !== badge.id)
        : [...form.account_badge_ids, badge.id]
}
</script>

<template>
    <ManageFormPage title="Account Badges" description="Choose the badges assigned to this account." :framed="false">
        <template #actions>
            <FilledButton
                variant="primary"
                @click="submit"
                :disabled="!form.isDirty"
            >
                Update
            </FilledButton>
        </template>

        <section class="mt-4">
            <Card padding="none">
            <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

            <DataTable :fields="fields" :rows="badges" :on-select="toggleBadge">
                <template #select="{ item }">
                    <input
                        v-model="form.account_badge_ids"
                        :value="item.id"
                        :id="'badge-' + item.id"
                        type="checkbox"
                        class="w-4 h-4 text-primary bg-surface-muted border-border-strong rounded focus:ring-focus ring-offset-surface focus:ring-2"
                        @click.stop
                    />
                </template>
                <template #list_hidden="{ item }">
                    <BooleanCheck :value="item.list_hidden" />
                </template>
            </DataTable>
            </Card>
        </section>
    </ManageFormPage>
</template>
