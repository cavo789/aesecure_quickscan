(function () {
  'use strict';

  (function (document, Joomla) {
    if (!Joomla) {
      throw new Error('Joomla API is not properly initialised');
    }

    /* global hotkeys */
    Joomla.addShortcut = function (hotkey, callback) {
      hotkeys(hotkey, 'joomla', function (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        callback.call();
      });
    };
    Joomla.addClickShortcut = function (hotkey, selector) {
      Joomla.addShortcut(hotkey, function () {
        var element = document.querySelector(selector);
        if (element) {
          element.click();
        }
      });
    };
    Joomla.addFocusShortcut = function (hotkey, selector) {
      Joomla.addShortcut(hotkey, function () {
        var element = document.querySelector(selector);
        if (element) {
          element.focus();
        }
      });
    };
    Joomla.addLinkShortcut = function (hotkey, selector) {
      Joomla.addShortcut(hotkey, function () {
        window.location.href = selector;
      });
    };
    var setShortcutFilter = function setShortcutFilter() {
      hotkeys.filter = function (event) {
        var target = event.target || event.srcElement;
        var tagName = target.tagName;

        // Checkboxes should not block a shortcut event
        if (target.type === 'checkbox') {
          return true;
        }
        // Default hotkeys filter behavior
        return !(target.isContentEditable || tagName === 'INPUT' || tagName === 'SELECT' || tagName === 'TEXTAREA');
      };
    };
    var startupShortcuts = function startupShortcuts() {
      hotkeys('J', function (event) {
        // If we're already in the scope, it's a normal shortkey
        if (hotkeys.getScope() === 'joomla') {
          return;
        }
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        hotkeys.setScope('joomla');

        // Leave the scope after x milliseconds
        setTimeout(function () {
          hotkeys.setScope(false);
        }, Joomla.getOptions('plg_system_shortcut.timeout', 2000));
      });
    };
    var addOverviewHint = function addOverviewHint() {
      var mainContainer = document.querySelector('.com_cpanel .container-main');
      if (mainContainer) {
        var containerElement = document.createElement('section');
        containerElement.className = 'content pt-4';
        containerElement.insertAdjacentHTML('beforeend', Joomla.Text._('PLG_SYSTEM_SHORTCUT_OVERVIEW_HINT'));
        mainContainer.appendChild(containerElement);
      }
    };
    var initOverviewModal = function initOverviewModal(options) {
      var dlItems = new Map();
      Object.values(options).forEach(function (value) {
        if (!value.shortcut || !value.title) {
          return;
        }
        var titles = [];
        if (dlItems.has(value.shortcut)) {
          titles = dlItems.get(value.shortcut);
          titles.push(value.title);
        } else {
          titles = [value.title];
        }
        dlItems.set(value.shortcut, titles);
      });
      var dl = '<dl>';
      dlItems.forEach(function (titles, shortcut) {
        dl += '<div>';
        dl += '<dt class="d-inline-block"><kbd>J</kbd>';
        shortcut.split('+').forEach(function (key) {
          dl += " " + Joomla.Text._('PLG_SYSTEM_SHORTCUT_THEN') + " <kbd>" + key.trim() + "</kbd>";
        });
        dl += '</dt>';
        titles.forEach(function (title) {
          dl += "<dd class=\"d-inline-block ms-1\">" + title + "</dd>";
        });
        dl += '</div>';
      });
      dl += '</dl>';
      var modal = "\n      <div class=\"modal fade\" id=\"shortcutOverviewModal\" tabindex=\"-1\" role=\"dialog\" data-bs-backdrop=\"static\" aria-labelledby=\"shortcutOverviewModalLabel\" aria-hidden=\"true\">\n        <div class=\"modal-dialog\" role=\"document\">\n          <div class=\"modal-content\">\n            <div class=\"modal-header\">\n              <h3 id=\"shortcutOverviewModalLabel\" class=\"modal-title\">\n                " + Joomla.Text._('PLG_SYSTEM_SHORTCUT_OVERVIEW_TITLE') + "\n              </h3>\n              <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"" + Joomla.Text._('JCLOSE') + "\"></button>\n            </div>\n            <div class=\"modal-body p-3\">\n              <p>" + Joomla.Text._('PLG_SYSTEM_SHORTCUT_OVERVIEW_DESC') + "</p>\n              <div class=\"mb-3\">\n                " + dl + "\n              </div>\n            </div>\n          </div>\n        </div>\n      </div>\n    ";
      document.body.insertAdjacentHTML('beforeend', modal);
      var bootstrapModal = new bootstrap.Modal(document.getElementById('shortcutOverviewModal'), {
        keyboard: true,
        backdrop: true
      });
      hotkeys('X', 'joomla', function () {
        return bootstrapModal.show();
      });
    };
    document.addEventListener('DOMContentLoaded', function () {
      var options = Joomla.getOptions('plg_system_shortcut.shortcuts');
      Object.values(options).forEach(function (value) {
        if (!value.shortcut || !value.selector) {
          return;
        }
        if (value.selector.startsWith('/') || value.selector.startsWith('http://') || value.selector.startsWith('www.')) {
          Joomla.addLinkShortcut(value.shortcut, value.selector);
        } else if (value.selector.includes('input')) {
          Joomla.addFocusShortcut(value.shortcut, value.selector);
        } else {
          Joomla.addClickShortcut(value.shortcut, value.selector);
        }
      });
      // Show hint and overview on logged in backend only (not login page)
      if (document.querySelector('nav')) {
        initOverviewModal(options);
        addOverviewHint();
      }
      setShortcutFilter();
      startupShortcuts();
    });
  })(document, Joomla);

})();
