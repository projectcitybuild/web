import React, { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faWarning } from "@fortawesome/free-solid-svg-icons";

export type AlertProps = {
    error?: string,
    success?: string,
}

export const Alert = (props: AlertProps) => {
    const [ error, setError ] = useState<string|null>();
    const [ success, setSuccess ] = useState<string|null>();

    useEffect(() => {
        setError(props.error ?? null)
        setSuccess(props.success ?? null)
    }, [props.error]);

    return (
        <>
            <SuccessAlert
                message={success ?? null}
                onDismiss={() => setSuccess(null)}
            />
            <ErrorAlert
                message={error ?? null}
                onDismiss={() => setError(null)}
            />
        </>
    )
}

const ErrorAlert = (props: {message: string|null, onDismiss: () => void}) => {
    if (! props.message || props.message == "") return null;

    return (
        <div className="notification is-danger">
            <a className="delete" onClick={props.onDismiss}></a>
            <h1>
                <strong><FontAwesomeIcon icon={faWarning} /> Error</strong>
            </h1>
            {props.message}
        </div>
    )
}

const SuccessAlert = (props: {message: string|null, onDismiss: () => void}) => {
    if (! props.message || props.message == "") return null;

    return (
        <div className="notification is-success">
            <a className="delete" onClick={props.onDismiss}></a>
            {props.message}
        </div>
    )
}