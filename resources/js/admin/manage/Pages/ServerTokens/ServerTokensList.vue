<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import ServerTokenListTable from './Partials/ServerTokenListTable.vue'
import { ServerToken } from '../../Data/ServerToken'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    tokens: ServerToken[],
    success?: string,
}

const props = defineProps<Props>()

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Server Tokens"/>

    <div class="space-y-6">
    <PageHeader title="Server Tokens" description="Manage credentials used by trusted applications to access the PCB API.">
        <template #actions>
            <LinkButton v-if="can('web.manage.server_tokens.edit')" href="/manage/server-tokens/create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Token
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <Card padding="none">
            <ServerTokenListTable :tokens="props.tokens"/>
        </Card>
    </section>
    </div>
</template>
