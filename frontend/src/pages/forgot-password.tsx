import {NextPage} from "next"
import {useForm} from "react-hook-form"
import {yupResolver} from "@hookform/resolvers/yup"
import * as yup from "yup"
import {DisplayableError} from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import NavBar from "@/components/navbar";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEnvelope} from "@fortawesome/free-solid-svg-icons";
import {useState} from "react";
import {Routes} from "@/constants/routes";

type FormData = {
    email: string
}

const ForgotPassword: NextPage = (props): JSX.Element => {
    const { forgotPassword } = useAuth({
        middleware: AuthMiddleware.GUEST,
        redirectIfAuthenticated: Routes.DASHBOARD,
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
            const message = await forgotPassword(data.email)
            setSuccess(message)

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
    const Success = () => {
        if (!success || success == "") return null;
        return (
            <div className="notification is-success">
                {success}
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
                    <Success />

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
                            <button
                                type="submit"
                                disabled={formState.isSubmitting || loading}
                                className={`button is-success ${loading ? 'is-loading' : ''}`}
                            >
                                Send Email
                            </button>
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default ForgotPassword