import { NavigationHandler } from "./Navigation";
import { queueRead, queueWrite } from "../../library/DomQueue";

type ClickListener = (event: Event) => void;

interface MenuState {
    linkElement: HTMLLinkElement;
    menuElement: HTMLUListElement;
    isCollapsed: boolean;
    expandedHeight: number;
    clickListener: ClickListener;
}

export default class NavDrawerHandler implements NavigationHandler {

    /**
     * Whether the nav drawer is currently showing
     */
    private _isDrawerOpen = false;

    /**
     * State of all drop-down menu items in the drawer
     */
    private _menuStates: Array<MenuState> = [];

    private _drawerElement: HTMLDivElement = document.querySelector('#main-nav');

    private _bodyElement: HTMLElement = document.querySelector('main');

    private _drawerBtnElement: HTMLElement = document.querySelector('#drawer-btn');


    constructor() {
        this._getInitialState();

        this._toggleDrawer = this._toggleDrawer.bind(this);
        this._toggleMenu = this._toggleMenu.bind(this);
    }
    
    private _getInitialState() : void {
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
    }

    /**
     * Registers event listeners and collapsed all dropdown menus
     */
    public onCreate() : void {
        this._drawerBtnElement.addEventListener('click', this._toggleDrawer);

        this._menuStates.forEach(state => {
            state.clickListener = (event) => this._toggleMenu(event, state);
            state.linkElement.addEventListener('click', state.clickListener);

            queueWrite(() => {
                state.menuElement.style.maxHeight = '0';
                state.menuElement.classList.add('collapsed');
                state.linkElement.classList.add('collapsed');
            });
        });
    }

    /**
     * Removes all event listeners
     */
    public onDestroy() : void {
        queueWrite(() => {
            this._drawerElement.classList.remove('opened');
            this._bodyElement.classList.remove('pushed');
        });

        this._drawerBtnElement.removeEventListener('click', this._toggleDrawer);   
        this._menuStates.forEach(state => {
            state.linkElement.removeEventListener('click', state.clickListener);
            state.clickListener = null;
        });

        this._isDrawerOpen = false;
    }

    /**
     * Toggles the drawer open or closed
     * 
     * @param event
     */
    private _toggleDrawer(event: Event) : void {
        event.preventDefault();
        event.stopPropagation();

        queueWrite(() => {
            if(this._isDrawerOpen) {
                this._bodyElement.removeEventListener('click', this._toggleDrawer);
            } else {
                this._bodyElement.addEventListener('click', this._toggleDrawer);
            }
            this._drawerElement.classList.toggle('opened');
            this._bodyElement.classList.toggle('pushed');
            
            this._isDrawerOpen = !this._isDrawerOpen;
        });
    }

    /**
     * Toggles a dropdown menu open
     * 
     * @param event 
     * @param state 
     */
    private _toggleMenu(event: Event, state: MenuState) : void {
        event.preventDefault();

        queueWrite(() => {
            state.menuElement.style.maxHeight = state.isCollapsed ? state.expandedHeight + 'px' : '0';

            state.menuElement.classList.toggle('expanded');
            state.menuElement.classList.toggle('collapsed');
            state.linkElement.classList.toggle('expanded');
            state.linkElement.classList.toggle('collapsed');
    
            state.isCollapsed = !state.isCollapsed;
        });
    }
}