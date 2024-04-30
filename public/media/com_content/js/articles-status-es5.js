(function () {
  'use strict';

  /**
   * @copyright  (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    document.addEventListener('DOMContentLoaded', function () {
      var elements = [].slice.call(document.querySelectorAll('.article-status'));
      elements.forEach(function (element) {
        element.addEventListener('click', function (event) {
          event.stopPropagation();
        });
      });
    });
  })();

})();
