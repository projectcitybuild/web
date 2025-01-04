<script setup lang="ts">
import { computed } from 'vue'

type Icon = 'plus' | 'pencil' | 'cloud-push'

interface Props {
    icon: Icon,
}
const props = defineProps<Props>()

const path = computed(() => {
    switch (props.icon) {
        case 'plus':
            return 'M12 4.5v15m7.5-7.5h-15'
        case 'pencil':
            return 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125'
        case 'cloud-push':
            return 'M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z'
    }
})
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            :d="path"
        />
    </svg>
</template>
