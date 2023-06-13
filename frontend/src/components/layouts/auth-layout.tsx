import styles from "@/components/layouts/auth-layout.module.scss";
import Link from "next/link";
import { Routes } from "@/constants/routes";
import Image from "next/image";
import logoImage from "@/assets/images/logo.png";
import React, { ReactElement } from "react";
import { Urls } from "@/constants/urls";

type Props = {
  children: ReactElement
}

export default function AuthLayout(props: Props) {
  return (
    <div className={`columns ${styles.columns}`}>
      <div className={`column is-half-tablet ${styles.contentsCol}`}>
        <nav>
          <Link href={Routes.HOME} className={styles.logo}>
            <Image
              src={logoImage}
              width={159}
              height={47}
              alt="Project City Build"
            />
          </Link>
        </nav>

        <main className={styles.contents}>
          {props.children}
        </main>

        <footer className={styles.footer}>
          <a href={Urls.TERMS_OF_SERVICE} target="_blank">Terms of Service</a> | <a href={Urls.PRIVACY_POLICY} target="_blank">Privacy Policy</a>
        </footer>
      </div>

      <div className={`column is-hidden-mobile ${styles.background}`}></div>
    </div>
  )
}