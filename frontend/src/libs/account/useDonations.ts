import http from "@/libs/http/Http"
import { useAuth } from "@/providers/useAuth"
import { Donation } from "@/types/donation"
import querystring from "querystring"

export const useDonations = () => {
  const { csrf } = useAuth()

  const getDonations = async (): Promise<Donation[]> => {
    await csrf()

    try {
      const response = await http.get('account/donations')
      return response.data
    } catch {
      return []
    }
  }

  return {
    getDonations,
  }
}