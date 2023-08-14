import styles from "@/components/layouts/dashboard-layout.module.scss";
import NavBar, { NavBarColorVariant, NavBarMenuSet } from "@/components/navbar"
import React, { ReactElement } from "react";

type Props = {
  children: ReactElement|ReactElement[],
}

export default function DashboardLayout(props: Props) {
  return (
    <>
      <NavBar
        menuSet={NavBarMenuSet.dashboard}
        colorVariant={NavBarColorVariant.opaque}
      />

      <main className={styles.contents}>
        <div className="container">
          {props.children}
        </div>
      </main>
    </>
  )
}