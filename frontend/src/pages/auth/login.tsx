import withoutAuth from "@/hooks/withoutAuth"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { useAuth } from "@/providers/useAuth"
import React from "react";
import { useForm } from "react-hook-form"
import Link from "next/link";
import * as yup from "yup"
import { yupResolver } from "@hookform/resolvers/yup"
import  {Routes } from "@/constants/Routes";
import { Alert } from "@/components/alert";
import styles from "@/pages/auth/login.module.scss";
import Icon, { IconToken } from "@/components/icon";
import FormField from "@/components/form-field";
import FilledButton from "@/components/filled-button";
import AuthLayout from "@/components/layouts/auth-layout";

type FormData = {
  email: string
  password: string
  remember: boolean
}

const Page = (): JSX.Element => {
  const { login } = useAuth()

  const schema = yup
    .object({
      email: yup.string()
        .required("Cannot be empty")
        .email("Must be a valid email address"),

      password: yup.string()
        .required("Cannot be empty"),

      remember: yup.boolean(),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    try {
      await login({
        email: data.email,
        password: data.password,
        remember: data.remember,
      })
    } catch (error: any) {
      setError("root", { message: getHumanReadableError(error) })
    }
  }

  return (
    <AuthLayout>
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
              <input type="checkbox" {...register("remember")} /> Remember me
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
    </AuthLayout>
  )
}

export default withoutAuth(Page)