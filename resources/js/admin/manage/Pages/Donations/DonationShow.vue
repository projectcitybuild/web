<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { format } from '../../Utilities/DateFormatter'
import SvgIcon from '../../Components/SvgIcon.vue'
import { Icons } from '../../Icons'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Donations/DonationShow']>()
</script>

<template>
    <div class="space-y-6">
        <Head :title="`Donation #${donation.id}`" />

        <PageHeader :title="`Donation #${donation.id}`" description="Donation record details.">
            <template #actions>
                <LinkButton href="/manage/donations" variant="outline">
                    Donations
                </LinkButton>
                <LinkButton :href="`/manage/donations/${donation.id}/edit`" variant="primary">
                    <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                    Edit Donation
                </LinkButton>
            </template>
        </PageHeader>

        <Card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium text-muted">Account</dt>
                    <dd class="mt-1 text-sm text-foreground">
                        {{ donation.account?.username ?? (donation.account_id ? `Account #${donation.account_id}` : 'Unassigned') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">Recorded amount</dt>
                    <dd class="mt-1 text-sm text-foreground">{{ donation.amount }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">Paid amount</dt>
                    <dd class="mt-1 text-sm text-foreground">
                        {{ donation.payment
                            ? `${donation.payment.paid_unit_amount} ${donation.payment.paid_currency}`
                            : 'Not available' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">Original amount</dt>
                    <dd class="mt-1 text-sm text-foreground">
                        {{ donation.payment
                            ? `${donation.payment.original_unit_amount} ${donation.payment.original_currency}`
                            : 'Not available' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">Created</dt>
                    <dd class="mt-1 text-sm text-foreground">{{ format(donation.created_at) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-muted">Last updated</dt>
                    <dd class="mt-1 text-sm text-foreground">{{ format(donation.updated_at) }}</dd>
                </div>
            </dl>
        </Card>
    </div>
</template>