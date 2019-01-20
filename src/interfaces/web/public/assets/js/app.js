webpackJsonp([1],{

/***/ 117:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_date_fns__ = __webpack_require__(40);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_date_fns___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_date_fns__);


class Component extends __WEBPACK_IMPORTED_MODULE_0_react__["Component"] {
    constructor() {
        super(...arguments);
        this.state = {};
    }
    createMarkup(html) {
        return { __html: html };
    }
    getRandom(min, max, localSeed = this.props.seed) {
        let x = Math.sin(localSeed) * 10000;
        x = x - Math.floor(x);
        x = (x * (max - min)) + min;
        return x;
    }
    renderAnnouncement(announcement) {
        const username = (announcement.details &&
            announcement.details.details &&
            announcement.details.details.created_by &&
            announcement.details.details.created_by.username) || "";
        const post = announcement.details &&
            announcement.details.post_stream &&
            announcement.details.post_stream.posts
            ? announcement.details.post_stream.posts[0]
            : {
                cooked: "",
                avatar_template: "",
            };
        const date = __WEBPACK_IMPORTED_MODULE_1_date_fns__["format"](announcement.created_at, 'ddd, Do \of MMMM, YYYY');
        const avatarUrl = "https://forums.projectcitybuild.com" + post.avatar_template.replace('{size}', '16');
        let markup = post.cooked;
        markup = markup.replace(/href="\//, 'href="https://forums.projectcitybuild.com/');
        return (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("article", { className: "article card", key: announcement.id },
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__container" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("h2", { className: "article__heading" }, announcement.title),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__date" }, date),
                announcement.details
                    ? __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__body", dangerouslySetInnerHTML: { __html: markup } })
                    : (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__body" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "spinner" },
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "rect1" }),
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "rect2" }),
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "rect3" }),
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "rect4" }),
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "rect5" })))),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__author" },
                    "Posted by",
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("img", { src: avatarUrl, width: "16", alt: username }),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("a", { href: `https://forums.projectcitybuild.com/u/${username}` }, username))),
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__footer" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stats-container" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stat" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__figure" }, announcement.posts_count - 1),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__heading" }, "Comments")),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stat" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__figure" }, announcement.views),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__heading" }, "Post Views"))),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "actions" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("a", { className: "button button--accent button--large", href: `https://forums.projectcitybuild.com/t/${announcement.slug}/${announcement.id}` },
                        "Read Post",
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("i", { className: "fa fa-chevron-right" }))))));
    }
    renderSkeleton(index) {
        let bodySkeleton = [];
        for (let i = 0; i < this.getRandom(3, 6); i++) {
            bodySkeleton.push(__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { key: i, className: "skeleton", style: { width: this.getRandom(60, 100, this.props.seed + i) + '%' } }));
        }
        return (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("article", { className: "article card", key: index },
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__container" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__heading" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--dark skeleton--large", style: { width: this.getRandom(40, 80) + '%' } })),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__date" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton", style: { width: '20%' } })),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__body" }, bodySkeleton),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__author" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton-row" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton", style: { width: '200px' } }),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--square skeleton--dark" }),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton" })))),
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__footer" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stats-container" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stat" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__figure" },
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--medium skeleton--dark skeleton--middle", style: { width: '15px' } })),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__heading" },
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--small", style: { width: '50px' } }))),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "stat" },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__figure" },
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--medium skeleton--dark skeleton--middle", style: { width: '15px' } })),
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("span", { className: "stat__heading" },
                            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--small", style: { width: '50px' } })))),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "article__actions" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "skeleton skeleton--button" })))));
    }
    render() {
        if (!this.props.announcement || !this.props.announcement.details) {
            return this.renderSkeleton(0);
        }
        return this.renderAnnouncement(this.props.announcement);
    }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = Component;



/***/ }),

/***/ 219:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__donation_amount__ = __webpack_require__(220);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__donation_amount__["a"]; });



/***/ }),

