import styles from '@/components/server-feed.module.scss'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { faDiscord } from "@fortawesome/free-brands-svg-icons"

export default function ServerFeed() {
    return (
        <>
            <div className={styles.server}>
                <span className={styles.title}>Minecraft (Java)</span>
                <span className={["icon-text", styles.playerCount].join(" ")}>
                    <span className="icon">
                        <FontAwesomeIcon icon={faUser} />
                      <i className="fas user"></i>
                    </span>
                    <span>1 / 40</span>
                </span>
                <span className={styles.address}>
                    <a href="javascript:void(0)" data-server-address="">
                        <span className="icon">
                          <i className="fas fa-copy"></i>
                        </span>
                        192.168.0.1
                    </a>
                </span>
            </div>

            <div className={styles.server}>
                <span className={["icon-text", styles.title].join(" ")}>
                    <span className="icon">
                      <FontAwesomeIcon icon={faDiscord} />
                    </span>
                    <span>Discord</span>
                </span>
                <span className={styles.address}>
                    <a href="https://discord.gg/3NYaUeScDX" target="_blank" rel="noopener noreferrer">Connect / Open</a>
                </span>
            </div>
        </>
    )
}