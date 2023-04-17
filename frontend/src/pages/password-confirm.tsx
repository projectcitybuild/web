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
import {useState} from "react";

type FormData = {
    password: string
}

const PasswordConfirm: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { passwordConfirm } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    const schema = yup
        .object({
            password: yup.string().required(),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState
    const [ loading, setLoading ] = useState(false)

    const onSubmit = async (data: FormData) => {
        setLoading(true)

        try {
            await passwordConfirm({
                password: data.password,
            })
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
                Enter Your Password

                <RootError />

                <form onSubmit={handleSubmit(onSubmit)}>
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
                            <button
                                type="submit"
                                disabled={loading}
                                className={`button is-primary ${loading ? 'is-loading' : ''}`}
                            >Confirm</button>
                        </p>
                    </div>
                </form>
            </section>
        </div>
    )
}

export default PasswordConfirm