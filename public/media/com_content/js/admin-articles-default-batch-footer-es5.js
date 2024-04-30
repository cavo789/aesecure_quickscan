(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (document, submitForm) {
    // Selectors used by this script
    var buttonDataSelector = 'data-submit-task';
    var formId = 'adminForm';

    /**
     * Submit the task
     * @param task
     */
    var submitTask = function submitTask(task) {
      var form = document.getElementById(formId);
      if (form && task === 'article.batch') {
        submitForm(task, form);
      }
    };

    // Register events
    document.addEventListener('DOMContentLoaded', function () {
      var button = document.getElementById('batch-submit-button-id');
      if (button) {
        button.addEventListener('click', function (e) {
          var task = e.target.getAttribute(buttonDataSelector);
          submitTask(task);
          return false;
        });
      }
    });
  })(document, Joomla.submitform);

})();
