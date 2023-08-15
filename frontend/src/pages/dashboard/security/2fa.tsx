import { Alert } from "@/components/alert";
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import { Routes } from "@/constants/Routes";
import withAuth from "@/hooks/withAuth"
import { use2FA } from "@/libs/account/use2FA"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { useAuth } from "@/providers/useAuth"
import { yupResolver } from "@hookform/resolvers/yup";
import { NextPage } from "next"
import Link from "next/link";
import { useRouter } from "next/router"
import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import * as yup from "yup"

type FormData = {
  code: string
}

const TwoFactorAuthentication: NextPage = (props): JSX.Element => {
  const {
    twoFactorEnable,
    twoFactorDisable,
  } = use2FA()

  const router = useRouter()
  const { user } = useAuth()
  const [ loading, setLoading ] = useState(false)

  const schema = yup
    .object({
      code: yup.string().required(),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onEnableTwoFactor = async () => {
    setLoading(true)

    try {
      await twoFactorEnable()
      await router.push(Routes.TWO_FACTOR_AUTH_SETUP)
    } catch (error) {
      console.log(error)
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  const onDisableTwoFactor = async () => {
    setLoading(true)

    try {
      await twoFactorDisable()
    } catch (error) {
      console.log(error)
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <DashboardSecurityLayout>
      {/*<div className="modal is-active">*/}
      {/*    <div className="modal-background"></div>*/}
      {/*    <div className="modal-content">*/}
      {/*        Todo*/}
      {/*    </div>*/}
      {/*    <button className="modal-close is-large" aria-label="close"></button>*/}
      {/*</div>*/}

      <Link href={Routes.SETTINGS_SECURITY}>
        <Icon token={IconToken.chevronLeft}/> Back
      </Link>

      <h1>Two Step Verification</h1>

      <hr/>

      <Alert
        error={errors.root?.message}
      />

      <div>
        {user?.two_factor_confirmed_at === null && (
          <button
            className={`button is-primary ${loading ? "is-loading" : ""}`}
            disabled={loading}
            onClick={onEnableTwoFactor}
          >Enable 2FA
          </button>
        )}
        {user?.two_factor_confirmed_at !== null && (
          <button
            className={`button is-primary ${loading ? "is-loading" : ""}`}
            disabled={loading}
            onClick={onDisableTwoFactor}
          >Disable 2FA
          </button>
        )}
      </div>
    </DashboardSecurityLayout>
  )
}

export default withAuth(TwoFactorAuthentication)