(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  Joomla = window.Joomla || {};
  (function (document, Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
      [].slice.call(document.querySelectorAll('.cache-entry')).forEach(function (el) {
        el.addEventListener('click', function (_ref) {
          var currentTarget = _ref.currentTarget;
          Joomla.isChecked(currentTarget.checked);
        });
      });
    });
  })(document, Joomla);

})();
