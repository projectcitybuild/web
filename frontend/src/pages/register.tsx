import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { DisplayableError } from "@/libs/http/http";
import { AuthMiddleware, useAuth } from "@/hooks/useAuth";
import { Routes } from "@/constants/routes";
import React, {ReactElement, useState} from "react";
import { Alert } from "@/components/alert";
import { NextPageWithLayout } from "@/pages/_app";
import AuthLayout from "@/components/layouts/auth-layout";
import Icon, { IconToken } from "@/components/icon";
import FormField from "@/components/form-field";
import FilledButton from "@/components/filled-button";
import styles from "@/pages/login.module.scss";
import Link from "next/link";
import {Urls} from "@/constants/urls";

type FormData = {
  username: string
  email: string
  password: string
  passwordConfirm: string
  acceptTerms: boolean
}

const Register: NextPageWithLayout = (props): JSX.Element => {
  const router = useRouter()
  const { register: registerAccount, login } = useAuth({
    middleware: AuthMiddleware.GUEST,
    redirectIfAuthenticated: Routes.DASHBOARD,
  })

  const schema = yup
    .object({
      username: yup.string()
        .required("Cannot be empty"),

      email: yup.string()
        .required("Cannot be empty")
        .email("Must be a valid email address"),

      password: yup.string()
        .required("Cannot be empty"),

      passwordConfirm: yup.string()
        .required("Cannot be empty")
        .oneOf([yup.ref('password')], 'Passwords must match'),

      acceptTerms: yup.boolean()
        .isTrue("You must accept to continue"),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState
  const [ loading, setLoading ] = useState(false)

  const onSubmit = async (data: FormData) => {
    setLoading(true)

    try {
      // !!!!!!!!!!!
      // TODO: send ToS accept
      // !!!!!!!!!!!

      await registerAccount({
        username: data.username,
        email: data.email,
        password: data.password,
        passwordConfirm: data.passwordConfirm,
      })
      await login({
        email: data.email,
        password: data.password,
      })
      await router.push(Routes.VERIFY_EMAIL)
    } catch (error) {
      if (error instanceof DisplayableError) {
        setError("root", { message: error.message })
      } else {
        console.error(error)
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-heading-xl">Create an Account</h1>

      <Alert error={errors.root?.message} />

      <form onSubmit={handleSubmit(onSubmit)}>
        <FormField
          label="Username"
          errorText={errors.username?.message}
        >
          <p className="control has-icons-left">
            <input
              type="text"
              placeholder="Notch"
              className={`input ${errors.username && "is-danger"}`}
              {...register("username")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.user} />
              </span>
          </p>
        </FormField>

        <FormField
          label="Email Address"
          errorText={errors.email?.message}
        >
          <p className="control has-icons-left">
            <input
              type="email"
              placeholder="me@pcbmc.co"
              className={`input ${errors.email && "is-danger"}`}
              {...register("email")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.envelope} />
              </span>
          </p>
        </FormField>

        <FormField
          label="Password"
          errorText={errors.password?.message}
        >
          <p className="control has-icons-left">
            <input
              type="password"
              className={`input ${errors.password && "is-danger"}`}
              {...register("password")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.lock} />
              </span>
          </p>
        </FormField>

        <FormField
          errorText={errors.passwordConfirm?.message}
        >
          <p className="control has-icons-left">
            <input
              type="password"
              placeholder="(Confirm Password)"
              className={`input ${errors.passwordConfirm && "is-danger"}`}
              {...register("passwordConfirm")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.lock} />
              </span>
          </p>
        </FormField>

        <div className="field mt-4">
          <div className="box">
            <label className={`checkbox ${styles.checkbox}`}>
              <input
                type="checkbox"
                {...register("acceptTerms")}
              /> I agree to the <a href={Urls.TERMS_OF_SERVICE} target="_blank">terms of service</a> and <a href={Urls.PRIVACY_POLICY} target="_blank">privacy policy</a>
            </label>
            <p className="help is-danger">{errors.acceptTerms?.message}</p>
          </div>
        </div>

        <div className="field mt-5">
          <FilledButton
            text="Submit"
            submit={true}
            loading={loading}
            disabled={loading}
          />
        </div>

        <div>
          <span className="text-label-md">
            Already have an account? <strong><Link href={Routes.LOGIN}>Sign in</Link></strong>
          </span>
        </div>
      </form>
    </div>
  )
}

Register.getLayout = function getLayout(page: ReactElement) {
  return (
    <AuthLayout>
      {page}
    </AuthLayout>
  )
}

export default Register