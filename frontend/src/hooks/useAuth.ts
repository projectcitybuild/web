import useSWR from 'swr'
import http from "@/libs/http/http"
import querystring from "querystring"
import { useRouter } from "next/router"
import { useCookies } from "react-cookie"
import { useEffect } from "react";

export type LoginParams = {
    email: string
    password: string
}

interface AuthHookParams {
    middleware?: string
    redirectIfAuthenticated?: string
    redirectIfNotAdmin?: string
}

export const useAuth = ({
    middleware,
    redirectIfAuthenticated = '/dashboard',
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
                    router.push('/verify-email')
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

    // const register = async ({ ...props }: RegisterParams) => {
    //     await csrf()
    //
    //     await http
    //         .post('register', props)
    //         .then(() => {
    //             setCookies('isAuth', true, { sameSite: 'lax' })
    //             mutate()
    //         })
    //         .catch(error => {
    //             if (error.response.status !== 422) throw error
    //
    //             console.log(error)
    //             // setErrors(
    //             //     Object.values(error.response.data.errors).flat() as []
    //             // )
    //         })
    // }

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
        if (middleware === 'guest' && redirectIfAuthenticated && user) {
            router.push(redirectIfAuthenticated)
        }
        if ((middleware === 'auth' || middleware === 'admin') && error) {
            console.error(error)
            // logout()
        }
        if (middleware === 'admin' && user && !isAdmin()) {
            redirectIfNotAdmin
                ? router.push(redirectIfNotAdmin)
                : router.push('/')
        }
    })

    function tryUser() {
        if (
            middleware === 'auth' ||
            middleware === 'admin' ||
            cookies.isAuth !== 'false'
        ) {
            return true
        }
        return null
    }

    return {
        user,
        // register,
        login,
        forgotPassword,
        resetPassword,
        resendEmailVerification,
        logout,
    }
}
