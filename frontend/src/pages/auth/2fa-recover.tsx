import FilledButton from "@/components/filled-button"
import FormField from "@/components/form-field"
import Icon, { IconToken } from "@/components/icon"
import AuthLayout from "@/components/layouts/auth-layout"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import styles from "@/pages/auth/login.module.scss"
import { NextPage } from "next"
import Link from "next/link"
import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { use2FA } from "@/libs/account/use2FA";
import React, { useState } from "react";
import { Alert } from "@/components/alert";
import { Routes } from "@/constants/Routes";

type FormData = {
  recoveryCode: string
}

const Recover2FA: NextPage = (props): JSX.Element => {
  const router = useRouter()
  const { twoFactorRecovery } = use2FA()
  const [ loading, setLoading ] = useState(false)

  const schema = yup
    .object({
      recoveryCode: yup.string().required(),
    })
    .required("Cannot be empty")

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)

    try {
      await twoFactorRecovery({ recoveryCode: data.recoveryCode })
      await router.push(Routes.DASHBOARD)
    } catch (error: any) {
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout>
      <Link href={Routes.TWO_FACTOR_CHALLENGE}>
        <Icon token={IconToken.chevronLeft}/> Enter Device Code
      </Link>

      <h1 className="text-heading-xl">Enter Recovery Code</h1>

      <p>
        If you have lost access to your OTP device, you can still sign-in using a recovery code.
        After signing-in, please set up your two factor authentication again.
        <br />
        The same recovery code cannot be used more than once.
      </p>

      <form onSubmit={handleSubmit(onSubmit)}>
        <Alert error={errors.root?.message} />

        <FormField
          label="Recovery Code"
          errorText={errors.recoveryCode?.message}
        >
          <p className="control has-icons-left">
            <input
              type="text"
              className={`input ${errors.recoveryCode && "is-danger"}`}
              {...register("recoveryCode")}
            />
            <span className="icon is-small is-left">
            <Icon token={IconToken.question} />
          </span>
          </p>
        </FormField>

        <div className="field">
          <FilledButton
            text="Confirm"
            submit={true}
            loading={loading}
            disabled={loading}
          />
        </div>
      </form>
    </AuthLayout>
  )
}

export default Recover2FA