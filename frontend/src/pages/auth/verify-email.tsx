import { useAccount } from "@/libs/account/useAccount"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import React, { useState } from "react";
import { Alert } from "@/components/alert";
import AuthLayout from "@/components/layouts/auth-layout";
import FilledButton from "@/components/filled-button";

const Page = (): JSX.Element => {
  const { resendEmailVerification } = useAccount()
  const [ error, setError ] = useState("")
  const [ loading, setLoading ] = useState(false)
  const [ success, setSuccess ] = useState("")

  const onResend = async () => {
    setLoading(true)
    setError("")
    setSuccess("")

    try {
      await resendEmailVerification()
      setSuccess("We've sent another email to your registered email address")
    } catch (error: any) {
      console.log(error)
      setError(getHumanReadableError(error))
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout>
      <h1 className="text-heading-xl">Verify Email Address</h1>

      <Alert
        error={error}
        success={success}
      />

      <p className="text-body-lg">
        An email has been sent to your email address. Please click the verification link inside to complete registration.
      </p>

      <hr/>

      <h2 className="text-heading-sm">Didn't receive an email?</h2>

      <p className="text-body-sm">
        Please wait up to a few minutes, and be sure to check your spam inbox.<br/>
        If you still can't find the email, you can request another with the below button.
      </p>

      <FilledButton
        text="Resend Verification Email"
        loading={loading}
        disabled={loading}
        onClick={onResend}
      />
    </AuthLayout>
  )
}

export default Page