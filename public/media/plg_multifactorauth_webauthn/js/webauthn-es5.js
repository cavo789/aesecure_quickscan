(function () {
  'use strict';

  /**
   * @package     Joomla.Plugin
   * @subpackage  Multifactorauth.webauthn
   *
   * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function (Joomla, document) {
    var authData = null;
    var arrayToBase64String = function arrayToBase64String(a) {
      return btoa(String.fromCharCode.apply(String, a));
    };
    var base64url2base64 = function base64url2base64(input) {
      var output = input.replace(/-/g, '+').replace(/_/g, '/');
      var pad = output.length % 4;
      if (pad) {
        if (pad === 1) {
          throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
        }
        output += new Array(5 - pad).join('=');
      }
      return output;
    };
    var displayError = function displayError(message) {
      try {
        Joomla.renderMessages({
          error: message
        });
      } catch (e) {
        alert(message);
      }
    };
    var handleError = function handleError(message) {
      try {
        document.getElementById('plg_multifactorauth_webauthn_validate_button').style.disabled = 'null';
      } catch (e) {
        // Do nothing
      }
      displayError(message);
    };
    var setUp = function setUp(e) {
      e.preventDefault();

      // Make sure the browser supports Webauthn
      if (!('credentials' in navigator)) {
        displayError(Joomla.Text._('PLG_MULTIFACTORAUTH_WEBAUTHN_ERR_NOTAVAILABLE_HEAD'));
        return false;
      }
      var rawPKData = document.forms['com-users-method-edit'].querySelectorAll('input[name="pkRequest"]')[0].value;
      var publicKey = JSON.parse(atob(rawPKData));

      // Convert the public key information to a format usable by the browser's credentials manager
      publicKey.challenge = Uint8Array.from(window.atob(base64url2base64(publicKey.challenge)), function (c) {
        return c.charCodeAt(0);
      });
      publicKey.user.id = Uint8Array.from(window.atob(publicKey.user.id), function (c) {
        return c.charCodeAt(0);
      });
      if (publicKey.excludeCredentials) {
        publicKey.excludeCredentials = publicKey.excludeCredentials.map(function (data) {
          data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), function (c) {
            return c.charCodeAt(0);
          });
          return data;
        });
      }

      // Ask the browser to prompt the user for their authenticator
      navigator.credentials.create({
        publicKey: publicKey
      }).then(function (data) {
        var publicKeyCredential = {
          id: data.id,
          type: data.type,
          rawId: arrayToBase64String(new Uint8Array(data.rawId)),
          response: {
            clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
            attestationObject: arrayToBase64String(new Uint8Array(data.response.attestationObject))
          }
        };

        // Store the WebAuthn reply
        document.getElementById('com-users-method-code').value = btoa(JSON.stringify(publicKeyCredential));

        // Submit the form
        document.forms['com-users-method-edit'].submit();
      }, function (error) {
        // An error occurred: timeout, request to provide the authenticator refused, hardware / software
        // error...
        handleError(error);
      });
      return false;
    };
    var validate = function validate() {
      // Make sure the browser supports Webauthn
      if (!('credentials' in navigator)) {
        displayError(Joomla.Text._('PLG_MULTIFACTORAUTH_WEBAUTHN_ERR_NOTAVAILABLE_HEAD'));
        return;
      }
      var publicKey = authData;
      if (!publicKey.challenge) {
        handleError(Joomla.Text._('PLG_MULTIFACTORAUTH_WEBAUTHN_ERR_NO_STORED_CREDENTIAL'));
        return;
      }
      publicKey.challenge = Uint8Array.from(window.atob(base64url2base64(publicKey.challenge)), function (c) {
        return c.charCodeAt(0);
      });
      if (publicKey.allowCredentials) {
        publicKey.allowCredentials = publicKey.allowCredentials.map(function (data) {
          data.id = Uint8Array.from(window.atob(base64url2base64(data.id)), function (c) {
            return c.charCodeAt(0);
          });
          return data;
        });
      }
      navigator.credentials.get({
        publicKey: publicKey
      }).then(function (data) {
        var publicKeyCredential = {
          id: data.id,
          type: data.type,
          rawId: arrayToBase64String(new Uint8Array(data.rawId)),
          response: {
            authenticatorData: arrayToBase64String(new Uint8Array(data.response.authenticatorData)),
            clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
            signature: arrayToBase64String(new Uint8Array(data.response.signature)),
            userHandle: data.response.userHandle ? arrayToBase64String(new Uint8Array(data.response.userHandle)) : null
          }
        };
        document.getElementById('users-mfa-code').value = btoa(JSON.stringify(publicKeyCredential));
        document.getElementById('users-mfa-captive-form').submit();
      }, function (error) {
        // Example: timeout, interaction refused...
        handleError(error);
      });
    };
    var onValidateClick = function onValidateClick(event) {
      event.preventDefault();
      authData = JSON.parse(window.atob(Joomla.getOptions('com_users.authData')));
      document.getElementById('users-mfa-captive-button-submit').style.disabled = 'disabled';
      validate();
      return false;
    };
    document.getElementById('multifactorauth-webauthn-missing').style.display = 'none';
    if (typeof navigator.credentials === 'undefined') {
      document.getElementById('multifactorauth-webauthn-missing').style.display = 'block';
      document.getElementById('multifactorauth-webauthn-controls').style.display = 'none';
    }
    window.addEventListener('DOMContentLoaded', function () {
      if (Joomla.getOptions('com_users.pagetype') === 'validate') {
        document.getElementById('users-mfa-captive-button-submit').addEventListener('click', onValidateClick);
      } else {
        document.querySelectorAll('.multifactorauth_webauthn_setup').forEach(function (btn) {
          btn.addEventListener('click', setUp);
        });
      }
    });
  })(Joomla, document);

})();
