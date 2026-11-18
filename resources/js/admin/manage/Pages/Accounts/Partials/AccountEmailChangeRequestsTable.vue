<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { EmailChangeRequest } from '../../../Data/EmailChangeRequest'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { ref } from 'vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import { router } from '@inertiajs/vue3'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    requests: EmailChangeRequest[],
    account_id: number,
}
const props = defineProps<Props>()

const approveModal = ref<InstanceType<typeof ConfirmDialog>>()
const approveId = ref<number | null>(null)
const fields = [
    { key: 'id', label: 'ID' },
    { key: 'email', label: 'New Email' },
    { key: 'created_at', label: 'Created At' },
    { key: 'expires_at', label: 'Expires At' },
]

function showDialog(id: number) {
    approveId.value = id
    approveModal.value?.open()
}

function forceApprove() {
    approveModal.value?.close()
    router.post('/manage/accounts/' + props.account_id + '/email-change/' + approveId.value + '/approve')
}
</script>

<template>
    <div>
        <ConfirmDialog
            ref="approveModal"
            message="This will force approve their email address change without proof that it's valid or belongs to them."
            confirm-title="Yes, Proceed"
            :on-confirm="forceApprove"
        />

        <DataTable :fields="fields" :rows="requests" empty-message="No pending email changes">
            <template #created_at="{ item }">
                {{ format(item.created_at) }}
            </template>
            <template #expires_at="{ item }">
                {{ format(item.expires_at) }}
            </template>
            <template #actions="{ item }">
                <OutlinedButton variant="danger" size="sm" @click="showDialog(item.id)">
                    Approve
                </OutlinedButton>
            </template>
        </DataTable>
    </div>
</template>
