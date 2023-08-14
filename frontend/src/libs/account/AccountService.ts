import http from "@/libs/http/Http"
import { useAuth } from "@/providers/useAuth"
import querystring from "querystring"

export const useAccount = () => {
  const { csrf } = useAuth()

  const register = async (props: {
    username: string
    email: string
    password: string
    passwordConfirm: string
  }) => {
    await csrf()

    const params = querystring.stringify({
      username: props.username,
      email: props.email,
      password: props.password,
      password_confirmation: props.passwordConfirm,
    })
    await http.post('register', params)
  }

  const resendEmailVerification = async () => {
    await http.post('../email/verification-notification')
  }

  return {
    register,
    resendEmailVerification,
  }
}