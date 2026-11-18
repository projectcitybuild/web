<script setup lang="ts">
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import LinkButton from '../../../../shared/Components/LinkButton.vue'

interface Props {
    permissions: App.Http.Data.AdminPermissionData[],
}
defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="permissions">
        <template #actions="{ item }" v-if="can('web.manage.permissions.edit')">
            <LinkButton
                variant="outline"
                size="md"
                :href="'/manage/permissions/' + item.id + '/edit'"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
