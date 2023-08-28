import FilledButton from "@/components/filled-button"
import FormField from "@/components/form-field"
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import withAuth from "@/hooks/withAuth"
import { useAccount } from "@/libs/account/useAccount"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { NextPage } from "next"
import Link from "next/link";
import { useRouter } from "next/router"
import React, { useEffect, useState } from "react";
import {Alert} from "@/components/alert";
import {Routes} from "@/constants/Routes";
import * as yup from "yup";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup";

type FormData = {
    email: string
}

const ChangeEmail: NextPage = (props): JSX.Element => {
    const router = useRouter()
    const { updateEmail } = useAccount()
    const [ loading, setLoading ] = useState(false)
    const [ success, setSuccess ] = useState("")
    const [ verified, _ ] = useState<boolean>(router.query.verified === "1")

    useEffect(() => {
        if (verified) {
            setSuccess("Email address successfully updated")
        }
    }, [verified])

    const schema = yup
        .object({
            email: yup.string()
              .required("Cannot be empty")
              .email("Must be a valid email address"),
        })
        .required()

    const { register, handleSubmit, formState, setError } = useForm<FormData>({ resolver: yupResolver(schema) })
    const { errors } = formState

    const onSubmit = async (data: FormData) => {
        setLoading(true)
        setSuccess("")

        try {
            await updateEmail({email: data.email })
            setSuccess(`A verification email has been sent to ${data.email}. Please follow the email instructions to complete the process`)
        } catch (error) {
            console.log(error)
            setError("root", { message: getHumanReadableError(error) })
        } finally {
            setLoading(false)
        }
    }

    return (
        <DashboardSecurityLayout>
            <header className="card-header">
                <p className="card-header-title">
                    <Link href={Routes.SETTINGS_SECURITY}>
                        <Icon token={IconToken.chevronLeft} /> Back
                    </Link>
                </p>
                <hr />
            </header>

            <div className="card-content">
                <div className="block">
                    <h1 className="text-heading-md">Update Email Address</h1>
                    We'll send a verification email to confirm that you own the new email address
                </div>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Alert
                      error={errors.root?.message}
                      success={success}
                    />

                    <div className="block">
                        <FormField
                          label="New Email Address"
                          errorText={errors.email?.message}
                        >
                            <p className="control has-icons-left">
                                <input
                                  type="text"
                                  placeholder="me@pcbmc.co"
                                  className={`input ${errors.email && "is-danger"}`}
                                  {...register("email")}
                                />
                                <span className="icon is-small is-left">
                                <Icon token={IconToken.envelope}/>
                            </span>
                            </p>
                        </FormField>
                    </div>

                    <div className="block">
                        <FilledButton
                          text="Verify Email"
                          submit={true}
                          loading={loading}
                          disabled={formState.isSubmitting || loading}
                        />
                    </div>
                </form>
            </div>
        </DashboardSecurityLayout>
    )
}

export default withAuth(ChangeEmail)