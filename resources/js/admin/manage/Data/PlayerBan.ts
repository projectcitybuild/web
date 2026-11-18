export type PlayerBan = Omit<
    App.Http.Data.AdminGamePlayerBanData,
    'banned_player' | 'banner_player' | 'unbanner_player'
>

export const UnbanType = {
    expired: 'expired',
    manual: 'manual',
    appealed: 'appealed',
} as const satisfies Record<string, App.Domains.Bans.Data.UnbanType>
