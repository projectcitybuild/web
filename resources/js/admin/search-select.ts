import Choices from "choices.js";

const searchableSelectList = [].slice.call(document.querySelectorAll('[data-pcb-search-select]'));

searchableSelectList.map(function (searchableSelectEl: HTMLSelectElement) {
    return new Choices(searchableSelectEl);
});
