<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BanForm from './Partials/IPBanForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { IPBan } from '../../Data/IPBan'

interface Props {
    ban: IPBan,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<IPBan>) {
    form.put('/manage/ip-bans/' + props.ban.id)
}
</script>

<template>
    <section>
        <Head title="Edit an IP Ban"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/ip-bans" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit an IP Ban</h2>
                <div class="text-sm text-gray-500">Prevent an IP address from connecting to our server. This should
                    be a last resort as IP address bans are extremely unreliable
                </div>
                <hr class="my-6"/>

                <BanForm :ban="props.ban" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
