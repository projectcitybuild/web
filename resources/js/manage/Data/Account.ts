import { Player } from './Player'

export interface Account {
    account_id: number,
    username: string,
    email: string,
    activated: boolean,
    is_totp_enabled: boolean,
    minecraft_account?: Player[],
    created_at: string,
    updated_at: string,
}
