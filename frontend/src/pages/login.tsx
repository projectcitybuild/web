import React from "react";
import {useForm} from "react-hook-form"
import {NextPage} from "next"
import Link from "next/link";
import Image from "next/image";
import * as yup from "yup"
import {yupResolver} from "@hookform/resolvers/yup"
import {DisplayableError} from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {Routes} from "@/constants/routes";
import {Alert} from "@/components/alert";
import logoImage from "@/assets/images/logo.png";
import styles from "@/pages/login.module.scss";
import Icon, {IconToken} from "@/components/icon";
import FormField from "@/components/form-field";

type FormData = {
  email: string
  password: string
}

const Login: NextPage = (props): JSX.Element => {
  const { login } = useAuth({
    middleware: AuthMiddleware.GUEST,
    redirectIfAuthenticated: Routes.DASHBOARD,
  })

  const schema = yup
    .object({
      email: yup.string().required().email(),
      password: yup.string().required(),
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
    <div className={`columns ${styles.columns}`}>
      <div className={`column is-half-tablet ${styles.formCol}`}>
        <Link href={Routes.HOME} className={styles.logo}>
          <Image
            src={logoImage}
            width={159}
            height={47}
            alt="Project City Build"
          />
        </Link>

        <div className={styles.contents}>
          <h1 className="text-heading-xl">Sign In</h1>

          <form onSubmit={handleSubmit(onSubmit)}>
            <Alert error={errors.root?.message} />

            <FormField label="Email Address" errorText={errors.email?.message}>
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

            <FormField label="Password" errorText={errors.password?.message}>
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

            <div className="field is-horizontal">
              <div className="field-body">
                <div className="field">
                  <label className="checkbox">
                    <input type="checkbox" /> Remember me
                  </label>
                </div>

                <Link href={Routes.FORGOT_PASSWORD}>Forgot password?</Link>
              </div>
            </div>

            <div className="field">
              <p className="control">
                <button
                  type="submit"
                  disabled={formState.isSubmitting}
                  className={`button is-primary ${formState.isSubmitting ? "is-loading" : null}`}
                >Sign in</button>
              </p>
            </div>
          </form>
        </div>
      </div>

      <div className={`column is-hidden-mobile ${styles.background}`}></div>
    </div>
  )
}

export default Login