(function () {
  'use strict';

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

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  window.customElements.define('joomla-field-permissions', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;
      if (!Joomla) {
        throw new Error('Joomla API is not properly initiated');
      }
      if (!_this.getAttribute('data-uri')) {
        throw new Error('No valid url for validation');
      }
      _this.query = window.location.search.substring(1);
      _this.buttons = '';
      _this.buttonDataSelector = 'data-onchange-task';
      _this.onDropdownChange = _this.onDropdownChange.bind(_assertThisInitialized(_this));
      _this.getUrlParam = _this.getUrlParam.bind(_assertThisInitialized(_this));
      _this.component = _this.getUrlParam('component');
      _this.extension = _this.getUrlParam('extension');
      _this.option = _this.getUrlParam('option');
      _this.view = _this.getUrlParam('view');
      _this.asset = 'not';
      _this.context = '';
      return _this;
    }

    /**
     * Lifecycle
     */
    var _proto = _class.prototype;
    _proto.connectedCallback = function connectedCallback() {
      var _this2 = this;
      this.buttons = [].slice.call(document.querySelectorAll("[" + this.buttonDataSelector + "]"));
      if (this.buttons) {
        this.buttons.forEach(function (button) {
          button.addEventListener('change', _this2.onDropdownChange);
        });
      }
    }

    /**
     * Lifecycle
     */;
    _proto.disconnectedCallback = function disconnectedCallback() {
      var _this3 = this;
      if (this.buttons) {
        this.buttons.forEach(function (button) {
          button.removeEventListener('change', _this3.onDropdownChange);
        });
      }
    }

    /**
     * Lifecycle
     */;
    _proto.onDropdownChange = function onDropdownChange(event) {
      event.preventDefault();
      var task = event.target.getAttribute(this.buttonDataSelector);
      if (task === 'permissions.apply') {
        this.sendPermissions(event);
      }
    };
    _proto.sendPermissions = function sendPermissions(event) {
      var target = event.target;

      // Set the icon while storing the values
      var icon = document.getElementById("icon_" + target.id);
      icon.removeAttribute('class');
      icon.setAttribute('class', 'joomla-icon joomla-field-permissions__spinner');

      // Get values add prepare GET-Parameter
      var value = target.value;
      if (document.getElementById('jform_context')) {
        this.context = document.getElementById('jform_context').value;
        var _this$context$split = this.context.split('.');
        this.context = _this$context$split[0];
      }
      if (this.option === 'com_config' && !this.component && !this.extension) {
        this.asset = 'root.1';
      } else if (!this.extension && this.view === 'component') {
        this.asset = this.component;
      } else if (this.context) {
        if (this.view === 'group') {
          this.asset = this.context + ".fieldgroup." + this.getUrlParam('id');
        } else {
          this.asset = this.context + ".field.{this.getUrlParam('id')}";
        }
        this.title = document.getElementById('jform_title').value;
      } else if (this.extension && this.view) {
        this.asset = this.extension + "." + this.view + "." + this.getUrlParam('id');
        this.title = document.getElementById('jform_title').value;
      } else if (!this.extension && this.view) {
        this.asset = this.option + "." + this.view + "." + this.getUrlParam('id');
        this.title = document.getElementById('jform_title').value;
      }
      var id = target.id.replace('jform_rules_', '');
      var lastUnderscoreIndex = id.lastIndexOf('_');
      var permissionData = {
        comp: this.asset,
        action: id.substring(0, lastUnderscoreIndex),
        rule: id.substring(lastUnderscoreIndex + 1),
        value: value,
        title: this.title
      };

      // Remove JS messages, if they exist.
      Joomla.removeMessages();

      // Ajax request
      Joomla.request({
        url: this.getAttribute('data-uri'),
        method: 'POST',
        data: JSON.stringify(permissionData),
        perform: true,
        headers: {
          'Content-Type': 'application/json'
        },
        onSuccess: function onSuccess(data) {
          var response;
          try {
            response = JSON.parse(data);
          } catch (e) {
            // eslint-disable-next-line no-console
            console.error(e);
          }
          icon.removeAttribute('class');

          // Check if everything is OK
          if (response.data && response.data.result) {
            icon.setAttribute('class', 'joomla-icon joomla-field-permissions__allowed');
            var badgeSpan = target.parentNode.parentNode.nextElementSibling.querySelector('span');
            badgeSpan.removeAttribute('class');
            badgeSpan.setAttribute('class', response.data.class);
            badgeSpan.innerHTML = Joomla.sanitizeHtml(response.data.text);
          }

          // Render messages, if any. There are only message in case of errors.
          if (typeof response.messages === 'object' && response.messages !== null) {
            Joomla.renderMessages(response.messages);
            if (response.data && response.data.result) {
              icon.setAttribute('class', 'joomla-icon joomla-field-permissions__allowed');
            } else {
              icon.setAttribute('class', 'joomla-icon joomla-field-permissions__denied');
            }
          }
        },
        onError: function onError(xhr) {
          // Remove the spinning icon.
          icon.removeAttribute('style');
          Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr, xhr.statusText));
          icon.setAttribute('class', 'joomla-icon joomla-field-permissions__denied');
        }
      });
    };
    _proto.getUrlParam = function getUrlParam(variable) {
      var vars = this.query.split('&');
      var i = 0;
      for (i; i < vars.length; i += 1) {
        var pair = vars[i].split('=');
        if (pair[0] === variable) {
          return pair[1];
        }
      }
      return false;
    };
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
