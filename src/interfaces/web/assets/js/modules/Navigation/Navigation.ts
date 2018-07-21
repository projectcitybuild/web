import { queueRead, queueWrite } from '../../library/DomQueue';
import { default as NavBarHandler } from './NavBarHandler';
import { default as NavDrawerHandler } from './NavDrawerHandler';


export interface NavigationHandler {
    onCreate(): void;
    onDestroy(): void;
}

/**
 * Simple state-machine to toggle between nav-drawer and nav-bar
 * depending on the viewport width
 */
export default class Navigation {

    /**
     * Max viewport width before a drawer is no longer necessary
     */
    private _drawerViewportMax = 576;

    /**
     * List of all navigators
     */
    private _navigationStates: Array<NavigationHandler> = [
        new NavBarHandler(),
        new NavDrawerHandler(),
    ];

    /**
     * The current navigator being displayed
     */
    private _currentState: NavigationHandler;

    /**
     * Whether a new animation frame has been requested already
     */
    private _isRequestingFrame = false;

    /**
     * Last recorded viewport width
     */
    private _lastKnownViewportWidth = -1;


    constructor() {
        this._currentState = this._navigationStates[ this._isDrawerNeededFor(window.innerWidth) ? 1 : 0 ];
        this._currentState.onCreate();

        window.addEventListener('resize', this._requestFrame.bind(this));
        this._onResize();
    }

    private _requestFrame() : void {
        if(!this._isRequestingFrame) {
            requestAnimationFrame(this._onResize.bind(this));
            this._isRequestingFrame = true;
        }
    }

    /**
     * Gracefully switches the current navigation to the 
     * given navigator
     * 
     * @param newState 
     */
    private _switchNav(newState: NavigationHandler) : void {
        this._currentState.onDestroy();
        this._currentState = newState;
        this._currentState.onCreate();
    }

    /**
     * Returns whether a drawer should be displayed based on the
     * given viewport width
     */
    private _isDrawerNeededFor(viewportWidth: number) : boolean {
        return viewportWidth <= this._drawerViewportMax;
    }

    /**
     * Checks the viewport size to see if a navigation change
     * is required
     */
    private _onResize() : void {
        const viewportWidth = window.innerWidth;
        const isDrawerNeeded = this._isDrawerNeededFor(viewportWidth);

        if(isDrawerNeeded !== this._isDrawerNeededFor(this._lastKnownViewportWidth)) {
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