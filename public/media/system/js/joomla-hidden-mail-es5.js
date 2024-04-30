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

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  window.customElements.define('joomla-hidden-mail', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;
      _this.newElement = '';
      _this.base = '';
      return _this;
    }

    /**
     * Lifecycle
     */
    var _proto = _class.prototype;
    _proto.disconnectedCallback = function disconnectedCallback() {
      this.innerHTML = '';
    }

    /**
     * Lifecycle
     */;
    _proto.connectedCallback = function connectedCallback() {
      var _this2 = this;
      this.base = this.getAttribute('base') + "/";
      if (this.getAttribute('is-link') === '1') {
        this.newElement = document.createElement('a');
        this.newElement.setAttribute('href', "mailto:" + this.constructor.b64DecodeUnicode(this.getAttribute('first')) + "@" + this.constructor.b64DecodeUnicode(this.getAttribute('last')));

        // Get all of the original element attributes, and pass them to the link
        [].slice.call(this.attributes).forEach(function (attribute, index) {
          var _this2$attributes$ite = _this2.attributes.item(index),
            nodeName = _this2$attributes$ite.nodeName;
          if (nodeName) {
            // We do care for some attributes
            if (['is-link', 'is-email', 'first', 'last', 'text'].indexOf(nodeName) === -1) {
              var _this2$attributes$ite2 = _this2.attributes.item(index),
                nodeValue = _this2$attributes$ite2.nodeValue;
              _this2.newElement.setAttribute(nodeName, nodeValue);
            }
          }
        });
      } else {
        this.newElement = document.createElement('span');
      }
      if (this.getAttribute('text')) {
        var innerStr = this.constructor.b64DecodeUnicode(this.getAttribute('text'));
        innerStr = innerStr.replace('src="images/', "src=\"" + this.base + "images/").replace('src="media/', "src=\"" + this.base + "media/");
        this.newElement.innerHTML = Joomla.sanitizeHtml(innerStr);
      } else {
        this.newElement.innerText = window.atob(this.getAttribute('first')) + "@" + window.atob(this.getAttribute('last'));
      }

      // Remove class and style Attributes
      this.removeAttribute('class');
      this.removeAttribute('style');

      // Remove the noscript message
      this.innerText = '';

      // Display the new element
      this.appendChild(this.newElement);
    };
    _class.b64DecodeUnicode = function b64DecodeUnicode(str) {
      return decodeURIComponent(Array.prototype.map.call(atob(str), function (c) {
        return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
    };
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
