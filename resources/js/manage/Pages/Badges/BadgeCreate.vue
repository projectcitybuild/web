<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BadgeForm from './Partials/BadgeForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { Badge } from '../../Data/Badge'

function submit(form: InertiaForm<Badge>) {
    form.post('/manage/badges')
}
</script>

<template>
    <section>
        <Head title="Create a Badge"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/badges" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Badge</h2>
                <div class="text-sm text-gray-500">Badges are publicly displayable accolades that can be awarded to
                    players
                </div>
                <hr class="my-6"/>

                <BadgeForm :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
