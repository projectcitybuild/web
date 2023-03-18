import api from "@/libs/http/api"
import querystring from "querystring"

interface LoginCredentials {
    email: string
    password: string
}

interface AuthProvider {
    login: (credentials: LoginCredentials) => void
    logout: () => void
    isLoggedIn: boolean
}

export const useAuth = (): AuthProvider => {
    const apiBaseURL = process.env.NEXT_PUBLIC_API_BASE_URL as string
    const apiClient = api(apiBaseURL)

    const login = async (credentials: LoginCredentials) => {
        const params = querystring.stringify({
            email: credentials.email,
            password: credentials.password,
        })
        const response = await apiClient.post("login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)
        const test = await fetch("login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(response.data.account),
        })
        console.log(test)
    }

    const logout = async () => {
        const response = await apiClient.post("logout", {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)
    }

    const isLoggedIn = false

    return {
        login,
        logout,
        isLoggedIn,
    }
}
