export const BuilderRankApplicationStatus = {
    pending: 1,
    approved: 2,
    denied: 3,
} as const satisfies Record<string, App.Domains.BuilderRankApplications.Data.ApplicationStatus>
