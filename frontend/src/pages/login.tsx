import { NextPage } from "next"
import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { DisplayableError } from "@/libs/http/api";
import { useAuth } from "@/hooks/useAuth";

interface Props {}

type FormData = {
    email: string
    password: string
}

const Login: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()
    const { login } = useAuth()

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
            await login({ email: data.email, password: data.password })
            await router.push('/dashboard')
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
            <h1>Login</h1>

            <form onSubmit={handleSubmit(onSubmit)}>
                <p>{errors.root?.message}</p>

                <input type="email" placeholder="foo@bar.com" {...register("email")} />
                <p>{errors.email?.message}</p>
                <input type="password" {...register("password")} />
                <p>{errors.password?.message}</p>

                <input type="submit" value="Sign In" disabled={formState.isSubmitting} />
            </form>
        </div>
    )
}

export default Login