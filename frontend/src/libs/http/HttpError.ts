import { HumanReadableError } from "@/libs/http/HumanReadableError"

export class UnauthenticatedError extends Error {}

/**
 * An error thrown by the backend API containing a message
 */
export class ResponseBodyError extends Error implements HumanReadableError {
  userFacingMessage: string

  constructor({ message }: { message: string }) {
    super()
    this.userFacingMessage = message
  }
}

export class ValidationError extends Error implements HumanReadableError {
  userFacingMessage: string

  constructor({ message }: { message: string }) {
    super()
    this.userFacingMessage = message
  }
}