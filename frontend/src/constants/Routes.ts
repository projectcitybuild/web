export abstract class Routes {
  static readonly HOME = '/'
  static readonly MAPS = '/maps'

  static readonly LOGIN = '/auth/login'
  static readonly REGISTER = '/auth/register'
  static readonly FORGOT_PASSWORD = '/auth/forgot-password'
  static readonly VERIFY_EMAIL = '/auth/verify-email'
  static readonly TWO_FACTOR_CHALLENGE = '/auth/2fa-challenge'
  static readonly TWO_FACTOR_RECOVER = '/auth/2fa-recover'
  static readonly PASSWORD_CONFIRM = '/auth/password-confirm'

  static readonly DASHBOARD = '/dashboard'
  static readonly SETTINGS_PROFILE = '/dashboard/profile'
  static readonly CHANGE_USERNAME = '/dashboard/profile/change-username'
  static readonly SETTINGS_SECURITY = '/dashboard/security'
  static readonly CHANGE_EMAIL = '/dashboard/security/change-email'
  static readonly CHANGE_PASSWORD = '/dashboard/security/change-password'
  static readonly TWO_FACTOR_AUTH = '/dashboard/security/2fa'
  static readonly TWO_FACTOR_AUTH_SETUP = '/dashboard/security/2fa-setup'
  static readonly DONATIONS = '/dashboard/donations'
  static readonly LINKING = '/dashboard/linking'
}