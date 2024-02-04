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

type PasswordConfirmContextType = {
  needsConfirm: boolean
  setNeedsConfirm: Dispatch<SetStateAction<boolean>>
}

const PasswordConfirmContext = createContext<PasswordConfirmContextType>({
  needsConfirm: false,
  setNeedsConfirm: () => {},
})

export const PasswordConfirmProvider = ({
  children,
}: {
  children: ReactNode,
}): ReactElement => {
  const [needsConfirm, setNeedsConfirm] = useState(false)
  const router = useRouter()


  const confirmPassword = async (props: ConfirmPasswordProps) => {
    const params = querystring.stringify({
      password: props.password,
    })
    await http.post('user/confirm-password', params)
  }

  return (
    <PasswordConfirmContext.Provider
      value={{
        needsConfirm,
        setNeedsConfirm,
      }}
    >
      {children}
    </PasswordConfirmContext.Provider>
  )
}

export function usePasswordConfirm(): PasswordConfirmContextType {
  const context = useContext(PasswordConfirmContext)
  if (context === null) {
    throw new Error("PasswordConfirmContext not found in view hierarchy")
  }
  return context
}