import FilledButton from "@/components/filled-button"
import Icon, { IconToken } from "@/components/icon"
import DashboardSecurityLayout from "@/components/layouts/dashboard-security-layout"
import withAuth from "@/hooks/withAuth"
import { useAccount } from "@/libs/account/useAccount"
import { getHumanReadableError } from "@/libs/errors/HumanReadableError"
import { NextPage } from "next"
import Link from "next/link";
import React, { useState } from "react";
import { Alert } from "@/components/alert";
import { Routes } from "@/constants/Routes";
import * as yup from "yup";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";

type FormData = {
  oldPassword: string
  newPassword: string
  newPasswordConfirm: string
}

const ChangePassword: NextPage = (props): JSX.Element => {
  const { updatePassword } = useAccount()
  const [ loading, setLoading ] = useState(false)
  const [ success, setSuccess ] = useState("")

  const schema = yup
    .object({
      oldPassword: yup.string()
        .required("Cannot be empty"),
      newPassword: yup.string()
        .required("Cannot be empty")
        .notOneOf([ yup.ref("oldPassword") ], "Cannot be the same as your current password"),
      newPasswordConfirm: yup.string()
        .required("Cannot be empty")
        .oneOf([ yup.ref("newPassword") ], "New passwords must match"),
    })
    .required()

  const { register, handleSubmit, formState, setError, reset } = useForm<FormData>({ resolver: yupResolver(schema) })
  const { errors } = formState

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    setSuccess("")

    try {
      await updatePassword({
        oldPassword: data.oldPassword,
        newPassword: data.newPassword,
        newPasswordConfirm: data.newPasswordConfirm,
      })
      setSuccess("Password successfully updated")
      reset()
    } catch (error: any) {
      setError("root", { message: getHumanReadableError(error) })
    } finally {
      setLoading(false)
    }
  }

  return (
    <DashboardSecurityLayout>
      <Link href={Routes.SETTINGS_SECURITY}>
        <Icon token={IconToken.chevronLeft} /> Back
      </Link>

      <h1 className="text-heading-md">Update Password</h1>

      <hr/>

      <Alert
        error={errors.root?.message}
        success={success}
      />

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="field">
          <p className="control has-icons-left">
            <input type="password" placeholder="Current Password" className="input" {...register("oldPassword")} />
            <span className="icon is-small is-left">
              <Icon token={IconToken.lock} />
            </span>
          </p>
          <p className="help is-danger">{errors.oldPassword?.message}</p>
        </div>
        <div className="field">
          <p className="control has-icons-left">
            <input type="password" placeholder="New Password" className="input" {...register("newPassword")} />
            <span className="icon is-small is-left">
              <Icon token={IconToken.lock} />
            </span>
          </p>
          <p className="help is-danger">{errors.newPassword?.message}</p>
        </div>
        <div className="field">
          <p className="control has-icons-left">
            <input
              type="password"
              placeholder="New Password (Confirm)"
              className="input"
              {...register("newPasswordConfirm")}
            />
            <span className="icon is-small is-left">
              <Icon token={IconToken.lock} />
            </span>
          </p>
          <p className="help is-danger">{errors.newPasswordConfirm?.message}</p>
        </div>
        <div className="field">
          <p className="control">
            <FilledButton
              text="Update"
              submit={true}
              loading={loading}
              disabled={formState.isSubmitting || loading}
            />
          </p>
        </div>
      </form>
    </DashboardSecurityLayout>
  )
}

export default withAuth(ChangePassword)