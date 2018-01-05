import INavigator from "./INavigator";
import { queueRead, queueWrite } from "../libs/domQueue";

interface MenuState {
    linkElement: HTMLLinkElement;
    menuElement: HTMLUListElement;
    isCollapsed: boolean;
    expandedHeight: number;
    clickListener(event: ClickEvent): void;
}

interface ClickEvent {
    preventDefault(): void;
}

export default class DrawerNav implements INavigator {

    /**
     * Whether the nav drawer is currently showing
     */
    private _isDrawerOpen: boolean = false;

    /**
     * State of all drop-down menu items in the drawer
     */
    private _menuStates: Array<MenuState> = [];

    private _drawerElement: HTMLDivElement = document.querySelector('#main-nav');

    private _bodyElement: HTMLMainElement = document.querySelector('main');

    private _drawerBtnElement: HTMLElement = document.querySelector('#drawer-btn');


    constructor() {
        this._getInitialState();
        this._toggleDrawer = this._toggleDrawer.bind(this);
        this._toggleMenu = this._toggleMenu.bind(this);
    }
    
    private _getInitialState() : void {
        queueRead(() => {
            const links = document.querySelectorAll('#main-nav .nav-dropdown');

            for(let i = 0; i < links.length; i++) {
                const link = links[i];
                const menu = link.nextElementSibling;

                this._menuStates.push({
                    linkElement: <HTMLLinkElement>link,
                    menuElement: <HTMLUListElement>menu,
                    isCollapsed: true,
                    expandedHeight: menu.clientHeight,
                    clickListener: null,
                });
            }
        });
    }

    /**
     * Registers event listeners and collapsed all dropdown menus
     */
    public create() : void {
        queueWrite(() => {
            this._drawerBtnElement.addEventListener('click', this._toggleDrawer);
            this._menuStates.forEach(state => {
                state.clickListener = (event) => this._toggleMenu(event, state);
                state.linkElement.addEventListener('click', state.clickListener);

                state.menuElement.style.maxHeight = '0';
                state.menuElement.classList.add('collapsed');
                state.linkElement.classList.add('collapsed');
            });
        });
    }

    /**
     * Removes all event listeners
     */
    public destroy() : void {
        this._drawerBtnElement.removeEventListener('click', this._toggleDrawer);   
        this._menuStates.forEach(state => {
            state.linkElement.removeEventListener('click', state.clickListener);
            state.clickListener = null;
        });
    }

    /**
     * Toggles the drawer open or closed
     * 
     * @param event
     */
    private _toggleDrawer(event: ClickEvent) : void {
        event.preventDefault();

        queueWrite(() => {
            if(this._isDrawerOpen) {
                this._drawerElement.style.transform = 'translateX(-400px)';
                this._bodyElement.style.transform = 'translateX(0)';
            } else {
                this._drawerElement.style.transform = 'translateX(0)';
                this._bodyElement.style.transform = 'translateX(400px)';
            }
            this._isDrawerOpen = !this._isDrawerOpen;
        });
    }

    /**
     * Toggles a dropdown menu open
     * 
     * @param event 
     * @param state 
     */
    private _toggleMenu(event: ClickEvent, state: MenuState) : void {
        event.preventDefault();

        queueWrite(() => {
            if(state.isCollapsed) {
                state.menuElement.style.maxHeight = state.expandedHeight + 'px';
            } else {
                state.menuElement.style.maxHeight = '0';
            }
            state.menuElement.classList.toggle('expanded');
            state.menuElement.classList.toggle('collapsed');
            state.linkElement.classList.toggle('expanded');
            state.linkElement.classList.toggle('collapsed');
    
            state.isCollapsed = !state.isCollapsed;
        });
    }
    

}