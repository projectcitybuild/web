import {Tooltip} from 'bootstrap';

console.log('hey');
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
console.log(tooltipTriggerList);
tooltipTriggerList.map(function (tooltipTriggerEl: HTMLElement): Tooltip {
    console.log(tooltipTriggerEl);
    return new Tooltip(tooltipTriggerEl)
})

