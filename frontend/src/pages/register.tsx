import {NextPage} from "next"
import {useRouter} from "next/router"
import {useForm} from "react-hook-form"
import {yupResolver} from "@hookform/resolvers/yup"
import * as yup from "yup"
import {DisplayableError} from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import NavBar from "@/components/navbar";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEnvelope, faLock, faUser} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/routes";
import React, {useState} from "react";
import {Alert} from "@/components/alert";

type FormData = {
    username: string
    email: string
    password: string
    passwordConfirm: string
}

const Register: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { register: registerAccount, login } = useAuth({
        middleware: AuthMiddleware.GUEST,
        redirectIfAuthenticated: Routes.DASHBOARD,
    })

    const schema = yup
        .object({
            username: yup.string().required(),
            email: yup.string().required().email(),
            password: yup.string().required(),
            passwordConfirm: yup.string().required()
                .oneOf([yup.ref('password')], 'Passwords must match'),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState
    const [ loading, setLoading ] = useState(false)

    const onSubmit = async (data: FormData) => {
        setLoading(true)

        try {
            await registerAccount({
                username: data.username,
                email: data.email,
                password: data.password,
                passwordConfirm: data.passwordConfirm,
            })
            await login({
                email: data.email,
                password: data.password,
            })
            router.push(Routes.VERIFY_EMAIL)
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
            <NavBar />

            <section className="section">
                Register

                <Alert error={errors.root?.message} />

                <form onSubmit={handleSubmit(onSubmit)}>
                    <div className="field">
                        <p className="control has-icons-left">
                            <input type="text" placeholder="Username" className="input" {...register("username")} />
                            <span className="icon is-small is-left">
                                <FontAwesomeIcon icon={faUser} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.username?.message}</p>
                    </div>
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
                        <p className="control has-icons-left">
                            <input type="password" placeholder="Password (Confirm)" className="input" {...register("passwordConfirm")} />
                            <span className="icon is-small is-left">
                              <FontAwesomeIcon icon={faLock} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.passwordConfirm?.message}</p>
                    </div>
                    <div className="field">
                        <p className="control has-icons-left">
                            TODO: agree to terms
                        </p>
                        {/*<p className="help is-danger">{errors.password?.message}</p>*/}
                    </div>
                    <div className="field">
                        <p className="control">
                            <button
                                type="submit"
                                disabled={loading}
                                className={`button is-primary ${loading ? 'is-loading' : ''}`}
                            >Create Account</button>
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default Register