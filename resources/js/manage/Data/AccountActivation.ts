import { Player } from './Player'
import { Badge } from './Badge'
import { Role } from './Role'

export interface AccountActivation {
    id: number,
    created_at: string,
    updated_at: string,
    expires_at: string,
}
