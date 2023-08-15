import FilledButton from "@/components/filled-button"
import Icon, { IconToken } from "@/components/icon"
import AuthLayout from "@/components/layouts/auth-layout"
import { Routes } from "@/constants/Routes"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { useAuth } from "@/providers/useAuth"
import { NextPage } from "next"
import Link from "next/link"
import { useRouter } from "next/router"
import { useForm } from "react-hook-form"
import { yupResolver } from "@hookform/resolvers/yup"
import * as yup from "yup"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faLock } from "@fortawesome/free-solid-svg-icons";
import React, { useState } from "react";
import {Alert} from "@/components/alert";

type FormData = {
    password: string
}

const PasswordConfirm: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { confirmPassword } = useAuth()

    const schema = yup
        .object({
            password: yup
              .string()
              .required('Cannot be empty'),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState
    const [ loading, setLoading ] = useState(false)

    const onSubmit = async (data: FormData) => {
        setLoading(true)

        try {
            await confirmPassword({
                password: data.password,
            })
        } catch (error) {
            console.log(error)
            setError("root", { message: getHumanReadableError(error) })
        } finally {
            setLoading(false)
        }
    }

    return (
        <AuthLayout>
            <h1 className="text-heading-xl">Enter Your Password</h1>

            <div>Password confirmation is required to continue</div>

            <Alert error={errors.root?.message} />

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
                    <FilledButton
                      text="Confirm"
                      submit={true}
                      loading={loading}
                      disabled={loading}
                    />
                </div>
            </form>
        </AuthLayout>
    )
}

export default PasswordConfirm