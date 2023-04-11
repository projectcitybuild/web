import styles from '@/components/navbar.module.scss'

export default function NavBar() {
    return (
        <nav className={["navbar is-fixed-top", styles.navbar].join(" ")} role="navigation" aria-label="main navigation">
            <div className="navbar-brand">
                <a className="navbar-item" href="https://projectcitybuild.com">
                    <img src="https://projectcitybuild.com/build/assets/logo-2x.d21e50a7.png" alt="Project City Build"/>
                </a>

                <a role="button" className="navbar-burger" aria-label="menu" aria-expanded="false"
                   data-target="nav-menu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="nav-menu" className="navbar-menu">
                <div className="navbar-start">
                    <a className="navbar-item">
                        Home
                    </a>

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
                            <a className="button is-primary">
                                <strong>Sign up</strong>
                            </a>
                            <a className="button is-light">
                                Log in
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    )
}