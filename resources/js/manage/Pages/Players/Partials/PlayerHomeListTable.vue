<script setup lang="ts">
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { format } from '../../../Utilities/DateFormatter'
import { Home } from '../../../Data/Home';
import usePermissions from '../../../Composables/usePermissions';
import OutlinedButton from '../../../Components/OutlinedButton.vue';
import SvgIcon from '../../../Components/SvgIcon.vue';
import { Icons } from '../../../Icons';
import { Link } from '@inertiajs/vue3';
import Table from '../../../../shared/Components/Table.vue'
import TableCell from '../../../../shared/Components/TableCell.vue'
import TableHead from '../../../../shared/Components/TableHead.vue'
import TableRow from '../../../../shared/Components/TableRow.vue'

const { can } = usePermissions()

interface Props {
    homes: Home[],
}
defineProps<Props>()
</script>

<template>
    <Table density="compact">
        <template #header>
            <TableRow :hoverable="false">
                <TableHead>ID</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>World</TableHead>
                <TableHead>x</TableHead>
                <TableHead>y</TableHead>
                <TableHead>z</TableHead>
                <TableHead>Pitch</TableHead>
                <TableHead>Yaw</TableHead>
                <TableHead>Created At</TableHead>
                <TableHead v-if="can('web.manage.homes.edit')" align="end">Actions</TableHead>
            </TableRow>
        </template>
        <TableRow v-for="home in homes" :key="home.id">
            <TableCell>{{ home.id }}</TableCell>
            <TableCell>{{ home.name }}</TableCell>
            <TableCell>{{ home.world }}</TableCell>
            <TableCell>{{ Math.round(home.x) }}</TableCell>
            <TableCell>{{ Math.round(home.y) }}</TableCell>
            <TableCell>{{ Math.round(home.z) }}</TableCell>
            <TableCell>{{ Number(home.pitch).toFixed(2) }}</TableCell>
            <TableCell>{{ Number(home.yaw).toFixed(2) }}</TableCell>
            <TableCell>{{ format(home.created_at) }}</TableCell>
            <TableCell v-if="can('web.manage.homes.edit')" align="end">
                    <LinkButton :href="'/manage/homes/' + home.id + '/edit'" v-if="can('web.manage.homes.edit')" variant="outline" size="sm">
                        <SvgIcon :svg="Icons.pencil" />
                        Edit
                    </LinkButton>
            </TableCell>
        </TableRow>
    </Table>
</template>
