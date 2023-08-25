import { Routes } from "@/constants/Routes"
import http from "@/libs/http/Http"
import { UnauthenticatedError } from "@/libs/http/HttpError"
import { User } from "@/types/user"
import { useRouter } from "next/router"
import querystring from "querystring"
import {
  createContext,
  Dispatch,
  ReactElement,
  ReactNode,
  SetStateAction,
  useContext,
  useEffect,
  useState
} from "react"

type AuthContextType = {
  isAuthenticated: boolean
  isLoading: boolean
  setAuthenticated: Dispatch<SetStateAction<boolean>>
  user: User|null
  invalidateUser: () => Promise<void>
  csrf: () => Promise<void>
  login: (props: LoginProps) => Promise<void>
  logout: () => Promise<void>
  confirmPassword: (props: ConfirmPasswordProps) => Promise<void>
}

const AuthContext = createContext<AuthContextType>({
  isAuthenticated: false,
  isLoading: true,
  setAuthenticated: () => {},
  user: null,
  invalidateUser: () => Promise.resolve(),
  csrf: async () => Promise.resolve(),
  login: async (_) => Promise.resolve(),
  logout: async () => Promise.resolve(),
  confirmPassword: async (_) => Promise.resolve(),
})

type LoginProps = {
  email: string
  password: string
  remember: boolean
}

type ConfirmPasswordProps = {
  password: string
}

export const AuthProvider = ({
  children,
}: {
  children: ReactNode,
}): ReactElement => {
  const [isAuthenticated, setAuthenticated] = useState(false)
  const [isLoading, setLoading] = useState(true)
  const [user, setUser] = useState<User|null>(null)
  const router = useRouter()

  const csrf = async () => {
    await http.get('../sanctum/csrf-cookie')
  }

  const login = async (props: LoginProps) => {
    await csrf()

    const params = querystring.stringify({
      email: props.email,
      password: props.password,
      remember: props.remember,
    })
    const response = await http.post('login', params)
    if (response.data && response.data.two_factor) {
      await router.push(Routes.TWO_FACTOR_CHALLENGE)
    } else {
      await fetchUser()
    }
  }

  const logout = async () => {
    await http.post('logout')
    setAuthenticated(false)
    setUser(null)
  }

  const confirmPassword = async (props: ConfirmPasswordProps) => {
    const params = querystring.stringify({
      password: props.password,
    })
    await http.post('user/confirm-password', params)
  }

  const invalidateUser = async () => {
    await fetchUser()
  }

  const fetchUser = async () => {
    try {
      const response = await http.get('profile/me')
      setUser(response.data)
      setAuthenticated(true)
    } catch (error: any) {
      setAuthenticated(false)
      if (!(error instanceof UnauthenticatedError)) {
        throw error
      }
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchUser()
  }, [])

  return (
    <AuthContext.Provider
      value={{
        isAuthenticated,
        isLoading,
        setAuthenticated,
        user,
        invalidateUser,
        csrf,
        login,
        logout,
        confirmPassword,
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth(): AuthContextType {
  const context = useContext(AuthContext)
  if (context === null) {
    throw new Error("AuthProvider not found in view hierarchy")
  }
  return context
}