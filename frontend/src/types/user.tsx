export interface User {
    account_id: number
    username: string
    email: string
    email_verified_at: string|undefined
    activated: boolean
    balance: number
    created_at: string
    updated_at: string
    two_factor_confirmed_at: string|undefined
    password_changed_at: string|undefined
}
