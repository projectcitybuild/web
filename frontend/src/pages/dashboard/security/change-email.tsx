import DashboardLayout from "@/components/layouts/dashboard-layout"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import withAuth from "@/hooks/withAuth"
import { useAccount } from "@/libs/account/AccountService"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { GetStaticProps, NextPage } from "next"
import Link from "next/link";
import React, {useState} from "react";
import {Alert} from "@/components/alert";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faEnvelope} from "@fortawesome/free-solid-svg-icons";
import {Routes} from "@/constants/Routes";
import * as yup from "yup";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup";

type FormData = {
    email: string
}

const ChangeEmail: NextPage = (props): JSX.Element => {
    const { updateEmail } = useAccount()
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
            await updateEmail({email: data.email })
        } catch (error) {
            console.log(error)
            setError("root", { message: getHumanReadableError(error) })
        } finally {
            setLoading(false)
        }
    }

    return (
        <DashboardSecurityLayout>
            <Link href={Routes.SECURITY}>
                <FontAwesomeIcon icon={faChevronLeft} /> Back
            </Link>

            <h1 className="text-heading-md">Email Address</h1>

            <hr />

            <form onSubmit={handleSubmit(onSubmit)}>
                <Alert
                    error={errors.root?.message}
                    success={success}
                />
                <div className="field">
                    <p className="control has-icons-left">
                        <input type="email" placeholder="New Email address" className="input" {...register("email")} />
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
                            Verify Email
                        </button>
                    </p>
                </div>
            </form>
        </DashboardSecurityLayout>
    )
}

export default withAuth(ChangeEmail)