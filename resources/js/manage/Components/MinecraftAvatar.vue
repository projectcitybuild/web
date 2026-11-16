<script setup lang="ts">
import { computed, ref } from 'vue'
import { Result } from 'typescript-result'
import Spinner from './Spinner.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Icons'

interface Props {
    // Usernames are accepted as well, but are apparently more
    // susceptible to rate limiting
    uuid: string,
    size?: number,
}

const props = defineProps<Props>()

const loaded = ref<Result<void | null, Error>>(null)

const size = computed(() => props.size ?? 128)
const url = computed(() => `https://minotar.net/helm/${props.uuid}/${size.value}.png`)

function didLoad() {
    loaded.value = Result.ok()
}

function didFail(error: Error) {
    loaded.value = Result.error(error)
}

</script>

<template>
    <div
        class="bg-gray-50 flex justify-center items-center"
        :style="{width: props.size + 'px', height: props.size + 'px'}"
    >
        <Spinner v-if="loaded == null"/>

        <img
            v-show="loaded?.isOk()"
            :src="url"
            :width="size"
            @load="didLoad"
            @error="didFail"
            :alt="props.uuid"
        />

        <SvgIcon v-if="loaded?.isError()" :svg="Icons.close" class="size-6 text-gray-400" aria-hidden="true" />
    </div>
</template>
