import {NextPage} from "next"
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import NavBar from "@/components/navbar";
import React, {useState} from "react";
import {Routes} from "@/constants/routes";
import {Alert} from "@/components/alert";

const VerifyEmail: NextPage = (props): JSX.Element => {
    const { resendEmailVerification } = useAuth({
        middleware: AuthMiddleware.GUEST,
        redirectIfAuthenticated: Routes.DASHBOARD,
    })
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
        } catch (error: any) { // TODO
            setError(error.message)
        } finally {
            setLoading(false)
        }
    }

    const Success = () => {
        if (!success || success == "") return null;
        return (
            <div className="notification is-success">
                {success}
            </div>
        )
    }

    return (
        <div>
            <NavBar />

            <section className="section">
                <h1>Verify your email address</h1>

                <Alert error={error} />
                <Success />

                <p>
                    Please click the button below to confirm your email address and activate your account.
                </p>

                <button
                    disabled={loading}
                    className={`button is-primary ${loading ? 'is-loading' : ''}`}
                    onClick={onSubmit}
                >Confirm Email</button>

                <p>
                    <strong>Didn't receive an email?</strong><br/>
                    <span>
                        Please wait up to a few minutes, and be sure to check your spam inbox just in-case.<br />
                        If you still don't receive anything, you can <a href="" onClick={onResend}>send another email</a>.
                    </span>
                </p>
            </section>
        </div>
    )
}

export default VerifyEmail