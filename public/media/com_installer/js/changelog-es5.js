(function () {
  'use strict';

  /**
   * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  Joomla = window.Joomla || {};
  (function (Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
      var modals = document.getElementsByClassName('changelogModal');
      Array.from(modals).forEach(function (element) {
        element.addEventListener('click', function (modal) {
          Joomla.loadChangelog(modal.target.dataset.jsExtensionid, modal.target.dataset.jsView);
        });
      });
    });

    /**
     * Load the changelog data
     *
     * @param extensionId The extension ID to load the changelog for
     * @param view The view the changelog is for,
     *             this is used to determine which version number to show
     *
     * @since   4.0.0
     */
    Joomla.loadChangelog = function (extensionId, view) {
      var modal = document.querySelector("#changelogModal" + extensionId + " .modal-body");
      Joomla.request({
        url: "index.php?option=com_installer&task=manage.loadChangelog&eid=" + extensionId + "&source=" + view + "&format=json",
        onSuccess: function onSuccess(response) {
          var message = '';
          try {
            var result = JSON.parse(response);
            if (result.error) {
              message = result[0];
            } else {
              message = result.data;
            }
          } catch (exception) {
            message = exception;
          }
          modal.innerHTML = Joomla.sanitizeHtml(message);
        },
        onError: function onError(xhr) {
          modal.innerHTML = Joomla.sanitizeHtml(xhr.statusText);
        }
      });
    };
  })(Joomla);

})();
