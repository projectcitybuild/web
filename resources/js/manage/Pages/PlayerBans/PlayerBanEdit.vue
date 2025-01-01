<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BanForm from './Partials/PlayerBanForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { PlayerBan } from '../../Data/PlayerBan'

interface Props {
    ban: PlayerBan,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<PlayerBan>) {
    form.put('/manage/player-bans/' + props.ban.id)
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Edit a Player Ban"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/player-bans" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Player Ban</h2>
                    <div class="text-sm text-gray-500">Prevent a Minecraft UUID from connecting to our server</div>
                    <hr class="my-6"/>

                    <BanForm :ban="props.ban" :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
