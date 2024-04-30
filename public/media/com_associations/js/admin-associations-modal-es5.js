(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  Joomla = window.Joomla || {};
  (function (Joomla, document) {
    document.addEventListener('DOMContentLoaded', function () {
      var targetAssociation = window.parent.document.getElementById('target-association');
      var links = [].slice.call(document.querySelectorAll('.select-link'));
      links.forEach(function (item) {
        item.addEventListener('click', function (_ref) {
          var target = _ref.target;
          targetAssociation.src = targetAssociation.getAttribute('data-editurl') + "&task=" + targetAssociation.getAttribute('data-item') + ".edit&id=" + parseInt(target.getAttribute('data-id'), 10);
          window.parent.Joomla.Modal.getCurrent().close();
        });
      });
    });
  })(Joomla, document);

})();
