export class TokenStorage {
    set(accessToken: AccessToken) {
        localStorage.setItem("token", accessToken)
    }

    get(): AccessToken|null {
        return localStorage.getItem("token")
    }
}

export type AccessToken = string