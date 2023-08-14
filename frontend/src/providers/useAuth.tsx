import http from "@/libs/http/http"
import { UnauthenticatedError } from "@/libs/http/httpError"
import { User } from "@/types/user"
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
}

const AuthContext = createContext<AuthContextType>({
  isAuthenticated: false,
  isLoading: true,
  setAuthenticated: () => {},
  user: null,
})

export const AuthProvider = ({
  children,
}: {
  children: ReactNode,
}): ReactElement => {
  const [isAuthenticated, setAuthenticated] = useState(false)
  const [isLoading, setLoading] = useState(true)
  const [user, setUser] = useState<User|null>(null)

  useEffect(() => {
    const checkAuth = async (): Promise<void> => {
      try {
        const response = await http.get('profile/me')
        setUser(response.data)
        setAuthenticated(true)
      } catch (error: any) {
        setAuthenticated(false)
        if (error satisfies UnauthenticatedError) {}
      } finally {
        setLoading(false)
      }
    }
    checkAuth()
  })

  return (
    <AuthContext.Provider
      value={{
        isAuthenticated,
        isLoading,
        setAuthenticated,
        user,
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

export function useIsAuthenticated(): boolean {
  const context = useAuth()
  return context.isAuthenticated
}