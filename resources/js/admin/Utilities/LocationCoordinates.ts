type LocationCoordinate = string | number | null | undefined

export function formatLocationCoordinates(coordinates: LocationCoordinate[]): string | null {
    const values = coordinates.map((value) => {
        if (value === null || value === undefined) {
            return null
        }

        const coordinate = String(value).trim()

        return coordinate !== '' && Number.isFinite(Number(coordinate))
            ? coordinate
            : null
    })

    return values.every((value) => value !== null) ? values.join(', ') : null
}
