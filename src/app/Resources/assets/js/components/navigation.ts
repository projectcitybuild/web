import { queueRead, queueWrite } from '../library/DomQueue';
import { default as NavBar } from './NavBar';
import { default as NavDrawer } from './NavDrawer';
import { default as INavigator } from './INavigator';

/**
 * Simple state-machine to toggle between nav-drawer and nav-bar
 * depending on the viewport width
 */
export default class Navigation {

    /**
     * Max viewport width before a drawer is no longer necessary
     */
    private _drawerViewportMax: number = 576;

    /**
     * List of all navigators
     */
    private _navigationStates: Array<INavigator> = [];

    /**
     * The current navigator being displayed
     */
    private _currentState: INavigator;

    /**
     * Whether a new animation frame has been requested already
     */
    private _isRequestingFrame: boolean = false;

    /**
     * Last recorded viewport width
     */
    private _lastKnownViewportWidth: number = -1;

    constructor() {
        this._navigationStates.push(
            new NavBar(),
            new NavDrawer(),
        );

        this._currentState = this._navigationStates[ this._isDrawerNeeded(window.innerWidth) ? 1 : 0 ];
        this._currentState.create();

        // this._onResize = this._onResize.bind(this);
        window.addEventListener('resize', () => this._requestFrame());
        this._onResize();
    }

    /**
     * Gracefully switches the current navigation to the 
     * given navigator
     * 
     * @param newState 
     */
    private _switchNav(newState: INavigator) : void {
        this._currentState.destroy();
        this._currentState = newState;
        this._currentState.create();
    }

    private _requestFrame() : void {
        if(!this._isRequestingFrame) {
            requestAnimationFrame(this._onResize.bind(this));
            this._isRequestingFrame = true;
        }
    }

    /**
     * Whether a drawer should be displayed based on the
     * given viewport width
     */
    private _isDrawerNeeded(viewportWidth: number) : boolean {
        return viewportWidth <= this._drawerViewportMax;
    }

    /**
     * Checks the viewport size to see if a navigation change
     * is required
     */
    private _onResize() : void {
        const viewportWidth = window.innerWidth;
        const isDrawerNeeded = this._isDrawerNeeded(viewportWidth);

        if(isDrawerNeeded !== this._isDrawerNeeded(this._lastKnownViewportWidth)) {
            if(viewportWidth > this._drawerViewportMax) {
                this._switchNav(this._navigationStates[0]);
            } else {
                this._switchNav(this._navigationStates[1]);
            }    
        }

        this._lastKnownViewportWidth = viewportWidth;
        this._isRequestingFrame = false;
    }

}