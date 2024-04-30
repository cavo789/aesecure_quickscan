(function () {
  'use strict';

  /**
   * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  Joomla = window.Joomla || {};
  (function (Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
      Joomla.submitbuttonurl = function () {
        var form = document.getElementById('adminForm');
        var loading = document.getElementById('loading');
        if (loading) {
          loading.classList.remove('hidden');
        }
        form.installtype.value = 'url';
        form.submit();
      };
    });
  })(Joomla);

})();
