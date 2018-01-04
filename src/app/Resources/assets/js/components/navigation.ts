import { queueRead, queueWrite } from '../libs/domQueue';


/**
 * Maximum viewport size of a 'mobile' device, in which case
 * the drawer will show instead of the navbar
 */
const MAX_MOBILE_WIDTH : number = 576;

/**
 * Has a animation frame been requested already?
 */
let isRequestingFrame : boolean = false;

let lastKnownViewportWidth : number = -1;

function isDrawerNeeded() : boolean {
    return lastKnownViewportWidth <= MAX_MOBILE_WIDTH;
}

function handleScroll() {
    lastKnownViewportWidth = window.innerWidth;
    isRequestingFrame = false;
}

function onItemClick(event: ClickEvent, element : HTMLUListElement) {
    queueWrite(() => {
        const link = event.target;
        const state = navState.get(link);
        if(state.isCollapsed) {
            element.style.maxHeight = state.height + 'px';
        } else {
            element.style.maxHeight = '0';
        }
        element.classList.toggle('expanded');
        element.classList.toggle('collapsed');
        link.classList.toggle('expanded');
        link.classList.toggle('collapsed');

        state.isCollapsed = !state.isCollapsed;
        navState.set(element, state);
    });
}

interface ClickEvent {
    preventDefault: Function;
    target: any;
}

interface NavItemState {
    isCollapsed: boolean,
    height: number,
}

const navState: Map<Element, NavItemState> = new Map();

function hookClickEvents() {
    queueRead(() => {
        const dropdownItems = document.querySelectorAll('#main-nav .nav-dropdown');
        for(let i = 0; i < dropdownItems.length; i++) {
            const root = <HTMLUListElement>dropdownItems[i];
        
            const expandedNav = root.parentElement.querySelector('ul');
            const height = expandedNav.clientHeight;
            expandedNav.style.maxHeight = '0';
            expandedNav.classList.add('collapsed');
            root.classList.add('collapsed');

            navState.set(root, {
                isCollapsed: true,
                height: height,
            });

            root.addEventListener('click', (event: ClickEvent) => {
                event.preventDefault();
                onItemClick(event, expandedNav);
            });
        }
    });
}

let isDrawerOpen = false;

function hookDrawerOpenClick() {
    const drawer = <HTMLDivElement>document.querySelector('#main-nav');
    const body = <HTMLMainElement>document.querySelector('main');
    const btn = document.getElementById('drawer-btn');

    btn.addEventListener('click', event => {
        event.preventDefault();
        if(isDrawerOpen) {
            drawer.style.transform = 'translateX(-400px)';
            body.style.transform = 'translateX(0)';
        } else {
            drawer.style.transform = 'translateX(0)';
            body.style.transform = 'translateX(400px)';
        }
        isDrawerOpen = !isDrawerOpen;
    });
}

export function hookResizeEvents() : void {
    window.addEventListener('resize', () => queueRead(handleScroll));
    hookClickEvents();
    hookDrawerOpenClick();
}