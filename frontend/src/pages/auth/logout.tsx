import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {NextPage} from "next";
import React, { useEffect } from "react";
import { useRouter } from "next/router";
import {Routes} from "@/constants/routes";
import styles from "./logout.module.scss";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {faCube} from "@fortawesome/free-solid-svg-icons";
import Link from "next/link";

const Logout: NextPage = (): JSX.Element => {
  const { logout } = useAuth({
      middleware: AuthMiddleware.AUTH,
  })
  const router = useRouter()

  useEffect(() => {
      logout()
          .then(() => router.push(Routes.LOGIN))
          .catch(console.error)
  }, [])

  return (
    <div className={`hero is-fullheight ${styles.hero}`}>
      <div className="hero-body has-text-centered">
        <div className="container">
          <h1 className="text-heading-sm mb-3">Logging out...</h1>
          <FontAwesomeIcon icon={faCube} beatFade={true} size={"3x"} />
        </div>
      </div>
      <div className="hero-foot has-text-centered">
        <div className="container">
          <span className="text-body-sm">
            Page not loading? Go <Link href={Routes.HOME}>back home</Link>
          </span>
        </div>
      </div>
    </div>
  )
}

export default Logout