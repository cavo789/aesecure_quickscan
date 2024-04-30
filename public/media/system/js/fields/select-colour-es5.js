(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function () {
    var onChange = function onChange(_ref) {
      var target = _ref.target;
      var self = target;
      var value = parseInt(self.value, 10);
      self.classList.remove('form-select-success', 'form-select-danger');
      if (value === 1) {
        self.classList.add('form-select-success');
      } else if (value === 0 || value === -2) {
        self.classList.add('form-select-danger');
      }
    };
    var updateSelectboxColour = function updateSelectboxColour() {
      var colourSelects = [].slice.call(document.querySelectorAll('.form-select-color-state'));
      colourSelects.forEach(function (colourSelect) {
        var value = parseInt(colourSelect.value, 10);

        // Add class on page load
        if (value === 1) {
          colourSelect.classList.add('form-select-success');
        } else if (value === 0 || value === -2) {
          colourSelect.classList.add('form-select-danger');
        }

        // Add class when value is changed
        colourSelect.addEventListener('change', onChange);
      });

      // Cleanup
      document.removeEventListener('DOMContentLoaded', updateSelectboxColour, true);
    };

    // On document loaded
    document.addEventListener('DOMContentLoaded', updateSelectboxColour, true);

    // On Joomla updated
    document.addEventListener('joomla:updated', updateSelectboxColour, true);
  })();

})();
