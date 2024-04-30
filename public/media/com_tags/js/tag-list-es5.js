(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (document) {
    var onClick = function onClick() {
      var form = document.getElementById('adminForm');
      document.getElementById('filter-search').value = '';
      form.submit();
    };
    var onBoot = function onBoot() {
      var form = document.getElementById('adminForm');
      var element = form.querySelector('button[type="reset"]');
      if (element) {
        element.addEventListener('click', onClick);
      }
      document.removeEventListener('DOMContentLoaded', onBoot);
    };
    document.addEventListener('DOMContentLoaded', onBoot);
  })(document);

})();
