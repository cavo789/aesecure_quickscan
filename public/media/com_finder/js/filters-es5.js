(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (Joomla) {
    if (!Joomla) {
      window.Joomla = {};
    }
    var onBoot = function onBoot() {
      Joomla.submitbutton = function (pressbutton) {
        if (pressbutton === 'filters.delete' && !window.confirm(Joomla.Text._('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT'))) {
          return false;
        }
        Joomla.submitform(pressbutton);
        return true;
      };

      // Cleanup
      document.addEventListener('DOMContentLoaded', onBoot);
    };
    document.addEventListener('DOMContentLoaded', onBoot);
  })(Joomla);

})();
