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
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  window.customElements.define('joomla-editor-none', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;

      // Properties
      _this.editor = '';

      // Bindings
      _this.unregisterEditor = _this.unregisterEditor.bind(_assertThisInitialized(_this));
      _this.registerEditor = _this.registerEditor.bind(_assertThisInitialized(_this));
      _this.childrenChange = _this.childrenChange.bind(_assertThisInitialized(_this));
      _this.getSelection = _this.getSelection.bind(_assertThisInitialized(_this));

      // Watch for children changes.
      // eslint-disable-next-line no-return-assign
      new MutationObserver(function () {
        return _this.childrenChange();
      }).observe(_assertThisInitialized(_this), {
        childList: true
      });
      return _this;
    }

    /**
     * Lifecycle
     */
    var _proto = _class.prototype;
    _proto.connectedCallback = function connectedCallback() {
      // Note the mutation observer won't fire for initial contents,
      // so childrenChange is also called here.
      this.childrenChange();
    }

    /**
     * Lifecycle
     */;
    _proto.disconnectedCallback = function disconnectedCallback() {
      this.unregisterEditor();
    }

    /**
     * Get the selected text
     */;
    _proto.getSelection = function getSelection() {
      if (document.selection) {
        // IE support
        this.editor.focus();
        return document.selection.createRange();
      }
      if (this.editor.selectionStart || this.editor.selectionStart === 0) {
        // MOZILLA/NETSCAPE support
        return this.editor.value.substring(this.editor.selectionStart, this.editor.selectionEnd);
      }
      return this.editor.value;
    }

    /**
     * Register the editor
     */;
    _proto.registerEditor = function registerEditor() {
      var _this2 = this;
      if (!window.Joomla || !window.Joomla.editors || typeof window.Joomla.editors !== 'object') {
        throw new Error('The Joomla API is not correctly registered.');
      }
      window.Joomla.editors.instances[this.editor.id] = {
        id: function id() {
          return _this2.editor.id;
        },
        element: function element() {
          return _this2.editor;
        },
        // eslint-disable-next-line no-return-assign
        getValue: function getValue() {
          return _this2.editor.value;
        },
        // eslint-disable-next-line no-return-assign
        setValue: function setValue(text) {
          return _this2.editor.value = text;
        },
        // eslint-disable-next-line no-return-assign
        getSelection: function getSelection() {
          return _this2.getSelection();
        },
        // eslint-disable-next-line no-return-assign
        disable: function disable(disabled) {
          _this2.editor.disabled = disabled;
          _this2.editor.readOnly = disabled;
        },
        // eslint-disable-next-line no-return-assign
        replaceSelection: function replaceSelection(text) {
          if (_this2.editor.selectionStart || _this2.editor.selectionStart === 0) {
            _this2.editor.value = _this2.editor.value.substring(0, _this2.editor.selectionStart) + text + _this2.editor.value.substring(_this2.editor.selectionEnd, _this2.editor.value.length);
          } else {
            _this2.editor.value += text;
          }
        },
        onSave: function onSave() {}
      };
    }

    /**
     * Remove the editor from the Joomla API
     */;
    _proto.unregisterEditor = function unregisterEditor() {
      if (this.editor) {
        delete window.Joomla.editors.instances[this.editor.id];
      }
    }

    /**
     * Called when element's child list changes
     */;
    _proto.childrenChange = function childrenChange() {
      // Ensure the first child is an input with a textarea type.
      if (this.firstElementChild && this.firstElementChild.tagName && this.firstElementChild.tagName.toLowerCase() === 'textarea' && this.firstElementChild.getAttribute('id')) {
        this.editor = this.firstElementChild;
        this.unregisterEditor();
        this.registerEditor();
      }
    };
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
