import { NextPage } from "next"
import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { Auth } from "@/http/auth"
import { DisplayableError } from "@/http/api";

interface Props {}

type FormData = {
    email: string
    password: string
}

const Login: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()
    const auth = new Auth()

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
            await auth.login(data.email, data.password)
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