/***/ 220:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react_stripe_checkout__ = __webpack_require__(221);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react_stripe_checkout___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_react_stripe_checkout__);


;
var DonationAmountOption;
(function (DonationAmountOption) {
    DonationAmountOption[DonationAmountOption["SetAmount"] = 0] = "SetAmount";
    DonationAmountOption[DonationAmountOption["CustomAmount"] = 1] = "CustomAmount";
})(DonationAmountOption || (DonationAmountOption = {}));
class Component extends __WEBPACK_IMPORTED_MODULE_0_react__["Component"] {
    constructor() {
        super(...arguments);
        this.state = {
            selectedOption: DonationAmountOption.SetAmount,
            selectedAmount: 3000,
            customAmount: 0,
            stripeKey: "",
            stripeEmail: "",
            csrfToken: "",
            submitRoute: "",
        };
        this.useCustomAmount = () => {
            this.setState({
                selectedOption: DonationAmountOption.CustomAmount,
            }, () => {
                this.customAmountInput.focus();
            });
        };
        this.useSetAmount = (amount) => {
            this.setState({
                selectedOption: DonationAmountOption.SetAmount,
                selectedAmount: amount,
            });
        };
        this.handleCustomAmountChange = (event) => {
            const newValue = Number(event.target.value);
            if (!Number.isNaN(newValue) && newValue >= 0 && newValue < 999999) {
                this.setState({
                    customAmount: newValue,
                });
            }
        };
        this.isSetAmount = (amount) => {
            return this.state.selectedOption == DonationAmountOption.SetAmount &&
                this.state.selectedAmount == amount;
        };
        this.isCustomAmount = () => {
            return this.state.selectedOption == DonationAmountOption.CustomAmount;
        };
        this.isButtonDisabled = () => {
            if (this.state.selectedOption == DonationAmountOption.SetAmount) {
                return false;
            }
            return this.state.customAmount <= 0;
        };
        this.getAmount = () => {
            if (this.state.selectedOption == DonationAmountOption.SetAmount) {
                return this.state.selectedAmount.toString();
            }
            else {
                return this.state.customAmount.toString();
            }
        };
        this.onStripeTokenReceived = (token) => {
            this.setState({
                stripeToken: token.id,
                stripeEmail: token.email,
            }, () => {
                this.form.submit();
            });
        };
    }
    componentDidMount() {
        const stripeKey = document.head.querySelector('[name=stripe-key]').getAttribute('content');
        const submitRoute = document.head.querySelector('[name=stripe-submit]').getAttribute('content');
        const csrfToken = document.head.querySelector('[name=csrf-token]').getAttribute('content');
        this.setState({
            stripeKey: stripeKey,
            submitRoute: submitRoute,
            csrfToken: csrfToken,
        });
    }
    render() {
        return (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", null,
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "button-table" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "button-table__row" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: this.isSetAmount(500) ? "button button--fill button--accent" : "button button--fill button--secondary", onClick: () => this.useSetAmount(500) }, "$5"),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: this.isSetAmount(1000) ? "button button--fill button--accent" : "button button--fill button--secondary", onClick: () => this.useSetAmount(1000) }, "$10"),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: this.isSetAmount(2000) ? "button button--fill button--accent" : "button button--fill button--secondary", onClick: () => this.useSetAmount(2000) }, "$20")),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "button-table__row" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: this.isSetAmount(3000) ? "button button--fill button--accent" : "button button--fill button--secondary", onClick: () => this.useSetAmount(3000) }, "$30"),
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: this.isCustomAmount() ? "button button--fill button--accent" : "button button--fill button--secondary", onClick: this.useCustomAmount },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("i", { className: "fas fa-keyboard" }),
                        " Custom"))),
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "input-container" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "input-prefix" },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("i", { className: "fas fa-dollar-sign" })),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "input-suffix" }, "USD"),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("input", { ref: input => { this.customAmountInput = input; }, className: "input-text input-text--prefixed", type: "text", placeholder: "3.00", disabled: this.state.selectedOption == DonationAmountOption.SetAmount, value: this.state.customAmount.toString(), onChange: this.handleCustomAmountChange })),
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("form", { action: this.state.submitRoute, method: "POST", ref: form => { this.form = form; } },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("input", { type: "hidden", name: "_token", value: this.state.csrfToken }),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("input", { type: "hidden", name: "stripe_token", value: this.state.stripeToken }),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("input", { type: "hidden", name: "stripe_email", value: this.state.stripeEmail }),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("input", { type: "hidden", name: "stripe_amount", value: this.getAmount() }),
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"](__WEBPACK_IMPORTED_MODULE_1_react_stripe_checkout___default.a, { name: "Project City Build", description: "One-Time Donation", image: "https://forums.projectcitybuild.com/uploads/default/original/1X/847344a324d7dc0d5d908e5cad5f53a61372aded.png", amount: this.state.selectedOption == DonationAmountOption.SetAmount ? this.state.selectedAmount : this.state.customAmount * 100, stripeKey: this.state.stripeKey, locale: "auto", currency: "USD", token: this.onStripeTokenReceived },
                    __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("button", { className: "button button--large button--fill button--primary", type: "button", disabled: this.isButtonDisabled() },
                        __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("i", { className: "fas fa-credit-card" }),
                        " Donate via Card")))));
    }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = Component;



