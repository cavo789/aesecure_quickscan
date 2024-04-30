(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  if (Joomla && Joomla.getOptions('js-extensions-update')) {
    var update = function update(type, text) {
      var link = document.getElementById('plg_quickicon_joomlaupdate');
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
    var fetchUpdate = function fetchUpdate() {
      var options = Joomla.getOptions('js-joomla-update');

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
            update('success', Joomla.Text._('PLG_QUICKICON_JOOMLAUPDATE_UPTODATE'));
          } else {
            var updateInfo = updateInfoList.shift();
            if (updateInfo.version !== options.version) {
              update('danger', Joomla.Text._('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND').replace('%s', "<span class=\"badge text-dark bg-light\"> \u200E " + updateInfo.version + "</span>"));
            } else {
              update('success', Joomla.Text._('PLG_QUICKICON_JOOMLAUPDATE_UPTODATE'));
            }
          }
        } else {
          // An error occurred
          update('danger', Joomla.Text._('PLG_QUICKICON_JOOMLAUPDATE_ERROR'));
        }
      }).catch(function () {
        // An error occurred
        update('danger', Joomla.Text._('PLG_QUICKICON_JOOMLAUPDATE_ERROR'));
      });
    };

    // Give some times to the layout and other scripts to settle their stuff
    window.addEventListener('load', function () {
      setTimeout(fetchUpdate, 300);
    });
  }

})();
