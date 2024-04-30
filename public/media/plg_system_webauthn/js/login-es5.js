(function () {
  'use strict';

  /**
   * @package     Joomla.Plugin
   * @subpackage  System.webauthn
   *
   * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  window.Joomla = window.Joomla || {};
  (function (Joomla, document) {
    /**
     * Converts a simple object containing query string parameters to a single, escaped query string.
     * This method is a necessary evil since Joomla.request can only accept data as a string.
     *
     * @param    object   {object}  A plain object containing the query parameters to pass
     * @param    prefix   {string}  Prefix for array-type parameters
     *
     * @returns  {string}
     */
    var interpolateParameters = function interpolateParameters(object, prefix) {
      if (prefix === void 0) {
        prefix = '';
      }
      var encodedString = '';
      Object.keys(object).forEach(function (prop) {
        if (typeof object[prop] !== 'object') {
          if (encodedString.length > 0) {
            encodedString += '&';
          }
          if (prefix === '') {
            encodedString += encodeURIComponent(prop) + "=" + encodeURIComponent(object[prop]);
          } else {
            encodedString += encodeURIComponent(prefix) + "[" + encodeURIComponent(prop) + "]=" + encodeURIComponent(object[prop]);
          }
          return;
        }

        // Objects need special handling
        encodedString += "" + interpolateParameters(object[prop], prop);
      });
      return encodedString;
    };

    /**
     * Finds the first field matching a selector inside a form
     *
     * @param   {HTMLFormElement}  form           The FORM element
     * @param   {String}           fieldSelector  The CSS selector to locate the field
     *
     * @returns {Element|null}  NULL when no element is found
     */
    var findField = function findField(form, fieldSelector) {
      var elInputs = form.querySelectorAll(fieldSelector);
      if (!elInputs.length) {
        return null;
      }
      return elInputs[0];
    };

    /**
     * Find a form field described by the CSS selector fieldSelector.
     * The field must be inside a <form> element which is either the
     * outerElement itself or enclosed by outerElement.
     *
     * @param   {Element}  outerElement   The element which is either our form or contains our form.
     * @param   {String}   fieldSelector  The CSS selector to locate the field
     *
     * @returns {null|Element}  NULL when no element is found
     */
    var lookForField = function lookForField(outerElement, fieldSelector) {
      var elInput = null;
      if (!outerElement) {
        return elInput;
      }
      var elElement = outerElement.parentElement;
      if (elElement.nodeName === 'FORM') {
        elInput = findField(elElement, fieldSelector);
        return elInput;
      }
      var elForms = elElement.querySelectorAll('form');
      if (elForms.length) {
        for (var i = 0; i < elForms.length; i += 1) {
          elInput = findField(elForms[i], fieldSelector);
          if (elInput !== null) {
            return elInput;
          }
        }
      }
      return null;
    };

    /**
     * A simple error handler.
     *
     * @param   {String}  message
     */
    var handleLoginError = function handleLoginError(message) {
      Joomla.renderMessages({
        error: [message]
      });
    };

    /**
     * Handles the browser response for the user interaction with the authenticator. Redirects to an
     * internal page which handles the login server-side.
     *
     * @param {  Object}  publicKey     Public key request options, returned from the server
     */
    var handleLoginChallenge = function handleLoginChallenge(publicKey) {
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
      if (!publicKey.challenge) {
        handleLoginError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_INVALID_USERNAME'));
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

        // Send the response to your server
        var paths = Joomla.getOptions('system.paths');
        window.location = (paths ? paths.base + "/index.php" : window.location.pathname) + "?" + Joomla.getOptions('csrf.token') + "=1&option=com_ajax&group=system&plugin=webauthn&" + ("format=raw&akaction=login&encoding=redirect&data=" + btoa(JSON.stringify(publicKeyCredential)));
      }).catch(function (error) {
        // Example: timeout, interaction refused...
        handleLoginError(error);
      });
    };

    /**
     * Initialize the passwordless login, going through the server to get the registered certificates
     * for the user.
     *
     * @param   {string}   formId       The login form's or login module's HTML ID
     *
     * @returns {boolean}  Always FALSE to prevent BUTTON elements from reloading the page.
     */
    // eslint-disable-next-line no-unused-vars
    Joomla.plgSystemWebauthnLogin = function (formId) {
      // Get the username
      var elFormContainer = document.getElementById(formId);
      var elUsername = lookForField(elFormContainer, 'input[name=username]');
      var elReturn = lookForField(elFormContainer, 'input[name=return]');
      if (elUsername === null) {
        Joomla.renderMessages({
          error: [Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_CANNOT_FIND_USERNAME')]
        });
        return false;
      }
      var username = elUsername.value;
      var returnUrl = elReturn ? elReturn.value : null;

      // No username? We cannot proceed. We need a username to find the acceptable public keys :(
      if (username === '') {
        Joomla.renderMessages({
          error: [Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_EMPTY_USERNAME')]
        });
        return false;
      }

      // Get the Public Key Credential Request Options (challenge and acceptable public keys)
      var postBackData = {
        option: 'com_ajax',
        group: 'system',
        plugin: 'webauthn',
        format: 'raw',
        akaction: 'challenge',
        encoding: 'raw',
        username: username,
        returnUrl: returnUrl
      };
      postBackData[Joomla.getOptions('csrf.token')] = 1;
      var paths = Joomla.getOptions('system.paths');
      Joomla.request({
        url: (paths ? paths.base + "/index.php" : window.location.pathname) + "?" + Joomla.getOptions('csrf.token') + "=1",
        method: 'POST',
        data: interpolateParameters(postBackData),
        onSuccess: function onSuccess(rawResponse) {
          var jsonData = {};
          try {
            jsonData = JSON.parse(rawResponse);
          } catch (e) {
            /**
             * In case of JSON decoding failure fall through; the error will be handled in the login
             * challenge handler called below.
             */
          }
          handleLoginChallenge(jsonData);
        },
        onError: function onError(xhr) {
          handleLoginError(xhr.status + " " + xhr.statusText);
        }
      });
      return false;
    };

    // Initialization. Runs on DOM content loaded since this script is always loaded deferred.
    var loginButtons = [].slice.call(document.querySelectorAll('.plg_system_webauthn_login_button'));
    if (loginButtons.length) {
      loginButtons.forEach(function (button) {
        button.addEventListener('click', function (_ref) {
          var currentTarget = _ref.currentTarget;
          Joomla.plgSystemWebauthnLogin(currentTarget.getAttribute('data-webauthn-form'));
        });
      });
    }
  })(Joomla, document);

})();