/***/ }),

/***/ 221:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = __webpack_require__(2);

var _react2 = _interopRequireDefault(_react);

var _propTypes = __webpack_require__(222);

var _propTypes2 = _interopRequireDefault(_propTypes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var scriptLoading = false;
var scriptLoaded = false;
var scriptDidError = false;

var ReactStripeCheckout = function (_React$Component) {
  _inherits(ReactStripeCheckout, _React$Component);

  function ReactStripeCheckout(props) {
    _classCallCheck(this, ReactStripeCheckout);

    var _this = _possibleConstructorReturn(this, (ReactStripeCheckout.__proto__ || Object.getPrototypeOf(ReactStripeCheckout)).call(this, props));

    _this.onScriptLoaded = function () {
      if (!ReactStripeCheckout.stripeHandler) {
        ReactStripeCheckout.stripeHandler = StripeCheckout.configure({
          key: _this.props.stripeKey
        });
        if (_this.hasPendingClick) {
          _this.showStripeDialog();
        }
      }
    };

    _this.onScriptError = function () {
      for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      _this.hideLoadingDialog();
      if (_this.props.onScriptError) {
        _this.props.onScriptError.apply(_this, args);
      }
    };

    _this.onClosed = function () {
      for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      if (_this._isMounted) _this.setState({ open: false });
      if (_this.props.closed) {
        _this.props.closed.apply(_this, args);
      }
    };

    _this.onOpened = function () {
      for (var _len3 = arguments.length, args = Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        args[_key3] = arguments[_key3];
      }

      _this.setState({ open: true });
      if (_this.props.opened) {
        _this.props.opened.apply(_this, args);
      }
    };

    _this.getConfig = function () {
      return ['token', 'image', 'name', 'description', 'amount', 'locale', 'currency', 'panelLabel', 'zipCode', 'shippingAddress', 'billingAddress', 'email', 'allowRememberMe', 'bitcoin', 'alipay', 'alipayReusable'].reduce(function (config, key) {
        return _extends({}, config, _this.props.hasOwnProperty(key) && _defineProperty({}, key, _this.props[key]));
      }, {
        opened: _this.onOpened,
        closed: _this.onClosed
      });
    };

    _this.onClick = function () {
      // eslint-disable-line react/sort-comp
      if (_this.props.disabled) {
        return;
      }

      if (scriptDidError) {
        try {
          throw new Error('Tried to call onClick, but StripeCheckout failed to load');
        } catch (x) {} // eslint-disable-line no-empty
      } else if (ReactStripeCheckout.stripeHandler) {
        _this.showStripeDialog();
      } else {
        _this.showLoadingDialog();
        _this.hasPendingClick = true;
      }
    };

    _this.handleOnMouseDown = function () {
      _this.setState({
        buttonActive: true
      });
    };

    _this.handleOnMouseUp = function () {
      _this.setState({
        buttonActive: false
      });
    };

    _this.state = {
      open: false,
      buttonActive: false
    };
    return _this;
  }

  _createClass(ReactStripeCheckout, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      var _this2 = this;

      this._isMounted = true;
      if (scriptLoaded) {
        return;
      }

      if (scriptLoading) {
        return;
      }

      scriptLoading = true;

      var script = document.createElement('script');
      if (typeof this.props.onScriptTagCreated === 'function') {
        this.props.onScriptTagCreated(script);
      }

      script.src = 'https://checkout.stripe.com/checkout.js';
      script.async = 1;

      this.loadPromise = function () {
        var canceled = false;
        var promise = new Promise(function (resolve, reject) {
          script.onload = function () {
            scriptLoaded = true;
            scriptLoading = false;
            resolve();
            _this2.onScriptLoaded();
          };
          script.onerror = function (event) {
            scriptDidError = true;
            scriptLoading = false;
            reject(event);
            _this2.onScriptError(event);
          };
        });
        var wrappedPromise = new Promise(function (accept, cancel) {
          promise.then(function () {
            return canceled ? cancel({ isCanceled: true }) : accept();
          }); // eslint-disable-line no-confusing-arrow
          promise.catch(function (error) {
            return canceled ? cancel({ isCanceled: true }) : cancel(error);
          }); // eslint-disable-line no-confusing-arrow
        });

        return {
          promise: wrappedPromise,
          cancel: function cancel() {
            canceled = true;
          }
        };
      }();

      this.loadPromise.promise.then(this.onScriptLoaded).catch(this.onScriptError);

      document.body.appendChild(script);
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate() {
      if (!scriptLoading) {
        this.updateStripeHandler();
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this._isMounted = false;
      if (this.loadPromise) {
        this.loadPromise.cancel();
      }
      if (ReactStripeCheckout.stripeHandler && this.state.open) {
        ReactStripeCheckout.stripeHandler.close();
      }
    }
  }, {
    key: 'updateStripeHandler',
    value: function updateStripeHandler() {
      if (!ReactStripeCheckout.stripeHandler || this.props.reconfigureOnUpdate) {
        ReactStripeCheckout.stripeHandler = StripeCheckout.configure({
          key: this.props.stripeKey
        });
      }
    }
  }, {
    key: 'showLoadingDialog',
    value: function showLoadingDialog() {
      if (this.props.showLoadingDialog) {
        for (var _len4 = arguments.length, args = Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
          args[_key4] = arguments[_key4];
        }

        this.props.showLoadingDialog.apply(this, args);
      }
    }
  }, {
    key: 'hideLoadingDialog',
    value: function hideLoadingDialog() {
      if (this.props.hideLoadingDialog) {
        for (var _len5 = arguments.length, args = Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
          args[_key5] = arguments[_key5];
        }

        this.props.hideLoadingDialog.apply(this, args);
      }
    }
  }, {
    key: 'showStripeDialog',
    value: function showStripeDialog() {
      this.hideLoadingDialog();
      ReactStripeCheckout.stripeHandler.open(this.getConfig());
    }
  }, {
    key: 'renderDefaultStripeButton',
    value: function renderDefaultStripeButton() {
      return _react2.default.createElement(
        'button',
        _extends({}, _defineProperty({}, this.props.triggerEvent, this.onClick), {
          className: this.props.className,
          onMouseDown: this.handleOnMouseDown,
          onFocus: this.handleOnMouseDown,
          onMouseUp: this.handleOnMouseUp,
          onMouseOut: this.handleOnMouseUp,
          onBlur: this.handleOnMouseUp,
          style: _extends({}, {
            overflow: 'hidden',
            display: 'inline-block',
            background: 'linear-gradient(#28a0e5,#015e94)',
            border: 0,
            padding: 1,
            textDecoration: 'none',
            borderRadius: 5,
            boxShadow: '0 1px 0 rgba(0,0,0,0.2)',
            cursor: 'pointer',
            visibility: 'visible',
            userSelect: 'none'
          }, this.state.buttonActive && {
            background: '#005d93'
          }, this.props.style)
        }),
        _react2.default.createElement(
          'span',
          {
            style: _extends({}, {
              backgroundImage: 'linear-gradient(#7dc5ee,#008cdd 85%,#30a2e4)',
              fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif',
              fontSize: 14,
              position: 'relative',
              padding: '0 12px',
              display: 'block',
              height: 30,
              lineHeight: '30px',
              color: '#fff',
              fontWeight: 'bold',
              boxShadow: 'inset 0 1px 0 rgba(255,255,255,0.25)',
              textShadow: '0 -1px 0 rgba(0,0,0,0.25)',
              borderRadius: 4
            }, this.state.buttonActive && {
              color: '#eee',
              boxShadow: 'inset 0 1px 0 rgba(0,0,0,0.1)',
              backgroundImage: 'linear-gradient(#008cdd,#008cdd 85%,#239adf)'
            }, this.props.textStyle)
          },
          this.props.label
        )
      );
    }
  }, {
    key: 'renderDisabledButton',
    value: function renderDisabledButton() {
      return _react2.default.createElement(
        'button',
        {
          disabled: true,
          style: {
            background: 'rgba(0,0,0,0.2)',
            overflow: 'hidden',
            display: 'inline-block',
            border: 0,
            padding: 1,
            textDecoration: 'none',
            borderRadius: 5,
            userSelect: 'none'
          }
        },
        _react2.default.createElement(
          'span',
          {
            style: {
              boxShadow: 'inset 0 1px 0 rgba(255,255,255,0.25)',
              fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif',
              fontSize: 14,
              position: 'relative',
              padding: '0 12px',
              display: 'block',
              height: 30,
              lineHeight: '30px',
              borderRadius: 4,
              color: '#999',
              background: '#f8f9fa',
              textShadow: '0 1px 0 rgba(255,255,255,0.5)'
            }
          },
          this.props.label
        )
      );
    }
  }, {
    key: 'render',
    value: function render() {
      if (this.props.desktopShowModal === true && !this.state.open) {
        this.onClick();
      } else if (this.props.desktopShowModal === false && this.state.open) {
        ReactStripeCheckout.stripeHandler.close();
      }

      var ComponentClass = this.props.ComponentClass;

      if (this.props.children) {
        return _react2.default.createElement(ComponentClass, _extends({}, _defineProperty({}, this.props.triggerEvent, this.onClick), {
          children: this.props.children
        }));
      }
      return this.props.disabled ? this.renderDisabledButton() : this.renderDefaultStripeButton();
    }
  }]);

  return ReactStripeCheckout;
}(_react2.default.Component);

