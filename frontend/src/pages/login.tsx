import {NextPage} from "next"
import {useRouter} from "next/router"
import {useForm} from "react-hook-form"
import {yupResolver} from "@hookform/resolvers/yup"
import * as yup from "yup"
import {DisplayableError} from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEnvelope, faLock} from "@fortawesome/free-solid-svg-icons";
import React from "react";
import Link from "next/link";
import {Routes} from "@/constants/routes";
import {Alert} from "@/components/alert";
import {faChevronLeft} from "@fortawesome/free-solid-svg-icons";

type FormData = {
    email: string
    password: string
}

const Login: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { login } = useAuth({
        middleware: AuthMiddleware.GUEST,
        redirectIfAuthenticated: Routes.DASHBOARD,
    })

    const schema = yup
        .object({
            email: yup.string().required().email(),
            password: yup.string().required(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        try {
            await login({email: data.email, password: data.password})
        } catch (error) {
            if (error instanceof DisplayableError) {
                setError("root", { message: error.message })
            } else {
                console.error(error)
            }
        }
    }

    return (
        <div>
            <Link href={Routes.HOME}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <section className="section">
                <form onSubmit={handleSubmit(onSubmit)}>
                    <Alert error={errors.root?.message} />

                    <div className="field">
                        <p className="control has-icons-left">
                            <input type="email" placeholder="Email address" className="input" {...register("email")} />
                            <span className="icon is-small is-left">
                                <FontAwesomeIcon icon={faEnvelope} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.email?.message}</p>
                    </div>
                    <div className="field">
                        <p className="control has-icons-left">
                            <input type="password" placeholder="Password" className="input" {...register("password")} />
                            <span className="icon is-small is-left">
                              <FontAwesomeIcon icon={faLock} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.password?.message}</p>
                    </div>
                    <div className="field">
                        <p className="help">
                            <Link href={Routes.FORGOT_PASSWORD}>Forgot password?</Link>
                        </p>
                    </div>
                    <div className="field">
                        <p className="control">
                            <input type="submit" value="Sign In" disabled={formState.isSubmitting} className="button is-success" />
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default Login