import React, { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faWarning } from "@fortawesome/free-solid-svg-icons";

export type AlertProps = {
    error: string|undefined,
}

export const Alert = (props: AlertProps) => {
    const [ message, setMessage ] = useState<string|null>(props.error ?? null);

    useEffect(() => {
        setMessage(props.error ?? null)
    }, [props.error]);

    const onDismiss = () => {
        setMessage(null)
    }

    if (! message) return null;

    return (
        <div className="notification is-danger">
            <a className="delete" onClick={onDismiss}></a>
            <h1>
                <strong><FontAwesomeIcon icon={faWarning} /> Error</strong>
            </h1>
            {props.error}
        </div>
    )
}