export class UnauthenticatedError extends Error {}

/**
 * Represents an error thrown by the backend API that
 * contains an error message we can show to the user
 */
export class ResponseBodyError extends Error {
  message: string

  constructor({
    message,
  }: {
    message: string
  }) {
    super()
    this.message = message
  }
}