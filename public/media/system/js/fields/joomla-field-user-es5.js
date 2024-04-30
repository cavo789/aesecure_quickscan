(function () {
  'use strict';

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor);
    }
  }
  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }
  function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass);
  }
  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }
  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };
    return _setPrototypeOf(o, p);
  }
  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;
    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }
  function _construct(Parent, args, Class) {
    if (_isNativeReflectConstruct()) {
      _construct = Reflect.construct.bind();
    } else {
      _construct = function _construct(Parent, args, Class) {
        var a = [null];
        a.push.apply(a, args);
        var Constructor = Function.bind.apply(Parent, a);
        var instance = new Constructor();
        if (Class) _setPrototypeOf(instance, Class.prototype);
        return instance;
      };
    }
    return _construct.apply(null, arguments);
  }
  function _isNativeFunction(fn) {
    return Function.toString.call(fn).indexOf("[native code]") !== -1;
  }
  function _wrapNativeSuper(Class) {
    var _cache = typeof Map === "function" ? new Map() : undefined;
    _wrapNativeSuper = function _wrapNativeSuper(Class) {
      if (Class === null || !_isNativeFunction(Class)) return Class;
      if (typeof Class !== "function") {
        throw new TypeError("Super expression must either be null or a function");
      }
      if (typeof _cache !== "undefined") {
        if (_cache.has(Class)) return _cache.get(Class);
        _cache.set(Class, Wrapper);
      }
      function Wrapper() {
        return _construct(Class, arguments, _getPrototypeOf(this).constructor);
      }
      Wrapper.prototype = Object.create(Class.prototype, {
        constructor: {
          value: Wrapper,
          enumerable: false,
          writable: true,
          configurable: true
        }
      });
      return _setPrototypeOf(Wrapper, Class);
    };
    return _wrapNativeSuper(Class);
  }
  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }
    return self;
  }
  function _toPrimitive(input, hint) {
    if (typeof input !== "object" || input === null) return input;
    var prim = input[Symbol.toPrimitive];
    if (prim !== undefined) {
      var res = prim.call(input, hint || "default");
      if (typeof res !== "object") return res;
      throw new TypeError("@@toPrimitive must return a primitive value.");
    }
    return (hint === "string" ? String : Number)(input);
  }
  function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return typeof key === "symbol" ? key : String(key);
  }

  (function (customElements, Joomla) {
    var JoomlaFieldUser = /*#__PURE__*/function (_HTMLElement) {
      _inheritsLoose(JoomlaFieldUser, _HTMLElement);
      function JoomlaFieldUser() {
        var _this;
        _this = _HTMLElement.call(this) || this;
        _this.onUserSelect = '';
        _this.onchangeStr = '';

        // Bind events
        _this.buttonClick = _this.buttonClick.bind(_assertThisInitialized(_this));
        _this.iframeLoad = _this.iframeLoad.bind(_assertThisInitialized(_this));
        _this.modalClose = _this.modalClose.bind(_assertThisInitialized(_this));
        _this.setValue = _this.setValue.bind(_assertThisInitialized(_this));
        return _this;
      }
      var _proto = JoomlaFieldUser.prototype;
      _proto.connectedCallback = function connectedCallback() {
        // Set up elements
        this.modal = this.querySelector(this.modalClass);
        this.modalBody = this.querySelector('.modal-body');
        this.input = this.querySelector(this.inputId);
        this.inputName = this.querySelector(this.inputNameClass);
        this.buttonSelect = this.querySelector(this.buttonSelectClass);

        // Bootstrap modal init
        if (this.modal && window.bootstrap && window.bootstrap.Modal && !window.bootstrap.Modal.getInstance(this.modal)) {
          Joomla.initialiseModal(this.modal, {
            isJoomla: true
          });
        }
        if (this.buttonSelect) {
          this.buttonSelect.addEventListener('click', this.modalOpen.bind(this));
          this.modal.addEventListener('hide', this.removeIframe.bind(this));

          // Check for onchange callback,
          this.onchangeStr = this.input.getAttribute('data-onchange');
          if (this.onchangeStr) {
            /* eslint-disable */
            this.onUserSelect = new Function(this.onchangeStr);
            this.input.addEventListener('change', this.onUserSelect);
            /* eslint-enable */
          }
        }
      };
      _proto.disconnectedCallback = function disconnectedCallback() {
        if (this.onchangeStr && this.input) {
          this.input.removeEventListener('change', this.onUserSelect);
        }
        if (this.buttonSelect) {
          this.buttonSelect.removeEventListener('click', this);
        }
        if (this.modal) {
          this.modal.removeEventListener('hide', this);
        }
      };
      _proto.buttonClick = function buttonClick(_ref) {
        var target = _ref.target;
        this.setValue(target.getAttribute('data-user-value'), target.getAttribute('data-user-name'));
        this.modalClose();
      };
      _proto.iframeLoad = function iframeLoad() {
        var _this2 = this;
        var iframeDoc = this.iframeEl.contentWindow.document;
        var buttons = [].slice.call(iframeDoc.querySelectorAll('.button-select'));
        buttons.forEach(function (button) {
          button.addEventListener('click', _this2.buttonClick);
        });
      }

      // Opens the modal
      ;
      _proto.modalOpen = function modalOpen() {
        // Reconstruct the iframe
        this.removeIframe();
        var iframe = document.createElement('iframe');
        iframe.setAttribute('name', 'field-user-modal');
        iframe.src = this.url.replace('{field-user-id}', this.input.getAttribute('id'));
        iframe.setAttribute('width', this.modalWidth);
        iframe.setAttribute('height', this.modalHeight);
        this.modalBody.appendChild(iframe);
        this.modal.open();
        this.iframeEl = this.modalBody.querySelector('iframe');

        // handle the selection on the iframe
        this.iframeEl.addEventListener('load', this.iframeLoad);
      }

      // Closes the modal
      ;
      _proto.modalClose = function modalClose() {
        Joomla.Modal.getCurrent().close();
        this.modalBody.innerHTML = '';
      }

      // Remove the iframe
      ;
      _proto.removeIframe = function removeIframe() {
        this.modalBody.innerHTML = '';
      }

      // Sets the value
      ;
      _proto.setValue = function setValue(value, name) {
        this.input.setAttribute('value', value);
        this.inputName.setAttribute('value', name || value);
        // trigger change event both on the input and on the custom element
        this.input.dispatchEvent(new Event('change'));
        this.dispatchEvent(new CustomEvent('change', {
          detail: {
            value: value,
            name: name
          },
          bubbles: true
        }));
      };
      _createClass(JoomlaFieldUser, [{
        key: "url",
        get: function get() {
          return this.getAttribute('url');
        },
        set: function set(value) {
          this.setAttribute('url', value);
        }
      }, {
        key: "modalClass",
        get: function get() {
          return this.getAttribute('modal');
        },
        set: function set(value) {
          this.setAttribute('modal', value);
        }
      }, {
        key: "modalWidth",
        get: function get() {
          return this.getAttribute('modal-width');
        },
        set: function set(value) {
          this.setAttribute('modal-width', value);
        }
      }, {
        key: "modalHeight",
        get: function get() {
          return this.getAttribute('modal-height');
        },
        set: function set(value) {
          this.setAttribute('modal-height', value);
        }
      }, {
        key: "inputId",
        get: function get() {
          return this.getAttribute('input');
        },
        set: function set(value) {
          this.setAttribute('input', value);
        }
      }, {
        key: "inputNameClass",
        get: function get() {
          return this.getAttribute('input-name');
        },
        set: function set(value) {
          this.setAttribute('input-name', value);
        }
      }, {
        key: "buttonSelectClass",
        get: function get() {
          return this.getAttribute('button-select');
        },
        set: function set(value) {
          this.setAttribute('button-select', value);
        }
      }], [{
        key: "observedAttributes",
        get: function get() {
          return ['url', 'modal', 'modal-width', 'modal-height', 'input', 'input-name', 'button-select'];
        }
      }]);
      return JoomlaFieldUser;
    }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
    customElements.define('joomla-field-user', JoomlaFieldUser);
  })(customElements, Joomla);

})();
