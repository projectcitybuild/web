<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import ServerListTable from './Partials/ServerListTable.vue'
import type { Server } from '../../Data/Server'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'

interface Props {
    servers: Server[],
    success?: string,
}

const props = defineProps<Props>()

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Servers"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div class="flex justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                <Link href="/manage/servers/create" v-if="can('web.manage.servers.edit')">
                    <FilledButton variant="primary">
                        <SvgIcon :svg="Icons.plus" />
                        Create Server
                    </FilledButton>
                </Link>
            </div>

            <ServerListTable :servers="props.servers"/>
        </Card>
    </section>
</template>
