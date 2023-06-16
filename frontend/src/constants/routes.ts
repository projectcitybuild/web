export abstract class Routes {
  static readonly HOME = '/'
  static readonly MAPS = '/maps'

  static readonly LOGIN = '/auth/login'
  static readonly LOGOUT = '/auth/logout'
  static readonly REGISTER = '/auth/register'
  static readonly FORGOT_PASSWORD = '/auth/forgot-password'
  static readonly VERIFY_EMAIL = '/auth/verify-email'
  static readonly VERIFY_2FA = '/auth/2fa-challenge'
  static readonly PASSWORD_CONFIRM = '/auth/password-confirm'

  static readonly DASHBOARD = '/dashboard'
  static readonly SECURITY = '/dashboard/security'
  static readonly CHANGE_EMAIL = '/dashboard/security/change-email'
  static readonly CHANGE_PASSWORD = '/dashboard/security/change-password'
  static readonly TWO_FACTOR_AUTH = '/dashboard/security/2fa'
}