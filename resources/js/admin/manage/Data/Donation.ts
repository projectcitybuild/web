import { Payment } from './Payment'

export interface Donation {
    id: number,
    account?: App.Http.Data.AdminAccountData,
    payment?: Payment,
    amount: number,
    created_at: string,
    updated_at: string,
}
