import { NextPage } from "next"
import { useRouter } from "next/router";
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup"
import {login} from "@/http/auth";

interface Props {}

type FormData = {
    email: string
    password: string
}

const schema = yup
    .object({
        email: yup.string().required().email(),
        password: yup.string().required(),
    })
    .required()

const Login: NextPage = (props: Props): JSX.Element => {
    const router = useRouter()

    const { register, handleSubmit, formState } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    // TODO
    const onSubmit = async (data: FormData) => {
        try {
            await login(data.email, data.password)
        } catch (e) {
            console.error(e)
        }
    }

    return (
        <div>
            <form onSubmit={handleSubmit(onSubmit)}>
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