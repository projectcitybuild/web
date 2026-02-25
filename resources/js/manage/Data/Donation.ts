import { Account } from './Account'
import { Payment } from './Payment'

export interface Donation {
    donation_id: number,
    account?: Account,
    payment?: Payment,
    amount: number,
    created_at: string,
    updated_at: string,
}