ReactStripeCheckout.defaultProps = {
  className: 'StripeCheckout',
  label: 'Pay With Card',
  locale: 'auto',
  ComponentClass: 'span',
  reconfigureOnUpdate: false,
  triggerEvent: 'onClick'
};
ReactStripeCheckout.propTypes = {
  // Opens / closes the checkout modal by value
  // WARNING: does not work on mobile due to browser security restrictions
  // NOTE: Must be set to false when receiving token to prevent modal from
  //       opening automatically after closing
  desktopShowModal: _propTypes2.default.bool,

  triggerEvent: _propTypes2.default.oneOf(['onClick', 'onTouchTap', 'onTouchStart']),

  // If included, will render the default blue button with label text.
  // (Requires including stripe-checkout.css or adding the .styl file
  // to your pipeline)
  label: _propTypes2.default.string,

  // Custom styling for default button
  style: _propTypes2.default.object,
  // Custom styling for <span> tag inside default button
  textStyle: _propTypes2.default.object,

  // Prevents any events from opening the popup
  // Adds the disabled prop to the button and adjusts the styling as well
  disabled: _propTypes2.default.bool,

  // Named component to wrap button (eg. div)
  ComponentClass: _propTypes2.default.string,

  // Show a loading indicator
  showLoadingDialog: _propTypes2.default.func,
  // Hide the loading indicator
  hideLoadingDialog: _propTypes2.default.func,

  // Run this method when the scrupt fails to load. Will run if the internet
  // connection is offline when attemting to load the script.
  onScriptError: _propTypes2.default.func,

  // Runs when the script tag is created, but before it is added to the DOM
  onScriptTagCreated: _propTypes2.default.func,

  // By default, any time the React component is updated, it will call
  // StripeCheckout.configure, which may result in additional XHR calls to the
  // stripe API.  If you know the first configuration is all you need, you
  // can set this to false.  Subsequent updates will affect the StripeCheckout.open
  // (e.g. different prices)
  reconfigureOnUpdate: _propTypes2.default.bool,

  // =====================================================
  // Required by stripe
  // see Stripe docs for more info:
  //   https://stripe.com/docs/checkout#integration-custom
  // =====================================================

  // Your publishable key (test or live).
  // can't use "key" as a prop in react, so have to change the keyname
  stripeKey: _propTypes2.default.string.isRequired,

  // The callback to invoke when the Checkout process is complete.
  //   function(token)
  //     token is the token object created.
  //     token.id can be used to create a charge or customer.
  //     token.email contains the email address entered by the user.
  token: _propTypes2.default.func.isRequired,

  // ==========================
  // Highly Recommended Options
  // ==========================

  // Name of the company or website.
  name: _propTypes2.default.string,

  // A description of the product or service being purchased.
  description: _propTypes2.default.string,

  // A relative URL pointing to a square image of your brand or product. The
  // recommended minimum size is 128x128px. The recommended image types are
  // .gif, .jpeg, and .png.
  image: _propTypes2.default.string,

  // The amount (in cents) that's shown to the user. Note that you will still
  // have to explicitly include it when you create a charge using the API.
  amount: _propTypes2.default.number,

  // Specify auto to display Checkout in the user's preferred language, if
  // available. English will be used by default.
  //
  // https://stripe.com/docs/checkout#supported-languages
  // for more info.
  locale: _propTypes2.default.oneOf(['auto', // (Default) Automatically chosen by checkout
  'zh', // Simplified Chinese
  'da', // Danish
  'nl', // Dutch
  'en', // English
  'fr', // French
  'de', // German
  'it', // Italian
  'ja', // Japanease
  'no', // Norwegian
  'es', // Spanish
  'sv']),

  // ==============
  // Optional Props
  // ==============

  // The currency of the amount (3-letter ISO code). The default is USD.
  currency: _propTypes2.default.oneOf(['AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', // eslint-disable-line comma-spacing
  'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', // eslint-disable-line comma-spacing
  'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EEK', // eslint-disable-line comma-spacing
  'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', // eslint-disable-line comma-spacing
  'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', // eslint-disable-line comma-spacing
  'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LTL', // eslint-disable-line comma-spacing
  'LVL', 'MAD', 'MDL', 'MGA', 'MKD', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', // eslint-disable-line comma-spacing
  'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', // eslint-disable-line comma-spacing
  'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', // eslint-disable-line comma-spacing
  'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SVC', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', // eslint-disable-line comma-spacing
  'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', // eslint-disable-line comma-spacing
  'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW']),

  // The label of the payment button in the Checkout form (e.g. “Subscribe”,
  // “Pay {{amount}}”, etc.). If you include {{amount}}, it will be replaced
  // by the provided amount. Otherwise, the amount will be appended to the
  // end of your label.
  panelLabel: _propTypes2.default.string,

  // Specify whether Checkout should validate the billing ZIP code (true or
  // false)
  zipCode: _propTypes2.default.bool,

  // Specify whether Checkout should collect the user's billing address
  // (true or false). The default is false.
  billingAddress: _propTypes2.default.bool,

  // Specify whether Checkout should collect the user's shipping address
  // (true or false). The default is false.
  shippingAddress: _propTypes2.default.bool,

  // Specify whether Checkout should validate the billing ZIP code (true or
  // false). The default is false.
  email: _propTypes2.default.string,

  // Specify whether to include the option to "Remember Me" for future
  // purchases (true or false). The default is true.
  allowRememberMe: _propTypes2.default.bool,

  // Specify whether to accept Bitcoin in Checkout. The default is false.
  bitcoin: _propTypes2.default.bool,

  // Specify whether to accept Alipay ('auto', true, or false). The default
  // is false.
  alipay: _propTypes2.default.oneOf(['auto', true, false]),

  // Specify if you need reusable access to the customer's Alipay account
  // (true or false). The default is false.
  alipayReusable: _propTypes2.default.bool,

  // function() The callback to invoke when Checkout is opened (not supported
  // in IE6 and IE7).
  opened: _propTypes2.default.func,

  // function() The callback to invoke when Checkout is closed (not supported
  // in IE6 and IE7).
  closed: _propTypes2.default.func
};
ReactStripeCheckout._isMounted = false;
exports.default = ReactStripeCheckout;


