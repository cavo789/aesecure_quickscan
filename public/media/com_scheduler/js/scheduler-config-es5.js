(function () {
  'use strict';

  /**
   * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  if (!window.Joomla) {
    throw new Error('Joomla API was not properly initialised!');
  }
  var copyToClipboardFallback = function copyToClipboardFallback(input) {
    input.focus();
    input.select();
    try {
      var copy = document.execCommand('copy');
      if (copy) {
        Joomla.renderMessages({
          message: [Joomla.Text._('COM_SCHEDULER_CONFIG_WEBCRON_LINK_COPY_SUCCESS')]
        });
      } else {
        Joomla.renderMessages({
          error: [Joomla.Text._('COM_SCHEDULER_CONFIG_WEBCRON_LINK_COPY_FAIL')]
        });
      }
    } catch (err) {
      Joomla.renderMessages({
        error: [err]
      });
    }
  };
  var copyToClipboard = function copyToClipboard() {
    var button = document.getElementById('link-copy');
    button.addEventListener('click', function (_ref) {
      var currentTarget = _ref.currentTarget;
      var input = currentTarget.previousElementSibling;
      if (!navigator.clipboard) {
        copyToClipboardFallback(input);
        return;
      }
      navigator.clipboard.writeText(input.value).then(function () {
        Joomla.renderMessages({
          message: [Joomla.Text._('COM_SCHEDULER_CONFIG_WEBCRON_LINK_COPY_SUCCESS')]
        });
      }, function () {
        Joomla.renderMessages({
          error: [Joomla.Text._('COM_SCHEDULER_CONFIG_WEBCRON_LINK_COPY_FAIL')]
        });
      });
    });
  };
  var onBoot = function onBoot() {
    copyToClipboard();
    document.removeEventListener('DOMContentLoaded', onBoot);
  };
  document.addEventListener('DOMContentLoaded', onBoot);

})();
