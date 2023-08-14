import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { NextPage } from "next"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { AuthMiddleware, useAuth } from "@/hooks/legacyUseAuth";
import NavBar from "@/components/navbar";
import React, { useState } from "react";
import { useRouter } from "next/router";
import { Routes } from "@/constants/Routes";
import { Alert } from "@/components/alert";

type FormData = {
  email: string,
  password: string
  passwordConfirm: string
}

const PasswordReset: NextPage = (props): JSX.Element => {
  const { resetPassword } = useAuth({
    middleware: AuthMiddleware.GUEST,
    redirectIfAuthenticated: Routes.DASHBOARD,
  })
  const [ loading, setLoading ] = useState(false)
  const router = useRouter()
  const routerQuery = router.query

  const schema = yup
    .object({
      email: yup.string().required().email(),
      password: yup.string().required(),
      passwordConfirm: yup.string().required()
        .oneOf([ yup.ref("password") ], "Passwords must match"),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({
    resolver: yupResolver(schema),
    defaultValues: {
      email: routerQuery.email as string | undefined,
    }
  })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)

    try {
      await resetPassword(data.email, data.password, data.passwordConfirm)
    } catch (error) {
      console.log(error)
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <NavBar/>

      <section className="section">
        <h1>Set a New Password</h1>

        <form onSubmit={handleSubmit(onSubmit)}>
          <Alert error={errors.root?.message}/>

          <div className="field">
            <p className="control">
              <input type="email" placeholder="Email Address" className="input" {...register("email")} />
            </p>
            <p className="help is-danger">{errors.email?.message}</p>
          </div>
          <div className="field">
            <p className="control">
              <input type="password" placeholder="New Password" className="input" {...register("password")} />
            </p>
            <p className="help is-danger">{errors.password?.message}</p>
          </div>
          <div className="field">
            <p className="control">
              <input type="password" placeholder="New Password (Confirm)"
                     className="input" {...register("passwordConfirm")} />
            </p>
            <p className="help is-danger">{errors.passwordConfirm?.message}</p>
          </div>
          <div className="field">
            <p className="control">
              <button
                type="submit"
                disabled={formState.isSubmitting || loading}
                className={`button is-success ${loading ? "is-loading" : ""}`}
              >
                Save
              </button>
            </p>
          </div>
        </form>
      </section>
    </div>
  )
}

export default PasswordReset