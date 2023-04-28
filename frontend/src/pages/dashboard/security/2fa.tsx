import { NextPage } from "next"
import Link from "next/link";
import React, {useState} from "react";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faEnvelope} from "@fortawesome/free-solid-svg-icons";
import * as yup from "yup"
import {Routes} from "@/constants/routes";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup/dist/yup";
import {Alert} from "@/components/alert";

type FormData = {
    code: string
}

const TwoFactorAuthentication: NextPage = (props): JSX.Element => {
    const {
        user,
        twoFactorEnable,
        twoFactorQRCode,
        twoFactorRecoveryCodes,
        twoFactorConfirmSetup,
    } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    const [ loading, setLoading ] = useState(false)
    const [ confirmError, setConfirmError ] = useState<string|undefined>()
    const [ qrCode, setQrCode ] = useState<string|undefined>()

    const onEnableTwoFactor = async () => {
        setLoading(true)
        try {
            await twoFactorEnable()

            const qr = await twoFactorQRCode()
            setQrCode(qr.data.svg)

            await twoFactorRecoveryCodes()
        } catch (error) {

        } finally {
            setLoading(false)
        }
    }

    const onConfirmTwoFactorSetup = async (data: FormData) => {
        setLoading(true)

        twoFactorConfirmSetup({code: data.code})
            .then((res) => console.log(res))
            .catch((err) => setConfirmError(err.message))
            .finally(() => setLoading(false))
    }

    const schema = yup
        .object({
            code: yup.string().required(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    return (
        <div>
            {/*<div className="modal is-active">*/}
            {/*    <div className="modal-background"></div>*/}
            {/*    <div className="modal-content">*/}
            {/*        Todo*/}
            {/*    </div>*/}
            {/*    <button className="modal-close is-large" aria-label="close"></button>*/}
            {/*</div>*/}

            <Link href={Routes.SECURITY}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Two Step Verification</h1>

            <hr />

            <section className="section">
                <h2>2FA</h2>

                <button
                    className={`button is-primary ${loading ? 'is-loading' : ''}`}
                    disabled={loading}
                    onClick={onEnableTwoFactor}
                >Enable 2FA</button>

                { qrCode && (<p dangerouslySetInnerHTML={{ __html: qrCode }} />) }
                
            </section>

            <section className="section">
                <h1>Confirm</h1>

                <Alert error={confirmError} />

                <form onSubmit={handleSubmit(onConfirmTwoFactorSetup)}>
                    <div className="field">
                        <p className="control has-icons-left">
                            <input type="text" placeholder="2FA Code" className="input" {...register("code")} />
                            <span className="icon is-small is-left">
                                <FontAwesomeIcon icon={faEnvelope} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.code?.message}</p>
                    </div>
                    <div className="field">
                        <p className="control">
                            <button
                                type="submit"
                                disabled={formState.isSubmitting || loading}
                                className={`button is-success ${loading ? 'is-loading' : ''}`}
                            >
                                Confirm
                            </button>
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default TwoFactorAuthentication