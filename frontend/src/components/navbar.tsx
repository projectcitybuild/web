import styles from '@/components/navbar.module.scss'
import Link from "next/link";
import {Routes} from "@/constants/routes";
import Image from "next/image";
import logoImage from '../assets/images/logo.png';

export default function NavBar() {
    return (
        <nav className={`navbar is-fixed-top is-transparent ${styles.navbar}`} role="navigation" aria-label="main navigation">
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

                <a role="button" className="navbar-burger" aria-label="menu" aria-expanded="false"
                   data-target="nav-menu">
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

                    <a className="navbar-item">
                        Live Maps
                    </a>

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
                            <Link className="button is-text" href={Routes.REGISTER}>
                                Log In
                            </Link>
                            <Link className="button is-light" href={Routes.LOGIN}>
                                Sign Up
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    )
}