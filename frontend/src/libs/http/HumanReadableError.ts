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