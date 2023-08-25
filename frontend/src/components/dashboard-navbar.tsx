import { useAuth } from "@/providers/useAuth"
import Link from "next/link";
import Image from "next/image";
import styles from '@/components/navbar.module.scss'
import { Routes } from "@/constants/Routes";
import logoImage from '@/assets/images/logo.png';
import React, { ReactElement, useEffect, useRef } from "react"

export default function DashboardNavBar() {
  const { user, logout } = useAuth()

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
          <Link className="navbar-item" href={Routes.DASHBOARD}>
            Dashboard
          </Link>

          <a className="navbar-item">
            Account Linking
          </a>

          <Link className="navbar-item" href={Routes.DONATIONS}>
            Donations
          </Link>

          <Link className="navbar-item" href={Routes.SETTINGS_SECURITY}>
            Account Settings
          </Link>
        </div>

        <div className="navbar-end">
          <div className="navbar-item has-dropdown is-hoverable">
            <a className="navbar-link">
              <figure className="image mr-2" style={{"width": 28}}>
                <img
                  className="is-rounded"
                  src="https://bulma.io/images/placeholders/128x128.png"
                />
              </figure>

              { user?.username ?? "-" }
            </a>

            <div className="navbar-dropdown">
              <hr className="navbar-divider" />

              <a className="navbar-item" onClick={logout}>
                Logout
              </a>
            </div>
          </div>

          <div className="navbar-item">
            <div className="buttons">
              <Link className="button is-light" href={Routes.REGISTER}>
                Apply for...
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>
  )
}