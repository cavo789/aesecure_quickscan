(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  /**
   * JavaScript behavior to allow shift select in administrator grids
   */
  (function (Joomla) {
    var JMultiSelect = /*#__PURE__*/function () {
      function JMultiSelect(formElement) {
        var _this = this;
        this.tableEl = document.querySelector(formElement);
        if (this.tableEl) {
          this.boxes = [].slice.call(this.tableEl.querySelectorAll('td input[type=checkbox]'));
          this.rows = [].slice.call(document.querySelectorAll('tr[class^="row"]'));
          this.checkallToggle = document.querySelector('[name="checkall-toggle"]');
          this.onCheckallToggleClick = this.onCheckallToggleClick.bind(this);
          this.onRowClick = this.onRowClick.bind(this);
          if (this.checkallToggle) {
            this.checkallToggle.addEventListener('click', this.onCheckallToggleClick);
          }
          if (this.rows.length) {
            this.rows.forEach(function (row) {
              row.addEventListener('click', _this.onRowClick);
            });
          }
        }
      }

      // Changes the background-color on every cell inside a <tr>
      // eslint-disable-next-line class-methods-use-this
      var _proto = JMultiSelect.prototype;
      _proto.changeBg = function changeBg(row, isChecked) {
        // Check if it should add or remove the background colour
        if (isChecked) {
          [].slice.call(row.querySelectorAll('td, th')).forEach(function (elementToMark) {
            elementToMark.classList.add('row-selected');
          });
        } else {
          [].slice.call(row.querySelectorAll('td, th')).forEach(function (elementToMark) {
            elementToMark.classList.remove('row-selected');
          });
        }
      };
      _proto.onCheckallToggleClick = function onCheckallToggleClick(_ref) {
        var _this2 = this;
        var target = _ref.target;
        var isChecked = target.checked;
        this.rows.forEach(function (row) {
          _this2.changeBg(row, isChecked);
        });
      };
      _proto.onRowClick = function onRowClick(_ref2) {
        var target = _ref2.target,
          shiftKey = _ref2.shiftKey;
        // Do not interfere with links or buttons
        if (target.tagName && (target.tagName.toLowerCase() === 'a' || target.tagName.toLowerCase() === 'button')) {
          return;
        }
        if (!this.boxes.length) {
          return;
        }
        var closestRow = target.closest('tr');
        var currentRowNum = this.rows.indexOf(closestRow);
        var currentCheckBox = closestRow.querySelector('td input[type=checkbox]');
        if (currentCheckBox) {
          var isChecked = currentCheckBox.checked;
          if (!(target.id === currentCheckBox.id)) {
            // We will prevent selecting text to prevent artifacts
            if (shiftKey) {
              document.body.style['-webkit-user-select'] = 'none';
              document.body.style['-moz-user-select'] = 'none';
              document.body.style['-ms-user-select'] = 'none';
              document.body.style['user-select'] = 'none';
            }
            currentCheckBox.checked = !currentCheckBox.checked;
            isChecked = currentCheckBox.checked;
            Joomla.isChecked(isChecked, this.tableEl.id);
          }
          this.changeBg(this.rows[currentRowNum], isChecked);

          // Restore normality
          if (shiftKey) {
            document.body.style['-webkit-user-select'] = 'none';
            document.body.style['-moz-user-select'] = 'none';
            document.body.style['-ms-user-select'] = 'none';
            document.body.style['user-select'] = 'none';
          }
        }
      };
      return JMultiSelect;
    }();
    var onBoot = function onBoot() {
      var formId = '#adminForm';
      if (Joomla && Joomla.getOptions('js-multiselect', {}).formName) {
        formId = "#" + Joomla.getOptions('js-multiselect', {}).formName;
      }
      // eslint-disable-next-line no-new
      new JMultiSelect(formId);
    };
    document.addEventListener('DOMContentLoaded', onBoot);
  })(Joomla);

})();
