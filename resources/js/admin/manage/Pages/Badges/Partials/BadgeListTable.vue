<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import LinkButton from '../../../../shared/Components/LinkButton.vue'

interface Props {
    badges: App.Http.Data.AdminAccountBadgeData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
    { key: 'list_hidden', label: 'Hidden in List' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.badges.map((badge) => ({
        ...badge,
        created_at: format(badge.created_at),
        updated_at: format(badge.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #list_hidden="{ item }">
            <BooleanCheck :value="item.list_hidden" />
        </template>

        <template #actions="{ item }" v-if="can('web.manage.badges.edit')">
            <LinkButton
                :href="'/manage/badges/' + item.id + '/edit'"
                variant="outline"
                size="sm"
            >
                <SvgIcon :svg="Icons.pencil" class="size-4" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
