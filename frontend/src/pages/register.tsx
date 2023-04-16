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

interface Props {}

type FormData = {
    email: string
    password: string
}

const Register: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()
    const { login } = useAuth({
        middleware: 'guest',
        redirectIfAuthenticated: '/dashboard'
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
            await router.push('/dashboard')
        } catch (error) {
            if (error instanceof DisplayableError) {
                setError("root", { message: error.message })
            } else {
                console.error(error)
            }
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
                Register

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
                        <p className="control has-icons-left">
                            <input type="password" placeholder="Password" className="input" {...register("password")} />
                            <span className="icon is-small is-left">
                              <FontAwesomeIcon icon={faLock} />
                            </span>
                        </p>
                        <p className="help is-danger">{errors.password?.message}</p>
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

export default Register