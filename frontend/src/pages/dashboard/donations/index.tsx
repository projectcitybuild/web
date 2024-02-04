import DashboardLayout from "@/components/layouts/dashboard-layout"
import withAuth from "@/hooks/withAuth"
import { useDonations } from "@/libs/account/useDonations"
import { Donation } from "@/types/donation"
import { GetStaticProps, NextPage } from "next"
import React, { useEffect, useState } from "react";
import { useFormatter } from "use-intl"

const Page: NextPage = (props): JSX.Element => {
  const formatter = useFormatter();
  const { getDonations } = useDonations()
  const [ loading, setLoading ] = useState(true)
  const [ donations, setDonations ] = useState<Donation[]>([])

  useEffect(() => {
    loadDonations()
  }, [])

  const loadDonations = async () => {
    setLoading(true)

    try {
      const donations = await getDonations()
      setDonations(donations)
    } catch {

    } finally {
      setLoading(false)
    }
  }

  const DonationRows = () => {
    const rows = donations?.map((donation, _) =>
      <tr key={donation.donation_id}>
        <td>
          <span className="tag">Active</span>
        </td>
        <td></td>
        <td>
          {
            formatter.dateTime(new Date(donation.created_at), {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
            })
          }
        </td>
        <td>Sep 5th, 2023</td>
        <td>-</td>
      </tr>
    )

    return <tbody>{rows}</tbody>
  }

  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Donation Perks</h1>

      <div className="card">
        { loading &&
          <span>Loading...</span>
        }
        { !loading &&
            <table className="table is-striped is-fullwidth">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Tier</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th>Gifter</th>
                </tr>
                </thead>
                <DonationRows />
            </table>
        }
      </div>
    </DashboardLayout>
  )
}

export const getStaticProps: GetStaticProps = async () => {
  return {
    props: {
      backgroundClassName: "background-secondary",
    }
  }
}

export default withAuth(Page)