/***/ }),

/***/ 222:
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var REACT_ELEMENT_TYPE = (typeof Symbol === 'function' &&
    Symbol.for &&
    Symbol.for('react.element')) ||
    0xeac7;

  var isValidElement = function(object) {
    return typeof object === 'object' &&
      object !== null &&
      object.$$typeof === REACT_ELEMENT_TYPE;
  };

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(223)(isValidElement, throwOnDirectAccess);
} else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = require('./factoryWithThrowingShims')();
}


/***/ }),

/***/ 223:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var assign = __webpack_require__(14);

var ReactPropTypesSecret = __webpack_require__(31);
var checkPropTypes = __webpack_require__(16);

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message) {
    this.message = message;
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ("development" !== 'production' && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName  + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
       true ? printWarning('Invalid argument supplied to oneOf, expected an instance of array.') : void 0;
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues);
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + propValue + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (propValue.hasOwnProperty(key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : void 0;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        if (checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret) == null) {
          return null;
        }
      }

      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (!checker) {
          continue;
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from
      // props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' +  JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ 224:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 225:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 33:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Announcement__ = __webpack_require__(95);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__Announcement__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__DonationAmount__ = __webpack_require__(219);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_1__DonationAmount__["a"]; });




/***/ }),

/***/ 76:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(77);
__webpack_require__(224);
module.exports = __webpack_require__(225);


