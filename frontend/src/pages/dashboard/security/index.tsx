import Icon, { IconToken } from "@/components/icon"
import DashboardLayout from "@/components/layouts/dashboard-layout"
import withAuth from "@/hooks/withAuth"
import { useAuth } from "@/providers/useAuth"
import { GetStaticProps } from "next"
import Link from "next/link";
import React from "react";
import { Routes } from "@/constants/Routes";

const Page = (): JSX.Element => {
  const { user } = useAuth()

  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Account Settings</h1>

      <div className="tab-bar mb-4 mt-2">
        <div className="tab-item">
          Profile
        </div>
        <div className="tab-item is-active">
          Security
        </div>
        <div className="tab-item">
          Billing
        </div>
      </div>

      <div className="card">

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
                <span className="tag is-success">Verified</span>
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
              Last updated: TODO
            </div>
          </div>

          <div className="list-row">
            <div className="list-icon">
              <Icon token={IconToken.lock} />
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
              {user?.two_factor_confirmed_at == null ? "Not Setup" : "Active"}
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
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