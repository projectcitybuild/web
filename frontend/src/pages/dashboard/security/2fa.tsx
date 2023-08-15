import { Alert } from "@/components/alert";
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import { Routes } from "@/constants/Routes";
import withAuth from "@/hooks/withAuth"
import { use2FA } from "@/libs/account/use2FA"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { yupResolver } from "@hookform/resolvers/yup/dist/yup";
import { NextPage } from "next"
import Link from "next/link";
import { useRouter } from "next/router"
import React, { useState } from "react";
import { useForm } from "react-hook-form";
import * as yup from "yup"

type FormData = {
  code: string
}

const TwoFactorAuthentication: NextPage = (props): JSX.Element => {
  const {
    twoFactorEnable,
    twoFactorDisable,
    twoFactorConfirmSetup,
  } = use2FA()

  const router = useRouter()
  const [ loading, setLoading ] = useState(false)
  const [ confirmError, setConfirmError ] = useState<string | undefined>()

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
      await router.push(Routes.TWO_FACTOR_AUTH_ENABLE)
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

    } finally {
      setLoading(false)
    }
  }

  const onConfirmTwoFactorSetup = async (data: FormData) => {
    setLoading(true)

    twoFactorConfirmSetup({ code: data.code })
      .then((res) => console.log(res))
      .catch((err) => setConfirmError(err.message))
      .finally(() => setLoading(false))
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

      <Link href={Routes.SECURITY}>
        <Icon token={IconToken.chevronLeft}/> Back
      </Link>

      <h1>Two Step Verification</h1>

      <hr/>

      <Alert
        error={errors.root?.message}
      />

      <section className="section">
        <h2>2FA</h2>

        <button
          className={`button is-primary ${loading ? "is-loading" : ""}`}
          disabled={loading}
          onClick={onEnableTwoFactor}
        >Enable 2FA
        </button>

      </section>

      <section className="section">
        <h2>Disable</h2>

        <button
          className={`button is-primary ${loading ? "is-loading" : ""}`}
          disabled={loading}
          onClick={onDisableTwoFactor}
        >Disable 2FA
        </button>

      </section>
    </DashboardSecurityLayout>
  )
}

export default withAuth(TwoFactorAuthentication)