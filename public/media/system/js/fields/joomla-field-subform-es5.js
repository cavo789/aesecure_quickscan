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
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function (customElements) {
    var KEYCODE = {
      SPACE: 32,
      ESC: 27,
      ENTER: 13
    };

    /**
     * Helper for testing whether a selection modifier is pressed
     * @param {Event} event
     *
     * @returns {boolean|*}
     */
    function hasModifier(event) {
      return event.ctrlKey || event.metaKey || event.shiftKey;
    }
    var JoomlaFieldSubform = /*#__PURE__*/function (_HTMLElement) {
      _inheritsLoose(JoomlaFieldSubform, _HTMLElement);
      function JoomlaFieldSubform() {
        var _this;
        _this = _HTMLElement.call(this) || this;
        var that = _assertThisInitialized(_this);

        // Get the rows container
        _this.containerWithRows = _assertThisInitialized(_this);
        if (_this.rowsContainer) {
          var allContainers = _this.querySelectorAll(_this.rowsContainer);

          // Find closest, and exclude nested
          Array.from(allContainers).forEach(function (container) {
            if (container.closest('joomla-field-subform') === _assertThisInitialized(_this)) {
              _this.containerWithRows = container;
            }
          });
        }

        // Keep track of row index, this is important to avoid a name duplication
        // Note: php side should reset the indexes each time, eg: $value = array_values($value);
        _this.lastRowIndex = _this.getRows().length - 1;

        // Template for the repeating group
        _this.template = '';

        // Prepare a row template, and find available field names
        _this.prepareTemplate();

        // Bind buttons
        if (_this.buttonAdd || _this.buttonRemove) {
          _this.addEventListener('click', function (event) {
            var btnAdd = null;
            var btnRem = null;
            if (that.buttonAdd) {
              btnAdd = event.target.matches(that.buttonAdd) ? event.target : event.target.closest(that.buttonAdd);
            }
            if (that.buttonRemove) {
              btnRem = event.target.matches(that.buttonRemove) ? event.target : event.target.closest(that.buttonRemove);
            }

            // Check active, with extra check for nested joomla-field-subform
            if (btnAdd && btnAdd.closest('joomla-field-subform') === that) {
              var row = btnAdd.closest(that.repeatableElement);
              row = row && row.closest('joomla-field-subform') === that ? row : null;
              that.addRow(row);
              event.preventDefault();
            } else if (btnRem && btnRem.closest('joomla-field-subform') === that) {
              var _row = btnRem.closest(that.repeatableElement);
              that.removeRow(_row);
              event.preventDefault();
            }
          });
          _this.addEventListener('keydown', function (event) {
            if (event.keyCode !== KEYCODE.SPACE) return;
            var isAdd = that.buttonAdd && event.target.matches(that.buttonAdd);
            var isRem = that.buttonRemove && event.target.matches(that.buttonRemove);
            if ((isAdd || isRem) && event.target.closest('joomla-field-subform') === that) {
              var row = event.target.closest(that.repeatableElement);
              row = row && row.closest('joomla-field-subform') === that ? row : null;
              if (isRem && row) {
                that.removeRow(row);
              } else if (isAdd) {
                that.addRow(row);
              }
              event.preventDefault();
            }
          });
        }

        // Sorting
        if (_this.buttonMove) {
          _this.setUpDragSort();
        }
        return _this;
      }

      /**
       * Search for existing rows
       * @returns {HTMLElement[]}
       */
      var _proto = JoomlaFieldSubform.prototype;
      _proto.getRows = function getRows() {
        var _this2 = this;
        var rows = Array.from(this.containerWithRows.children);
        var result = [];

        // Filter out the rows
        rows.forEach(function (row) {
          if (row.matches(_this2.repeatableElement)) {
            result.push(row);
          }
        });
        return result;
      }

      /**
       * Prepare a row template
       */;
      _proto.prepareTemplate = function prepareTemplate() {
        var tmplElement = [].slice.call(this.children).filter(function (el) {
          return el.classList.contains('subform-repeatable-template-section');
        });
        if (tmplElement[0]) {
          this.template = tmplElement[0].innerHTML;
        }
        if (!this.template) {
          throw new Error('The row template is required for the subform element to work');
        }
      }

      /**
       * Add new row
       * @param {HTMLElement} after
       * @returns {HTMLElement}
       */;
      _proto.addRow = function addRow(after) {
        // Count how many we already have
        var count = this.getRows().length;
        if (count >= this.maximum) {
          return null;
        }

        // Make a new row from the template
        var tmpEl;
        if (this.containerWithRows.nodeName === 'TBODY' || this.containerWithRows.nodeName === 'TABLE') {
          tmpEl = document.createElement('tbody');
        } else {
          tmpEl = document.createElement('div');
        }
        tmpEl.innerHTML = this.template;
        var row = tmpEl.children[0];

        // Add to container
        if (after) {
          after.parentNode.insertBefore(row, after.nextSibling);
        } else {
          this.containerWithRows.append(row);
        }

        // Add draggable attributes
        if (this.buttonMove) {
          row.setAttribute('draggable', 'false');
          row.setAttribute('aria-grabbed', 'false');
          row.setAttribute('tabindex', '0');
        }

        // Marker that it is new
        row.setAttribute('data-new', '1');
        // Fix names and ids, and reset values
        this.fixUniqueAttributes(row, count);

        // Tell about the new row
        this.dispatchEvent(new CustomEvent('subform-row-add', {
          detail: {
            row: row
          },
          bubbles: true
        }));
        row.dispatchEvent(new CustomEvent('joomla:updated', {
          bubbles: true,
          cancelable: true
        }));
        return row;
      }

      /**
       * Remove the row
       * @param {HTMLElement} row
       */;
      _proto.removeRow = function removeRow(row) {
        // Count how much we have
        var count = this.getRows().length;
        if (count <= this.minimum) {
          return;
        }

        // Tell about the row will be removed
        this.dispatchEvent(new CustomEvent('subform-row-remove', {
          detail: {
            row: row
          },
          bubbles: true
        }));
        row.dispatchEvent(new CustomEvent('joomla:removed', {
          bubbles: true,
          cancelable: true
        }));
        row.parentNode.removeChild(row);
      }

      /**
       * Fix name and id for fields that are in the row
       * @param {HTMLElement} row
       * @param {Number} count
       */;
      _proto.fixUniqueAttributes = function fixUniqueAttributes(row, count) {
        var _this3 = this;
        var countTmp = count || 0;
        var group = row.getAttribute('data-group'); // current group name
        var basename = row.getAttribute('data-base-name');
        var countnew = Math.max(this.lastRowIndex, countTmp);
        var groupnew = basename + countnew; // new group name

        this.lastRowIndex = countnew + 1;
        row.setAttribute('data-group', groupnew);

        // Fix inputs that have a "name" attribute
        var haveName = row.querySelectorAll('[name]');
        var ids = {}; // Collect id for fix checkboxes and radio

        // Filter out nested
        haveName = [].slice.call(haveName).filter(function (el) {
          if (el.nodeName === 'JOOMLA-FIELD-SUBFORM') {
            // Skip self in .closest() call
            return el.parentElement.closest('joomla-field-subform') === _this3;
          }
          return el.closest('joomla-field-subform') === _this3;
        });
        haveName.forEach(function (elem) {
          var $el = elem;
          var name = $el.getAttribute('name');
          var aria = $el.getAttribute('aria-describedby');
          var id = name.replace(/(\[\]$)/g, '').replace(/(\]\[)/g, '__').replace(/\[/g, '_').replace(/\]/g, ''); // id from name
          var nameNew = name.replace("[" + group + "][", "[" + groupnew + "]["); // New name
          var idNew = id.replace(group, groupnew).replace(/\W/g, '_'); // Count new id
          var countMulti = 0; // count for multiple radio/checkboxes
          var forOldAttr = id; // Fix "for" in the labels

          if ($el.type === 'checkbox' && name.match(/\[\]$/)) {
            // <input type="checkbox" name="name[]"> fix
            // Recount id
            countMulti = ids[id] ? ids[id].length : 0;
            if (!countMulti) {
              // Set the id for fieldset and group label
              var fieldset = $el.closest('fieldset.checkboxes');
              var elLbl = row.querySelector("label[for=\"" + id + "\"]");
              if (fieldset) {
                fieldset.setAttribute('id', idNew);
              }
              if (elLbl) {
                elLbl.setAttribute('for', idNew);
                elLbl.setAttribute('id', idNew + "-lbl");
              }
            }
            forOldAttr += countMulti;
            idNew += countMulti;
          } else if ($el.type === 'radio') {
            // <input type="radio"> fix
            // Recount id
            countMulti = ids[id] ? ids[id].length : 0;
            if (!countMulti) {
              // Set the id for fieldset and group label
              var _fieldset = $el.closest('fieldset.radio');
              var _elLbl = row.querySelector("label[for=\"" + id + "\"]");
              if (_fieldset) {
                _fieldset.setAttribute('id', idNew);
              }
              if (_elLbl) {
                _elLbl.setAttribute('for', idNew);
                _elLbl.setAttribute('id', idNew + "-lbl");
              }
            }
            forOldAttr += countMulti;
            idNew += countMulti;
          }

          // Cache already used id
          if (ids[id]) {
            ids[id].push(true);
          } else {
            ids[id] = [true];
          }

          // Replace the name to new one
          $el.name = nameNew;
          if ($el.id) {
            $el.id = idNew;
          }
          if (aria) {
            $el.setAttribute('aria-describedby', nameNew + "-desc");
          }

          // Check if there is a label for this input
          var lbl = row.querySelector("label[for=\"" + forOldAttr + "\"]");
          if (lbl) {
            lbl.setAttribute('for', idNew);
            lbl.setAttribute('id', idNew + "-lbl");
          }
        });
      }

      /**
       * Use of HTML Drag and Drop API
       * https://developer.mozilla.org/en-US/docs/Web/API/HTML_Drag_and_Drop_API
       * https://www.sitepoint.com/accessible-drag-drop/
       */;
      _proto.setUpDragSort = function setUpDragSort() {
        var that = this; // Self reference
        var item = null; // Storing the selected item
        var touched = false; // We have a touch events

        // Find all existing rows and add draggable attributes
        var rows = Array.from(this.getRows());
        rows.forEach(function (row) {
          row.setAttribute('draggable', 'false');
          row.setAttribute('aria-grabbed', 'false');
          row.setAttribute('tabindex', '0');
        });

        // Helper method to test whether Handler was clicked
        function getMoveHandler(element) {
          return !element.form // This need to test whether the element is :input
          && element.matches(that.buttonMove) ? element : element.closest(that.buttonMove);
        }

        // Helper method to move row to selected position
        function switchRowPositions(src, dest) {
          var isRowBefore = false;
          if (src.parentNode === dest.parentNode) {
            for (var cur = src; cur; cur = cur.previousSibling) {
              if (cur === dest) {
                isRowBefore = true;
                break;
              }
            }
          }
          if (isRowBefore) {
            dest.parentNode.insertBefore(src, dest);
          } else {
            dest.parentNode.insertBefore(src, dest.nextSibling);
          }
        }

        /**
         *  Touch interaction:
         *
         *  - a touch of "move button" marks a row draggable / "selected",
         *     or deselect previous selected
         *
         *  - a touch of "move button" in the destination row will move
         *     a selected row to a new position
         */
        this.addEventListener('touchstart', function (event) {
          touched = true;

          // Check for .move button
          var handler = getMoveHandler(event.target);
          var row = handler ? handler.closest(that.repeatableElement) : null;
          if (!row || row.closest('joomla-field-subform') !== that) {
            return;
          }

          // First selection
          if (!item) {
            row.setAttribute('draggable', 'true');
            row.setAttribute('aria-grabbed', 'true');
            item = row;
          } else {
            // Second selection
            // Move to selected position
            if (row !== item) {
              switchRowPositions(item, row);
            }
            item.setAttribute('draggable', 'false');
            item.setAttribute('aria-grabbed', 'false');
            item = null;
          }
          event.preventDefault();
        });

        // Mouse interaction
        // - mouse down, enable "draggable" and allow to drag the row,
        // - mouse up, disable "draggable"
        this.addEventListener('mousedown', function (_ref) {
          var target = _ref.target;
          if (touched) return;

          // Check for .move button
          var handler = getMoveHandler(target);
          var row = handler ? handler.closest(that.repeatableElement) : null;
          if (!row || row.closest('joomla-field-subform') !== that) {
            return;
          }
          row.setAttribute('draggable', 'true');
          row.setAttribute('aria-grabbed', 'true');
          item = row;
        });
        this.addEventListener('mouseup', function () {
          if (item && !touched) {
            item.setAttribute('draggable', 'false');
            item.setAttribute('aria-grabbed', 'false');
            item = null;
          }
        });

        // Keyboard interaction
        // - "tab" to navigate to needed row,
        // - modifier (ctr,alt,shift) + "space" select the row,
        // - "tab" to select destination,
        // - "enter" to place selected row in to destination
        // - "esc" to cancel selection
        this.addEventListener('keydown', function (event) {
          if (event.keyCode !== KEYCODE.ESC && event.keyCode !== KEYCODE.SPACE && event.keyCode !== KEYCODE.ENTER || event.target.form || !event.target.matches(that.repeatableElement)) {
            return;
          }
          var row = event.target;

          // Make sure we handle correct children
          if (!row || row.closest('joomla-field-subform') !== that) {
            return;
          }

          // Space is the selection or unselection keystroke
          if (event.keyCode === KEYCODE.SPACE && hasModifier(event)) {
            // Unselect previously selected
            if (row.getAttribute('aria-grabbed') === 'true') {
              row.setAttribute('draggable', 'false');
              row.setAttribute('aria-grabbed', 'false');
              item = null;
            } else {
              // Select new
              // If there was previously selected
              if (item) {
                item.setAttribute('draggable', 'false');
                item.setAttribute('aria-grabbed', 'false');
                item = null;
              }

              // Mark new selection
              row.setAttribute('draggable', 'true');
              row.setAttribute('aria-grabbed', 'true');
              item = row;
            }

            // Prevent default to suppress any native actions
            event.preventDefault();
          }

          // Escape is the cancel keystroke (for any target element)
          if (event.keyCode === KEYCODE.ESC && item) {
            item.setAttribute('draggable', 'false');
            item.setAttribute('aria-grabbed', 'false');
            item = null;
          }

          // Enter, to place selected item in selected position
          if (event.keyCode === KEYCODE.ENTER && item) {
            item.setAttribute('draggable', 'false');
            item.setAttribute('aria-grabbed', 'false');

            // Do nothing here
            if (row === item) {
              item = null;
              return;
            }

            // Move the item to selected position
            switchRowPositions(item, row);
            event.preventDefault();
            item = null;
          }
        });

        // dragstart event to initiate mouse dragging
        this.addEventListener('dragstart', function (_ref2) {
          var dataTransfer = _ref2.dataTransfer;
          if (item) {
            // We going to move the row
            dataTransfer.effectAllowed = 'move';

            // This need to work in Firefox and IE10+
            dataTransfer.setData('text', '');
          }
        });
        this.addEventListener('dragover', function (event) {
          if (item) {
            event.preventDefault();
          }
        });

        // Handle drag action, move element to hovered position
        this.addEventListener('dragenter', function (_ref3) {
          var target = _ref3.target;
          // Make sure the target in the correct container
          if (!item || target.parentElement.closest('joomla-field-subform') !== that) {
            return;
          }

          // Find a hovered row
          var row = target.closest(that.repeatableElement);

          // One more check for correct parent
          if (!row || row.closest('joomla-field-subform') !== that) return;
          switchRowPositions(item, row);
        });

        // dragend event to clean-up after drop or cancelation
        // which fires whether or not the drop target was valid
        this.addEventListener('dragend', function () {
          if (item) {
            item.setAttribute('draggable', 'false');
            item.setAttribute('aria-grabbed', 'false');
            item = null;
          }
        });
      };
      _createClass(JoomlaFieldSubform, [{
        key: "buttonAdd",
        get:
        // Attribute getters
        function get() {
          return this.getAttribute('button-add');
        }
      }, {
        key: "buttonRemove",
        get: function get() {
          return this.getAttribute('button-remove');
        }
      }, {
        key: "buttonMove",
        get: function get() {
          return this.getAttribute('button-move');
        }
      }, {
        key: "rowsContainer",
        get: function get() {
          return this.getAttribute('rows-container');
        }
      }, {
        key: "repeatableElement",
        get: function get() {
          return this.getAttribute('repeatable-element');
        }
      }, {
        key: "minimum",
        get: function get() {
          return this.getAttribute('minimum');
        }
      }, {
        key: "maximum",
        get: function get() {
          return this.getAttribute('maximum');
        }
      }, {
        key: "name",
        get: function get() {
          return this.getAttribute('name');
        },
        set: function set(value) {
          // Update the template
          this.template = this.template.replace(new RegExp(" name=\"" + this.name.replace(/[[\]]/g, '\\$&'), 'g'), " name=\"" + value);
          this.setAttribute('name', value);
        }
      }]);
      return JoomlaFieldSubform;
    }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
    customElements.define('joomla-field-subform', JoomlaFieldSubform);
  })(customElements);

})();
