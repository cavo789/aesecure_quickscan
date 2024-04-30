(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  document.addEventListener('DOMContentLoaded', function () {
    var batchSelector = document.getElementById('batch-group-id');
    var batchCopyMove = document.getElementById('batch-copy-move');
    batchCopyMove.classList.add('hidden');
    batchSelector.addEventListener('change', function () {
      if (batchSelector.value === 'nogroup' || batchSelector.value !== '') {
        batchCopyMove.classList.remove('hidden');
      } else {
        batchCopyMove.classList.add('hidden');
      }
    }, false);
  });

})();
