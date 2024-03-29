import http from "@/libs/http/Http"
import { useAuth } from "@/providers/useAuth"
import querystring from "querystring"

export const useAccount = () => {
  const { csrf, login } = useAuth()

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
    await login({
      email: props.email,
      password: props.password,
      remember: true,
    })
  }

  const resendEmailVerification = async () => {
    await http.post('email/verification-notification')
  }

  const updateEmail = async (props: {
    email: string
  }) => {
    await http.post('account/email', {
      email: props.email,
    })
  }

  const updateUsername = async (props: {
    username: string
  }) => {
    const params = querystring.stringify({
      username: props.username,
    })
    await http.patch('account/username', params)
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

  const resetPassword = async (props: {
    email: string,
    password: string,
    passwordConfirm: string,
    token: string,
  }) => {
    await csrf()

    await http.post('reset-password', {
      token: props.token,
      email: props.email,
      password: props.password,
      password_confirmation: props.passwordConfirm,
    })
  }

  const forgotPassword = async (props: {
    email: string
  }) => {
    await csrf()

    await http.post('forgot-password', {
      email: props.email
    })
  }

  return {
    register,
    resendEmailVerification,
    updateEmail,
    updateUsername,
    updatePassword,
    resetPassword,
    forgotPassword,
  }
}