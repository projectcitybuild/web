import { NextPage } from "next"
import Link from "next/link";
import React, {useState} from "react";
import {DisplayableError} from "@/libs/http/Http";
import {AuthMiddleware, useAuth} from "@/hooks/legacyUseAuth";
import {Alert} from "@/components/alert";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faLock} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/Routes";
import * as yup from "yup";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup";

type FormData = {
    oldPassword: string
    newPassword: string
    newPasswordConfirm: string
}

interface ErrorList {
    current_password?: string[]
    password?: string[]
    password_confirm?: string[]
}

const ChangePassword: NextPage = (props): JSX.Element => {
    const { updatePassword } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })
    const [ loading, setLoading ] = useState(false)
    const [ success, setSuccess ] = useState("")

    const schema = yup
        .object({
            oldPassword: yup.string().required(),
            newPassword: yup.string().required(),
            newPasswordConfirm: yup.string().required()
                .oneOf([yup.ref('newPassword')], 'New passwords must match'),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        setLoading(true)
        setSuccess("")

        updatePassword({
            oldPassword: data.oldPassword,
            newPassword: data.newPassword,
            newPasswordConfirm: data.newPasswordConfirm,
        })
            .catch(error => {
                setError("oldPassword", error.errors?.current_password?.first)
                setError("newPassword", error.errors?.password?.first)
                setError("newPasswordConfirm", error.errors?.passwordConfirm?.first)

                if (error instanceof DisplayableError) {
                    setError("root", { message: error.message })
                }
            })
            .finally(() => setLoading(false))
    }

    return (
        <div>
            <Link href={Routes.SECURITY}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1>Update Password</h1>

            <hr />

            <form onSubmit={handleSubmit(onSubmit)}>
                <Alert
                    error={errors.root?.message}
                    success={success}
                />
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="password" placeholder="Current Password" className="input" {...register("oldPassword")} />
                        <span className="icon is-small is-left">
                            <FontAwesomeIcon icon={faLock} />
                        </span>
                    </p>
                    <p className="help is-danger">{errors.oldPassword?.message}</p>
                </div>
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="password" placeholder="New Password" className="input" {...register("newPassword")} />
                        <span className="icon is-small is-left">
                            <FontAwesomeIcon icon={faLock} />
                        </span>
                    </p>
                    <p className="help is-danger">{errors.newPassword?.message}</p>
                </div>
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="password" placeholder="New Password (Confirm)" className="input" {...register("newPasswordConfirm")} />
                        <span className="icon is-small is-left">
                            <FontAwesomeIcon icon={faLock} />
                        </span>
                    </p>
                    <p className="help is-danger">{errors.newPasswordConfirm?.message}</p>
                </div>
                <div className="field">
                    <p className="control">
                        <button
                            type="submit"
                            disabled={formState.isSubmitting || loading}
                            className={`button is-success ${loading ? 'is-loading' : ''}`}
                        >
                            Update
                        </button>
                    </p>
                </div>
            </form>
        </div>
    )
}

export default ChangePassword