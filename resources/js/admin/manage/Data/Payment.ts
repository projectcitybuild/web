export interface Payment {
    payment_id: number,
    paid_currency: string,
    paid_unit_amount: number,
    original_currency: string,
    original_unit_amount: number,
    unit_quantity: number,
    created_at: string,
    updated_at: string,
}
