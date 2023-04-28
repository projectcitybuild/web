export abstract class Routes {
    static readonly HOME = '/'
    static readonly LOGIN = '/login'
    static readonly LOGOUT = '/logout'
    static readonly REGISTER = '/register'
    static readonly FORGOT_PASSWORD = '/forgot-password'
    static readonly VERIFY_EMAIL = '/verify-email'
    static readonly PASSWORD_CONFIRM = '/password-confirm'

    static readonly DASHBOARD = '/dashboard'
    static readonly SECURITY = '/dashboard/security'
    static readonly CHANGE_EMAIL = '/dashboard/security/change-email'
    static readonly TWO_FACTOR_AUTH = '/dashboard/security/2fa'
}