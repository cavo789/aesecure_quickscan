(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    // Ajax call to get the override status.
    var checkOverride = function checkOverride() {
      if (Joomla.getOptions('js-override-check')) {
        var options = Joomla.getOptions('js-override-check');
        var update = function update(type, text, linkHref) {
          var link = document.getElementById('plg_quickicon_overridecheck');
          var linkSpans = link.querySelectorAll('span.j-links-link');
          if (link) {
            link.classList.add(type);
            if (linkHref) {
              link.setAttribute('href', linkHref);
            }
          }
          if (linkSpans.length) {
            linkSpans.forEach(function (span) {
              span.innerHTML = Joomla.sanitizeHtml(text);
            });
          }
        };

        /**
         * DO NOT use fetch() for QuickIcon requests. They must be queued.
         *
         * @see https://github.com/joomla/joomla-cms/issues/38001
         */
        Joomla.enqueueRequest({
          url: options.ajaxUrl,
          method: 'GET',
          promise: true
        }).then(function (xhr) {
          var response = xhr.responseText;
          var updateInfoList = JSON.parse(response);
          if (updateInfoList.installerOverride !== 'disabled') {
            if (Array.isArray(updateInfoList)) {
              if (updateInfoList.length === 0) {
                // No overrides found
                update('success', Joomla.Text._('PLG_QUICKICON_OVERRIDECHECK_UPTODATE'), '');
              } else {
                // Scroll to page top
                window.scrollTo(0, 0);
                update('danger', Joomla.Text._('PLG_QUICKICON_OVERRIDECHECK_OVERRIDEFOUND').replace('%s', "<span class=\"badge text-dark bg-light\">" + updateInfoList.length + "</span>"), '');
              }
            } else {
              throw new Error('Override check: unexpected value type');
            }
          } else {
            update('danger', Joomla.Text._('PLG_QUICKICON_OVERRIDECHECK_ERROR_ENABLE'), "index.php?option=com_plugins&task=plugin.edit&extension_id=" + options.pluginId);
          }
        }).catch(function () {
          // An error occurred
          update('danger', Joomla.Text._('PLG_QUICKICON_OVERRIDECHECK_ERROR'), '');
        });
      }
    };

    // Give some times to the layout and other scripts to settle their stuff
    window.addEventListener('load', function () {
      setTimeout(checkOverride, 390);
    });
  })();

})();
