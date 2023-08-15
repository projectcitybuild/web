import http from "@/libs/http/Http"
import { ResourceLockedError } from "@/libs/http/HttpError"
import { useAuth } from "@/providers/useAuth"
import querystring from "querystring"
import { useRouter } from "next/router"
import { Routes } from "@/constants/Routes";

export const use2FA = () => {
  const router = useRouter()
  const { invalidateUser } = useAuth()

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

  const twoFactorQRCode = async () => {
    const response = await http.get('user/two-factor-qr-code')
    console.log(response.data)
    return response.data.svg
  }

  const twoFactorConfirmSetup = async (props: {
    code: string
  }) => {
    const params = querystring.stringify({
      code: props.code,
    })
    const res = await http.post('user/confirmed-two-factor-authentication', params)
    console.log(res.data)
  }

  const twoFactorRecoveryCodes = async () => {
    const response = await http.get('user/two-factor-recovery-codes')
    console.log(response.data)
    return response.data
  }

  const twoFactorChallenge = async (props: {
    code: string
  }) => {
    const params = querystring.stringify({
      code: props.code,
    })
    const res = await http.post('two-factor-challenge', params)
    console.log(res.data)
    // setCookies('isAuth', true, { sameSite: 'lax', path: '/' })
    // await mutate()
  }

  /**
   * Attempts access to an account using a recovery code. For use
   * in the case where a user loses access to their OTP code
   * generating device
   *
   * @param props
   */
  const twoFactorRecovery = async (props: {
    recoveryCode: string
  }) => {
    const params = querystring.stringify({
      recovery_code: props.recoveryCode,
    })
    const res = await http.post('two-factor-challenge', params)
    console.log(res.data)
    if (res.status == 204) {
      // setCookies('isAuth', true, { sameSite: 'lax', path: '/' })
      // await mutate()
    }
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
