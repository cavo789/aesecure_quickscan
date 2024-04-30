(function () {
  'use strict';

  /**
    * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
    * @license    GNU General Public License version 2 or later; see LICENSE.txt
    */

  document.addEventListener('DOMContentLoaded', function () {
    // Get the elements
    var modulesLinks = [].slice.call(document.querySelectorAll('.js-module-insert'));
    var positionsLinks = [].slice.call(document.querySelectorAll('.js-position-insert'));

    // Assign listener for click event (for single module id insertion)
    modulesLinks.forEach(function (element) {
      element.addEventListener('click', function (event) {
        event.preventDefault();
        var modid = event.target.getAttribute('data-module');
        var editor = event.target.getAttribute('data-editor');

        // Use the API
        if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && Object.prototype.hasOwnProperty.call(window.parent.Joomla.editors.instances, editor)) {
          window.parent.Joomla.editors.instances[editor].replaceSelection("{loadmoduleid " + modid + "}");
        }
        if (window.parent.Joomla.Modal) {
          window.parent.Joomla.Modal.getCurrent().close();
        }
      });
    });

    // Assign listener for click event (for position insertion)
    positionsLinks.forEach(function (element) {
      element.addEventListener('click', function (event) {
        event.preventDefault();
        var position = event.target.getAttribute('data-position');
        var editor = event.target.getAttribute('data-editor');

        // Use the API
        if (window.Joomla && window.Joomla.editors && Joomla.editors.instances && Object.prototype.hasOwnProperty.call(window.parent.Joomla.editors.instances, editor)) {
          window.parent.Joomla.editors.instances[editor].replaceSelection("{loadposition " + position + "}");
        }
        if (window.parent.Joomla.Modal) {
          window.parent.Joomla.Modal.getCurrent().close();
        }
      });
    });
  });

})();
