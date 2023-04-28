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
            <Link href={Routes.DASHBOARD}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Security Settings</h1>

            <hr />

            {/*<section className="section">*/}
            {/*    <h2>2FA</h2>*/}

            {/*    <button*/}
            {/*        className={`button is-primary ${loading ? 'is-loading' : ''}`}*/}
            {/*        disabled={loading}*/}
            {/*        onClick={onEnableTwoFactor}*/}
            {/*    >Enable 2FA</button>*/}
            {/*</section>*/}

            <nav className="columns">
                <div className="column is-three-quarters">
                    <h2 className="is-size-6 has-text-weight-bold">
                        <Link href={Routes.CHANGE_EMAIL}>Email Address</Link>
                    </h2>
                    The email address associated with your account
                </div>
                <div className="column">
                    {user.email}<br />
                    {user.email_verified_at == null ? "Unverified" : "Verified"}
                </div>
            </nav>

            <nav className="columns">
                <div className="column is-three-quarters">
                    <h2 className="is-size-6 has-text-weight-bold">
                        <Link href={Routes.CHANGE_EMAIL}>2-Step Verification</Link>
                    </h2>
                    Keep your account secure. Along with your password, you'll need to enter a code from another device
                </div>
                <div className="column">

                </div>
            </nav>
        </div>
    )
}

export default Dashboard