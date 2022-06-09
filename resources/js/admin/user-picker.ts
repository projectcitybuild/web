// import $ from 'jquery';
// import 'selectize/dist/js/selectize';

// const userSelectElements = [].slice.call(document.querySelectorAll('[data-pcb-user-picker]'));
//
// userSelectElements.map(function (userSelectEl: HTMLElement) {
//     console.info("Initialising user select " + userSelectEl);
//     $(userSelectEl).selectize({
//         valueField: "account_id",
//         labelField: "username",
//         searchField: ["username", "email", "id"],
//         create: false,
//         closeAfterSelect: true,
//         placeholder: "Start typing...",
//         // openOnFocus: true,
//         render: {
//             option: function (item, escape) {
//                 return (
//                     `<div class="option">#${escape(item.account_id)}: ${escape(item.username)} <span class="text-muted">(${escape(item.email)})</span></div>`
//                 );
//             },
//         },
//         load: function (query: string, callback: Function) {
//             if (!query.length) return callback();
//             $.get('/panel/api/accounts?query=' + query)
//                 .then((res) => {
//                     callback(res.data);
//                 })
//         },
//     });
// });

import Choices from 'choices.js';

const element = document.querySelector('[data-pcb-user-picker]');
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
                    choices.setChoices(async () => {
                        try {
                            // TODO: grab URL from .env file since
                            const accounts = await fetch('../api/accounts?query=' + query);
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