/***/ }),

/***/ 77:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__modules_Navigation__ = __webpack_require__(78);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_react_dom__ = __webpack_require__(32);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_react_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_react_dom__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__react_components__ = __webpack_require__(33);

const navigation = new __WEBPACK_IMPORTED_MODULE_0__modules_Navigation__["a" /* Navigation */]();




let announcementContainer = document.getElementById('announcements');
if (announcementContainer) {
    __WEBPACK_IMPORTED_MODULE_2_react_dom__["render"](__WEBPACK_IMPORTED_MODULE_1_react__["createElement"](__WEBPACK_IMPORTED_MODULE_3__react_components__["a" /* AnnouncementManager */], null), announcementContainer);
}
let donationAmountContainer = document.getElementById('donation-amount');
if (donationAmountContainer) {
    __WEBPACK_IMPORTED_MODULE_2_react_dom__["render"](__WEBPACK_IMPORTED_MODULE_1_react__["createElement"](__WEBPACK_IMPORTED_MODULE_3__react_components__["b" /* DonationAmount */], null), donationAmountContainer);
}


/***/ }),

/***/ 78:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Navigation__ = __webpack_require__(79);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__Navigation__["a"]; });



/***/ }),

/***/ 79:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__NavBarHandler__ = __webpack_require__(80);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__NavDrawerHandler__ = __webpack_require__(81);


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

