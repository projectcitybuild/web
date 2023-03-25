import api from "@/libs/http/api"
import querystring from "querystring"

interface LoginCredentials {
    email: string
    password: string
}

interface AuthProvider {
    login: (credentials: LoginCredentials) => Promise<void>
    logout: () => Promise<void>
}

export const useAuth = (): AuthProvider => {
    const apiClient = api('/api/proxy')
    // const localApiClient = localApi()

    const login = async (credentials: LoginCredentials) => {
        const params = querystring.stringify({
            email: credentials.email,
            password: credentials.password,
        })
        const response = await apiClient.post("login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)
    }

    const logout = async () => {
        const response = await apiClient.post("logout", {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)
    }

    return {
        login,
        logout,
    }
}
