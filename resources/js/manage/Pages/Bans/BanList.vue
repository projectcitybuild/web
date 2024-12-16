<script setup>
import { ref } from 'vue'
import { useIntersectionObserver } from '@vueuse/core'
import axios from 'axios'

const props = defineProps({
    bans: Object
})

const lastElement = ref(null)
const reachedEnd = ref(false)

const loadNextPage = () => {
    axios.get(`${props.bans.path}?cursor=${props.bans.next_cursor}`).then((response) => {
        props.bans.data = [...props.bans.data, ...response.data.data]

        if (!response.next_cursor) {
            reachedEnd.value = true
            stop()
        }
    })
}

const { stop } = useIntersectionObserver(
    lastElement,
    ([{ isIntersecting }]) => {
        if (isIntersecting) loadNextPage()
    }
)

</script>

<template>
    <Head title="Manage" />

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Active</th>
                <th>Player</th>
                <th>Reason</th>
                <th>Banned By</th>
                <th>Expires At</th>
                <th>Banned At</th>
                <th>Unbanned At</th>
                <th>Unban Type</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="ban in bans.data">
                <td>{{ ban.id }}</td>
                <td></td>
                <td>{{ ban.banned_player.alias ?? ban.banned_alias_at_time }}</td>
                <td>{{ ban.reason }}</td>
                <td>{{ ban.banner_player?.alias ?? '-' }}</td>
                <td>{{ ban.expires_at ?? 'Never' }}</td>
                <td>{{ ban.created_at }}</td>
                <td>{{ ban.unbanned_at }}</td>
                <td>{{ ban.unban_type }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Invisible element to trigger loading next page -->
    <div ref="lastElement" class="-translate-y-32"></div>

    <div v-if="reachedEnd">
        Reached the End
    </div>
</template>
