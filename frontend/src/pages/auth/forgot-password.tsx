import withoutAuth from "@/hooks/withoutAuth"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { AuthMiddleware, useAuth } from "@/hooks/legacyUseAuth";
import React, { useState } from "react";
import { Routes } from "@/constants/Routes";
import { Alert} from "@/components/alert";
import AuthLayout from "@/components/layouts/auth-layout";
import FilledButton from "@/components/filled-button";
import Icon, { IconToken } from "@/components/icon";
import Link from "next/link";
import styles from "@/pages/auth/login.module.scss";
import FormField from "@/components/form-field";

type FormData = {
  email: string
}

const Page = (): JSX.Element => {
  const { forgotPassword } = useAuth({
    middleware: AuthMiddleware.GUEST,
    redirectIfAuthenticated: Routes.DASHBOARD,
  })
  const [ loading, setLoading ] = useState(false)
  const [ success, setSuccess ] = useState("")

  const schema = yup
    .object({
      email: yup.string()
        .required("Cannot be empty")
        .email("Must be a valid email address"),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    setSuccess("")

    try {
      await forgotPassword(data.email)
      setSuccess("A password reset link has been emailed to you")

    } catch (error) {
      console.log(error)
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout>
      <Link href={Routes.LOGIN}>
        <Icon token={IconToken.chevronLeft} /> Back to Sign In
      </Link>

      <h1 className="text-heading-xl">Forgot Password</h1>

      <form onSubmit={handleSubmit(onSubmit)}>
        <Alert
          error={errors.root?.message}
          success={success}
        />

        <FormField
          label="Email Address"
          errorText={errors.email?.message}
          className={styles.fieldEmail}
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

        <div className="field">
          <FilledButton
            text="Send Email"
            submit={true}
            loading={loading}
            disabled={loading}
          />
        </div>
      </form>
    </AuthLayout>
  )
}

export default withoutAuth(Page)