<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { EmailChangeRequest } from '../../../Data/EmailChangeRequest'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { ref } from 'vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import { router } from '@inertiajs/vue3'

interface Props {
    requests: EmailChangeRequest[],
    account_id: number,
}
const props = defineProps<Props>()

const approveModal = ref<InstanceType<typeof ConfirmDialog>>()
const approveId = ref(null)

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

        <table v-if="requests.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">Id</th>
                <th scope="col" class="px-4 py-3">New Email</th>
                <th scope="col" class="px-4 py-3">Created At</th>
                <th scope="col" class="px-4 py-3">Expires At</th>
                <th scope="col" class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b dark:border-gray-700" v-for="request in requests">
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ request.id }}</td>
                <td class="px-4 py-3">{{ request.email }}</td>
                <td class="px-4 py-3">{{ format(request.created_at) }}</td>
                <td class="px-4 py-3">{{ format(request.expires_at) }}</td>
                <td class="px-4 py-1 flex justify-end">
                    <OutlinedButton variant="danger" @click="showDialog(request.id)">
                        Approve
                    </OutlinedButton>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-else class="flex p-4">
            <span class="text-sm text-gray-400">No pending email changes</span>
        </div>
    </div>
</template>
