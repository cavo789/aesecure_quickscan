(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function (document) {
    var checkPrivacy = function checkPrivacy() {
      var variables = Joomla.getOptions('js-privacy-check');
      var ajaxUrl = variables.plg_quickicon_privacycheck_ajax_url;
      var url = variables.plg_quickicon_privacycheck_url;
      var text = variables.plg_quickicon_privacycheck_text;
      var quickicon = document.getElementById('plg_quickicon_privacycheck');
      var link = quickicon.querySelector('span.j-links-link');

      /**
       * DO NOT use fetch() for QuickIcon requests. They must be queued.
       *
       * @see https://github.com/joomla/joomla-cms/issues/38001
       */
      Joomla.enqueueRequest({
        url: ajaxUrl,
        method: 'GET',
        promise: true
      }).then(function (xhr) {
        var response = xhr.responseText;
        var request = JSON.parse(response);
        if (request.data.number_urgent_requests) {
          // Quickicon on dashboard shows message
          var countBadge = document.createElement('span');
          countBadge.classList.add('badge', 'text-dark', 'bg-light');
          countBadge.textContent = request.data.number_urgent_requests;
          link.textContent = text.REQUESTFOUND + " ";
          link.appendChild(countBadge);

          // Quickicon becomes red
          quickicon.classList.add('danger');

          // Span in alert
          var countSpan = document.createElement('span');
          countSpan.classList.add('label', 'label-important');
          countSpan.textContent = text.REQUESTFOUND_MESSAGE.replace('%s', request.data.number_urgent_requests) + " ";

          // Button in alert to 'view requests'
          var requestButton = document.createElement('button');
          requestButton.classList.add('btn', 'btn-primary', 'btn-sm');
          requestButton.setAttribute('onclick', "document.location='" + url + "'");
          requestButton.textContent = text.REQUESTFOUND_BUTTON;
          var div = document.createElement('div');
          div.classList.add('alert', 'alert-error', 'alert-joomlaupdate');
          div.appendChild(countSpan);
          div.appendChild(requestButton);

          // Add elements to container for alert messages
          var container = document.querySelector('#system-message-container');
          container.insertBefore(div, container.firstChild);
        } else {
          quickicon.classList.add('success');
          link.textContent = text.NOREQUEST;
        }
      }).catch(function () {
        quickicon.classList.add('danger');
        link.textContent = text.ERROR;
      });
    };

    // Give some times to the layout and other scripts to settle their stuff
    window.addEventListener('load', function () {
      setTimeout(checkPrivacy, 360);
    });
  })(document);

})();
