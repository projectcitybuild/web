import { NextPage } from "next"
import { useRouter } from "next/router"
import Link from "next/link";
import React, {useEffect, useState} from "react";
import http from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {Alert} from "@/components/alert";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faEnvelope} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/routes";

const Dashboard: NextPage = (props): JSX.Element => {
    const router = useRouter()

    const { user, twoFactorEnable } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    const [ loading, setLoading ] = useState(false)

    const onEnableTwoFactor = async () => {
        setLoading(true)
        try {
            await twoFactorEnable()
        } catch (error) {

        } finally {
            setLoading(false)
        }
    }

    return (
        <div>
            <Link href={Routes.SECURITY}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Email Address</h1>

            <hr />

            <form onSubmit={handleSubmit(onEmailChangeSubmit)}>
                <Alert
                    error={errors.root?.message}
                    success={success}
                />
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="email" placeholder="Email address" className="input" {...register("email")} />
                        <span className="icon is-small is-left">
                                <FontAwesomeIcon icon={faEnvelope} />
                            </span>
                    </p>
                    <p className="help is-danger">{errors.email?.message}</p>
                </div>
                <div className="field">
                    <p className="control">
                        <button
                            type="submit"
                            disabled={formState.isSubmitting || loading}
                            className={`button is-success ${loading ? 'is-loading' : ''}`}
                        >
                            Send Email
                        </button>
                    </p>
                </div>
            </form>
        </div>
    )
}

export default Dashboard