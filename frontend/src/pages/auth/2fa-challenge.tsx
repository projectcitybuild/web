import {NextPage} from "next"
import {useRouter} from "next/router"
import {useForm} from "react-hook-form"
import {yupResolver} from "@hookform/resolvers/yup"
import * as yup from "yup"
import {AuthMiddleware, useAuth} from "@/libs/account/use2FA";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEnvelope, faLock} from "@fortawesome/free-solid-svg-icons";
import React, {useState} from "react";
import {Alert} from "@/components/alert";
import {Routes} from "@/constants/Routes";

type FormData = {
    code: string
}

const Challenge2FA: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { twoFactorChallenge } = useAuth({
        middleware: AuthMiddleware.GUEST,
    })
    const [ loading, setLoading ] = useState(false)
    const [ rootError, setRootError ] = useState<string|undefined>()

    const schema = yup
        .object({
            code: yup.number().required(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        console.log("test")
        setLoading(true)

        twoFactorChallenge({code: data.code})
            .then((res) => router.push(Routes.DASHBOARD))
            .catch((err) => setRootError(err.message))
            .finally(() => setLoading(false))
    }

    return (
        <div>
            <section className="section">
                <h1>Enter Device Code</h1>

                <Alert error={rootError} />

                <form onSubmit={handleSubmit(onSubmit)}>
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

export default Challenge2FA