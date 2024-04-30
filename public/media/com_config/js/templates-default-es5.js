(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (document, submitForm) {
    // Selectors used by this script
    var buttonDataSelector = 'data-submit-task';

    /**
     * Submit the task
     * @param task
     * @param form
     */
    var submitTask = function submitTask(task, form) {
      if (task === 'templates.cancel' || document.formvalidator.isValid(form)) {
        submitForm(task, form);
      }
    };

    /**
     * Register events
     */
    var registerEvents = function registerEvents() {
      var buttons = [].slice.call(document.querySelectorAll("[" + buttonDataSelector + "]"));
      buttons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          var task = e.currentTarget.getAttribute(buttonDataSelector);
          submitTask(task, e.currentTarget.form);
        });
      });
    };
    document.addEventListener('DOMContentLoaded', function () {
      registerEvents();
    });
  })(document, Joomla.submitform);

})();
