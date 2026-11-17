export interface EmailChangeRequest {
    id: number,
    email: string,
    token: string,
    created_at: string,
    updated_at: string,
    expires_at: string,
}
