import styles from "@/components/layouts/dashboard-layout.module.scss";
import Link from "next/link";
import { Routes } from "@/constants/routes";
import Image from "next/image";
import logoImage from "@/assets/images/logo.png";
import React, { ReactElement } from "react";
import { Urls } from "@/constants/urls";

type Props = {
  children: ReactElement
}

export default function DashboardLayout(props: Props) {
  return (
    <>
      <nav className="navbar" role="navigation" aria-label="main navigation">
        <div className="navbar-brand">
          <div className={`navbar-item ${styles.logoContainer}`}>
            <Link href={Routes.DASHBOARD}>
              <Image
                src={logoImage}
                width={159}
                height={47}
                alt="Project City Build"
              />
            </Link>
          </div>

          <a role="button" className="navbar-burger" aria-label="menu" aria-expanded="false"
             data-target="nav-menu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
          </a>
        </div>

        <div id="nav-menu" className="navbar-menu">
          <div className="navbar-start">
            <Link className="navbar-item" href={Routes.DASHBOARD}>
              Dashboard
            </Link>

            <Link className="navbar-item" href={Routes.SECURITY}>
              Account Settings
            </Link>

            <a className="navbar-item">
              Account Linking
            </a>

            <a className="navbar-item">
              Donations
            </a>

            <Link className="navbar-item" href={Routes.LOGOUT}>
              Logout
            </Link>
          </div>

          <div className="navbar-end">
            <div className="navbar-item">
              <div className="buttons">
                <Link className="button is-text" href={Routes.LOGIN}>
                  Username
                </Link>
                <Link className="button is-light" href={Routes.REGISTER}>
                  Apply for...
                </Link>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <main className={styles.contents}>
        {props.children}
      </main>
    </>
  )
}