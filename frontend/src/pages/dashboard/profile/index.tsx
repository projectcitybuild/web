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
            <Icon token={IconToken.user} />
          </div>
          <div className="list-content">
            <h2 className="list-title">
              <Link href={Routes.CHANGE_USERNAME}>Username</Link>
            </h2>
            <span className="list-description">
                The unique name associated with your account
              </span>
          </div>
          <div className="list-details">
            {user?.username}
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