(function () {
  'use strict';

  Joomla = window.Joomla || {};
  (function (Joomla) {
    /**
     * Method that resets the filter inputs and submits the relative form
     *
     * @param {HTMLElement}  element  The element that initiates the call
     * @returns {void}
     * @since   4.0.0
     */
    Joomla.resetFilters = function (element) {
      var form = element.form;
      if (!form) {
        throw new Error('Element must be inside a form!');
      }
      var elementsArray = [].slice.call(form.elements);
      if (elementsArray.length) {
        var newElementsArray = [];
        elementsArray.forEach(function (elem) {
          // Skip the token, the task, the boxchecked and the calling element
          if (elem.getAttribute('name') === 'task' || elem.getAttribute('name') === 'boxchecked' || elem.value === '1' && /^[0-9A-F]{32}$/i.test(elem.name) || elem === element) {
            return;
          }
          newElementsArray.push(elem);
        });

        // Reset all filters
        newElementsArray.forEach(function (elem) {
          elem.value = '';
        });
        form.submit();
      }
    };
    var Searchtools = /*#__PURE__*/function () {
      function Searchtools(elem, options) {
        var _this = this;
        var defaults = {
          // Form options
          formSelector: '.js-stools-form',
          // Search
          searchFieldSelector: '.js-stools-field-search',
          clearBtnSelector: '.js-stools-btn-clear',
          // Global container
          mainContainerSelector: '.js-stools',
          // Filter fields
          searchBtnSelector: '.js-stools-btn-search',
          filterBtnSelector: '.js-stools-btn-filter',
          filterContainerSelector: '.js-stools-container-filters',
          filtersHidden: true,
          // List fields
          listBtnSelector: '.js-stools-btn-list',
          listContainerSelector: '.js-stools-container-list',
          listHidden: true,
          // Ordering specific
          orderColumnSelector: '.js-stools-column-order',
          orderBtnSelector: '.js-stools-btn-order',
          orderFieldSelector: '.js-stools-field-order',
          orderFieldName: 'list[fullordering]',
          limitFieldSelector: '.js-stools-field-limit',
          defaultLimit: 20,
          activeOrder: null,
          activeDirection: 'ASC',
          // Extra
          clearListOptions: false,
          listSelectAutoSubmit: 'js-select-submit-on-change',
          listSelectAutoReset: 'js-select-reset-on-change'
        };
        this.element = elem;
        this.options = Joomla.extend(defaults, options);

        // Initialise selectors
        this.theForm = document.querySelector(this.options.formSelector);

        // Filters
        this.filterButton = document.querySelector(this.options.formSelector + " " + this.options.filterBtnSelector);
        this.filterContainer = document.querySelector(this.options.formSelector + " " + this.options.filterContainerSelector) ? document.querySelector(this.options.formSelector + " " + this.options.filterContainerSelector) : '';
        this.filtersHidden = this.options.filtersHidden;

        // List fields
        this.listButton = document.querySelector(this.options.listBtnSelector);
        this.listContainer = document.querySelector(this.options.formSelector + " " + this.options.listContainerSelector);
        this.listHidden = this.options.listHidden;

        // Main container
        this.mainContainer = document.querySelector(this.options.mainContainerSelector);

        // Search
        this.searchButton = document.querySelector(this.options.formSelector + " " + this.options.searchBtnSelector);
        this.searchField = document.querySelector(this.options.formSelector + " " + this.options.searchFieldSelector);
        this.searchString = null;
        this.clearButton = document.querySelector(this.options.clearBtnSelector);

        // Ordering
        this.orderCols = Array.prototype.slice.call(document.querySelectorAll(this.options.formSelector + " " + this.options.orderColumnSelector));
        this.orderField = document.querySelector(this.options.formSelector + " " + this.options.orderFieldSelector);

        // Limit
        this.limitField = document.querySelector(this.options.formSelector + " " + this.options.limitFieldSelector);

        // Init trackers
        this.activeColumn = null;
        this.activeDirection = this.options.activeDirection;
        this.activeOrder = this.options.activeOrder;
        this.activeLimit = null;

        // Extra options
        this.clearListOptions = this.options.clearListOptions;
        var self = this;

        // Get values
        this.searchString = this.searchField ? this.searchField.value : '';

        // Do some binding
        this.showFilters = this.showFilters.bind(this);
        this.hideFilters = this.hideFilters.bind(this);
        this.showList = this.showList.bind(this);
        this.hideList = this.hideList.bind(this);
        this.toggleFilters = this.toggleFilters.bind(this);
        this.toggleList = this.toggleList.bind(this);
        this.checkFilter = this.checkFilter.bind(this);
        this.clear = this.clear.bind(this);
        this.createOrderField = this.createOrderField.bind(this);
        this.checkActiveStatus = this.checkActiveStatus.bind(this);
        this.activeFilter = this.activeFilter.bind(this);
        this.deactiveFilter = this.deactiveFilter.bind(this);
        this.getFilterFields = this.getFilterFields.bind(this);
        this.getListFields = this.getListFields.bind(this);
        this.hideContainer = this.hideContainer.bind(this);
        this.showContainer = this.showContainer.bind(this);
        this.toggleContainer = this.toggleContainer.bind(this);
        this.toggleDirection = this.toggleDirection.bind(this);
        this.updateFieldValue = this.updateFieldValue.bind(this);
        this.findOption = this.findOption.bind(this);
        if (this.filterContainer && this.filterContainer.classList.contains('js-stools-container-filters-visible')) {
          this.showFilters();
          this.showList();
        } else {
          this.hideFilters();
          this.hideList();
        }
        if (this.filterButton) {
          this.filterButton.addEventListener('click', function (e) {
            self.toggleFilters();
            e.stopPropagation();
            e.preventDefault();
          });
        }
        if (this.listButton) {
          this.listButton.addEventListener('click', function (e) {
            self.toggleList();
            e.stopPropagation();
            e.preventDefault();
          });
        }

        // Do we need to add to mark filter as enabled?
        this.getFilterFields().forEach(function (i) {
          var needsFormSubmit = !i.classList.contains(_this.options.listSelectAutoSubmit) && i.closest("joomla-field-fancy-select." + _this.options.listSelectAutoSubmit);
          var needsFormReset = !i.classList.contains(_this.options.listSelectAutoReset) && i.closest("joomla-field-fancy-select." + _this.options.listSelectAutoReset);
          self.checkFilter(i);
          i.addEventListener('change', function () {
            self.checkFilter(i);
            if (i.classList.contains(_this.options.listSelectAutoSubmit) || needsFormSubmit) {
              i.form.submit();
            }
            if (i.classList.contains(_this.options.listSelectAutoReset) || needsFormReset) {
              _this.clear(i);
            }
          });
        });
        if (this.clearButton) {
          this.clearButton.addEventListener('click', self.clear);
        }

        // Check/create ordering field
        this.createOrderField();
        this.orderCols.forEach(function (item) {
          item.addEventListener('click', function (_ref) {
            var target = _ref.target;
            var element = target.tagName.toLowerCase() === 'span' ? target.parentNode : target;

            // Order to set
            var newOrderCol = element.getAttribute('data-order');
            var newDirection = element.getAttribute('data-direction');
            var newOrdering = newOrderCol + " " + newDirection;

            // The data-order attribute is required
            if (newOrderCol.length) {
              self.activeColumn = newOrderCol;
              if (newOrdering !== self.activeOrder) {
                self.activeDirection = newDirection;
                self.activeOrder = newOrdering;

                // Update the order field
                self.updateFieldValue(self.orderField, newOrdering);
              } else {
                self.toggleDirection();
              }
              self.theForm.submit();
            }
          });
        });
        this.checkActiveStatus(this);
      }
      var _proto = Searchtools.prototype;
      _proto.checkFilter = function checkFilter(element) {
        if (element.tagName.toLowerCase() === 'select') {
          var option = element.querySelector('option:checked');
          if (option) {
            if (option.value !== '') {
              this.activeFilter(element, this);
            } else {
              this.deactiveFilter(element, this);
            }
          }
        } else if (element.value !== '') {
          this.activeFilter(element, this);
        } else {
          this.deactiveFilter(element, this);
        }
      };
      _proto.clear = function clear(exceptElement) {
        var _this2 = this;
        if (exceptElement === void 0) {
          exceptElement = null;
        }
        var self = this;
        if (self.searchField) {
          self.searchField.value = '';
        }
        self.getFilterFields().forEach(function (i) {
          if (exceptElement && i === exceptElement || !i.closest(_this2.options.filterContainerSelector)) {
            return;
          }
          i.value = '';
          self.checkFilter(i);
          if (window.jQuery && window.jQuery.chosen) {
            window.jQuery(i).trigger('chosen:updated');
          }
        });
        if (self.clearListOptions) {
          self.getListFields().forEach(function (i) {
            i.value = '';
            self.checkFilter(i);
            if (window.jQuery && window.jQuery.chosen) {
              window.jQuery(i).trigger('chosen:updated');
            }
          });

          // Special case to limit box to the default config limit
          document.querySelector('#list_limit').value = self.options.defaultLimit;
          if (window.jQuery && window.jQuery.chosen) {
            window.jQuery('#list_limit').trigger('chosen:updated');
          }
        }
        self.theForm.submit();
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.updateFilterCount = function updateFilterCount(count) {
        if (this.clearButton) {
          this.clearButton.disabled = count === 0 && !this.searchString.length;
        }
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.checkActiveStatus = function checkActiveStatus(cont) {
        var _this3 = this;
        var activeFilterCount = 0;
        this.getFilterFields().forEach(function (item) {
          if (!item.closest(_this3.options.filterContainerSelector)) {
            return;
          }
          if (item.classList.contains('active')) {
            activeFilterCount += 1;
            if (cont.filterButton) {
              cont.filterButton.classList.remove('btn-secondary');
              cont.filterButton.classList.add('btn-primary');
            }
          }
        });

        // If there are no active filters - remove the filtered caption area from the table
        if (activeFilterCount === 0) {
          var filteredByCaption = document.getElementById('filteredBy');
          if (filteredByCaption) {
            filteredByCaption.parentNode.removeChild(filteredByCaption);
          }
        }

        // Disable clear button when no filter is active and search is empty
        if (this.clearButton) {
          this.clearButton.disabled = activeFilterCount === 0 && !this.searchString.length;
        }
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.activeFilter = function activeFilter(element) {
        element.classList.add('active');
        var chosenId = "#" + element.getAttribute('id');
        var tmpEl = element.querySelector(chosenId);
        if (tmpEl) {
          tmpEl.classList.add('active');
        }

        // Add all active filters to the table caption for screen-readers
        var filteredByCaption = document.getElementById('filteredBy');
        var isHidden = Object.prototype.hasOwnProperty.call(element.attributes, 'type') && element.attributes.type.value === 'hidden';

        // The caption won't exist if no items match the filters so check for the element first
        if (filteredByCaption && !isHidden) {
          var captionContent = '';
          if (element.tagName.toLowerCase() === 'select') {
            if (element.multiple === true) {
              var selectedOptions = element.querySelectorAll('option:checked');
              var selectedTextValues = [].slice.call(selectedOptions).map(function (el) {
                return el.text;
              });
              captionContent = element.labels[0].textContent + " - " + selectedTextValues.join();
            } else {
              captionContent = element.labels[0].textContent + " - " + element.options[element.selectedIndex].text;
            }
          } else {
            captionContent = element.labels[0].textContent + " - " + element.value;
          }
          filteredByCaption.textContent += captionContent;
        }
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.deactiveFilter = function deactiveFilter(element) {
        element.classList.remove('active');
        var chosenId = "#" + element.getAttribute('id');
        var tmpEl = element.querySelector(chosenId);
        if (tmpEl) {
          tmpEl.classList.remove('active');
        }
      }

      // eslint-disable-next-line consistent-return
      ;
      _proto.getFilterFields = function getFilterFields() {
        if (this.mainContainer) {
          return Array.prototype.slice.call(this.mainContainer.querySelectorAll('select,input'));
        }
        if (this.filterContainer) {
          return Array.prototype.slice.call(this.filterContainer.querySelectorAll('select,input'));
        }
        return [];
      };
      _proto.getListFields = function getListFields() {
        return Array.prototype.slice.call(this.listContainer.querySelectorAll('select'));
      }

      // Common container functions
      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.hideContainer = function hideContainer(container) {
        if (container) {
          container.classList.remove('js-stools-container-filters-visible');
          document.body.classList.remove('filters-shown');
        }
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.showContainer = function showContainer(container) {
        container.classList.add('js-stools-container-filters-visible');
        document.body.classList.add('filters-shown');
      };
      _proto.toggleContainer = function toggleContainer(container) {
        if (container.classList.contains('js-stools-container-filters-visible')) {
          this.hideContainer(container);
        } else {
          this.showContainer(container);
        }
      }

      // List container management
      ;
      _proto.hideList = function hideList() {
        this.hideContainer(this.filterContainer);
      };
      _proto.showList = function showList() {
        this.showContainer(this.filterContainer);
      };
      _proto.toggleList = function toggleList() {
        this.toggleContainer(this.filterContainer);
      }

      // Filters container management
      ;
      _proto.hideFilters = function hideFilters() {
        this.hideContainer(this.filterContainer);
      };
      _proto.showFilters = function showFilters() {
        this.showContainer(this.filterContainer);
      };
      _proto.toggleFilters = function toggleFilters() {
        this.toggleContainer(this.filterContainer);
      };
      _proto.toggleDirection = function toggleDirection() {
        var self = this;
        var newDirection = 'ASC';
        if (self.activeDirection.toUpperCase() === 'ASC') {
          newDirection = 'DESC';
        }
        self.activeDirection = newDirection;
        self.activeOrder = self.activeColumn + " " + newDirection;
        self.updateFieldValue(self.orderField, self.activeOrder);
      };
      _proto.createOrderField = function createOrderField() {
        var _this4 = this;
        var self = this;
        if (!this.orderField) {
          this.orderField = document.createElement('input');
          this.orderField.setAttribute('type', 'hidden');
          this.orderField.setAttribute('id', 'js-stools-field-order');
          this.orderField.setAttribute('class', 'js-stools-field-order');
          this.orderField.setAttribute('name', self.options.orderFieldName);
          this.orderField.setAttribute('value', self.activeOrder + " " + this.activeDirection);
          this.theForm.append(this.orderField);
        }

        // Add missing columns to the order select
        if (this.orderField.tagName.toLowerCase() === 'select') {
          var allOptions = [].slice.call(this.orderField.options);
          allOptions.forEach(function (option) {
            var value = option.getAttribute('data-order');
            var name = option.getAttribute('data-name');
            var direction = option.getAttribute('data-direction');
            if (value && value.length) {
              value = value + " " + direction;
              var $option = self.findOption(self.orderField, value);
              if (!$option.length) {
                $option = document.createElement('option');
                $option.text = name;
                $option.value = value;

                // If it is the active option select it
                if (option.classList.contains('active')) {
                  $option.setAttribute('selected', 'selected');
                }

                // Append the option and repopulate the chosen field
                _this4.orderFieldName.innerHTML += Joomla.sanitizeHtml($option);
              }
            }
          });
          if (window.jQuery && window.jQuery.chosen) {
            window.jQuery(this.orderField).trigger('chosen:updated');
          }
        }
        this.activeOrder = this.orderField.value;
      }

      // eslint-disable-next-line class-methods-use-this
      ;
      _proto.updateFieldValue = function updateFieldValue(field, newValue) {
        var type = field.getAttribute('type');
        if (type === 'hidden' || type === 'text') {
          field.setAttribute('value', newValue);
        } else if (field.tagName.toLowerCase() === 'select') {
          var allOptions = [].slice.call(field.options);
          var desiredOption;

          // Select the option result
          allOptions.forEach(function (option) {
            if (option.value === newValue) {
              desiredOption = option;
            }
          });
          if (desiredOption && desiredOption.length) {
            desiredOption.setAttribute('selected', 'selected');
          } else {
            // If the option does not exist create it on the fly
            var option = document.createElement('option');
            option.text = newValue;
            option.value = newValue;
            option.setAttribute('selected', 'selected');

            // Append the option and repopulate the chosen field
            field.appendChild(option);
          }
          field.value = newValue;
          // Trigger the chosen update
          if (window.jQuery && window.jQuery.chosen) {
            field.trigger('chosen:updated');
          }
        }
      }

      // eslint-disable-next-line class-methods-use-this,consistent-return
      ;
      _proto.findOption = function findOption(select, value) {
        // eslint-disable-next-line no-plusplus
        for (var i = 0, l = select.length; l > i; i++) {
          if (select[i].value === value) {
            return select[i];
          }
        }
      };
      return Searchtools;
    }();
    var onBoot = function onBoot() {
      if (Joomla.getOptions('searchtools')) {
        var options = Joomla.getOptions('searchtools');
        var element = document.querySelector(options.selector);

        // eslint-disable-next-line no-new
        new Searchtools(element, options);
      }
      var sort = document.getElementById('sorted');
      var order = document.getElementById('orderedBy');
      if (sort && sort.hasAttribute('data-caption') && order) {
        var orderedBy = sort.getAttribute('data-caption');
        order.textContent += orderedBy;
      }
      if (sort && sort.hasAttribute('data-sort')) {
        var ariasort = sort.getAttribute('data-sort');
        sort.parentNode.setAttribute('aria-sort', ariasort);
      }

      // Cleanup
      document.removeEventListener('DOMContentLoaded', onBoot);
    };

    // Execute on DOM Loaded Event
    document.addEventListener('DOMContentLoaded', onBoot);
  })(Joomla);

})();
