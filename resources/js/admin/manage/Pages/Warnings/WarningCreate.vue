<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarningForm from './Partials/WarningForm.vue'
import { Warning } from 'postcss'

function submit(form: InertiaForm<Warning>) {
    form.post('/manage/warnings')
}
</script>

<template>
    <ManageFormPage title="Create Warning" description="Record an infraction against a Minecraft player.">
        <WarningForm :submit="submit" />
    </ManageFormPage>
</template>
