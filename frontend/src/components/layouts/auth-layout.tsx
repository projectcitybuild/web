import styles from "@/components/layouts/auth-layout.module.scss";
import Link from "next/link";
import { Routes } from "@/constants/routes";
import Image from "next/image";
import logoImage from "@/assets/images/logo.png";
import React, { ReactElement } from "react";

type Props = {
  children: ReactElement
}

export default function AuthLayout(props: Props) {
  return (
    <div className={`columns ${styles.columns}`}>
      <div className={`column is-half-tablet ${styles.formCol}`}>
        <Link href={Routes.HOME} className={styles.logo}>
          <Image
            src={logoImage}
            width={159}
            height={47}
            alt="Project City Build"
          />
        </Link>

        <main className={styles.contents}>
          {props.children}
        </main>
      </div>

      <div className={`column is-hidden-mobile ${styles.background}`}></div>
    </div>
  )
}