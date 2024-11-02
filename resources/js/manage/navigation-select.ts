const navigationSelectList = [].slice.call(document.querySelectorAll('[data-pcb-navigation-select]'));

navigationSelectList.forEach(function (navigationSelectEl: HTMLSelectElement) {
    navigationSelectEl.addEventListener('change', (e) => {
        window.location.href = (e.currentTarget as HTMLSelectElement).value
    });
});