/***/ 80:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
class NavBarHandler {
    onCreate() { }
    onDestroy() { }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = NavBarHandler;



/***/ }),

/***/ 81:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__library_DomQueue__ = __webpack_require__(82);

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

/***/ 82:
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

/***/ 95:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__announcement_manager__ = __webpack_require__(96);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__announcement_manager__["a"]; });



/***/ }),

/***/ 96:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__api__ = __webpack_require__(97);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__announcement__ = __webpack_require__(117);
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};



;
class Component extends __WEBPACK_IMPORTED_MODULE_0_react__["Component"] {
    constructor() {
        super(...arguments);
        this.state = {
            announcements: [],
            randomSeed: 6,
            seeds: [],
            error: null,
        };
    }
    componentDidMount() {
        return __awaiter(this, void 0, void 0, function* () {
            let hasError = false;
            let response;
            try {
                response = yield __WEBPACK_IMPORTED_MODULE_1__api__["a" /* getAnnouncements */]();
                if (!response) {
                    throw new Error('Empty response from server');
                }
            }
            catch (e) {
                const error = e;
                this.setState({ error: error.message });
                hasError = true;
            }
            if (hasError) {
                return;
            }
            const announcements = response.topic_list.topics.slice(0, 3);
            this.setState({
                announcements: announcements,
            });
            if (response.topic_list &&
                response.topic_list.topics &&
                response.topic_list.topics.length > 0) {
                announcements.forEach((topic) => __awaiter(this, void 0, void 0, function* () {
                    const post = yield __WEBPACK_IMPORTED_MODULE_1__api__["b" /* getTopic */](topic.id);
                    if (!post)
                        return;
                    const storedTopics = this.state.announcements;
                    const storedTopicIndex = this.state.announcements.findIndex(t => t.id === topic.id);
                    storedTopics[storedTopicIndex].details = post;
                    this.setState({
                        announcements: storedTopics,
                    });
                }));
            }
        });
    }
    renderError(error) {
        return (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("div", { className: "alert alert--warning" },
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("h3", { className: "alert__header" },
                __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("i", { className: "fas fa-exclamation-circle" }),
                " Failed to fetch announcements"),
            __WEBPACK_IMPORTED_MODULE_0_react__["createElement"]("p", { className: "alert__message" }, error)));
    }
    render() {
        if (this.state.error) {
            return this.renderError(this.state.error);
        }
        return this.state.announcements.map((value, index) => {
            if (!this.state.seeds[index]) {
                this.state.seeds[index] = Math.random() * 100;
            }
            const seed = this.state.seeds[index];
            return (__WEBPACK_IMPORTED_MODULE_0_react__["createElement"](__WEBPACK_IMPORTED_MODULE_2__announcement__["a" /* default */], { key: seed, announcement: value, seed: seed }));
        });
    }
}
/* harmony export (immutable) */ __webpack_exports__["a"] = Component;



/***/ }),

/***/ 97:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(34);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};

const getAnnouncements = () => __awaiter(this, void 0, void 0, function* () {
    try {
        const response = yield __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('https://forums.projectcitybuild.com/c/announcements/l/latest.json?order=created');
        if (response.status !== 200) {
            throw new Error(`${response.status} error while fetching announcements`);
        }
        return response.data;
    }
    catch (e) {
        console.error(e);
        throw new Error(e);
    }
});
/* harmony export (immutable) */ __webpack_exports__["a"] = getAnnouncements;

const getTopic = (topicId) => __awaiter(this, void 0, void 0, function* () {
    try {
        let response = yield __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(`https://forums.projectcitybuild.com/t/${topicId}.json`);
        if (response.status !== 200) {
            throw new Error(`${response.status} error while fetching announcements`);
        }
        return response.data;
    }
    catch (e) {
        console.error(e);
    }
});
/* harmony export (immutable) */ __webpack_exports__["b"] = getTopic;



/***/ })

},[76]);