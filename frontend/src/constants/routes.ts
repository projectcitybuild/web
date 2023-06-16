export abstract class Routes {
  static readonly HOME = '/'
  static readonly MAPS = '/maps'
  static readonly LOGIN = '/login'
  static readonly LOGOUT = '/logout'
  static readonly REGISTER = '/register'
  static readonly FORGOT_PASSWORD = '/forgot-password'
  static readonly VERIFY_EMAIL = '/verify-email'
  static readonly VERIFY_2FA = '/2fa-challenge'
  static readonly PASSWORD_CONFIRM = '/password-confirm'

  static readonly DASHBOARD = '/dashboard'
  static readonly SECURITY = '/dashboard/security'
  static readonly CHANGE_EMAIL = '/dashboard/security/change-email'
  static readonly CHANGE_PASSWORD = '/dashboard/security/change-password'
  static readonly TWO_FACTOR_AUTH = '/dashboard/security/2fa'
}