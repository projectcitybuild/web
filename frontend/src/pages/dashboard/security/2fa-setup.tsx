import { Alert } from "@/components/alert"
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import withAuth from "@/hooks/withAuth"
import { use2FA } from "@/libs/account/use2FA"
import { NextPage } from "next"
import Link from "next/link";
import React, { useEffect, useState } from "react";
import * as yup from "yup"
import { Routes } from "@/constants/Routes";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup/dist/yup";

type FormData = {
  code: string
}

const TwoFactorAuthenticationSetup: NextPage = (props): JSX.Element => {
  const {
    twoFactorQRCode,
    twoFactorRecoveryCodes,
    twoFactorConfirmSetup,
  } = use2FA()

  const [ loading, setLoading ] = useState(false)
  const [ confirmError, setConfirmError ] = useState<string | undefined>()
  const [ qrCode, setQrCode ] = useState<string | undefined>()
  const [ recoveryCodes, setRecoveryCodes ] = useState<[ string ]>()

  useEffect(() => {
    loadCodes()
  })

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

    twoFactorConfirmSetup({ code: data.code })
      .then((res) => console.log(res))
      .catch((err) => setConfirmError(err.message))
      .finally(() => setLoading(false))
  }

  const schema = yup
    .object({
      code: yup.string().required(),
    })
    .required()

  const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  return (
    <DashboardSecurityLayout>
      <Link href={Routes.SECURITY}>
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

        <Alert error={confirmError}/>

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
          <div className="field">
            <p className="control">
              <button
                type="submit"
                disabled={formState.isSubmitting || loading}
                className={`button is-success ${loading ? "is-loading" : ""}`}
              >
                Confirm
              </button>
            </p>
          </div>
        </form>
      </section>
    </DashboardSecurityLayout>
  )
}

export default withAuth(TwoFactorAuthenticationSetup)