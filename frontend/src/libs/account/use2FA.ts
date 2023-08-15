import http from "@/libs/http/Http"
import { ResourceLockedError } from "@/libs/http/HttpError"
import { useAuth } from "@/providers/useAuth"
import querystring from "querystring"
import { useRouter } from "next/router"
import { Routes } from "@/constants/Routes";

export const use2FA = () => {
  const router = useRouter()
  const { invalidateUser } = useAuth()

  /**
   * Generates (or overwrites) the user's secret key for
   * use with 2FA.
   *
   * Note: 2FA is not actually considered active until the user
   * finishes set up by sending a valid code (see `twoFactorConfirmSetup`)
   */
  const twoFactorEnable = async () => {
    try {
      await http.post('user/two-factor-authentication')
      await invalidateUser()
    } catch (error: any) {
      if (error instanceof ResourceLockedError) { // TODO: move this to an app-wide navigation observer
        await router.push(Routes.PASSWORD_CONFIRM)
      } else {
        throw error
      }
    }
  }

  /**
   * Deletes the user's secret key and disables 2FA
   */
  const twoFactorDisable = async () => {
    try {
      await http.delete('user/two-factor-authentication')
      await invalidateUser()
    } catch (error: any) {
      if (error instanceof ResourceLockedError) { // TODO: move this to an app-wide navigation observer
        await router.push(Routes.PASSWORD_CONFIRM)
      } else {
        throw error
      }
    }
  }

  /**
   * Finishes setting up 2FA for the user by confirming that
   * the given code (generated from their device) is valid
   *
   * @param props
   */
  const twoFactorConfirmSetup = async (props: {
    code: string
  }) => {
    const params = querystring.stringify({
      code: props.code,
    })
    await http.post('user/confirmed-two-factor-authentication', params)
    await invalidateUser()
  }

  /**
   * Fetches a SVG (as HTML) of a QR code the user can scan
   * to register 2FA on their secondary device
   */
  const twoFactorQRCode = async () => {
    const response = await http.get('user/two-factor-qr-code')
    return response.data.svg
  }

  /**
   * Fetches a list of the user's recovery codes.
   *
   * All codes can be assumed to be valid, as using a recovery code
   * will cause it to be replaced with a new one
   */
  const twoFactorRecoveryCodes = async (): Promise<[string]> => {
    const response = await http.get('user/two-factor-recovery-codes')
    return response.data
  }

  /**
   * Attempts authentication using an OTP code
   *
   * @param props
   */
  const twoFactorChallenge = async (props: {
    code: string
  }) => {
    const params = querystring.stringify({
      code: props.code,
    })
    await http.post('two-factor-challenge', params)
    await invalidateUser()
  }

  /**
   * Attempts authentication using a recovery code, for cases
   * where the user loses access to their OTP generation-capable
   * device
   *
   * @param props
   */
  const twoFactorRecovery = async (props: {
    recoveryCode: string
  }) => {
    console.log("test")
    const params = querystring.stringify({
      recovery_code: props.recoveryCode,
    })
    await http.post('two-factor-challenge', params)
    await invalidateUser()
  }

  return {
    twoFactorEnable,
    twoFactorDisable,
    twoFactorQRCode,
    twoFactorConfirmSetup,
    twoFactorRecoveryCodes,
    twoFactorChallenge,
    twoFactorRecovery,
  }
}
