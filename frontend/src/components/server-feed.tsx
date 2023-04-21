import styles from '@/components/server-feed.module.scss'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faCopy, faUser } from '@fortawesome/free-solid-svg-icons'
import { faDiscord } from "@fortawesome/free-brands-svg-icons"
import {useEffect, useState} from "react";
import http from "@/libs/http/http";
import {Server} from "@/types/server";

type ServerProps = {
    server: Server,
}

export default function ServerFeed() {
    const [ loading, setLoading ] = useState(false)
    const [ error, setError ] = useState<Error|null>()
    const [ data, setData ] = useState<[Server]|null>()

    useEffect(() => {
        setLoading(true)
        setData(null)
        setError(null)

        http.get('servers')
            .then((res) => setData(res.data as [Server]))
            .catch(setError)
            .finally(() => setLoading(false))
    }, [])

    const ServerComponent = ({ server }: ServerProps) => {
        let address = server.ip_alias
        if (!address) {
            address = server.ip
            if (server.port) address += server.port
        }

        return (
            <div className={styles.server}>
                <div className={styles.title}>{server.name}</div>
                {
                    server.is_online && (
                        <div className={styles.playerCount} key={server.server_id}>
                            <FontAwesomeIcon icon={faUser} />
                            <span>{server.num_of_players} / {server.num_of_slots}</span>
                        </div>
                    )
                }
                <div className={styles.address}>
                    <a href="" data-server-address="">
                        <FontAwesomeIcon icon={faCopy} />
                        {address}
                    </a>
                </div>
            </div>
        )
    }

    return (
        <div className={styles.servers}>
            {
                loading && (
                    <div className={styles.server}>
                        <div className={styles.title}>Loading</div>
                    </div>
                )
            }
            {
                error && (
                    <div className={styles.server}>
                        <div className={styles.title}>Load failed</div>
                    </div>
                )
            }
            {
                data && data.map((server) => <ServerComponent server={server} />)
            }
            <div className={styles.server}>
                <span className={`icon-text ${styles.title}`}>
                    <span className="icon">
                      <FontAwesomeIcon icon={faDiscord} />
                    </span>
                    <span>Discord</span>
                </span>
                <span className={styles.address}>
                    <a href="https://discord.gg/3NYaUeScDX" target="_blank" rel="noopener noreferrer">Connect / Open</a>
                </span>
            </div>
        </div>
    )
}