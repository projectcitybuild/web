import { isAxiosError } from "axios"
import useSWR from 'swr'
import http from "@/libs/http/Http"
import querystring from "querystring"
import { useRouter } from "next/router"
import { useCookies } from "react-cookie"
import { useEffect } from "react";
import {Routes} from "@/constants/Routes";

type LoginParams = {
    email: string
    password: string
}

type RegisterParams = {
    username: string
    email: string
    password: string
    passwordConfirm: string
}

type UpdatePasswordProps = {
    oldPassword: string
    newPassword: string
    newPasswordConfirm: string
}

type PasswordConfirmProps = {
    password: string
}

type TwoFactorConfirmParams = {
    code: string
}

type TwoFactorChallengeParams = {
    code: string
}

type TwoFactorRecoveryParams = {
    recovery_code: string
}

interface AuthHookParams {
    middleware?: AuthMiddleware
    redirectIfAuthenticated?: string
    redirectIfNotAdmin?: string
}

export enum AuthMiddleware {
    // Grants access only to guests
    GUEST,

    // Grants access only to logged-in users
    AUTH,

    // Grants access only to logged-in admin
    AUTH_ADMIN,
}

export const useAuth = ({
    middleware,
    redirectIfAuthenticated,
    redirectIfNotAdmin,
}: AuthHookParams = {}) => {
    const router = useRouter()
    const [cookies, setCookies, removeCookies] = useCookies()

    const {
        data: user,
        error,
        mutate
    } = useSWR(tryUser() ? 'user' : null, async () => {
        try {
            const response = await http.get('profile/me')
            return response.data
        } catch (e) {
            if (!isAxiosError(e) || !e.response) {
                throw e
            }
            if (e.response.status === 401) {
                removeCookies('isAuth')
                return null
            } else if (e.response.status === 409) {
                await router.push(Routes.VERIFY_EMAIL)
            } else {
                setCookies('isAuth', false, { sameSite: 'lax' })
                throw e
            }
        }
      }
    )

    const csrf = () => http.get('../sanctum/csrf-cookie')

    const register = async ({ ...props }: RegisterParams) => {
        await csrf()

        const params = querystring.stringify({
            username: props.username,
            email: props.email,
            password: props.password,
            password_confirmation: props.passwordConfirm,
        })
        await http
            .post('register', params)
            .then(() => {
                setCookies('isAuth', true, { sameSite: 'lax' })
                mutate()
            })
            .catch(error => {
                if (error.response.status !== 422) throw error
                console.log(error)
            })
    }

    const forgotPassword = async (email: string): Promise<string> => {
        await csrf()

        return await http
            .post('../forgot-password', { email })
            .then(response => response.data.status)
            .catch(error => {
                if (error.status !== 422) throw error

                console.log(error)
            })
    }

    const resetPassword = async (email: string, password: string, passwordConfirm: string) => {
        await csrf()
        await http
            .post('../reset-password', {
                token: router.query.token,
                email: email,
                password: password,
                password_confirmation: passwordConfirm,
            })
            .catch(error => {
                if (error.response.status != 422) throw error
                console.error(error)
            })
    }

    const updateEmail = async (email: string) => {
        await http
            .post('../email/change', {
                email: email,
            })
            .catch(error => {
                if (error.response.status != 422) throw error
                console.error(error)
            })
    }

    const updatePassword = async ({ ...props }: UpdatePasswordProps) => {
        await http
            .put('user/password', {
                current_password: props.oldPassword,
                password: props.newPassword,
                password_confirmation: props.newPasswordConfirm,
            })
    }

    const resendEmailVerification = () => http.post('../email/verification-notification')

    const passwordConfirm = async ({ ...props }: PasswordConfirmProps) => {
        const params = querystring.stringify({
            password: props.password,
        })
        const res = await http.post('user/confirm-password', params)
        console.log(res)
    }

    const twoFactorEnable = async () => {
        const response = await http.post('user/two-factor-authentication')
            .catch(error => {
                if (error.response.status === 423) {
                    router.push(Routes.PASSWORD_CONFIRM)
                } else {
                    throw error
                }
            })

        console.log(response)
    }

    const twoFactorDisable = async () => {
        const response = await http.delete('user/two-factor-authentication')
            .catch(error => {
                if (error.response.status === 423) {
                    router.push(Routes.PASSWORD_CONFIRM)
                } else {
                    throw error
                }
            })

        console.log(response)
    }

    const twoFactorQRCode = async () => {
        const response = await http.get('user/two-factor-qr-code')
        console.log(response.data)
        return response.data.svg
    }

    const twoFactorConfirmSetup = async ({ ... props }: TwoFactorConfirmParams) => {
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

    const twoFactorChallenge = async ({ ... props }: TwoFactorChallengeParams) => {
        const params = querystring.stringify({
            code: props.code,
        })
        const res = await http.post('two-factor-challenge', params)
        console.log(res.data)
        setCookies('isAuth', true, { sameSite: 'lax', path: '/' })
        await mutate()
    }

    /**
     * Attempts access to an account using a recovery code. For use
     * in the case where a user loses access to their OTP code
     * generating device
     *
     * @param props
     */
    const twoFactorRecovery = async ({ ... props }: TwoFactorRecoveryParams) => {
        const params = querystring.stringify({
            recovery_code: props.recovery_code,
        })
        const res = await http.post('two-factor-challenge', params)
        console.log(res.data)
        if (res.status == 204) {
            setCookies('isAuth', true, { sameSite: 'lax', path: '/' })
            await mutate()
        }
    }

    const isAdmin = () => {
        return false // TODO
        // return user?.roles.some(role => role.name === 'Admin')
    }

    useEffect(() => {
        if (middleware === AuthMiddleware.GUEST && redirectIfAuthenticated && user) {
            router.push(redirectIfAuthenticated)
        }
        if ((middleware === AuthMiddleware.AUTH || middleware === AuthMiddleware.AUTH_ADMIN) && error) {
            console.error(error)
            // logout() // TODO
        }
        if (middleware === AuthMiddleware.AUTH_ADMIN && user && !isAdmin()) {
            redirectIfNotAdmin
                ? router.push(redirectIfNotAdmin)
                : router.push('/')
        }
    })

    function tryUser() {
        if (
            middleware === AuthMiddleware.AUTH_ADMIN ||
            middleware === AuthMiddleware.AUTH ||
            cookies.isAuth !== 'false'
        ) {
            return true
        }
        return null
    }

    return {
        user,
        register,
        forgotPassword,
        resetPassword,
        updateEmail,
        updatePassword,
        resendEmailVerification,
        passwordConfirm,
        twoFactorEnable,
        twoFactorDisable,
        twoFactorQRCode,
        twoFactorConfirmSetup,
        twoFactorRecoveryCodes,
        twoFactorChallenge,
        twoFactorRecovery,
    }
}
