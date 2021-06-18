import {Tooltip} from 'bootstrap';

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl: HTMLElement): Tooltip {
    return new Tooltip(tooltipTriggerEl)
})

