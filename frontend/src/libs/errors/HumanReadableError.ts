import { FieldValues } from "react-hook-form/dist/types"
import { UseFormSetError } from "react-hook-form/dist/types/form"

/**
 * A user-facing error type.
 *
 * A typical Error contains technical terms that are usually
 * nonsensical or irrelevant to the user. An error type that
 * conforms to this type can be assumed safe to show to the user.
 */
export type HumanReadableError = {
  userFacingMessage: string
}

export function isHumanReadableError(error: any): error is HumanReadableError {
  return (error as HumanReadableError) !== undefined
}

/**
 * Returns a user-facing error message if possible, otherwise defaults
 * to a generic error message
 *
 * @param error The incoming error
 */
export function getHumanReadableError(error: any) {
  if (isHumanReadableError(error)) {
    return error.userFacingMessage
  } else {
    return "An unexpected error occurred"
  }
}