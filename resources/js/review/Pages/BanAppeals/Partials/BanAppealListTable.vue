<script setup lang="ts">
import { format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
import { BanAppeal } from '../../../Data/BanAppeal'
import FilledButton from '../../../../manage/Components/FilledButton.vue'
import BooleanCheck from '../../../../manage/Components/BooleanCheck.vue'
import Pill from '../../../../manage/Components/Pill.vue'
import { Link } from '@inertiajs/vue3'

interface Props {
    banAppeals: BanAppeal[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'email', label: 'Email' },
    { key: 'is_account_verified', label: 'Account Verified' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.banAppeals.map((banAppeal) => ({
        ...banAppeal,
        created_at: format(banAppeal.created_at),
        updated_at: format(banAppeal.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #is_account_verified="{ item }">
            <BooleanCheck :value="!!item.is_account_verified" />
        </template>

        <template #status="{ item }">
            <Pill v-if="item.status === 0" variant="danger">Awaiting Review</Pill>
            <Pill v-if="item.status === 1" variant="success">Unbanned</Pill>
            <Pill v-if="item.status === 2" variant="success">Temp Banned</Pill>
            <Pill v-if="item.status === 3" variant="success">Denied</Pill>
        </template>

        <template #actions="{ item }">
            <Link :href="'/review/ban-appeals/' + item.id">
                <FilledButton variant="danger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                    </svg>
                    Review
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
