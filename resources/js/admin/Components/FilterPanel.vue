<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import FilledButton from './FilledButton.vue'
import OutlinedButton from './OutlinedButton.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Navigation/Icons'

interface Props {
    id: string,
    activeCount: number,
    applyLabel?: string,
}

const props = withDefaults(defineProps<Props>(), {
    applyLabel: 'Apply filters',
})

const emit = defineEmits(['apply', 'clear'])
const isOpen = ref(false)
const trigger = ref<HTMLButtonElement | null>(null)
const closeButton = ref<HTMLButtonElement | null>(null)
const panel = ref<HTMLElement | null>(null)
let previousBodyOverflow = ''

function closePanel() {
    isOpen.value = false
}

function applyFilters() {
    emit('apply')
    closePanel()
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        closePanel()
        return
    }

    if (event.key !== 'Tab' || panel.value === null) {
        return
    }

    const focusableElements = panel.value.querySelectorAll<HTMLElement>(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )
    const firstElement = focusableElements.item(0)
    const lastElement = focusableElements.item(focusableElements.length - 1)

    if (focusableElements.length === 0) {
        event.preventDefault()
    } else if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault()
        lastElement.focus()
    } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault()
        firstElement.focus()
    }
}

watch(isOpen, async (open) => {
    if (open) {
        previousBodyOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
        window.addEventListener('keydown', onKeydown)
        await nextTick()
        closeButton.value?.focus()
        return
    }

    window.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = previousBodyOverflow
    await nextTick()
    trigger.value?.focus()
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown)
    if (isOpen.value) {
        document.body.style.overflow = previousBodyOverflow
    }
})
</script>

<template>
    <button
        ref="trigger"
        type="button"
        :aria-expanded="isOpen"
        :aria-controls="id"
        class="inline-flex min-h-8 items-center justify-center gap-2 rounded-md border border-border bg-surface px-3 py-1 text-xs font-medium text-foreground transition-colors hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface"
        @click="isOpen = true"
    >
        <SvgIcon :svg="Icons.filter" aria-hidden="true" />
        <span>Filters</span>
        <span
            v-if="activeCount > 0"
            class="inline-flex min-w-5 items-center justify-center rounded-full bg-primary px-1.5 py-0.5 text-xs font-semibold text-primary-foreground"
            aria-label="Active filters"
        >
            {{ activeCount }}
        </span>
    </button>

    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center" @click.self="closePanel">
                <button
                    type="button"
                    class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"
                    aria-label="Close filters"
                    @click="closePanel"
                />

                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out motion-reduce:transition-none"
                    enter-from-class="translate-y-full sm:translate-y-4 sm:scale-[0.98]"
                    enter-to-class="translate-y-0 sm:scale-100"
                    leave-active-class="transition duration-150 ease-in motion-reduce:transition-none"
                    leave-from-class="translate-y-0 sm:scale-100"
                    leave-to-class="translate-y-full sm:translate-y-4 sm:scale-[0.98]"
                >
                    <section
                        :id="id"
                        ref="panel"
                        role="dialog"
                        aria-modal="true"
                        :aria-labelledby="`${id}-title`"
                        tabindex="-1"
                        class="relative flex h-[min(85dvh,48rem)] w-full max-w-xl flex-col rounded-t-lg border border-border bg-surface shadow-2xl outline-hidden sm:rounded-lg"
                    >
                        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-border px-5 py-4">
                            <div>
                                <h2 :id="`${id}-title`" class="text-base font-semibold text-foreground">Filters</h2>
                                <p class="mt-0.5 text-sm text-muted">
                                    {{ activeCount ? `${activeCount} active` : 'No filters selected' }}
                                </p>
                            </div>
                            <button
                                ref="closeButton"
                                type="button"
                                class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg text-muted transition-colors hover:bg-surface-hover hover:text-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                                aria-label="Close filters"
                                title="Close filters"
                                @click="closePanel"
                            >
                                <SvgIcon :svg="Icons.close" aria-hidden="true" />
                            </button>
                        </header>

                        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-5">
                            <slot />
                        </div>

                        <footer class="flex shrink-0 gap-3 border-t border-border bg-surface px-5 py-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                            <OutlinedButton
                                variant="secondary"
                                class="min-h-11 flex-1 justify-center"
                                :disabled="activeCount === 0"
                                @click="emit('clear')"
                            >
                                Clear all
                            </OutlinedButton>
                            <FilledButton
                                variant="primary"
                                class="min-h-11 flex-1 justify-center"
                                @click="applyFilters"
                            >
                                {{ props.applyLabel }}
                            </FilledButton>
                        </footer>
                    </section>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
