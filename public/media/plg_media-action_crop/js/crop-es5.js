(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  /* global Cropper */
  var formElements;
  var activated = false;
  var instance;
  var addListeners = function addListeners() {
    formElements.cropX.addEventListener('change', function (_ref) {
      var currentTarget = _ref.currentTarget;
      instance.setData({
        x: parseInt(currentTarget.value, 10)
      });
    });
    formElements.cropY.addEventListener('change', function (_ref2) {
      var currentTarget = _ref2.currentTarget;
      instance.setData({
        y: parseInt(currentTarget.value, 10)
      });
    });
    formElements.cropWidth.addEventListener('change', function (_ref3) {
      var currentTarget = _ref3.currentTarget;
      instance.setData({
        width: parseInt(currentTarget.value, 10)
      });
    });
    formElements.cropHeight.addEventListener('change', function (_ref4) {
      var currentTarget = _ref4.currentTarget;
      instance.setData({
        height: parseInt(currentTarget.value, 10)
      });
    });
    formElements.aspectRatio.addEventListener('change', function (_ref5) {
      var currentTarget = _ref5.currentTarget;
      instance.setAspectRatio(currentTarget.value);
    });
    activated = true;
  };
  var init = function init(image) {
    // Set default aspect ratio after numeric check, option has a dummy value
    var defaultCropFactor = image.naturalWidth / image.naturalHeight;
    if (!Number.isNaN(defaultCropFactor) && Number.isFinite(defaultCropFactor)) {
      formElements.cropAspectRatioOption.value = defaultCropFactor;
    }

    // Initiate the cropper
    instance = new Cropper(image, {
      viewMode: 1,
      responsive: true,
      restore: true,
      autoCrop: true,
      movable: false,
      zoomable: false,
      rotatable: false,
      autoCropArea: 1,
      // scalable: false,
      crop: function crop(e) {
        formElements.cropX.value = Math.round(e.detail.x);
        formElements.cropY.value = Math.round(e.detail.y);
        formElements.cropWidth.value = Math.round(e.detail.width);
        formElements.cropHeight.value = Math.round(e.detail.height);
        var format = Joomla.MediaManager.Edit.original.extension === 'jpg' ? 'jpeg' : Joomla.MediaManager.Edit.original.extension;
        var quality = formElements.cropQuality.value;

        // Update the store
        Joomla.MediaManager.Edit.current.contents = this.cropper.getCroppedCanvas().toDataURL("image/" + format, quality);

        // Notify the app that a change has been made
        window.dispatchEvent(new Event('mediaManager.history.point'));
      }
    });

    // Add listeners
    if (!activated) {
      addListeners();
    }
    instance.setAspectRatio(formElements.cropAspectRatioOption.value);
  };

  // Register the Events
  window.addEventListener('media-manager-edit-init', function () {
    formElements = {
      aspectRatio: document.getElementById('jform_aspectRatio'),
      cropHeight: document.getElementById('jform_crop_height'),
      cropWidth: document.getElementById('jform_crop_width'),
      cropY: document.getElementById('jform_crop_y'),
      cropX: document.getElementById('jform_crop_x'),
      cropQuality: document.getElementById('jform_crop_quality'),
      cropAspectRatioOption: document.querySelector('.crop-aspect-ratio-option')
    };
    Joomla.MediaManager.Edit.plugins.crop = {
      Activate: function Activate(image) {
        return new Promise(function (resolve /* , reject */) {
          init(image);
          resolve();
        });
      },
      Deactivate: function Deactivate(image) {
        return new Promise(function (resolve /* , reject */) {
          if (image.cropper) {
            image.cropper.destroy();
            instance = null;
          }
          resolve();
        });
      }
    };
  }, {
    once: true
  });

})();
