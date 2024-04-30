(function () {
  'use strict';

  /**
   * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    document.addEventListener('DOMContentLoaded', function () {
      var _document$getElementB;
      var elCodeField = document.getElementById('users-mfa-code');
      var elValidateButton = document.getElementById('users-mfa-captive-button-submit');
      var elToolbarButton = (_document$getElementB = document.getElementById('toolbar-user-mfa-submit')) == null ? void 0 : _document$getElementB.querySelector('button');

      // Focus the code field. If the code field is hidden, focus the submit button (useful e.g. for WebAuthn)
      if (elCodeField && elCodeField.style.display !== 'none' && !elCodeField.classList.contains('visually-hidden') && elCodeField.type !== 'hidden') {
        elCodeField.focus();
      } else {
        if (elValidateButton) {
          elValidateButton.focus();
        }
        if (elToolbarButton) {
          elToolbarButton.focus();
        }
      }

      // Capture the admin toolbar buttons, make them click the inline buttons
      document.querySelectorAll('.button-user-mfa-submit').forEach(function (elButton) {
        elButton.addEventListener('click', function (e) {
          e.preventDefault();
          elValidateButton.click();
        });
      });
      document.querySelectorAll('.button-user-mfa-logout').forEach(function (elButton) {
        elButton.addEventListener('click', function (e) {
          e.preventDefault();
          var elLogout = document.getElementById('users-mfa-captive-button-logout');
          if (elLogout) {
            elLogout.click();
          }
        });
      });
      document.querySelectorAll('.button-user-mfa-choose-another').forEach(function (elButton) {
        elButton.addEventListener('click', function (e) {
          e.preventDefault();
          var elChooseAnother = document.getElementById('users-mfa-captive-form-choose-another');
          if (elChooseAnother) {
            elChooseAnother.click();
          }
        });
      });
    });
  })();

})();
