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

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  window.customElements.define('joomla-toolbar-button', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    /**
     * Lifecycle
     */
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;
      if (!Joomla) {
        throw new Error('Joomla API is not properly initiated');
      }
      _this.onChange = _this.onChange.bind(_assertThisInitialized(_this));
      _this.executeTask = _this.executeTask.bind(_assertThisInitialized(_this));
      return _this;
    }

    /**
     * Lifecycle
     */
    var _proto = _class.prototype;
    _proto.connectedCallback = function connectedCallback() {
      // We need a button to support button behavior,
      // because we cannot currently extend HTMLButtonElement
      this.buttonElement = this.querySelector('button, a');
      this.buttonElement.addEventListener('click', this.executeTask);

      // Check whether we have a form
      var formSelector = this.form || 'adminForm';
      this.formElement = document.getElementById(formSelector);
      this.disabled = false;
      // If list selection is required, set button to disabled by default
      if (this.listSelection) {
        this.setDisabled(true);
      }
      if (this.listSelection) {
        if (!this.formElement) {
          throw new Error("The form \"" + formSelector + "\" is required to perform the task, but the form was not found on the page.");
        }

        // Watch on list selection
        this.formElement.boxchecked.addEventListener('change', this.onChange);
      }
    }

    /**
     * Lifecycle
     */;
    _proto.disconnectedCallback = function disconnectedCallback() {
      if (this.formElement.boxchecked) {
        this.formElement.boxchecked.removeEventListener('change', this.onChange);
      }
      this.buttonElement.removeEventListener('click', this.executeTask);
    };
    _proto.onChange = function onChange(_ref) {
      var target = _ref.target;
      // Check whether we have selected something
      this.setDisabled(target.value < 1);
    };
    _proto.setDisabled = function setDisabled(disabled) {
      // Make sure we have a boolean value
      this.disabled = !!disabled;

      // Switch attribute for native element
      // An anchor does not support "disabled" attribute, so use class
      if (this.buttonElement) {
        if (this.disabled) {
          if (this.buttonElement.nodeName === 'BUTTON') {
            this.buttonElement.disabled = true;
          } else {
            this.buttonElement.classList.add('disabled');
          }
        } else if (this.buttonElement.nodeName === 'BUTTON') {
          this.buttonElement.disabled = false;
        } else {
          this.buttonElement.classList.remove('disabled');
        }
      }
    };
    _proto.executeTask = function executeTask() {
      if (this.disabled) {
        return false;
      }

      // eslint-disable-next-line no-restricted-globals
      if (this.confirmMessage && !confirm(this.confirmMessage)) {
        return false;
      }
      if (this.task) {
        Joomla.submitbutton(this.task, this.form, this.formValidation);
      }
      return true;
    };
    _createClass(_class, [{
      key: "task",
      get:
      // Attribute getters
      function get() {
        return this.getAttribute('task');
      }
    }, {
      key: "listSelection",
      get: function get() {
        return this.hasAttribute('list-selection');
      }
    }, {
      key: "form",
      get: function get() {
        return this.getAttribute('form');
      }
    }, {
      key: "formValidation",
      get: function get() {
        return this.hasAttribute('form-validation');
      }
    }, {
      key: "confirmMessage",
      get: function get() {
        return this.getAttribute('confirm-message');
      }
    }]);
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
