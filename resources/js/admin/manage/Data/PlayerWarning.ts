export type PlayerWarning = Omit<
    App.Http.Data.AdminPlayerWarningData,
    'warned_player' | 'warner_player'
>
