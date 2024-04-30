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

  /**
   * Fancy select field, which use Choices.js
   *
   * Example:
   * <joomla-field-fancy-select ...attributes>
   *   <select>...</select>
   * </joomla-field-fancy-select>
   *
   * Possible attributes:
   *
   * allow-custom          Whether allow User to dynamically add a new value.
   * new-item-prefix=""    Prefix for a dynamically added value.
   *
   * remote-search         Enable remote search.
   * url=""                Url for remote search.
   * term-key="term"       Variable key name for searched term, will be appended to Url.
   *
   * min-term-length="1"   The minimum length a search value should be before choices are searched.
   * placeholder=""        The value of the inputs placeholder.
   * search-placeholder="" The value of the search inputs placeholder.
   *
   * data-max-results="30" The maximum amount of search results to be displayed.
   * data-max-render="30"  The maximum amount of items to be rendered, critical for large lists.
   */
  window.customElements.define('joomla-field-fancy-select', /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(_class, _HTMLElement);
    /**
     * Lifecycle
     */
    function _class() {
      var _this;
      _this = _HTMLElement.call(this) || this;

      // Keycodes
      _this.keyCode = {
        ENTER: 13
      };
      if (!Joomla) {
        throw new Error('Joomla API is not properly initiated');
      }
      if (!window.Choices) {
        throw new Error('JoomlaFieldFancySelect requires Choices.js to work');
      }
      _this.choicesCache = {};
      _this.activeXHR = null;
      _this.choicesInstance = null;
      _this.isDisconnected = false;
      return _this;
    }

    /**
     * Lifecycle
     */
    var _proto = _class.prototype;
    _proto.connectedCallback = function connectedCallback() {
      var _this2 = this;
      // Make sure Choices are loaded
      if (window.Choices || document.readyState === 'complete') {
        this.doConnect();
      } else {
        var callback = function callback() {
          _this2.doConnect();
          window.removeEventListener('load', callback);
        };
        window.addEventListener('load', callback);
      }
    };
    _proto.doConnect = function doConnect() {
      var _this3 = this;
      // Get a <select> element
      this.select = this.querySelector('select');
      if (!this.select) {
        throw new Error('JoomlaFieldFancySelect requires <select> element to work');
      }

      // The element was already initialised previously and perhaps was detached from DOM
      if (this.choicesInstance) {
        if (this.isDisconnected) {
          // Re init previous instance
          this.choicesInstance.init();
          this.isDisconnected = false;
        }
        return;
      }
      this.isDisconnected = false;

      // Add placeholder option for multiple mode,
      // Because it not supported as parameter by Choices for <select> https://github.com/jshjohnson/Choices#placeholder
      if (this.select.multiple && this.placeholder) {
        var option = document.createElement('option');
        option.setAttribute('placeholder', '');
        option.textContent = this.placeholder;
        this.select.appendChild(option);
      }

      // Init Choices
      // eslint-disable-next-line no-undef
      this.choicesInstance = new Choices(this.select, {
        placeholderValue: this.placeholder,
        searchPlaceholderValue: this.searchPlaceholder,
        removeItemButton: true,
        searchFloor: this.minTermLength,
        searchResultLimit: parseInt(this.select.dataset.maxResults, 10) || 10,
        renderChoiceLimit: parseInt(this.select.dataset.maxRender, 10) || -1,
        shouldSort: false,
        fuseOptions: {
          threshold: 0.3 // Strict search
        },

        noResultsText: Joomla.Text._('JGLOBAL_SELECT_NO_RESULTS_MATCH', 'No results found'),
        noChoicesText: Joomla.Text._('JGLOBAL_SELECT_NO_RESULTS_MATCH', 'No results found'),
        itemSelectText: Joomla.Text._('JGLOBAL_SELECT_PRESS_TO_SELECT', 'Press to select'),
        // Redefine some classes
        classNames: {
          button: 'choices__button_joomla' // It is need because an original styling use unavailable Icon.svg file
        }
      });

      // Handle typing of custom Term
      if (this.allowCustom) {
        // START Work around for issue https://github.com/joomla/joomla-cms/issues/29459
        // The choices.js always auto-highlights the first element
        // in the dropdown that not allow to add a custom Term.
        //
        // This workaround can be removed when choices.js
        // will have an option that allow to disable it.

        // eslint-disable-next-line no-underscore-dangle, prefer-destructuring
        var _highlightChoice = this.choicesInstance._highlightChoice;
        // eslint-disable-next-line no-underscore-dangle
        this.choicesInstance._highlightChoice = function (el) {
          // Prevent auto-highlight of first element, if nothing actually highlighted
          if (!el) return;

          // Call original highlighter
          _highlightChoice.call(_this3.choicesInstance, el);
        };

        // Unhighlight any highlighted items, when mouse leave the dropdown
        this.addEventListener('mouseleave', function () {
          if (!_this3.choicesInstance.dropdown.isActive) {
            return;
          }
          var highlighted = Array.from(_this3.choicesInstance.dropdown.element.querySelectorAll("." + _this3.choicesInstance.config.classNames.highlightedState));
          highlighted.forEach(function (choice) {
            choice.classList.remove(_this3.choicesInstance.config.classNames.highlightedState);
            choice.setAttribute('aria-selected', 'false');
          });

          // eslint-disable-next-line no-underscore-dangle
          _this3.choicesInstance._highlightPosition = 0;
        });
        // END workaround for issue #29459

        // Add custom term on ENTER keydown
        this.addEventListener('keydown', function (event) {
          if (event.keyCode !== _this3.keyCode.ENTER || event.target !== _this3.choicesInstance.input.element) {
            return;
          }
          event.preventDefault();

          // eslint-disable-next-line no-underscore-dangle
          if (_this3.choicesInstance._highlightPosition || !event.target.value) {
            return;
          }

          // Make sure nothing is highlighted
          var highlighted = _this3.choicesInstance.dropdown.element.querySelector("." + _this3.choicesInstance.config.classNames.highlightedState);
          if (highlighted) {
            return;
          }

          // Check if value already exist
          var lowerValue = event.target.value.toLowerCase();
          var valueInCache = false;

          // Check if value in existing choices
          _this3.choicesInstance.config.choices.some(function (choiceItem) {
            if (choiceItem.value.toLowerCase() === lowerValue || choiceItem.label.toLowerCase() === lowerValue) {
              valueInCache = choiceItem.value;
              return true;
            }
            return false;
          });
          if (valueInCache === false) {
            // Check if value in cache
            Object.keys(_this3.choicesCache).some(function (key) {
              if (key.toLowerCase() === lowerValue || _this3.choicesCache[key].toLowerCase() === lowerValue) {
                valueInCache = key;
                return true;
              }
              return false;
            });
          }

          // Make choice based on existing value
          if (valueInCache !== false) {
            _this3.choicesInstance.setChoiceByValue(valueInCache);
            event.target.value = null;
            _this3.choicesInstance.hideDropdown();
            return;
          }

          // Create and add new
          _this3.choicesInstance.setChoices([{
            value: _this3.newItemPrefix + event.target.value,
            label: event.target.value,
            selected: true,
            customProperties: {
              value: event.target.value // Store real value, just in case
            }
          }], 'value', 'label', false);
          _this3.choicesCache[event.target.value] = event.target.value;
          event.target.value = null;
          _this3.choicesInstance.hideDropdown();
        });
      }

      // Handle remote search
      if (this.remoteSearch && this.url) {
        // Cache existing
        this.choicesInstance.config.choices.forEach(function (choiceItem) {
          _this3.choicesCache[choiceItem.value] = choiceItem.label;
        });
        var lookupDelay = 300;
        var lookupTimeout = null;
        this.select.addEventListener('search', function () {
          clearTimeout(lookupTimeout);
          lookupTimeout = setTimeout(_this3.requestLookup.bind(_this3), lookupDelay);
        });
      }
    }

    /**
     * Lifecycle
     */;
    _proto.disconnectedCallback = function disconnectedCallback() {
      // Destroy Choices instance, to unbind event listeners
      if (this.choicesInstance) {
        this.choicesInstance.destroy();
        this.isDisconnected = true;
      }
      if (this.activeXHR) {
        this.activeXHR.abort();
        this.activeXHR = null;
      }
    };
    _proto.requestLookup = function requestLookup() {
      var _this4 = this;
      var url = this.url;
      url += url.indexOf('?') === -1 ? '?' : '&';
      url += encodeURIComponent(this.termKey) + "=" + encodeURIComponent(this.choicesInstance.input.value);

      // Stop previous request if any
      if (this.activeXHR) {
        this.activeXHR.abort();
      }
      this.activeXHR = Joomla.request({
        url: url,
        onSuccess: function onSuccess(response) {
          _this4.activeXHR = null;
          var items = response ? JSON.parse(response) : [];
          if (!items.length) {
            return;
          }

          // Remove duplications
          var item;
          // eslint-disable-next-line no-plusplus
          for (var i = items.length - 1; i >= 0; i--) {
            // The loop must be form the end !!!
            item = items[i];
            // eslint-disable-next-line prefer-template
            item.value = '' + item.value; // Make sure the value is a string, choices.js expect a string.

            if (_this4.choicesCache[item.value]) {
              items.splice(i, 1);
            } else {
              _this4.choicesCache[item.value] = item.text;
            }
          }

          // Add new options to field, assume that each item is object, eg {value: "foo", text: "bar"}
          if (items.length) {
            _this4.choicesInstance.setChoices(items, 'value', 'text', false);
          }
        },
        onError: function onError() {
          _this4.activeXHR = null;
        }
      });
    };
    _proto.disableAllOptions = function disableAllOptions() {
      // Choices.js does not offer a public API for accessing the choices
      // So we have to access the private store => don't eslint
      // eslint-disable-next-line no-underscore-dangle
      var choices = this.choicesInstance._store.choices;
      choices.forEach(function (elem, index) {
        choices[index].disabled = true;
        choices[index].selected = false;
      });
      this.choicesInstance.clearStore();
      this.choicesInstance.setChoices(choices, 'value', 'label', true);
    };
    _proto.enableAllOptions = function enableAllOptions() {
      // Choices.js does not offer a public API for accessing the choices
      // So we have to access the private store => don't eslint
      // eslint-disable-next-line no-underscore-dangle
      var choices = this.choicesInstance._store.choices;
      var values = this.choicesInstance.getValue(true);
      choices.forEach(function (elem, index) {
        choices[index].disabled = false;
      });
      this.choicesInstance.clearStore();
      this.choicesInstance.setChoices(choices, 'value', 'label', true);
      this.value = values;
    };
    _proto.disableByValue = function disableByValue($val) {
      // Choices.js does not offer a public API for accessing the choices
      // So we have to access the private store => don't eslint
      // eslint-disable-next-line no-underscore-dangle
      var choices = this.choicesInstance._store.choices;
      var values = this.choicesInstance.getValue(true);
      choices.forEach(function (elem, index) {
        if (elem.value === $val) {
          choices[index].disabled = true;
          choices[index].selected = false;
        }
      });
      var index = values.indexOf($val);
      if (index > -1) {
        values.slice(index, 1);
      }
      this.choicesInstance.clearStore();
      this.choicesInstance.setChoices(choices, 'value', 'label', true);
      this.value = values;
    };
    _proto.enableByValue = function enableByValue($val) {
      // Choices.js does not offer a public API for accessing the choices
      // So we have to access the private store => don't eslint
      // eslint-disable-next-line no-underscore-dangle
      var choices = this.choicesInstance._store.choices;
      var values = this.choicesInstance.getValue(true);
      choices.forEach(function (elem, index) {
        if (elem.value === $val) {
          choices[index].disabled = false;
        }
      });
      this.choicesInstance.clearStore();
      this.choicesInstance.setChoices(choices, 'value', 'label', true);
      this.value = values;
    };
    _createClass(_class, [{
      key: "allowCustom",
      get:
      // Attributes to monitor
      function get() {
        return this.hasAttribute('allow-custom');
      }
    }, {
      key: "remoteSearch",
      get: function get() {
        return this.hasAttribute('remote-search');
      }
    }, {
      key: "url",
      get: function get() {
        return this.getAttribute('url');
      }
    }, {
      key: "termKey",
      get: function get() {
        return this.getAttribute('term-key') || 'term';
      }
    }, {
      key: "minTermLength",
      get: function get() {
        return parseInt(this.getAttribute('min-term-length'), 10) || 1;
      }
    }, {
      key: "newItemPrefix",
      get: function get() {
        return this.getAttribute('new-item-prefix') || '';
      }
    }, {
      key: "placeholder",
      get: function get() {
        return this.getAttribute('placeholder');
      }
    }, {
      key: "searchPlaceholder",
      get: function get() {
        return this.getAttribute('search-placeholder');
      }
    }, {
      key: "value",
      get: function get() {
        return this.choicesInstance.getValue(true);
      },
      set: function set($val) {
        this.choicesInstance.setChoiceByValue($val);
      }
    }]);
    return _class;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)));

})();
