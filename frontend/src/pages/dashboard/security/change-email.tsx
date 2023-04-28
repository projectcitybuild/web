import { NextPage } from "next"
import Link from "next/link";
import React, {useState} from "react";
import {DisplayableError} from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {Alert} from "@/components/alert";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faEnvelope} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/routes";
import * as yup from "yup";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup";

type FormData = {
    email: string
}

const ChangeEmail: NextPage = (props): JSX.Element => {
    const { updateEmail } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })
    const [ loading, setLoading ] = useState(false)
    const [ success, setSuccess ] = useState("")

    const schema = yup
        .object({
            email: yup.string().required().email(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        setLoading(true)
        setSuccess("")

        try {
            // TODO
        } catch (error) {
            if (error instanceof DisplayableError) {
                setError("root", { message: error.message })
            } else {
                console.error(error)
            }
        } finally {
            setLoading(false)
        }
    }

    return (
        <div>
            <Link href={Routes.SECURITY}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Email Address</h1>

            <hr />

            <form onSubmit={handleSubmit(onSubmit)}>
                <Alert
                    error={errors.root?.message}
                    success={success}
                />
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="email" placeholder="New Email address" className="input" {...register("email")} />
                        <span className="icon is-small is-left">
                                <FontAwesomeIcon icon={faEnvelope} />
                            </span>
                    </p>
                    <p className="help is-danger">{errors.email?.message}</p>
                </div>
                <div className="field">
                    <p className="control">
                        <button
                            type="submit"
                            disabled={formState.isSubmitting || loading}
                            className={`button is-success ${loading ? 'is-loading' : ''}`}
                        >
                            Verify Email
                        </button>
                    </p>
                </div>
            </form>
        </div>
    )
}

export default ChangeEmail