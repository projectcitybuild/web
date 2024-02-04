import http from "@/libs/http/Http"
import { useAuth } from "@/providers/useAuth"
import { Donation } from "@/types/donation"
import querystring from "querystring"

export const useBilling = () => {
  const { csrf } = useAuth()

  const billingPortalUrl = async (props: {
    returnUrl: string,
  }) => {
    await csrf()

    try {
      const response = await http.post('account/billing', {
        return_url: props.returnUrl,
      })
      return response.data.url
    } catch {
      return []
    }
  }

  return {
    billingPortalUrl,
  }
}