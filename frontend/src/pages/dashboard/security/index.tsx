import { NextPage } from "next"
import Link from "next/link";
import React from "react";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/routes";

const Dashboard: NextPage = (props): JSX.Element => {
    const { user } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    return (
        <div>
            <Link href={Routes.DASHBOARD}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Security Settings</h1>

            <hr />

            <nav className="columns">
                <div className="column is-three-quarters">
                    <h2 className="is-size-6 has-text-weight-bold">
                        <Link href={Routes.CHANGE_EMAIL}>Email Address</Link>
                    </h2>
                    The email address associated with your account
                </div>
                <div className="column">
                    {user?.email}<br />
                    {user?.email_verified_at == null ? "Unverified" : "Verified"}
                </div>
            </nav>

            <nav className="columns">
                <div className="column is-three-quarters">
                    <h2 className="is-size-6 has-text-weight-bold">
                        <Link href={Routes.CHANGE_PASSWORD}>Password</Link>
                    </h2>
                    Update the password used to log-in to your account
                </div>
                <div className="column">
                    Last updated: TODO
                </div>
            </nav>

            <nav className="columns">
                <div className="column is-three-quarters">
                    <h2 className="is-size-6 has-text-weight-bold">
                        <Link href={Routes.TWO_FACTOR_AUTH}>2-Step Verification</Link>
                    </h2>
                    Keep your account secure. Along with your password, you'll need to enter a code from another device
                </div>
                <div className="column">
                    {user?.two_factor_confirmed_at == null ? "Not Setup" : "Active"}
                </div>
            </nav>
        </div>
    )
}

export default Dashboard