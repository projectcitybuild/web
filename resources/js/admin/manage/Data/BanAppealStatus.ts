export const BanAppealStatus = {
    pending: 0,
    unbanned: 1,
    tempBanned: 2,
    denied: 3,
} as const satisfies Record<string, App.Domains.BanAppeals.Data.BanAppealStatus>
