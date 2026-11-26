<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'

interface Props {
    authenticated: boolean,
    checkoutUrl: string,
    csrfToken: string,
    loginUrl: string,
}

const props = defineProps<Props>()

const dialog = ref<HTMLDialogElement | null>(null)
const tierName = ref('???')
const subscriptionPriceId = ref('')
const oneTimePriceId = ref('')

let openButton: HTMLButtonElement | null = null

function openDonationDialog(event: MouseEvent): void {
    if (!(event.target instanceof Element)) {
        return
    }

    const button = event.target.closest<HTMLButtonElement>('[data-donation-dialog-open]')

    if (!button) {
        return
    }

    const dialogElement = dialog.value

    if (!dialogElement) {
        throw new Error('The donation dialog is not available.')
    }

    tierName.value = button.dataset.tierName ?? '???'
    subscriptionPriceId.value = button.dataset.subscriptionPriceId ?? ''
    oneTimePriceId.value = button.dataset.oneTimePriceId ?? ''
    openButton = button
    dialogElement.showModal()
}

function closeDonationDialog(): void {
    dialog.value?.close()
}

function handleDialogClick(event: MouseEvent): void {
    if (event.target === dialog.value) {
        closeDonationDialog()
    }
}

function handleDialogClose(): void {
    openButton?.focus()
    openButton = null
}

onMounted(() => {
    document.addEventListener('click', openDonationDialog)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', openDonationDialog)
})
</script>

<template>
    <dialog
        ref="dialog"
        aria-labelledby="donation-dialog-title"
        class="m-auto max-h-[calc(100dvh-2rem)] w-[min(48rem,calc(100%-2rem))] overflow-y-auto rounded-xl border-0 bg-white p-0 text-gray-900 shadow-2xl backdrop:bg-gray-950/70"
        @click="handleDialogClick"
        @close="handleDialogClose"
    >
        <header class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-5 sm:px-8">
            <h2 id="donation-dialog-title" class="text-xl font-semibold">
                {{ authenticated ? 'How do you wish to pay?' : 'Please Log-in' }}
            </h2>
            <button
                type="button"
                aria-label="Close dialog"
                class="flex size-10 shrink-0 items-center justify-center rounded-full text-2xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500"
                @click="closeDonationDialog"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </header>

        <main v-if="authenticated" class="divide-y divide-gray-200">
            <section class="px-6 py-6 sm:px-8">
                <h3 class="text-2xl font-bold">Subscription</h3>
                <p class="mt-3 mb-6 leading-loose text-gray-500">
                    A subscription allows you to automatically pay once a month (cancellable at anytime).
                    We recommend this method of payment if you wish to continuously support us without your perks expiring
                </p>
                <form :action="props.checkoutUrl" method="POST">
                    <input type="hidden" name="_token" :value="props.csrfToken">
                    <input type="hidden" name="price_id" :value="subscriptionPriceId">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-gray-900 px-6 py-4 text-sm text-gray-50 shadow-lg hover:bg-gray-800 active:bg-gray-700 sm:text-lg"
                    >
                        Purchase
                    </button>
                    <p class="mt-3 text-xs text-gray-500">You will be redirected to Stripe to complete payment</p>
                </form>
            </section>

            <section class="px-6 py-6 sm:px-8">
                <h3 class="text-2xl font-bold">One-Time</h3>
                <p class="mt-3 mb-6 leading-loose text-gray-500">
                    If you wish to pay just once, up-front, please enter the number of months of perks you would like.
                    Your perks will expire after the given number of months have elapsed (can be extended after it expires)
                </p>
                <form :action="props.checkoutUrl" method="POST">
                    <input type="hidden" name="_token" :value="props.csrfToken">
                    <label for="donation-quantity">I would like to purchase</label>
                    <div class="mt-3 mb-6 flex flex-wrap items-center gap-3 text-gray-500">
                        <input
                            id="donation-quantity"
                            class="w-20 rounded-md border border-gray-300 px-3 py-2 text-center"
                            name="quantity"
                            type="text"
                            value="1"
                            placeholder="1"
                            maxlength="3"
                        >
                        <span>months of <strong class="text-gray-900">{{ tierName }}</strong></span>
                    </div>
                    <input type="hidden" name="price_id" :value="oneTimePriceId">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-gray-900 px-6 py-4 text-sm text-gray-50 shadow-lg hover:bg-gray-800 active:bg-gray-700 sm:text-lg"
                    >
                        Purchase
                    </button>
                    <p class="mt-3 text-xs text-gray-500">You will be redirected to Stripe to complete payment</p>
                </form>
            </section>
        </main>

        <main v-else class="px-6 py-6 sm:px-8">
            You must be <a :href="props.loginUrl" class="text-amber-700 underline">logged in</a> to make a donation.
        </main>
    </dialog>
</template>
