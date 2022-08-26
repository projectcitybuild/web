import { Tooltip } from 'bootstrap';

import './user-picker';

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl: HTMLElement): Tooltip {
    return new Tooltip(tooltipTriggerEl);
})

