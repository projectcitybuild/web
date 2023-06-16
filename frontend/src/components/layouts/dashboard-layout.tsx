import styles from "@/components/layouts/dashboard-layout.module.scss";
import NavBar, { NavBarVariant } from "@/components/navbar"
import React, { ReactElement } from "react";

type Props = {
  children: ReactElement
}

export default function DashboardLayout(props: Props) {
  return (
    <>
      <NavBar variant={NavBarVariant.dashboard} />

      <main className={styles.contents}>
        {props.children}
      </main>
    </>
  )
}