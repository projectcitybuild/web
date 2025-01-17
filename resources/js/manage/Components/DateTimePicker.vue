<script setup lang='ts'>
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const model = defineModel<Date>()
</script>

<style>
:root {
    --dp-border-radius: 8px;
}
</style>

<template>
    <VueDatePicker
        auto-apply
        v-model="model"
    />
</template>
