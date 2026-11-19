<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue'

interface Props {
    errors: Record<string, string>,
}

const props = defineProps<Props>()
const summary = ref<HTMLElement>()

const entries = computed(() => Object.entries(props.errors).filter(([, message]) => message.length > 0))

watch(entries, async (currentEntries) => {
    if (currentEntries.length === 0) {
        return
    }

    await nextTick()
    summary.value?.focus()
}, { flush: 'post' })

function focusField(id: string): void {
    document.getElementById(id)?.focus()
}
</script>

<template>
    <section
        v-if="entries.length"
        ref="summary"
        tabindex="-1"
        role="alert"
        aria-labelledby="form-error-summary-title"
        class="rounded-lg border border-danger bg-danger-soft p-4 text-sm text-danger focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-danger"
    >
        <h2 id="form-error-summary-title" class="font-semibold">
            Please correct the following errors
        </h2>
        <ul class="mt-2 grid list-inside list-disc gap-1">
            <li v-for="[field, message] in entries" :key="field">
                <a
                    :href="`#${field}`"
                    class="underline underline-offset-2 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-danger"
                    @click.prevent="focusField(field)"
                >
                    {{ message }}
                </a>
            </li>
        </ul>
    </section>
</template>
