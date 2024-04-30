(function () {
  'use strict';

  /**
    * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
    * @license     GNU General Public License version 2 or later; see LICENSE.txt
    */
  var helpIndex = document.getElementById('help-index');
  if (helpIndex) {
    [].slice.call(helpIndex.querySelectorAll('a')).map(function (element) {
      return element.addEventListener('click', function () {
        window.scroll(0, 0);
      });
    });
  }

})();
