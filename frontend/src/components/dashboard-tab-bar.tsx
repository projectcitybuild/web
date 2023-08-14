import React from "react"

export default function DashboardTabBar() {
  return (
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
  )
}