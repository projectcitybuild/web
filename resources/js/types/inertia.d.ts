import '@inertiajs/core'

declare module '@inertiajs/core' {
    interface InertiaConfig {
        sharedPageProps: Pick<App.Http.Data.AdminSharedPagePropsData, 'is_admin' | 'permissions'>
            & Partial<Omit<App.Http.Data.AdminSharedPagePropsData, 'is_admin' | 'permissions'>>
    }
}

export {}
