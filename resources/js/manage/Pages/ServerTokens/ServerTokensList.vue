<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import ServerTokenListTable from './Partials/ServerTokenListTable.vue'
import { ServerToken } from '../../Data/ServerToken'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

interface Props {
    tokens: ServerToken[],
    success?: string,
}

const props = defineProps<Props>()
</script>

<template>
    <Head title="Manage Server Tokens"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div class="flex justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                <Link href="/manage/server-tokens/create">
                    <FilledButton variant="primary">
                        <SvgIcon icon="plus" />
                        Create Token
                    </FilledButton>
                </Link>
            </div>

            <ServerTokenListTable :tokens="props.tokens"/>
        </Card>
    </section>
</template>
