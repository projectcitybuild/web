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

  const updateEmail = async (props: {
    email: string
  }) => {
    await http.post('../email/change', { email: props.email })
  }

  const updatePassword = async (props: {
    oldPassword: string
    newPassword: string
    newPasswordConfirm: string
  }) => {
    const params = querystring.stringify({
      current_password: props.oldPassword,
      password: props.newPassword,
      password_confirmation: props.newPasswordConfirm,
    })
    await http.put('account/password', params)
  }

  return {
    register,
    resendEmailVerification,
    updateEmail,
    updatePassword,
  }
}