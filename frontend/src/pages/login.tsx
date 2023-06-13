import React, {ReactElement} from "react";
import { useForm } from "react-hook-form"
import Link from "next/link";
import * as yup from "yup"
import { yupResolver } from "@hookform/resolvers/yup"
import { DisplayableError } from "@/libs/http/http";
import { AuthMiddleware, useAuth } from "@/hooks/useAuth";
import  {Routes } from "@/constants/routes";
import { Alert } from "@/components/alert";
import styles from "@/pages/login.module.scss";
import Icon, { IconToken } from "@/components/icon";
import FormField from "@/components/form-field";
import FilledButton from "@/components/filled-button";
import AuthLayout from "@/components/layouts/auth-layout";
import { NextPageWithLayout } from "@/pages/_app";

type FormData = {
  email: string
  password: string
}

const Page: NextPageWithLayout = (): JSX.Element => {
  const { login } = useAuth({
    middleware: AuthMiddleware.GUEST,
    redirectIfAuthenticated: Routes.DASHBOARD,
  })

  const schema = yup
    .object({
      email: yup.string()
        .required("Cannot be empty")
        .email("Must be a valid email address"),

      password: yup.string()
        .required("Cannot be empty"),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    try {
      await login({email: data.email, password: data.password})
    } catch (error) {
      if (error instanceof DisplayableError) {
        setError("root", { message: error.message })
      } else {
        console.error(error)
      }
    }
  }

  return (
    <>
      <h1 className="text-heading-xl">Sign In</h1>

      <form onSubmit={handleSubmit(onSubmit)}>
        <Alert error={errors.root?.message} />

        <FormField
          label="Email Address"
          errorText={errors.email?.message}
          className={styles.fieldEmail}
        >
          <p className="control has-icons-left">
            <input
              type="email"
              placeholder="Email address"
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
          className={styles.fieldPassword}
        >
          <p className="control has-icons-left">
            <input
              type="password"
              placeholder="Password"
              className={`input ${errors.password && "is-danger"}`}
              {...register("password")}
            />
            <span className="icon is-small is-left">
              <Icon token={IconToken.lock} />
            </span>
          </p>
        </FormField>

        <div className={`field ${styles.fieldOptions}`}>
          <div className="field">
            <label className={`checkbox ${styles.checkbox}`}>
              <input type="checkbox" /> Remember me
            </label>
          </div>

          <span className="text-label-sm">
            <Link href={Routes.FORGOT_PASSWORD}>Forgot password?</Link>
          </span>
        </div>

        <div className="field">
          <FilledButton
            text="Sign In"
            submit={true}
            loading={formState.isSubmitting}
            disabled={formState.isSubmitting}
          />
        </div>

        <div className={styles.registerText}>
          <span className="text-label-md">
            Don't have an account? <strong><Link href={Routes.REGISTER}>Register for free</Link></strong>
          </span>
        </div>
      </form>
    </>
  )
}

Page.getLayout = function getLayout(page: ReactElement) {
  return (
    <AuthLayout>
      {page}
    </AuthLayout>
  )
}

export default Page