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
  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }
  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
    return arr2;
  }
  function _createForOfIteratorHelperLoose(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
    if (it) return (it = it.call(o)).next.bind(it);
    if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
      if (it) o = it;
      var i = 0;
      return function () {
        if (i >= o.length) return {
          done: true
        };
        return {
          done: false,
          value: o[i++]
        };
      };
    }
    throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
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

  var AlertElement = /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(AlertElement, _HTMLElement);
    function AlertElement() {
      var _this;
      _this = _HTMLElement.call(this) || this;

      // Bindings
      _this.close = _this.close.bind(_assertThisInitialized(_this));
      _this.destroyCloseButton = _this.destroyCloseButton.bind(_assertThisInitialized(_this));
      _this.createCloseButton = _this.createCloseButton.bind(_assertThisInitialized(_this));
      _this.onMutation = _this.onMutation.bind(_assertThisInitialized(_this));
      _this.observer = new MutationObserver(_this.onMutation);
      _this.observer.observe(_assertThisInitialized(_this), {
        attributes: false,
        childList: true,
        subtree: true
      });

      // Handle the fade in animation
      _this.addEventListener('animationend', function (event) {
        if (event.animationName === 'joomla-alert-fade-in' && event.target === _assertThisInitialized(_this)) {
          _this.dispatchEvent(new CustomEvent('joomla.alert.shown'));
          _this.style.removeProperty('animationName');
        }
      });

      // Handle the fade out animation
      _this.addEventListener('animationend', function (event) {
        if (event.animationName === 'joomla-alert-fade-out' && event.target === _assertThisInitialized(_this)) {
          _this.dispatchEvent(new CustomEvent('joomla.alert.closed'));
          _this.remove();
        }
      });
      return _this;
    }

    /* Attributes to monitor */
    var _proto = AlertElement.prototype;
    /* Lifecycle, element appended to the DOM */
    _proto.connectedCallback = function connectedCallback() {
      this.dispatchEvent(new CustomEvent('joomla.alert.show'));
      this.style.animationName = 'joomla-alert-fade-in';

      // Default to info
      if (!this.type || !['info', 'warning', 'danger', 'success'].includes(this.type)) {
        this.setAttribute('type', 'info');
      }
      // Default to alert
      if (!this.role || !['alert', 'alertdialog'].includes(this.role)) {
        this.setAttribute('role', 'alert');
      }

      // Hydrate the button
      if (this.firstElementChild && this.firstElementChild.tagName === 'BUTTON') {
        this.button = this.firstElementChild;
        if (this.button.classList.contains('joomla-alert--close')) {
          this.button.classList.add('joomla-alert--close');
        }
        if (this.button.innerHTML === '') {
          this.button.innerHTML = '<span aria-hidden="true">&times;</span>';
        }
        if (!this.button.hasAttribute('aria-label')) {
          this.button.setAttribute('aria-label', this.closeText);
        }
      }

      // Append button
      if (this.hasAttribute('dismiss') && !this.button) {
        this.createCloseButton();
      }
      if (this.hasAttribute('auto-dismiss')) {
        this.autoDismiss();
      }
    }

    /* Lifecycle, element removed from the DOM */;
    _proto.disconnectedCallback = function disconnectedCallback() {
      if (this.button) {
        this.button.removeEventListener('click', this.close);
      }
      this.observer.disconnect();
    }

    /* Respond to attribute changes */;
    _proto.attributeChangedCallback = function attributeChangedCallback(attr, oldValue, newValue) {
      switch (attr) {
        case 'type':
          if (!newValue || newValue && ['info', 'warning', 'danger', 'success'].indexOf(newValue) === -1) {
            this.type = 'info';
          }
          break;
        case 'role':
          if (!newValue || newValue && ['alert', 'alertdialog'].indexOf(newValue) === -1) {
            this.role = 'alert';
          }
          break;
        case 'dismiss':
          if ((!newValue || newValue === '') && (!oldValue || oldValue === '')) {
            if (this.button && !this.hasAttribute('dismiss')) {
              this.destroyCloseButton();
            } else if (!this.button && this.hasAttribute('dismiss')) {
              this.createCloseButton();
            }
          } else if (this.button && newValue === 'false') {
            this.destroyCloseButton();
          } else if (!this.button && newValue !== 'false') {
            this.createCloseButton();
          }
          break;
        case 'close-text':
          if (!newValue || newValue !== oldValue) {
            if (this.button) {
              this.button.setAttribute('aria-label', newValue);
            }
          }
          break;
        case 'auto-dismiss':
          this.autoDismiss();
          break;
      }
    }

    /* Observe added elements */;
    _proto.onMutation = function onMutation(mutationsList) {
      // eslint-disable-next-line no-restricted-syntax
      for (var _iterator = _createForOfIteratorHelperLoose(mutationsList), _step; !(_step = _iterator()).done;) {
        var mutation = _step.value;
        if (mutation.type === 'childList') {
          if (mutation.addedNodes.length) {
            // Make sure that the button is always the first element
            if (this.button && this.firstElementChild !== this.button) {
              this.prepend(this.button);
            }
          }
        }
      }
    }

    /* Method to close the alert */;
    _proto.close = function close() {
      this.dispatchEvent(new CustomEvent('joomla.alert.close'));
      this.style.animationName = 'joomla-alert-fade-out';
    }

    /* Method to create the close button */;
    _proto.createCloseButton = function createCloseButton() {
      this.button = document.createElement('button');
      this.button.setAttribute('type', 'button');
      this.button.classList.add('joomla-alert--close');
      this.button.innerHTML = '<span aria-hidden="true">&times;</span>';
      this.button.setAttribute('aria-label', this.closeText);
      this.insertAdjacentElement('afterbegin', this.button);

      /* Add the required listener */
      this.button.addEventListener('click', this.close);
    }

    /* Method to remove the close button */;
    _proto.destroyCloseButton = function destroyCloseButton() {
      if (this.button) {
        this.button.removeEventListener('click', this.close);
        this.button.parentNode.removeChild(this.button);
        this.button = null;
      }
    }

    /* Method to auto-dismiss */;
    _proto.autoDismiss = function autoDismiss() {
      var timer = parseInt(this.getAttribute('auto-dismiss'), 10);
      setTimeout(this.close, timer >= 10 ? timer : 3000);
    };
    _createClass(AlertElement, [{
      key: "type",
      get: function get() {
        return this.getAttribute('type');
      },
      set: function set(value) {
        this.setAttribute('type', value);
      }
    }, {
      key: "role",
      get: function get() {
        return this.getAttribute('role');
      },
      set: function set(value) {
        this.setAttribute('role', value);
      }
    }, {
      key: "closeText",
      get: function get() {
        return this.getAttribute('close-text');
      },
      set: function set(value) {
        this.setAttribute('close-text', value);
      }
    }, {
      key: "dismiss",
      get: function get() {
        return this.getAttribute('dismiss');
      },
      set: function set(value) {
        this.setAttribute('dismiss', value);
      }
    }, {
      key: "autodismiss",
      get: function get() {
        return this.getAttribute('auto-dismiss');
      },
      set: function set(value) {
        this.setAttribute('auto-dismiss', value);
      }
    }], [{
      key: "observedAttributes",
      get: function get() {
        return ['type', 'role', 'dismiss', 'auto-dismiss', 'close-text'];
      }
    }]);
    return AlertElement;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
  if (!customElements.get('joomla-alert')) {
    customElements.define('joomla-alert', AlertElement);
  }

  /**
   * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  /**
   * Returns the container of the Messages
   *
   * @param {string|HTMLElement}  container  The container
   *
   * @returns {HTMLElement}
   */
  var getMessageContainer = function getMessageContainer(container) {
    var messageContainer;
    if (container instanceof HTMLElement) {
      return container;
    }
    if (typeof container === 'undefined' || container && container === '#system-message-container') {
      messageContainer = document.getElementById('system-message-container');
    } else {
      messageContainer = document.querySelector(container);
    }
    return messageContainer;
  };

  /**
   * Render messages send via JSON
   * Used by some javascripts such as validate.js
   *
   * @param   {object}  messages JavaScript object containing the messages to render.
   *          Example:
   *          const messages = {
   *              "message": ["This will be a green message", "So will this"],
   *              "error": ["This will be a red message", "So will this"],
   *              "info": ["This will be a blue message", "So will this"],
   *              "notice": ["This will be same as info message", "So will this"],
   *              "warning": ["This will be a orange message", "So will this"],
   *              "my_custom_type": ["This will be same as info message", "So will this"]
   *          };
   * @param  {string} selector The selector of the container where the message will be rendered
   * @param  {bool}   keepOld  If we shall discard old messages
   * @param  {int}    timeout  The milliseconds before the message self destruct
   * @return  void
   */
  Joomla.renderMessages = function (messages, selector, keepOld, timeout) {
    var messageContainer = getMessageContainer(selector);
    if (typeof keepOld === 'undefined' || keepOld && keepOld === false) {
      Joomla.removeMessages(messageContainer);
    }
    [].slice.call(Object.keys(messages)).forEach(function (type) {
      var alertClass = type;

      // Array of messages of this type
      var typeMessages = messages[type];
      var messagesBox = document.createElement('joomla-alert');
      if (['success', 'info', 'danger', 'warning'].indexOf(type) < 0) {
        alertClass = type === 'notice' ? 'info' : type;
        alertClass = type === 'message' ? 'success' : alertClass;
        alertClass = type === 'error' ? 'danger' : alertClass;
        alertClass = type === 'warning' ? 'warning' : alertClass;
      }
      messagesBox.setAttribute('type', alertClass);
      messagesBox.setAttribute('close-text', Joomla.Text._('JCLOSE'));
      messagesBox.setAttribute('dismiss', true);
      if (timeout && parseInt(timeout, 10) > 0) {
        messagesBox.setAttribute('auto-dismiss', timeout);
      }

      // Title
      var title = Joomla.Text._(type);

      // Skip titles with untranslated strings
      if (typeof title !== 'undefined') {
        var titleWrapper = document.createElement('div');
        titleWrapper.className = 'alert-heading';
        titleWrapper.innerHTML = Joomla.sanitizeHtml("<span class=\"" + type + "\"></span><span class=\"visually-hidden\">" + (Joomla.Text._(type) ? Joomla.Text._(type) : type) + "</span>");
        messagesBox.appendChild(titleWrapper);
      }

      // Add messages to the message box
      var messageWrapper = document.createElement('div');
      messageWrapper.className = 'alert-wrapper';
      typeMessages.forEach(function (typeMessage) {
        messageWrapper.innerHTML += Joomla.sanitizeHtml("<div class=\"alert-message\">" + typeMessage + "</div>");
      });
      messagesBox.appendChild(messageWrapper);
      messageContainer.appendChild(messagesBox);
    });
  };

  /**
   * Remove messages
   *
   * @param  {element} container The element of the container of the message
   * to be removed
   *
   * @return  {void}
   */
  Joomla.removeMessages = function (container) {
    var messageContainer = getMessageContainer(container);
    var alerts = [].slice.call(messageContainer.querySelectorAll('joomla-alert'));
    if (alerts.length) {
      alerts.forEach(function (alert) {
        alert.close();
      });
    }
  };
  document.addEventListener('DOMContentLoaded', function () {
    var messages = Joomla.getOptions('joomla.messages');
    if (messages) {
      Object.keys(messages).map(function (message) {
        return Joomla.renderMessages(messages[message], undefined, true, undefined);
      });
    }
  });

})();
