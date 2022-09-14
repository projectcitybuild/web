import Choices from 'choices.js'

// TODO: this will break when a port is present
const baseURL = import.meta.env.VITE_APP_URL + '/api/'
const accountSearchURL = baseURL + 'v1/accounts/search'
const playerSearchURL = baseURL + 'v1/minecraft/aliases/search'
const debounceInMilliseconds = 350

document.querySelectorAll('[data-pcb-user-picker]')
    .forEach((element: HTMLSelectElement) => {
        const choices = new Choices(element, {
            allowHTML: false,
            callbackOnInit: () => {
                let task: number|null = null

                element.addEventListener(
                    'search',
                    (event) => {
                        // @ts-expect-error
                        const query = event.detail.value

                        clearTimeout(task)
                        task = window.setTimeout(() => {
                            choices.clearChoices()
                            choices.setChoices(async () => {
                                try {
                                    const accounts = await fetch(accountSearchURL + '?query=' + query)
                                    const json = await accounts.json()

                                    // @ts-expect-error
                                    return json.data.map(mapJSONToChoice(account))
                                } catch (err) {
                                    console.log(err)
                                }
                            })
                        }, debounceInMilliseconds)
                    },
                    false,
                )
            }
        })

    const preselectedId = element.dataset.accountId

    if (preselectedId !== null) {
        const preselectedUsername = element.dataset.accountUsername

        choices.setChoices([
            { value: preselectedId, label: `${preselectedUsername} (#${preselectedId})`, selected: true }
        ])
    }
})

document.querySelectorAll('[data-pcb-player-picker]').forEach((element: HTMLSelectElement) => {
    const choices = new Choices(element, {
        allowHTML: false,
        callbackOnInit: () => {
            let task: number | null = null

            element.addEventListener(
                'search',
                (event) => {
                    // @ts-expect-error
                    const query = event.detail.value

                    clearTimeout(task)
                    task = window.setTimeout(() => {
                        choices.clearChoices()
                        choices.setChoices(async () => {
                            try {
                                const aliases = await fetch(playerSearchURL + '?query=' + query)
                                const json = await aliases.json()

                                // @ts-expect-error
                                return json.data.map((alias) => {
                                    return {
                                        value: alias.player_id,
                                        label: `${alias.alias} (#${alias.player_id})`,
                                    }
                                })
                            } catch (err) {
                                console.log(err)
                            }
                        })
                    }, debounceInMilliseconds)
                },
                false,
            )
        }
    })

    const preselectedPlayerId = element.dataset.playerId
    const preselectedAliasString = element.dataset.aliasString ?? '(No Alias)'

    if (preselectedPlayerId !== null) {
        choices.setChoices([
            {
                value: preselectedPlayerId,
                label: `${preselectedAliasString} (#${preselectedPlayerId})`,
                selected: true,
            }
        ])
    }
})
