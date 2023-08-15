import React, {useState} from "react";
import {AuthMiddleware, useAuth} from "@/libs/account/use2FA";

export const TwoFactorAuthEnable = () => {
    const {
        twoFactorEnable,
        twoFactorQRCode,
        twoFactorRecoveryCodes,
    } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    const [ loading, setLoading ] = useState(false)
    const [ confirmError, setConfirmError ] = useState<string|undefined>()
    const [ qrCode, setQrCode ] = useState<string|undefined>()
    const [ recoveryCodes, setRecoveryCodes ] = useState<[string]>()

    const onEnableTwoFactor = async () => {
        setLoading(true)
        try {
            await twoFactorEnable()

            twoFactorQRCode()
                .then(svg => setQrCode(svg))

            twoFactorRecoveryCodes()
                .then(recoveryCodes => setRecoveryCodes(recoveryCodes))

        } catch (error) {

        } finally {
            setLoading(false)
        }
    }

    return (
        <section className="section">
            <h2>2FA</h2>

            <button
                className={`button is-primary ${loading ? 'is-loading' : ''}`}
                disabled={loading}
                onClick={onEnableTwoFactor}
            >Enable 2FA</button>

            { qrCode && (<span dangerouslySetInnerHTML={{ __html: qrCode }} />) }

            <ul>
                { recoveryCodes?.map(code => (<li>{code}</li>)) }
            </ul>

        </section>
    )
}