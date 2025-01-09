import { Account } from './Account'

export interface Donation {
    donation_id: number,
    account?: Account,
    amount: number,
    created_at: string,
    updated_at: string,
}
