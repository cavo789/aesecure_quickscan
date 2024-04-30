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
     * A simple error handler
     *
     * @param   {String}  message
     */
    var handleCreationError = function handleCreationError(message) {
      Joomla.renderMessages({
        error: [message]
      });
    };

    /**
     * Ask the user to link an authenticator using the provided public key (created server-side).
     * Posts the credentials to the URL defined in post_url using AJAX.
     * That URL must re-render the management interface.
     * These contents will replace the element identified by the interface_selector CSS selector.
     */
    // eslint-disable-next-line no-unused-vars
    Joomla.plgSystemWebauthnInitCreateCredentials = function () {
      // Make sure the browser supports Webauthn
      if (!('credentials' in navigator)) {
        Joomla.renderMessages({
          error: [Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_NO_BROWSER_SUPPORT')]
        });
        return;
      }

      // Get the public key creation options through AJAX.
      var paths = Joomla.getOptions('system.paths');
      var postURL = "" + (paths ? paths.base + "/index.php" : window.location.pathname);
      var postBackData = {
        option: 'com_ajax',
        group: 'system',
        plugin: 'webauthn',
        format: 'json',
        akaction: 'initcreate',
        encoding: 'json'
      };
      postBackData[Joomla.getOptions('csrf.token')] = 1;
      Joomla.request({
        url: postURL,
        method: 'POST',
        data: interpolateParameters(postBackData),
        onSuccess: function onSuccess(response) {
          try {
            var publicKey = JSON.parse(response);
            Joomla.plgSystemWebauthnCreateCredentials(publicKey);
          } catch (exception) {
            handleCreationError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_XHR_INITCREATE'));
          }
        },
        onError: function onError(xhr) {
          handleCreationError(xhr.status + " " + xhr.statusText);
        }
      });
    };
    Joomla.plgSystemWebauthnCreateCredentials = function (publicKey) {
      var paths = Joomla.getOptions('system.paths');
      var postURL = "" + (paths ? paths.base + "/index.php" : window.location.pathname);
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

        // Send the response to your server
        var postBackData = {
          option: 'com_ajax',
          group: 'system',
          plugin: 'webauthn',
          format: 'raw',
          akaction: 'create',
          encoding: 'raw',
          data: btoa(JSON.stringify(publicKeyCredential))
        };
        postBackData[Joomla.getOptions('csrf.token')] = 1;
        Joomla.request({
          url: postURL,
          method: 'POST',
          data: interpolateParameters(postBackData),
          onSuccess: function onSuccess(responseHTML) {
            var elements = document.querySelectorAll('#plg_system_webauthn-management-interface');
            if (!elements) {
              return;
            }
            var elContainer = elements[0];
            elContainer.outerHTML = responseHTML;
            Joomla.plgSystemWebauthnInitialize();
            Joomla.plgSystemWebauthnReactivateTooltips();
          },
          onError: function onError(xhr) {
            handleCreationError(xhr.status + " " + xhr.statusText);
          }
        });
      }).catch(function (error) {
        // An error occurred: timeout, request to provide the authenticator refused, hardware /
        // software error...
        handleCreationError(error);
      });
    };

    /**
     * Edit label button
     *
     * @param   {Element} that      The button being clicked
     * @param   {String}  storeID  CSS ID for the element storing the configuration in its data
     *                              properties
     */
    // eslint-disable-next-line no-unused-vars
    Joomla.plgSystemWebauthnEditLabel = function (that) {
      var paths = Joomla.getOptions('system.paths');
      var postURL = "" + (paths ? paths.base + "/index.php" : window.location.pathname);

      // Find the UI elements
      var elTR = that.parentElement.parentElement;
      var credentialId = elTR.dataset.credential_id;
      var elTDs = elTR.querySelectorAll('.webauthnManagementCell');
      var elLabelTD = elTDs[0];
      var elButtonsTD = elTDs[1];
      var elButtons = elButtonsTD.querySelectorAll('button');
      var elEdit = elButtons[0];
      var elDelete = elButtons[1];

      // Show the editor
      var oldLabel = elLabelTD.innerText;
      var elContainer = document.createElement('div');
      elContainer.className = 'webauthnManagementEditorRow d-flex gap-2';
      var elInput = document.createElement('input');
      elInput.type = 'text';
      elInput.name = 'label';
      elInput.defaultValue = oldLabel;
      elInput.className = 'form-control';
      var elSave = document.createElement('button');
      elSave.className = 'btn btn-success btn-sm';
      elSave.innerText = Joomla.Text._('PLG_SYSTEM_WEBAUTHN_MANAGE_BTN_SAVE_LABEL');
      elSave.addEventListener('click', function () {
        var elNewLabel = elInput.value;
        if (elNewLabel !== '') {
          var postBackData = {
            option: 'com_ajax',
            group: 'system',
            plugin: 'webauthn',
            format: 'json',
            encoding: 'json',
            akaction: 'savelabel',
            credential_id: credentialId,
            new_label: elNewLabel
          };
          postBackData[Joomla.getOptions('csrf.token')] = 1;
          Joomla.request({
            url: postURL,
            method: 'POST',
            data: interpolateParameters(postBackData),
            onSuccess: function onSuccess(rawResponse) {
              var result = false;
              try {
                result = JSON.parse(rawResponse);
              } catch (exception) {
                result = rawResponse === 'true';
              }
              if (result !== true) {
                handleCreationError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_LABEL_NOT_SAVED'));
              }
            },
            onError: function onError(xhr) {
              handleCreationError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_LABEL_NOT_SAVED') + " -- " + xhr.status + " " + xhr.statusText);
            }
          });
        }
        elLabelTD.innerText = elNewLabel;
        elEdit.disabled = false;
        elDelete.disabled = false;
        return false;
      }, false);
      var elCancel = document.createElement('button');
      elCancel.className = 'btn btn-danger btn-sm';
      elCancel.innerText = Joomla.Text._('PLG_SYSTEM_WEBAUTHN_MANAGE_BTN_CANCEL_LABEL');
      elCancel.addEventListener('click', function () {
        elLabelTD.innerText = oldLabel;
        elEdit.disabled = false;
        elDelete.disabled = false;
        return false;
      }, false);
      elLabelTD.innerHTML = '';
      elContainer.appendChild(elInput);
      elContainer.appendChild(elSave);
      elContainer.appendChild(elCancel);
      elLabelTD.appendChild(elContainer);
      elEdit.disabled = true;
      elDelete.disabled = true;
      return false;
    };

    /**
     * Delete button
     *
     * @param   {Element} that      The button being clicked
     */
    // eslint-disable-next-line no-unused-vars
    Joomla.plgSystemWebauthnDelete = function (that) {
      if (!window.confirm(Joomla.Text._('JGLOBAL_CONFIRM_DELETE'))) {
        return false;
      }
      var paths = Joomla.getOptions('system.paths');
      var postURL = "" + (paths ? paths.base + "/index.php" : window.location.pathname);

      // Find the UI elements
      var elTR = that.parentElement.parentElement;
      var credentialId = elTR.dataset.credential_id;
      var elTDs = elTR.querySelectorAll('.webauthnManagementCell');
      var elButtonsTD = elTDs[1];
      var elButtons = elButtonsTD.querySelectorAll('button');
      var elEdit = elButtons[0];
      var elDelete = elButtons[1];
      elEdit.disabled = true;
      elDelete.disabled = true;

      // Delete the record
      var postBackData = {
        option: 'com_ajax',
        group: 'system',
        plugin: 'webauthn',
        format: 'json',
        encoding: 'json',
        akaction: 'delete',
        credential_id: credentialId
      };
      postBackData[Joomla.getOptions('csrf.token')] = 1;
      Joomla.request({
        url: postURL,
        method: 'POST',
        data: interpolateParameters(postBackData),
        onSuccess: function onSuccess(rawResponse) {
          var result = false;
          try {
            result = JSON.parse(rawResponse);
          } catch (e) {
            result = rawResponse === 'true';
          }
          if (result !== true) {
            handleCreationError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_NOT_DELETED'));
            return;
          }
          elTR.parentElement.removeChild(elTR);
        },
        onError: function onError(xhr) {
          elEdit.disabled = false;
          elDelete.disabled = false;
          handleCreationError(Joomla.Text._('PLG_SYSTEM_WEBAUTHN_ERR_NOT_DELETED') + " -- " + xhr.status + " " + xhr.statusText);
        }
      });
      return false;
    };
    Joomla.plgSystemWebauthnReactivateTooltips = function () {
      var tooltips = Joomla.getOptions('bootstrap.tooltip');
      if (typeof tooltips === 'object' && tooltips !== null) {
        Object.keys(tooltips).forEach(function (tooltip) {
          var opt = tooltips[tooltip];
          var options = {
            animation: opt.animation ? opt.animation : true,
            container: opt.container ? opt.container : false,
            delay: opt.delay ? opt.delay : 0,
            html: opt.html ? opt.html : false,
            selector: opt.selector ? opt.selector : false,
            trigger: opt.trigger ? opt.trigger : 'hover focus',
            fallbackPlacement: opt.fallbackPlacement ? opt.fallbackPlacement : null,
            boundary: opt.boundary ? opt.boundary : 'clippingParents',
            title: opt.title ? opt.title : '',
            customClass: opt.customClass ? opt.customClass : '',
            sanitize: opt.sanitize ? opt.sanitize : true,
            sanitizeFn: opt.sanitizeFn ? opt.sanitizeFn : null,
            popperConfig: opt.popperConfig ? opt.popperConfig : null
          };
          if (opt.placement) {
            options.placement = opt.placement;
          }
          if (opt.template) {
            options.template = opt.template;
          }
          if (opt.allowList) {
            options.allowList = opt.allowList;
          }
          var elements = Array.from(document.querySelectorAll(tooltip));
          if (elements.length) {
            elements.map(function (el) {
              return new window.bootstrap.Tooltip(el, options);
            });
          }
        });
      }
    };

    /**
     * Add New Authenticator button click handler
     *
     * @param   {MouseEvent} event  The mouse click event
     *
     * @returns {boolean} Returns false to prevent the default browser button behavior
     */
    Joomla.plgSystemWebauthnAddOnClick = function (event) {
      event.preventDefault();
      Joomla.plgSystemWebauthnInitCreateCredentials();
      return false;
    };

    /**
     * Edit Name button click handler
     *
     * @param   {MouseEvent} event  The mouse click event
     *
     * @returns {boolean} Returns false to prevent the default browser button behavior
     */
    Joomla.plgSystemWebauthnEditOnClick = function (event) {
      event.preventDefault();
      Joomla.plgSystemWebauthnEditLabel(event.currentTarget);
      return false;
    };

    /**
     * Remove button click handler
     *
     * @param   {MouseEvent} event  The mouse click event
     *
     * @returns {boolean} Returns false to prevent the default browser button behavior
     */
    Joomla.plgSystemWebauthnDeleteOnClick = function (event) {
      event.preventDefault();
      Joomla.plgSystemWebauthnDelete(event.currentTarget);
      return false;
    };

    /**
     * Initialization on page load.
     */
    Joomla.plgSystemWebauthnInitialize = function () {
      var addButton = document.getElementById('plg_system_webauthn-manage-add');
      if (addButton) {
        addButton.addEventListener('click', Joomla.plgSystemWebauthnAddOnClick);
      }
      var editLabelButtons = [].slice.call(document.querySelectorAll('.plg_system_webauthn-manage-edit'));
      if (editLabelButtons.length) {
        editLabelButtons.forEach(function (button) {
          button.addEventListener('click', Joomla.plgSystemWebauthnEditOnClick);
        });
      }
      var deleteButtons = [].slice.call(document.querySelectorAll('.plg_system_webauthn-manage-delete'));
      if (deleteButtons.length) {
        deleteButtons.forEach(function (button) {
          button.addEventListener('click', Joomla.plgSystemWebauthnDeleteOnClick);
        });
      }
    };

    // Initialization. Runs on DOM content loaded since this script is always loaded deferred.
    Joomla.plgSystemWebauthnInitialize();
  })(Joomla, document);

})();
