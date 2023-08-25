import Icon, { IconToken } from "@/components/icon"
import { useAuth } from "@/providers/useAuth"
import Link from "next/link";
import Image from "next/image";
import styles from '@/components/navbar.module.scss'
import { Routes } from "@/constants/Routes";
import logoImage from '@/assets/images/logo.png';
import React, { useEffect } from "react"

export enum NavBarColorVariant {
  floatingTransparent,
  opaque,
}

type Props = {
  colorVariant: NavBarColorVariant,
}

export default function NavBar(props: Props) {
  const { user } = useAuth()
  const isLoggedIn = user != null

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

  let navClass = ["navbar"]

  switch (props.colorVariant) {
    case NavBarColorVariant.floatingTransparent:
      navClass = [...navClass, "is-fixed-top", "is-transparent", "is-clear-variant"]
      break

    case NavBarColorVariant.opaque:
      navClass = [...navClass, "is-main-variant"]
      break
  }

  return (
    <nav
      className={navClass.join(" ")}
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
          <Link className="navbar-item" href={Routes.HOME}>
            Home
          </Link>

          <a className="navbar-item" href="https://portal.projectcitybuild.com">
            Portal
          </a>

          <Link className="navbar-item" href={Routes.MAPS}>
            Live Maps
          </Link>

          <a className="navbar-item">
            Vote For Us
          </a>

          <a className="navbar-item">
            Donate
          </a>

          <div className="navbar-item has-dropdown is-hoverable">
            <a className="navbar-link">
              More
            </a>

            <div className="navbar-dropdown">
              <a className="navbar-item">
                Todo
              </a>
              <a className="navbar-item">
                Todo
              </a>
            </div>
          </div>
        </div>

        <div className="navbar-end">
          <div className="navbar-item">
            { isLoggedIn
              ? (
                <div className="buttons">
                  <Link className="button is-text" href={Routes.DASHBOARD}>
                    <span className="mr-2">Dashboard</span>
                    <Icon token={IconToken.arrowRight} />
                  </Link>
                </div>
              )
              : (
                <div className="buttons">
                  <Link className="button is-text" href={Routes.LOGIN}>
                    Log In
                  </Link>
                  <Link className="button is-light" href={Routes.REGISTER}>
                    Sign Up
                  </Link>
                </div>
              )
            }
          </div>
        </div>
      </div>
    </nav>
  )
}