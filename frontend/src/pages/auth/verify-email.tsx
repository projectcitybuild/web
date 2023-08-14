import { useAccount } from "@/libs/account/AccountService"
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

  const onSubmit = async () => {
    setLoading(true)
    setError("")
    setSuccess("")

    try {
      await resendEmailVerification()
      setSuccess("Please check the email sent to your registered email address")
    } catch (error: any) { // TODO
      setError(error.message)
    } finally {
      setLoading(false)
    }
  }

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
        Please click the button below to confirm your email address and activate your account.
      </p>

      <FilledButton
        text="Send Email"
        loading={loading}
        disabled={loading}
        onClick={onSubmit}
      />

      <hr/>

      <div>
        <h2 className="text-heading-sm">Didn't receive an email?</h2>

        <p className="text-body-sm">
          Please wait up to a few minutes, and be sure to check your spam inbox just in-case.<br/>
          If you still don't receive anything, you can <a href="frontend/src/pages" onClick={onResend}>send another email</a>.
        </p>
      </div>
    </AuthLayout>
  )
}

export default Page