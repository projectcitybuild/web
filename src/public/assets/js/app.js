/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./app/Resources/assets/js/app.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__modules_Navigation__ = __webpack_require__("./app/Resources/assets/js/modules/Navigation/index.ts");

const navigation = new __WEBPACK_IMPORTED_MODULE_0__modules_Navigation__["a" /* Navigation */]();


/***/ }),

/***/ "./app/Resources/assets/js/library/DomQueue.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export queueRead */
/* harmony export (immutable) */ __webpack_exports__["a"] = queueWrite;
const readQueue = [];
const writeQueue = [];
let isRunning = false;
function processQueue(queue) {
    const size = queue.length;
    for (let i = 0; i < size; i++) {
        const action = queue.shift();
        action();
    }
}
function run() {
    processQueue(readQueue);
    processQueue(writeQueue);
    isRunning = false;
    if (readQueue.length > 0 || writeQueue.length > 0) {
        requestRun();
    }
}
function requestRun() {
    if (!isRunning) {
        isRunning = true;
        requestAnimationFrame(run);
    }
}
function queueRead(action) {
    readQueue.push(action);
    requestRun();
}
function queueWrite(action) {
    writeQueue.push(action);
    requestRun();
}


/***/ }),

/***/ "./app/Resources/assets/js/modules/Navigation/NavBarHandler.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
class NavBarHandler {
    onCreate() { }
    onDestroy() { }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = NavBarHandler;



/***/ }),

/***/ "./app/Resources/assets/js/modules/Navigation/NavDrawerHandler.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__library_DomQueue__ = __webpack_require__("./app/Resources/assets/js/library/DomQueue.ts");

class NavDrawerHandler {
    constructor() {
        this._isDrawerOpen = false;
        this._menuStates = [];
        this._drawerElement = document.querySelector('#main-nav');
        this._bodyElement = document.querySelector('main');
        this._drawerBtnElement = document.querySelector('#drawer-btn');
        this._getInitialState();
        this._toggleDrawer = this._toggleDrawer.bind(this);
        this._toggleMenu = this._toggleMenu.bind(this);
    }
    _getInitialState() {
        const links = document.querySelectorAll('#main-nav .nav-dropdown');
        for (let i = 0; i < links.length; i++) {
            const link = links[i];
            const menu = link.nextElementSibling;
            this._menuStates.push({
                linkElement: link,
                menuElement: menu,
                isCollapsed: true,
                expandedHeight: menu.clientHeight,
                clickListener: null,
            });
        }
    }
    onCreate() {
        this._drawerBtnElement.addEventListener('click', this._toggleDrawer);
        this._menuStates.forEach(state => {
            state.clickListener = (event) => this._toggleMenu(event, state);
            state.linkElement.addEventListener('click', state.clickListener);
            Object(__WEBPACK_IMPORTED_MODULE_0__library_DomQueue__["a" /* queueWrite */])(() => {
                state.menuElement.style.maxHeight = '0';
                state.menuElement.classList.add('collapsed');
                state.linkElement.classList.add('collapsed');
            });
        });
    }
    onDestroy() {
        Object(__WEBPACK_IMPORTED_MODULE_0__library_DomQueue__["a" /* queueWrite */])(() => {
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
    _toggleDrawer(event) {
        event.preventDefault();
        event.stopPropagation();
        Object(__WEBPACK_IMPORTED_MODULE_0__library_DomQueue__["a" /* queueWrite */])(() => {
            if (this._isDrawerOpen) {
                this._bodyElement.removeEventListener('click', this._toggleDrawer);
            }
            else {
                this._bodyElement.addEventListener('click', this._toggleDrawer);
            }
            this._drawerElement.classList.toggle('opened');
            this._bodyElement.classList.toggle('pushed');
            this._isDrawerOpen = !this._isDrawerOpen;
        });
    }
    _toggleMenu(event, state) {
        event.preventDefault();
        Object(__WEBPACK_IMPORTED_MODULE_0__library_DomQueue__["a" /* queueWrite */])(() => {
            state.menuElement.style.maxHeight = state.isCollapsed ? state.expandedHeight + 'px' : '0';
            state.menuElement.classList.toggle('expanded');
            state.menuElement.classList.toggle('collapsed');
            state.linkElement.classList.toggle('expanded');
            state.linkElement.classList.toggle('collapsed');
            state.isCollapsed = !state.isCollapsed;
        });
    }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = NavDrawerHandler;



/***/ }),

/***/ "./app/Resources/assets/js/modules/Navigation/Navigation.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__NavBarHandler__ = __webpack_require__("./app/Resources/assets/js/modules/Navigation/NavBarHandler.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__NavDrawerHandler__ = __webpack_require__("./app/Resources/assets/js/modules/Navigation/NavDrawerHandler.ts");


class Navigation {
    constructor() {
        this._drawerViewportMax = 576;
        this._navigationStates = [
            new __WEBPACK_IMPORTED_MODULE_0__NavBarHandler__["a" /* default */](),
            new __WEBPACK_IMPORTED_MODULE_1__NavDrawerHandler__["a" /* default */](),
        ];
        this._isRequestingFrame = false;
        this._lastKnownViewportWidth = -1;
        this._currentState = this._navigationStates[this._isDrawerNeededFor(window.innerWidth) ? 1 : 0];
        this._currentState.onCreate();
        window.addEventListener('resize', this._requestFrame.bind(this));
        this._onResize();
    }
    _requestFrame() {
        if (!this._isRequestingFrame) {
            requestAnimationFrame(this._onResize.bind(this));
            this._isRequestingFrame = true;
        }
    }
    _switchNav(newState) {
        this._currentState.onDestroy();
        this._currentState = newState;
        this._currentState.onCreate();
    }
    _isDrawerNeededFor(viewportWidth) {
        return viewportWidth <= this._drawerViewportMax;
    }
    _onResize() {
        const viewportWidth = window.innerWidth;
        const isDrawerNeeded = this._isDrawerNeededFor(viewportWidth);
        if (isDrawerNeeded !== this._isDrawerNeededFor(this._lastKnownViewportWidth)) {
            if (viewportWidth > this._drawerViewportMax) {
                this._switchNav(this._navigationStates[0]);
            }
            else {
                this._switchNav(this._navigationStates[1]);
            }
        }
        this._lastKnownViewportWidth = viewportWidth;
        this._isRequestingFrame = false;
    }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = Navigation;



/***/ }),

/***/ "./app/Resources/assets/js/modules/Navigation/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Navigation__ = __webpack_require__("./app/Resources/assets/js/modules/Navigation/Navigation.ts");
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__Navigation__["a"]; });



/***/ }),

/***/ "./app/Resources/assets/sass/app.scss":
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./app/Resources/assets/js/app.ts");
module.exports = __webpack_require__("./app/Resources/assets/sass/app.scss");


/***/ })

/******/ });