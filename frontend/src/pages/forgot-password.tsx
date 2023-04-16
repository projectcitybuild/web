import { NextPage } from "next"
import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { DisplayableError } from "@/libs/http/http";
import { useAuth } from "@/hooks/useAuth";
import NavBar from "@/components/navbar";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEnvelope, faLock} from "@fortawesome/free-solid-svg-icons";
import {useState} from "react";

interface Props {}

type FormData = {
    email: string
    password: string
}

const ForgotPassword: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()
    const { forgotPassword } = useAuth({
        middleware: 'guest',
        redirectIfAuthenticated: '/dashboard'
    })
    const [ loading, setLoading ] = useState(false)

    const schema = yup
        .object({
            email: yup.string().required().email(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        setLoading(true)

        try {
            await forgotPassword(data.email)
            // await router.push('/dashboard')
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

    const RootError = () => {
        if (!errors.root) return null;
        return (
            <div className="notification is-danger">
                {errors.root?.message}
            </div>
        )
    }

    return (
        <div>
            <NavBar />

            <section className="section">
                <h1>Forgot Email</h1>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <RootError />

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
                        <p className="control">
                            <input
                                type="submit"
                                value="Send Email"
                                disabled={formState.isSubmitting || loading}
                                className={`button is-success ${loading ? 'is-loading' : ''}`}
                            />
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default ForgotPassword