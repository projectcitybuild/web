import FilledButton from "@/components/filled-button"
import FormField from "@/components/form-field"
import Icon, { IconToken } from "@/components/icon"
import AuthLayout from "@/components/layouts/auth-layout"
import { useAccount } from "@/libs/account/useAccount"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { NextPage } from "next"
import Link from "next/link"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { Routes } from "@/constants/Routes";
import { Alert } from "@/components/alert";

type FormData = {
  email: string,
  password: string
  passwordConfirm: string
}

const PasswordReset: NextPage = (props): JSX.Element => {
  const { resetPassword } = useAccount()
  const [ loading, setLoading ] = useState(false)
  const [ success, setSuccess ] = useState("")
  const router = useRouter()
  const routerQuery = router.query

  const schema = yup
    .object({
      email: yup.string()
        .required('Cannot be empty')
        .email('Invalid email address'),
      password: yup.string()
        .required('Cannot be empty'),
      passwordConfirm: yup.string()
        .required('Cannot be empty')
        .oneOf([ yup.ref('password') ], 'Passwords must match'),
    })
    .required()

  const { register, handleSubmit, formState, setError, setValue, getValues } = useForm<FormData>({
    resolver: yupResolver(schema),
  })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    setSuccess("")

    const token = router.query.token
    if (!token) {
      setError("root", { message: "Invalid token" })
      return
    }

    try {
      await resetPassword({
        email: data.email,
        password: data.password,
        passwordConfirm: data.passwordConfirm,
        token: token as string,
      })
      await router.push(Routes.LOGIN)
    } catch (error) {
      console.log(error)
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    const values = getValues()
    if (!values.email || values.email == '') {
      setValue('email', routerQuery.email as string)
    }
  })

  return (
    <AuthLayout>
      <Link href={Routes.LOGIN}>
        <Icon token={IconToken.chevronLeft} /> Back to Sign In
      </Link>

      <h1 className="text-heading-xl">Set a New Password</h1>

      <form onSubmit={handleSubmit(onSubmit)}>
        <Alert
          error={errors.root?.message}
          success={success}
        />

        <FormField
          label="Email address"
          errorText={errors.email?.message}
        >
          <p className="control has-icons-left">
            <input
              type="email"
              placeholder="Email Address"
              className="input"
              readOnly={true}
              {...register("email")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.envelope}/>
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
              placeholder="New Password"
              className={`input ${errors.password && "is-danger"}`}
              {...register("password")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.lock}/>
              </span>
          </p>
        </FormField>

        <FormField
          errorText={errors.passwordConfirm?.message}
        >
          <p className="control has-icons-left">
            <input
              type="password"
              placeholder="New Password (Confirm)"
              className={`input ${errors.passwordConfirm && "is-danger"}`}
              {...register("passwordConfirm")}
            />
            <span className="icon is-small is-left">
                <Icon token={IconToken.lock}/>
              </span>
          </p>
        </FormField>

        <div className="field mt-5">
          <FilledButton
            text="Save and Return"
            submit={true}
            loading={formState.isSubmitting}
            disabled={formState.isSubmitting}
          />
        </div>
      </form>
    </AuthLayout>
  )
}

export default PasswordReset