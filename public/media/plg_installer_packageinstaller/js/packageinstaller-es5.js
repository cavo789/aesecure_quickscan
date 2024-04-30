(function () {
  'use strict';

  /**
   * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */

  Joomla = window.Joomla || {};
  (function (Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
      Joomla.submitbuttonpackage = function () {
        var form = document.getElementById('adminForm');

        // do field validation
        if (form.install_package.value === '') {
          Joomla.renderMessages({
            warning: [Joomla.Text._('PLG_INSTALLER_PACKAGEINSTALLER_NO_PACKAGE')]
          });
        } else if (form.install_package.files[0].size > form.max_upload_size.value) {
          Joomla.renderMessages({
            warning: [Joomla.Text._('COM_INSTALLER_MSG_WARNINGS_UPLOADFILETOOBIG')]
          });
        } else {
          var loading = document.getElementById('loading');
          if (loading) {
            loading.classList.remove('hidden');
          }
          form.installtype.value = 'upload';
          form.submit();
        }
      };
      if (typeof FormData === 'undefined') {
        document.querySelector('#legacy-uploader').classList.remove('hidden');
        document.querySelector('#uploader-wrapper').classList.add('hidden');
        return;
      }
      var uploading = false;
      var dragZone = document.querySelector('#dragarea');
      var fileInput = document.querySelector('#install_package');
      var fileSizeMax = document.querySelector('#max_upload_size').value;
      var button = document.querySelector('#select-file-button');
      var returnUrl = document.querySelector('#installer-return').value;
      var progress = document.getElementById('upload-progress');
      var progressBar = progress.querySelector('.progress-bar');
      var percentage = progress.querySelector('.uploading-number');
      var uploadUrl = 'index.php?option=com_installer&task=install.ajax_upload';
      function showError(res) {
        dragZone.setAttribute('data-state', 'pending');
        var message = Joomla.Text._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_UNKNOWN');
        if (res == null) {
          message = Joomla.Text._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_EMPTY');
        } else if (typeof res === 'string') {
          // Let's remove unnecessary HTML
          message = res.replace(/(<([^>]+)>|\s+)/g, ' ');
        } else if (res.message) {
          message = res.message;
        }
        Joomla.renderMessages({
          error: [message]
        });
      }
      if (returnUrl) {
        uploadUrl += "&return=" + returnUrl;
      }
      button.addEventListener('click', function () {
        fileInput.click();
      });
      fileInput.addEventListener('change', function () {
        if (uploading) {
          return;
        }
        Joomla.submitbuttonpackage();
      });
      dragZone.addEventListener('dragenter', function (event) {
        event.preventDefault();
        event.stopPropagation();
        dragZone.classList.add('hover');
        return false;
      });

      // Notify user when file is over the drop area
      dragZone.addEventListener('dragover', function (event) {
        event.preventDefault();
        event.stopPropagation();
        dragZone.classList.add('hover');
        return false;
      });
      dragZone.addEventListener('dragleave', function (event) {
        event.preventDefault();
        event.stopPropagation();
        dragZone.classList.remove('hover');
        return false;
      });
      dragZone.addEventListener('drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (uploading) {
          return;
        }
        dragZone.classList.remove('hover');
        var files = event.target.files || event.dataTransfer.files;
        if (!files.length) {
          return;
        }
        var file = files[0];
        var data = new FormData();
        if (!file.type) {
          Joomla.renderMessages({
            error: [Joomla.Text._('PLG_INSTALLER_PACKAGEINSTALLER_NO_PACKAGE')]
          });
          return;
        }
        if (file.size > fileSizeMax) {
          Joomla.renderMessages({
            warning: [Joomla.Text._('COM_INSTALLER_MSG_WARNINGS_UPLOADFILETOOBIG')]
          });
          return;
        }
        data.append('install_package', file);
        data.append('installtype', 'upload');
        dragZone.setAttribute('data-state', 'uploading');
        progressBar.setAttribute('aria-valuenow', 0);
        uploading = true;
        progressBar.style.width = 0;
        percentage.textContent = '0';

        // Upload progress
        var progressCallback = function progressCallback(evt) {
          if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            var number = Math.round(percentComplete * 100);
            progressBar.style.width = number + "%";
            progressBar.setAttribute('aria-valuenow', number);
            percentage.textContent = "" + number;
            if (number === 100) {
              dragZone.setAttribute('data-state', 'installing');
            }
          }
        };
        Joomla.request({
          url: uploadUrl,
          method: 'POST',
          perform: true,
          data: data,
          onBefore: function onBefore(xhr) {
            xhr.upload.addEventListener('progress', progressCallback);
          },
          onSuccess: function onSuccess(response) {
            if (!response) {
              showError(response);
              return;
            }
            var res;
            try {
              res = JSON.parse(response);
            } catch (e) {
              showError(e);
              return;
            }
            if (!res.success && !res.data) {
              showError(res);
              return;
            }

            // Always redirect that can show message queue from session
            if (res.data.redirect) {
              window.location.href = res.data.redirect;
            } else {
              window.location.href = 'index.php?option=com_installer&view=install';
            }
          },
          onError: function onError(error) {
            uploading = false;
            if (error.status === 200) {
              var res = error.responseText || error.responseJSON;
              showError(res);
            } else {
              showError(error.statusText);
            }
          }
        });
      });
      document.getElementById('installbutton_package').addEventListener('click', function (event) {
        event.preventDefault();
        Joomla.submitbuttonpackage();
      });
    });
  })(Joomla);

})();
