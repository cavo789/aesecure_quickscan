(function () {
  'use strict';

  /**
   * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  var recursiveApplyChanges = function recursiveApplyChanges(id) {
    var childs = [].slice.call(document.querySelectorAll("#filter-config select[data-parent=\"" + id + "\"]"));
    childs.map(function (child) {
      recursiveApplyChanges(child.dataset.id);
      child.value = 'NONE';
      return child;
    });
  };
  var applyChanges = function applyChanges(event) {
    var currentElement = event.currentTarget;
    var currentFilter = currentElement.options[currentElement.selectedIndex].value;
    if (currentFilter === 'NONE') {
      var childs = [].slice.call(document.querySelectorAll("#filter-config select[data-parent=\"" + currentElement.dataset.id + "\"]"));
      if (childs.length && window.confirm(Joomla.Text._('COM_CONFIG_TEXT_FILTERS_NOTE'))) {
        childs.map(function (child) {
          recursiveApplyChanges(child.dataset.id);
          child.value = 'NONE';
          return child;
        });
      }
    }
  };
  [].slice.call(document.querySelectorAll('#filter-config select')).map(function (select) {
    return select.addEventListener('change', applyChanges);
  });

})();
