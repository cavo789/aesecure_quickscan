(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    // Ajax call to get the update status of the installed extensions
    var fetchUpdate = function fetchUpdate() {
      if (Joomla.getOptions('js-extensions-update')) {
        var options = Joomla.getOptions('js-extensions-update');
        var update = function update(type, text) {
          var link = document.getElementById('plg_quickicon_extensionupdate');
          var linkSpans = [].slice.call(link.querySelectorAll('span.j-links-link'));
          if (link) {
            link.classList.add(type);
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
          if (Array.isArray(updateInfoList)) {
            if (updateInfoList.length === 0) {
              // No updates
              update('success', Joomla.Text._('PLG_QUICKICON_EXTENSIONUPDATE_UPTODATE'));
            } else {
              update('danger', Joomla.Text._('PLG_QUICKICON_EXTENSIONUPDATE_UPDATEFOUND').replace('%s', "<span class=\"badge text-dark bg-light\">" + updateInfoList.length + "</span>"));
            }
          } else {
            // An error occurred
            update('danger', Joomla.Text._('PLG_QUICKICON_EXTENSIONUPDATE_ERROR'));
          }
        }).catch(function () {
          // An error occurred
          update('danger', Joomla.Text._('PLG_QUICKICON_EXTENSIONUPDATE_ERROR'));
        });
      }
    };

    // Give some times to the layout and other scripts to settle their stuff
    window.addEventListener('load', function () {
      setTimeout(fetchUpdate, 330);
    });
  })();

})();
