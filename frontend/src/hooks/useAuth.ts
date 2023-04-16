import useSWR from 'swr'
import http from "@/libs/http/http"
import querystring from "querystring"
import {useRouter} from "next/router"
import { useCookies } from "react-cookie"
import {Dispatch, SetStateAction, useEffect, useState} from "react";
import {User} from "@/libs/auth/user";

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

    const forgotPassword = async (email: string) => {
        await csrf()
        await http
            .post('../forgot-password', { email })
            .then(response => console.log(response))
            .catch(error => {
                if (error.status !== 422) throw error

                console.log(error)
            })
    }
    //
    // const resetPassword = async ({setErrors, setStatus, ...props}: ResetPasswordParams) => {
    //     setLoading(true)
    //     await csrf()
    //
    //     setErrors([])
    //     setStatus(null)
    //
    //     api
    //         .post('reset-password', { token: router.query.token, ...props })
    //         .then(response =>
    //             router.push('/login?reset=' + window.btoa(response.data.status))
    //         )
    //         .catch(error => {
    //             if (error.response.status != 422) throw error
    //
    //             setErrors(
    //                 Object.values(error.response.data.errors).flat() as []
    //             )
    //         })
    //     setLoading(false)
    // }
    //
    // const resendEmailVerification = ({setStatus}: ResendEmailVerificationParams) => {
    //     api
    //         .post('email/verification-notification')
    //         .then(response => setStatus(response.data.status))
    // }
    //
    const isAdmin = (user?: User) => {
        return false // TODO
        // return user?.roles.some(role => role.name === 'Admin')
    }

    useEffect(() => {
        if (middleware === 'guest' && redirectIfAuthenticated && user) {
            router.push(redirectIfAuthenticated)
        }
        if ((middleware === 'auth' || middleware === 'admin') && error) {
            logout()
        }
        if (middleware === 'admin' && user && !isAdmin(user)) {
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
        // resetPassword,
        // resendEmailVerification,
        logout,
    }
}
