import FilledButton from "@/components/filled-button"
import Icon, { IconToken } from "@/components/icon"
import AuthLayout from "@/components/layouts/auth-layout"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
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
  code: string
}

const Challenge2FA: NextPage = (props): JSX.Element => {
  const router = useRouter()
  const { twoFactorChallenge } = use2FA()
  const [ loading, setLoading ] = useState(false)

  const schema = yup
    .object({
      code: yup.number().required(),
    })
    .required("Cannot be empty")

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)

    try {
      await twoFactorChallenge({ code: data.code })
      await router.push(Routes.DASHBOARD)
    } catch (error: any) {
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout>
      <Link href={Routes.LOGIN}>
        <Icon token={IconToken.chevronLeft} /> Go Back
      </Link>

      <h1 className="text-heading-xl">Enter Device Code</h1>

      <form onSubmit={handleSubmit(onSubmit)}>
        <Alert error={errors.root?.message} />

        <p className="control has-icons-left">
          <input
            type="text"
            className={`input ${errors.code && "is-danger"}`}
            {...register("code")}
          />
          <span className="icon is-small is-left">
            <Icon token={IconToken.mobile} />
          </span>
        </p>

        <span className="text-label-sm">
          <Link href={Routes.TWO_FACTOR_RECOVER}>Use a recovery code</Link>
        </span>

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

export default Challenge2FA