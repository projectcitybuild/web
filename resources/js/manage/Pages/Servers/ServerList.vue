<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import ServerListTable from './Partials/ServerListTable.vue'
import type { Server } from '../../Data/Server'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

interface Props {
    servers: Server[],
    success?: string,
}

const props = defineProps<Props>()
</script>

<template>
    <Head title="Manage Servers"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div class="flex justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <Link href="/manage/servers/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Server
                        </FilledButton>
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <ServerListTable :servers="props.servers"/>
                </div>
            </Card>
        </div>
    </section>
</template>
