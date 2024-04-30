(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    var onSelect = function onSelect() {
      var batchCategory = document.getElementById('batch-category-id');
      var batchMenu = document.getElementById('batch-menu-id');
      var batchPosition = document.getElementById('batch-position-id');
      var batchGroup = document.getElementById('batch-group-id');
      var batchCopyMove = document.getElementById('batch-copy-move');
      var batchSelector;
      var onChange = function onChange() {
        if (!batchSelector.value || batchSelector.value && parseInt(batchSelector.value, 10) === 0) {
          batchCopyMove.classList.add('hidden');
        } else {
          batchCopyMove.classList.remove('hidden');
        }
      };
      if (batchCategory) {
        batchSelector = batchCategory;
      }
      if (batchMenu) {
        batchSelector = batchMenu;
      }
      if (batchPosition) {
        batchSelector = batchPosition;
      }
      if (batchGroup) {
        batchSelector = batchGroup;
      }
      if (batchCopyMove) {
        batchCopyMove.classList.add('hidden');
      }
      if (batchCopyMove) {
        batchSelector.addEventListener('change', onChange);
      }

      // Cleanup
      document.removeEventListener('DOMContentLoaded', onSelect, true);
    };

    // Document loaded
    document.addEventListener('DOMContentLoaded', onSelect, true);

    // Joomla updated
    document.addEventListener('joomla:updated', onSelect, true);
  })();

})();
