(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  // eslint-disable no-alert
  (function (Joomla, document) {
    if (!Joomla) {
      throw new Error('core.js was not properly initialised');
    }
    Joomla.finderIndexer = function () {
      var getRequest;
      var totalItems = null;
      var offset = null;
      var progress = null;
      var optimized = false;
      var path = 'index.php?option=com_finder&tmpl=component&format=json';
      var token = "&" + document.getElementById('finder-indexer-token').getAttribute('name') + "=1";
      var removeElement = function removeElement(id) {
        var element = document.getElementById(id);
        if (element) {
          return element.parentNode.removeChild(element);
        }
        return null;
      };
      var updateProgress = function updateProgress(header, message) {
        progress = offset / totalItems * 100;
        var progressBar = document.getElementById('progress-bar');
        var progressHeader = document.getElementById('finder-progress-header');
        var progressMessage = document.getElementById('finder-progress-message');
        if (progressHeader) {
          progressHeader.innerText = header;
        }
        if (progressMessage) {
          progressMessage.innerHTML = Joomla.sanitizeHtml(message);
        }
        if (progressBar) {
          if (progress < 100) {
            progressBar.style.width = progress + "%";
            progressBar.setAttribute('aria-valuenow', progress);
          } else {
            progressBar.classList.remove('bar-success');
            progressBar.classList.add('bar-warning');
            progressBar.setAttribute('aria-valuemin', 100);
            progressBar.setAttribute('aria-valuemax', 200);
            progressBar.style.width = progress + "%";
            progressBar.setAttribute('aria-valuenow', progress);
          }

          // Auto close the window
          if (message === Joomla.Text._('COM_FINDER_INDEXER_MESSAGE_COMPLETE')) {
            removeElement('progress');
            window.parent.Joomla.Modal.getCurrent().close();
          }
        }
      };
      var handleResponse = function handleResponse(json, resp) {
        var progressHeader = document.getElementById('finder-progress-header');
        var progressMessage = document.getElementById('finder-progress-message');
        try {
          if (json === null) {
            throw new Error(resp);
          }
          if (json.error) {
            throw new Error(json);
          }
          if (json.start) {
            // eslint-disable-next-line prefer-destructuring
            totalItems = json.totalItems;
            if (document.getElementById('finder-debug-data')) {
              var debuglist = document.getElementById('finder-debug-data');
              Object.entries(json.pluginState).forEach(function (context) {
                var item = "<dt class=\"col-sm-3\">" + context[0] + "</dt>";
                item += "<dd id=\"finder-" + context[0].replace(/\s+/g, '-').toLowerCase() + "\" class=\"col-sm-9\"></dd>";
                debuglist.insertAdjacentHTML('beforeend', Joomla.sanitizeHtml(item, {
                  dd: ['class', 'id'],
                  dt: ['class']
                }));
              });
            }
          }
          offset += json.batchOffset;
          updateProgress(json.header, json.message);
          if (document.getElementById('finder-debug-data')) {
            Object.entries(json.pluginState).forEach(function (context) {
              document.getElementById("finder-" + context[0].replace(/\s+/g, '-').toLowerCase()).innerHTML = Joomla.sanitizeHtml(json.pluginState[context[0]].offset + " of " + json.pluginState[context[0]].total);
            });
          }
          if (offset < totalItems) {
            getRequest('indexer.batch');
          } else if (!optimized) {
            optimized = true;
            getRequest('indexer.optimize');
          }
        } catch (error) {
          removeElement('progress');
          try {
            if (json.error) {
              if (progressHeader) {
                progressHeader.innerText = json.header;
                progressHeader.classList.add('finder-error');
              }
              if (progressMessage) {
                progressMessage.innerHTML = Joomla.sanitizeHtml(json.message);
                progressMessage.classList.add('finder-error');
              }
            }
          } catch (ignore) {
            if (error === '') {
              // eslint-disable-next-line no-ex-assign
              error = Joomla.Text._('COM_FINDER_NO_ERROR_RETURNED');
            }
            if (progressHeader) {
              progressHeader.innerText = Joomla.Text._('COM_FINDER_AN_ERROR_HAS_OCCURRED');
              progressHeader.classList.add('finder-error');
            }
            if (progressMessage) {
              progressMessage.innerHTML = Joomla.sanitizeHtml(error);
              progressMessage.classList.add('finder-error');
            }
          }
        }
        return true;
      };
      var handleFailure = function handleFailure(error) {
        var progressHeader = document.getElementById('finder-progress-header');
        var progressMessage = document.getElementById('finder-progress-message');
        var data;
        if (error instanceof Error) {
          // Encode any html in the message
          var div = document.createElement('div');
          div.textContent = error.message;
          data = div.innerHTML;
          if (error instanceof SyntaxError) {
            data = Joomla.Text._('JLIB_JS_AJAX_ERROR_PARSE').replace('%s', data);
          }
        } else if (typeof error === 'object' && error.responseText) {
          data = error.responseText;
          try {
            data = JSON.parse(data);
          } catch (e) {
            data = Joomla.Text._('JLIB_JS_AJAX_ERROR_OTHER').replace('%s', error.status);
          }
        }
        removeElement('progress');
        var header = data && data.header ? data.header : Joomla.Text._('COM_FINDER_AN_ERROR_HAS_OCCURRED');
        var message = data && data.message ? data.message : Joomla.Text._('COM_FINDER_MESSAGE_RETURNED') + "<br>" + data;
        if (progressHeader) {
          progressHeader.innerText = header;
          progressHeader.classList.add('finder-error');
        }
        if (progressMessage) {
          progressMessage.innerHTML = Joomla.sanitizeHtml(message);
          progressMessage.classList.add('finder-error');
        }
      };
      getRequest = function getRequest(task) {
        Joomla.request({
          url: path + "&task=" + task + token,
          promise: true
        }).then(function (xhr) {
          handleResponse(JSON.parse(xhr.responseText));
        }).catch(function (error) {
          handleFailure(error);
        });
      };
      var initialize = function initialize() {
        offset = 0;
        progress = 0;
        getRequest('indexer.start');
      };
      initialize();
    };
  })(Joomla, document);

  // @todo use directly the Joomla.finderIndexer() instead of the Indexer()!!!
  document.addEventListener('DOMContentLoaded', function () {
    window.Indexer = Joomla.finderIndexer();
  });

})();
