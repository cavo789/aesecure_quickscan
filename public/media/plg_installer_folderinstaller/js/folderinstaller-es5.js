(function () {
  'use strict';

  /**
   * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  Joomla = window.Joomla || {};
  (function (Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
      Joomla.submitbuttonfolder = function () {
        var form = document.getElementById('adminForm');

        // do field validation
        if (form.install_directory.value === '') {
          Joomla.renderMessages({
            warning: [Joomla.Text._('PLG_INSTALLER_FOLDERINSTALLER_NO_INSTALL_PATH')]
          });
        } else {
          var loading = document.getElementById('loading');
          if (loading) {
            loading.classList.remove('hidden');
          }
          form.installtype.value = 'folder';
          form.submit();
        }
      };
    });
  })(Joomla);

})();
