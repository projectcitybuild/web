import { Alert } from "@/components/alert"
import FilledButton from "@/components/filled-button"
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import { Urls } from "@/constants/Urls"
import withAuth from "@/hooks/withAuth"
import { use2FA } from "@/libs/account/use2FA"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import styles from "@/pages/auth/login.module.scss"
import { NextPage } from "next"
import Link from "next/link";
import { useRouter } from "next/router"
import React, { useEffect, useState } from "react";
import * as yup from "yup"
import { Routes } from "@/constants/Routes";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";

type FormData = {
  code: string
  savedRecoveryCodes: boolean
}

const TwoFactorAuthenticationSetup: NextPage = (props): JSX.Element => {
  const {
    twoFactorQRCode,
    twoFactorRecoveryCodes,
    twoFactorConfirmSetup,
  } = use2FA()

  const router = useRouter()
  const [ loading, setLoading ] = useState(false)
  const [ qrCode, setQrCode ] = useState<string|undefined>()
  const [ recoveryCodes, setRecoveryCodes ] = useState<[string]>()

  const schema = yup
    .object({
      code: yup
        .string()
        .required('Cannot be empty'),
      acceptTerms: yup.boolean()
        .isTrue("You must accept to continue"),
    })
    .required()

  const { register, handleSubmit, formState, setError, watch } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  useEffect(() => {
    loadCodes()
  }, [])

  const loadCodes = async () => {
    setLoading(true)

    try {
      const svg = await twoFactorQRCode()
      setQrCode(svg)

      const recoveryCodes = await twoFactorRecoveryCodes()
      setRecoveryCodes(recoveryCodes)
    } catch (error) {
      console.log(error)
      // TODO
    } finally {
      setLoading(false)
    }
  }

  const onConfirmTwoFactorSetup = async (data: FormData) => {
    setLoading(true)

    try {
      await twoFactorConfirmSetup({ code: data.code })
      await router.push(Routes.TWO_FACTOR_AUTH)
    } catch (error: any) {
      console.log(error)
      setError("root", {message: getHumanReadableError(error)})
    } finally {
      setLoading(false)
    }
  }

  return (
    <DashboardSecurityLayout>
      <Link href={Routes.SETTINGS_SECURITY}>
        <Icon token={IconToken.chevronLeft}/> Back
      </Link>

      <h1>Enable Two Step Verification</h1>

      <hr/>

      <div>
        Please scan the below code on your secondary device's multi-factor app, then input the generated code into the textbox below.
      </div>

      <div>
        {qrCode && (<span dangerouslySetInnerHTML={{ __html: qrCode }}/>)}
      </div>

      <ul>
        {recoveryCodes?.map(code => (<li key={code}>{code}</li>))}
      </ul>

      <section className="section">
        <h1>Confirm</h1>

        <Alert error={errors.root?.message} />

        <form onSubmit={handleSubmit(onConfirmTwoFactorSetup)}>
          <div className="field">
            <p className="control has-icons-left">
              <input type="text" placeholder="2FA Code" className="input" {...register("code")} />
              <span className="icon is-small is-left">
                <Icon token={IconToken.envelope} />
              </span>
            </p>
            <p className="help is-danger">{errors.code?.message}</p>
          </div>

          <div className="field mt-4">
            <div className="box">
              <label className={`checkbox ${styles.checkbox}`}>
                <input
                  type="checkbox"
                  {...register("savedRecoveryCodes")}
                /> I have made a copy of the above recovery codes, and understand that I will not be able to view them again
              </label>
              <p className="help is-danger">{errors.savedRecoveryCodes?.message}</p>
            </div>
          </div>

          <div className="field">
            <p className="control">
              <FilledButton
                text="Confirm"
                submit={true}
                loading={loading}
                disabled={loading || !watch("savedRecoveryCodes")}
              />
            </p>
          </div>
        </form>
      </section>
    </DashboardSecurityLayout>
  )
}

export default withAuth(TwoFactorAuthenticationSetup)