import Choices from 'choices.js';

// TODO: this will break when a port is present
const baseURL = import.meta.env.VITE_APP_URL;
const accountSearchURL = baseURL + '/api/accounts/search'

const element = document.querySelector('[data-pcb-user-picker]') as HTMLSelectElement;
if (element !== null) {
    const choices = new Choices(element, {
        allowHTML: false,
        callbackOnInit: () => {
            let task: number | null = null

            element.addEventListener(
                'search',
                (event) => {
                    // @ts-expect-error
                    const query = event.detail.value;

                    clearTimeout(task);
                    task = window.setTimeout(() => {
                        choices.clearChoices();
                        choices.setChoices(async () => {
                            try {
                                const accounts = await fetch(accountSearchURL + '?query=' + query);
                                const json = await accounts.json()

                                // @ts-expect-error
                                return json.data.map((account) => {
                                    return { value: account.account_id, label: `${account.username} (${account.email})` }
                                });
                            } catch (err) {
                                console.log(err);
                            }
                        });
                    }, 350);
                },
                false,
            );
        }
    });

    const preselectedId = element.dataset.accountId;

    if (preselectedId !== null) {
        const preselectedUsername = element.dataset.accountUsername;
        const preselectedEmail = element.dataset.accountEmail;

        choices.setChoices([
            { value: preselectedId, label: `${preselectedUsername} (${preselectedEmail})`, selected: true }
        ])
    }
}
