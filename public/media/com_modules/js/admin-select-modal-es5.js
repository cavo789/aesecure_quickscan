(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function (document) {
    document.addEventListener('DOMContentLoaded', function () {
      var elems = document.querySelectorAll('#new-modules-list a.select-link');
      elems.forEach(function (elem) {
        elem.addEventListener('click', function (_ref) {
          var currentTarget = _ref.currentTarget,
            target = _ref.target;
          var targetElem = currentTarget;

          // There is some bug with events in iframe where currentTarget is "null"
          // => prevent this here by bubble up
          if (!targetElem) {
            targetElem = target;
            if (targetElem && !targetElem.classList.contains('select-link')) {
              targetElem = targetElem.parentNode;
            }
          }
          var functionName = targetElem.getAttribute('data-function');
          if (functionName && typeof window.parent[functionName] === 'function') {
            window.parent[functionName](targetElem);
          }
        });
      });
    });
  })(document);

})();
