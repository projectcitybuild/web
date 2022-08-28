import {Tooltip} from 'bootstrap';

import './user-picker';
import './navigation-select';
import './search-select';

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl: HTMLElement): Tooltip {
    return new Tooltip(tooltipTriggerEl);
})

