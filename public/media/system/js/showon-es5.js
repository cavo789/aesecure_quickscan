(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  /**
   * JField 'showon' class
   */
  var Showon = /*#__PURE__*/function () {
    /**
     * Constructor
     *
     * @param {HTMLElement} cont Container element
     */
    function Showon(cont) {
      var _this = this;
      var self = this;
      this.container = cont || document;
      this.fields = {
        // origin-field-name: {
        //   origin:  ['collection of all the trigger nodes'],
        //   targets: ['collection of nodes to be controlled control']
        // }
      };
      this.showonFields = [].slice.call(this.container.querySelectorAll('[data-showon]'));
      // Populate the fields data
      if (this.showonFields.length) {
        // @todo refactor this, dry
        this.showonFields.forEach(function (field) {
          // Set up only once
          if (field.hasAttribute('data-showon-initialised')) {
            return;
          }
          field.setAttribute('data-showon-initialised', '');
          var jsondata = field.getAttribute('data-showon') || '';
          var showonData = JSON.parse(jsondata);
          var localFields;
          if (showonData.length) {
            localFields = [].slice.call(self.container.querySelectorAll("[name=\"" + showonData[0].field + "\"], [name=\"" + showonData[0].field + "[]\"]"));
            if (!_this.fields[showonData[0].field]) {
              _this.fields[showonData[0].field] = {
                origin: [],
                targets: []
              };
            }

            // Add trigger elements
            localFields.forEach(function (cField) {
              if (_this.fields[showonData[0].field].origin.indexOf(cField) === -1) {
                _this.fields[showonData[0].field].origin.push(cField);
              }
            });

            // Add target elements
            _this.fields[showonData[0].field].targets.push(field);

            // Data showon can have multiple values
            if (showonData.length > 1) {
              showonData.forEach(function (value, index) {
                if (index === 0) {
                  return;
                }
                localFields = [].slice.call(self.container.querySelectorAll("[name=\"" + value.field + "\"], [name=\"" + value.field + "[]\"]"));
                if (!_this.fields[showonData[0].field]) {
                  _this.fields[showonData[0].field] = {
                    origin: [],
                    targets: []
                  };
                }

                // Add trigger elements
                localFields.forEach(function (cField) {
                  if (_this.fields[showonData[0].field].origin.indexOf(cField) === -1) {
                    _this.fields[showonData[0].field].origin.push(cField);
                  }
                });

                // Add target elements
                if (_this.fields[showonData[0].field].targets.indexOf(field) === -1) {
                  _this.fields[showonData[0].field].targets.push(field);
                }
              });
            }
          }
        });

        // Do some binding
        this.linkedOptions = this.linkedOptions.bind(this);

        // Attach events to referenced element, to check condition on change and keyup
        Object.keys(this.fields).forEach(function (key) {
          if (_this.fields[key].origin.length) {
            _this.fields[key].origin.forEach(function (elem) {
              // Initialize the showon behaviour for the given HTMLElement
              self.linkedOptions(key);

              // Setup listeners
              elem.addEventListener('change', function () {
                self.linkedOptions(key);
              });
              elem.addEventListener('keyup', function () {
                self.linkedOptions(key);
              });
              elem.addEventListener('click', function () {
                self.linkedOptions(key);
              });
            });
          }
        });
      }
    }

    /**
     *
     * @param key
     */
    var _proto = Showon.prototype;
    _proto.linkedOptions = function linkedOptions(key) {
      var _this2 = this;
      // Loop through the elements that need to be either shown or hidden
      this.fields[key].targets.forEach(function (field) {
        var elementShowonDatas = JSON.parse(field.getAttribute('data-showon')) || [];
        var showfield = true;
        var itemval;

        // Check if target conditions are satisfied
        elementShowonDatas.forEach(function (elementShowonData, index) {
          var condition = elementShowonData || {};
          condition.valid = 0;

          // Test in each of the elements in the field array if condition is valid
          _this2.fields[key].origin.forEach(function (originField) {
            if (originField.name.replace('[]', '') !== elementShowonData.field) {
              return;
            }
            var originId = originField.id;

            // If checkbox or radio box the value is read from properties
            if (originField.getAttribute('type') && ['checkbox', 'radio'].includes(originField.getAttribute('type').toLowerCase())) {
              if (!originField.checked) {
                // Unchecked fields will return a blank and so always match
                // a != condition so we skip them
                return;
              }
              itemval = document.getElementById(originId).value;
            } else if (originField.nodeName === 'SELECT' && originField.hasAttribute('multiple')) {
              itemval = Array.from(originField.querySelectorAll('option:checked')).map(function (el) {
                return el.value;
              });
            } else {
              // Select lists, text-area etc. Note that multiple-select list returns
              // an Array here s0 we can always treat 'itemval' as an array
              itemval = document.getElementById(originId).value;
              // A multi-select <select> $field  will return null when no elements are
              // selected so we need to define itemval accordingly
              if (itemval === null && originField.tagName.toLowerCase() === 'select') {
                itemval = [];
              }
            }

            // Convert to array to allow multiple values in the field (e.g. type=list multiple)
            // and normalize as string
            if (!(typeof itemval === 'object')) {
              itemval = JSON.parse("[\"" + itemval + "\"]");
            }

            // Test if any of the values of the field exists in showon conditions
            itemval.forEach(function (val) {
              // ":" Equal to one or more of the values condition
              if (condition.sign === '=' && condition.values.indexOf(val) !== -1) {
                condition.valid = 1;
              }
              // "!:" Not equal to one or more of the values condition
              if (condition.sign === '!=' && condition.values.indexOf(val) === -1) {
                condition.valid = 1;
              }
            });
          });

          // Verify conditions
          // First condition (no operator): current condition must be valid
          if (condition.op === '') {
            if (condition.valid === 0) {
              showfield = false;
            }
          } else {
            // Other conditions (if exists)
            // AND operator: both the previous and current conditions must be valid
            if (condition.op === 'AND' && condition.valid + elementShowonDatas[index - 1].valid < 2) {
              showfield = false;
              condition.valid = 0;
            }
            // OR operator: one of the previous and current conditions must be valid
            if (condition.op === 'OR' && condition.valid + elementShowonDatas[index - 1].valid > 0) {
              showfield = true;
              condition.valid = 1;
            }
          }
        });

        // If conditions are satisfied show the target field(s), else hide
        if (field.tagName !== 'option') {
          if (showfield) {
            field.classList.remove('hidden');
            field.dispatchEvent(new CustomEvent('joomla:showon-show', {
              bubbles: true
            }));
          } else {
            field.classList.add('hidden');
            field.dispatchEvent(new CustomEvent('joomla:showon-hide', {
              bubbles: true
            }));
          }
        } else {
          // @todo: If chosen or choices.js is active we should update them
          field.disabled = !showfield;
        }
      });
    };
    return Showon;
  }();
  if (!window.Joomla) {
    throw new Error('Joomla API is not properly initialized');
  }
  // Provide a public API
  if (!Joomla.Showon) {
    Joomla.Showon = {
      initialise: function initialise(container) {
        return new Showon(container);
      }
    };
  }

  /**
   * Initialize 'showon' feature at an initial page load
   */
  Joomla.Showon.initialise(document);

  /**
   * Search for matching parents
   *
   * @param {HTMLElement} $child
   * @param {String} selector
   * @returns {HTMLElement[]}
   */
  var getMatchedParents = function getMatchedParents($child, selector) {
    var $parent = $child;
    var $matchingParent;
    var parents = [];
    while ($parent) {
      $matchingParent = $parent.matches && $parent.matches(selector) ? $parent : null;
      if ($matchingParent) {
        parents.unshift($matchingParent);
      }
      $parent = $parent.parentNode;
    }
    return parents;
  };

  /**
   * Initialize 'showon' feature when part of the page was updated
   */
  document.addEventListener('joomla:updated', function (_ref) {
    var target = _ref.target;
    // Check is it subform, then wee need to fix some "showon" config
    if (target.classList.contains('subform-repeatable-group')) {
      var elements = [].slice.call(target.querySelectorAll('[data-showon]'));
      if (elements.length) {
        var search = [];
        var replace = [];

        // Collect all parent groups of changed group
        getMatchedParents(target, '.subform-repeatable-group').forEach(function ($parent) {
          search.push(new RegExp("\\[" + $parent.dataset.baseName + "X\\]", 'g'));
          replace.push("[" + $parent.dataset.group + "]");
        });

        // Fix showon field names in a current group
        elements.forEach(function (element) {
          var showon = element.dataset.showon;
          search.forEach(function (pattern, i) {
            showon = showon.replace(pattern, replace[i]);
          });
          element.dataset.showon = showon;
        });
      }
    }
    Joomla.Showon.initialise(target);
  });

})();
