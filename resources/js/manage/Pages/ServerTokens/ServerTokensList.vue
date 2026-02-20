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

interface Props {
    tokens: ServerToken[],
    success?: string,
}

const props = defineProps<Props>()

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Server Tokens"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div class="flex justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                <Link href="/manage/server-tokens/create" v-if="can('web.manage.server_tokens.edit')">
                    <FilledButton variant="primary">
                        <SvgIcon :svg="Icons.plus" />
                        Create Token
                    </FilledButton>
                </Link>
            </div>

            <ServerTokenListTable :tokens="props.tokens"/>
        </Card>
    </section>
</template>
