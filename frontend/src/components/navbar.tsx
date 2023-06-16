import Link from "next/link";
import Image from "next/image";
import styles from '@/components/navbar.module.scss'
import { Routes } from "@/constants/routes";
import logoImage from '@/assets/images/logo.png';
import React, { ReactElement, useEffect, useRef } from "react"

export enum NavBarMenuSet {
  home,
  dashboard,
}

export enum NavBarColorVariant {
  floatingTransparent,
  opaque,
}

type Props = {
  menuSet: NavBarMenuSet,
  colorVariant: NavBarColorVariant,
}

const variantElement = (menuSet: NavBarMenuSet) => {
  switch (menuSet) {
    case NavBarMenuSet.home: return HomeVariant();
    case NavBarMenuSet.dashboard: return DashboardVariant();
  }
}

export default function NavBar(props: Props) {
  useEffect(() => {
    const burgers = Array.prototype.slice
      .call(document.querySelectorAll('.navbar-burger'), 0)

    burgers.forEach(el => {
      el.addEventListener('click', () => {
        const target = el.dataset.target
        const targetElement = document.getElementById(target)

        el.classList.toggle('is-active')
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
    <NavBarWrapper className={navClass.join(" ")}>
      {variantElement(props.menuSet)}
    </NavBarWrapper>
  )
}

const NavBarWrapper = (props: {className: string, children: ReactElement}) => {
  return (
    <nav
      className={props.className}
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
        { props.children }
      </div>
    </nav>
  )
}

const HomeVariant = () => {
  return (
    <>
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
          <div className="buttons">
            <Link className="button is-text" href={Routes.LOGIN}>
              Log In
            </Link>
            <Link className="button is-light" href={Routes.REGISTER}>
              Sign Up
            </Link>
          </div>
        </div>
      </div>
    </>
  )
}

const DashboardVariant = () => {
  return (
    <>
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

            Username
          </a>

          <div className="navbar-dropdown">
            <hr className="navbar-divider" />

            <Link className="navbar-item" href={Routes.LOGOUT}>
              Logout
            </Link>
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
    </>
  )
}