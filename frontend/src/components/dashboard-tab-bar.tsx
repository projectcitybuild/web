import Icon, { IconToken } from "@/components/icon"
import { Routes } from "@/constants/Routes"
import Link from "next/link"
import { useRouter } from "next/router"
import React from "react"

export default function DashboardTabBar() {
  const router = useRouter()

  return (
    <div className="tab-bar mb-4 mt-2">
      <div className={`tab-item ${router.pathname.match(/^(\/dashboard\/profile)+/) && 'is-active'}`}>
        <Link href={Routes.SETTINGS_PROFILE}>
          Profile
        </Link>
      </div>
      <div className={`tab-item ${router.pathname.match(/^(\/dashboard\/security)+/) && 'is-active'}`}>
        <Link href={Routes.SETTINGS_SECURITY}>
          Security
        </Link>
      </div>
      <div className="tab-item">
        Billing <Icon token={IconToken.externalLink} />
      </div>
    </div>
  )
}