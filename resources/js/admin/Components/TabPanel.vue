<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    tabId: string,
    idPrefix: string,
    activeTab: string,
}

const props = defineProps<Props>()
const isActive = computed(() => props.activeTab === props.tabId)
</script>

<template>
    <section
        v-if="isActive"
        :id="idPrefix + '-panel-' + tabId"
        role="tabpanel"
        :aria-labelledby="idPrefix + '-tab-' + tabId"
    >
        <slot />
    </section>
</template>
