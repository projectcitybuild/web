<script setup>
import SideBar from './Partials/SideBar.vue'
import RootLayout from './RootLayout.vue'
</script>

<template>
    <RootLayout>
        <template #side-bar>
            <SideBar />
        </template>

        <template #default>
            <slot />
        </template>
    </RootLayout>
</template>
