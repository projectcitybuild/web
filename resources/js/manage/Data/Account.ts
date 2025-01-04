import { Player } from './Player'
import { Badge } from './Badge'
import { Group } from './Group'
import { AccountActivation } from './AccountActivation'

export interface Account {
    account_id: number,
    username: string,
    email: string,
    activated: boolean,
    is_totp_enabled: boolean,
    minecraft_account?: Player[],
    badges?: Badge[],
    groups?: Group[],
    activations?: AccountActivation[],
    created_at: string,
    updated_at: string,
}
