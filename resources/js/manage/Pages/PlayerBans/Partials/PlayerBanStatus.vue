<script setup lang="ts">
import Pill from '../../../Components/Pill.vue'
import { PlayerBan } from '../../../Data/PlayerBan'
import { compareAsc } from 'date-fns'
import { computed } from 'vue'

interface Props {
    ban: PlayerBan,
}
const props = defineProps<Props>()

const isActive = computed(() => {
    if (props.ban.unbanned_at) return false
    if (!props.ban.expires_at) return true

    const expiresAt = new Date(props.ban.expires_at)
    const now = new Date()

    // 1 = left date is after right date
    return (compareAsc(expiresAt, now) == 1)
})
</script>

<template>
    <Pill :variant="isActive ? 'danger' : null">
        {{ isActive ? 'Active' : 'Expired' }}
    </Pill>
</template>
