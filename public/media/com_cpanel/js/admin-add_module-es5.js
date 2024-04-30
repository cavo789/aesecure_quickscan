(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (document) {
    document.addEventListener('DOMContentLoaded', function () {
      window.jSelectModuleType = function () {
        var elements = document.querySelectorAll('#moduleDashboardAddModal .modal-footer .btn.hidden');
        if (elements.length) {
          setTimeout(function () {
            elements.forEach(function (button) {
              button.classList.remove('hidden');
            });
          }, 1000);
        }
      };
      var buttons = document.querySelectorAll('#moduleDashboardAddModal .modal-footer .btn');
      var hideButtons = [];
      var isSaving = false;
      if (buttons.length) {
        buttons.forEach(function (button) {
          if (button.classList.contains('hidden')) {
            hideButtons.push(button);
          }
          button.addEventListener('click', function (event) {
            var elem = event.currentTarget;

            // There is some bug with events in iframe where currentTarget is "null"
            // => prevent this here by bubble up
            if (!elem) {
              elem = event.target;
            }
            if (elem) {
              var clickTarget = elem.dataset.bsTarget;

              // We remember to be in the saving process
              isSaving = clickTarget === '#saveBtn';

              // Reset saving process, if e.g. the validation of the form fails
              setTimeout(function () {
                isSaving = false;
              }, 1500);
              var iframe = document.querySelector('#moduleDashboardAddModal iframe');
              var content = iframe.contentDocument || iframe.contentWindow.document;
              var targetBtn = content.querySelector(clickTarget);
              if (targetBtn) {
                targetBtn.click();
              }
            }
          });
        });
      }
      var elementH = document.querySelector('#moduleDashboardAddModal');
      if (elementH) {
        elementH.addEventListener('hide.bs.modal', function () {
          hideButtons.forEach(function (button) {
            button.classList.add('hidden');
          });
        });
        elementH.addEventListener('hidden.bs.modal', function () {
          if (isSaving) {
            setTimeout(function () {
              window.parent.location.reload();
            }, 1000);
          }
        });
      }
    });
  })(document);

})();
