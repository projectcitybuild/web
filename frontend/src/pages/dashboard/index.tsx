import { Alert } from "@/components/alert"
import withAuth from "@/hooks/withAuth"
import { useAuth } from "@/providers/useAuth"
import { GetStaticProps } from "next"
import { useRouter } from "next/router"
import React, { useEffect, useState } from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

const Page = (): JSX.Element => {
  const { user } = useAuth()
  const router = useRouter()
  const [verified, _] = useState<boolean>(router.query.verified === "1")
  const [success, setSuccess] = useState("")

  useEffect(() => {
    if (verified) {
      setSuccess("Your email address has been verified")
    }
  }, [verified])

  return (
    <DashboardLayout>
      <Alert success={success} />

      <h1 className="text-heading-xl">Welcome back, {user?.username}</h1>

      <div className="card">
        <div className="card-content">
          TODO
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