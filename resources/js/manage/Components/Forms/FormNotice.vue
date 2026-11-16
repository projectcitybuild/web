<script setup lang="ts">
import { computed } from 'vue'

type NoticeType = 'info' | 'success' | 'warning' | 'danger'

interface Props {
    type?: NoticeType,
    title?: string,
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
})

const noticeClasses = computed(() => ({
    info: 'border-info bg-info-soft text-info',
    success: 'border-success bg-success-soft text-success',
    warning: 'border-warning bg-warning-soft text-warning',
    danger: 'border-danger bg-danger-soft text-danger',
}[props.type]))
</script>

<template>
    <aside class="rounded-lg border-s-4 p-4 text-sm" :class="noticeClasses" :role="type === 'danger' ? 'alert' : 'status'">
        <p v-if="title" class="font-semibold">{{ title }}</p>
        <div :class="title ? 'mt-1' : ''">
            <slot />
        </div>
    </aside>
</template>
