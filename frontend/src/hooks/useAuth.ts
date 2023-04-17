import useSWR from 'swr'
import http from "@/libs/http/http"
import querystring from "querystring"
import { useRouter } from "next/router"
import { useCookies } from "react-cookie"
import { useEffect } from "react";
import {Routes} from "@/constants/routes";

type LoginParams = {
    email: string
    password: string
}

type RegisterParams = {
    email: string
    password: string
    passwordConfirm: string
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
    } = useSWR(tryUser() ? 'user' : null, () =>
        http
            .get('profile/me')
            .then(res => res.data)
            .catch(error => {
                removeCookies('isAuth')
                if (error.response.status === 409) {
                    router.push(Routes.VERIFY_EMAIL)
                } else {
                    setCookies('isAuth', false, { sameSite: 'lax' })
                    throw error
                }
            })
    )

    const csrf = () => http.get('../sanctum/csrf-cookie')

    const login = async ({...props }: LoginParams) => {
        await csrf()

        const params = querystring.stringify({
            email: props.email,
            password: props.password,
        })
        await http
            .post('../login', params)
            .then((data) => {
                setCookies('isAuth', true, { sameSite: 'lax', path: '/' })
                mutate()
            })
            .catch((error) => {
                removeCookies('isAuth')
                if (error.status !== 422) throw error

                console.log(error)
            })
    }

    const logout = async () => {
        if (!error) {
            removeCookies('isAuth', { sameSite: 'lax' })
            await http.post('../logout')
        }
        window.location.pathname = '/'
    }

    const register = async ({ ...props }: RegisterParams) => {
        await csrf()

        const params = querystring.stringify({
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

    const resendEmailVerification = () => http.post('../email/verification-notification')

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
        login,
        forgotPassword,
        resetPassword,
        resendEmailVerification,
        logout,
    }
}
