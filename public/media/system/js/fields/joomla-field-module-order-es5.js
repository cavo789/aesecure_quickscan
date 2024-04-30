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
   * @package         Joomla.JavaScript
   * @copyright       (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license         GNU General Public License version 2 or later; see LICENSE.txt
   */
  customElements.define('joomla-field-module-order', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;
      _this.linkedFieldSelector = '';
      _this.linkedFieldElement = '';
      _this.originalPosition = '';
      _this.writeDynaList.bind(_assertThisInitialized(_this));
      _this.getNewOrder.bind(_assertThisInitialized(_this));
      return _this;
    }
    var _proto = _class.prototype;
    _proto.connectedCallback = function connectedCallback() {
      this.linkedFieldSelector = this.getAttribute('data-linked-field') || 'jform_position';
      if (!this.linkedFieldSelector) {
        throw new Error('No linked field defined!');
      }
      this.linkedFieldElement = document.getElementById(this.linkedFieldSelector);
      if (!this.linkedFieldElement) {
        throw new Error('No linked field defined!');
      }
      var that = this;
      this.originalPosition = this.linkedFieldElement.value;

      /** Initialize the field * */
      this.getNewOrder(this.originalPosition);

      /** Watch for changes on the linked field * */
      this.linkedFieldElement.addEventListener('change', function () {
        that.originalPosition = that.linkedFieldElement.value;
        that.getNewOrder(that.linkedFieldElement.value);
      });
    };
    _proto.writeDynaList = function writeDynaList(selectProperties, source, originalPositionName, originalPositionValue) {
      var i = 0;
      var selectNode = document.createElement('select');
      if (this.hasAttribute('disabled')) {
        selectNode.setAttribute('disabled', '');
      }
      if (this.getAttribute('onchange')) {
        selectNode.setAttribute('onchange', this.getAttribute('onchange'));
      }
      if (this.getAttribute('size')) {
        selectNode.setAttribute('size', this.getAttribute('size'));
      }
      selectNode.classList.add(selectProperties.itemClass);
      selectNode.setAttribute('name', selectProperties.name);
      selectNode.id = selectProperties.id;

      // eslint-disable-next-line no-restricted-syntax
      for (var x in source) {
        // eslint-disable-next-line no-prototype-builtins
        if (!source.hasOwnProperty(x)) {
          // eslint-disable-next-line no-continue
          continue;
        }
        var node = document.createElement('option');
        var item = source[x];

        // eslint-disable-next-line prefer-destructuring
        node.value = item[1];
        // eslint-disable-next-line prefer-destructuring
        node.innerHTML = Joomla.sanitizeHtml(item[2]);
        if (originalPositionName && originalPositionValue === item[1] || !originalPositionName && i === 0) {
          node.setAttribute('selected', 'selected');
        }
        selectNode.appendChild(node);
        i += 1;
      }
      this.innerHTML = '';
      this.appendChild(selectNode);
    };
    _proto.getNewOrder = function getNewOrder(originalPosition) {
      var url = this.getAttribute('data-url');
      var clientId = this.getAttribute('data-client-id');
      var originalOrder = this.getAttribute('data-ordering');
      var name = this.getAttribute('data-name');
      var attr = this.getAttribute('data-client-attr') ? this.getAttribute('data-client-attr') : 'form-select';
      var id = "" + this.getAttribute('data-id');
      var moduleId = "" + this.getAttribute('data-module-id');
      var orders = [];
      var that = this;
      Joomla.request({
        url: url + "&client_id=" + clientId + "&position=" + originalPosition + "&module_id=" + moduleId,
        method: 'GET',
        perform: true,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        onSuccess: function onSuccess(resp) {
          if (resp) {
            var response;
            try {
              response = JSON.parse(resp);
            } catch (e) {
              // eslint-disable-next-line no-console
              console.error(e);
            }

            /** Check if everything is OK * */
            if (response.data.length > 0) {
              for (var i = 0; i < response.data.length; i += 1) {
                orders[i] = response.data[i].split(',');
              }
              that.writeDynaList({
                name: name,
                id: id,
                itemClass: attr
              }, orders, that.originalPosition, originalOrder);
            }
          }

          /** Render messages, if any. There are only message in case of errors. * */
          if (typeof resp.messages === 'object' && resp.messages !== null) {
            Joomla.renderMessages(resp.messages);
          }
        }
      });
    };
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
