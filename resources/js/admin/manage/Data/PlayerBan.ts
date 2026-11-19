export type PlayerBan = Omit<
    App.Http.Data.AdminGamePlayerBanData,
    'banned_player' | 'banner_player' | 'unbanner_player'
>
