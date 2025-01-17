import { Account } from './Account'

export interface BuilderRankApplication {
    id: number,
    account_id: number,
    account?: Account,
    minecraft_alias: string,
    current_builder_rank: string,
    build_location: string,
    build_description: string,
    additional_notes?: string,
    status: BuilderRankApplicationStatus,
    denied_reason?: string,
    closed_at?: string,
    created_at: string,
    updated_at: string,
}

export enum BuilderRankApplicationStatus {
    pending = 1,
    approved,
    denied,
}
