import { Player } from './Player'
import { Badge } from './Badge'

export interface Account {
    account_id: number,
    username: string,
    email: string,
    activated: boolean,
    is_totp_enabled: boolean,
    minecraft_account?: Player[],
    badges?: Badge[],
    created_at: string,
    updated_at: string,
}
