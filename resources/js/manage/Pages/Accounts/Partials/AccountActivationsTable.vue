<script setup lang="ts">
import { AccountActivation } from '../../../Data/AccountActivation'
import { format } from '../../../Utilities/DateFormatter'
import Table from '../../../../shared/Components/Table.vue'
import TableCell from '../../../../shared/Components/TableCell.vue'
import TableHead from '../../../../shared/Components/TableHead.vue'
import TableRow from '../../../../shared/Components/TableRow.vue'

interface Props {
    activations: AccountActivation[],
}
defineProps<Props>()
</script>

<template>
    <Table v-if="activations.length > 0">
        <template #header>
            <TableRow :hoverable="false">
                <TableHead>Id</TableHead>
                <TableHead>Created At</TableHead>
                <TableHead>Expires At</TableHead>
            </TableRow>
        </template>
        <TableRow v-for="activation in activations" :key="activation.id">
            <TableCell>{{ activation.id }}</TableCell>
            <TableCell>{{ format(activation.created_at) }}</TableCell>
            <TableCell>{{ format(activation.expires_at) }}</TableCell>
        </TableRow>
    </Table>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No pending activations</span>
    </div>
</template>
