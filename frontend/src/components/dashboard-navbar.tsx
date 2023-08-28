import logoImage from "@/assets/images/logo.png";
import Icon, { IconToken } from "@/components/icon"
import styles from "@/components/navbar.module.scss"
import { Routes } from "@/constants/Routes";
import { useAuth } from "@/providers/useAuth"
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/router"
import React, { useEffect } from "react"

export default function DashboardNavBar() {
  const { user, logout } = useAuth()
  const router = useRouter()

  useEffect(() => {
    const burgers = Array.prototype.slice
      .call(document.querySelectorAll('.navbar-burger'), 0)

    burgers.forEach(element => {
      element.addEventListener('click', () => {
        const target = element.dataset.target
        const targetElement = document.getElementById(target)

        element.classList.toggle('is-active')
        targetElement?.classList.toggle('is-active')
      })
    })
  }, [])

  return (
    <nav
      className="navbar is-main-variant"
      role="navigation"
      aria-label="main navigation"
    >
      <div className="navbar-brand">
        <div className={`navbar-item ${styles.logoContainer}`}>
          <Link href={Routes.HOME}>
            <Image
              src={logoImage}
              width={159}
              height={47}
              alt="Project City Build"
            />
          </Link>
        </div>

        <a
          role="button"
          className="navbar-burger"
          aria-label="menu"
          aria-expanded="false"
          data-target="nav-menu"
        >
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="nav-menu" className="navbar-menu">
        <div className="navbar-start">
          <Link className={`navbar-item is-tab ${router.pathname == '/dashboard' && 'is-active'}`} href={Routes.DASHBOARD}>
            Dashboard
          </Link>

          <Link className={`navbar-item is-tab ${router.pathname == '/dashboard/linking' && 'is-active'}`} href={Routes.LINKING}>
            Account Linking
          </Link>

          <Link className={`navbar-item is-tab ${router.pathname == '/dashboard/donations' && 'is-active'}`} href={Routes.DONATIONS}>
            Donations
          </Link>

          <Link className={`navbar-item is-tab ${router.pathname.match(/^(\/dashboard\/(security|profile))+/) && 'is-active'}`} href={Routes.SETTINGS_SECURITY}>
            Account Settings
          </Link>
        </div>

        <div className="navbar-end">
          <div className="navbar-item has-dropdown is-hoverable">
            <a className="navbar-link">
              { user?.avatarUrl &&
                  <figure className="image mr-2" style={{"width": 28}}>
                      <img
                          className="is-rounded"
                          src={user?.avatarUrl}
                      />
                  </figure>
              }

              { user?.username ?? "-" }
              &nbsp;
              <Icon token={IconToken.caretDown} />
            </a>

            <div className="navbar-dropdown">
              <a className="navbar-item" onClick={logout}>
                Logout
              </a>
            </div>
          </div>
        </div>
      </div>
    </nav>
  )
}