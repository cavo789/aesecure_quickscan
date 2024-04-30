(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (Joomla) {
    if (Joomla.getOptions('menus-default')) {
      // eslint-disable-next-line prefer-destructuring
      var items = Joomla.getOptions('menus-default').items;
      items.forEach(function (item) {
        window["jSelectPosition_" + item] = function (name) {
          document.getElementById(item).value = name;
          Joomla.Modal.getCurrent().close();
        };
      });
    }
    Array.from(document.querySelectorAll('.modal')).forEach(function (modalEl) {
      modalEl.addEventListener('hidden.bs.modal', function () {
        setTimeout(function () {
          window.parent.location.reload();
        }, 1000);
      });
    });
  })(Joomla);
  (function (originalFn) {
    Joomla.submitform = function (task, form) {
      originalFn(task, form);
      if (task === 'menu.exportXml') {
        document.adminForm.task.value = '';
      }
    };
  })(Joomla.submitform);

})();
