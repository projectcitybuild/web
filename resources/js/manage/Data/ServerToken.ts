export interface ServerToken {
    id: number,
    token: string,
    description: string,
    allowed_ips?: string,
    created_at: string,
    updated_at: string,
}
