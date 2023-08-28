import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import withAuth from "@/hooks/withAuth"
import { useAuth } from "@/providers/useAuth"
import { GetStaticProps } from "next"
import Link from "next/link";
import React from "react";
import { Routes } from "@/constants/Routes";

const Page = (): JSX.Element => {
  const { user } = useAuth()

  return (
    <DashboardSecurityLayout>
      <div className="list-rows">
        <div className="list-row">
          <div className="list-icon">
            <Icon token={IconToken.envelope} />
          </div>
          <div className="list-content">
            <h2 className="list-title">
              <Link href={Routes.CHANGE_EMAIL}>Email Address</Link>
            </h2>
            <span className="list-description">
                The email address associated with your account
              </span>
          </div>
          <div className="list-details">
            {user?.email}<br/>
            {user?.email_verified_at == null && (
              <span className="tag is-danger">Unverified</span>
            )}
            {user?.email_verified_at != null && (
              <span className="tag"><Icon token={IconToken.check} />&nbsp;Verified</span>
            )}
          </div>
        </div>

        <div className="list-row">
          <div className="list-icon">
            <Icon token={IconToken.lock} />
          </div>
          <div className="list-content">
            <h2 className="list-title">
              <Link href={Routes.CHANGE_PASSWORD}>Password</Link>
            </h2>
            <span className="list-description">
                Update the password used to sign-in to your account
              </span>
          </div>
          <div className="list-details">
            Last updated: {
              user?.password_changed_at == null
                ? 'Never'
                : user?.password_changed_at
            }
          </div>
        </div>

        <div className="list-row">
          <div className="list-icon">
            <Icon token={IconToken.mobile} />
          </div>
          <div className="list-content">
            <h2 className="list-title">
              <Link href={Routes.TWO_FACTOR_AUTH}>Two-Step Authentication</Link>
            </h2>
            <span className="list-description">
                Keep your account secure. Along with your password, you'll need to enter a code from another device
              </span>
          </div>
          <div className="list-details">
            {user?.two_factor_confirmed_at === null && (
              <span className="tag is-danger">Not Enabled</span>
            )}
            {user?.two_factor_confirmed_at !== null && (
              <span className="tag"><Icon token={IconToken.check} />&nbsp;Active</span>
            )}
          </div>
        </div>
      </div>
    </DashboardSecurityLayout>
  )
}

export const getStaticProps: GetStaticProps = async () => {
  return {
    props: {
      backgroundClassName: "background-secondary",
    }
  }
}

export default withAuth(Page)