import styles from '@/components/navbar.module.scss'
import Link from "next/link";
import {Routes} from "@/constants/routes";

export default function NavBar() {
    return (
        <nav className={["navbar is-fixed-top", styles.navbar].join(" ")} role="navigation" aria-label="main navigation">
            <div className="navbar-brand">
                <Link className="navbar-item" href={Routes.HOME}>
                    <img src="https://projectcitybuild.com/build/assets/logo-2x.d21e50a7.png" alt="Project City Build"/>
                </Link>

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
                            <Link className="button is-primary" href={Routes.REGISTER}>
                                <strong>Sign up</strong>
                            </Link>
                            <Link className="button is-light" href={Routes.LOGIN}>
                                Log in
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    )
}