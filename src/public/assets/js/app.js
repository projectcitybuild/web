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
/******/ 	return __webpack_require__(__webpack_require__.s = 216);
/******/ })
/************************************************************************/
/******/ ({

/***/ 216:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(217);
module.exports = __webpack_require__(220);


/***/ }),

/***/ 217:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__components_navigation__ = __webpack_require__(218);

Object(__WEBPACK_IMPORTED_MODULE_0__components_navigation__["a" /* hookResizeEvents */])();


/***/ }),

/***/ 218:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = hookResizeEvents;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__libs_domQueue__ = __webpack_require__(219);

const MAX_MOBILE_WIDTH = 576;
let isRequestingFrame = false;
let lastKnownViewportWidth = -1;
function isDrawerNeeded() {
    return lastKnownViewportWidth <= MAX_MOBILE_WIDTH;
}
function handleScroll() {
    lastKnownViewportWidth = window.innerWidth;
    isRequestingFrame = false;
}
function onItemClick(event, element) {
    Object(__WEBPACK_IMPORTED_MODULE_0__libs_domQueue__["b" /* queueWrite */])(() => {
        const link = event.target;
        const state = navState.get(link);
        if (state.isCollapsed) {
            element.style.maxHeight = state.height + 'px';
        }
        else {
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
const navState = new Map();
function hookClickEvents() {
    Object(__WEBPACK_IMPORTED_MODULE_0__libs_domQueue__["a" /* queueRead */])(() => {
        const dropdownItems = document.querySelectorAll('#main-nav .nav-dropdown');
        for (let i = 0; i < dropdownItems.length; i++) {
            const root = dropdownItems[i];
            const expandedNav = root.parentElement.querySelector('ul');
            const height = expandedNav.clientHeight;
            expandedNav.style.maxHeight = '0';
            expandedNav.classList.add('collapsed');
            root.classList.add('collapsed');
            navState.set(root, {
                isCollapsed: true,
                height: height,
            });
            root.addEventListener('click', (event) => {
                event.preventDefault();
                onItemClick(event, expandedNav);
            });
        }
    });
}
function hookResizeEvents() {
    window.addEventListener('resize', () => Object(__WEBPACK_IMPORTED_MODULE_0__libs_domQueue__["a" /* queueRead */])(handleScroll));
    hookClickEvents();
}


/***/ }),

/***/ 219:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = queueRead;
/* harmony export (immutable) */ __webpack_exports__["b"] = queueWrite;
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

/***/ 220:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });