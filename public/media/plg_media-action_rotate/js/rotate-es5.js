(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  var activated = false;

  // Update image
  var rotate = function rotate(angle, image) {
    // The canvas where we will rotate the image
    var canvas = document.createElement('canvas');

    // Pseudo rectangle calculation
    if (angle >= 0 && angle < 45 || angle >= 135 && angle < 225 || angle >= 315 && angle <= 360) {
      canvas.width = image.naturalWidth;
      canvas.height = image.naturalHeight;
    } else {
      // swap
      canvas.width = image.naturalHeight;
      canvas.height = image.naturalWidth;
    }
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(angle * Math.PI / 180);
    ctx.drawImage(image, -image.naturalWidth / 2, -image.naturalHeight / 2);

    // The format
    var format = Joomla.MediaManager.Edit.original.extension === 'jpg' ? 'jpeg' : 'jpg';

    // The quality
    var quality = document.getElementById('jform_rotate_quality').value;

    // Creating the data from the canvas
    Joomla.MediaManager.Edit.current.contents = canvas.toDataURL("image/" + format, quality);

    // Updating the preview element
    image.width = canvas.width;
    image.height = canvas.height;
    image.src = '';
    requestAnimationFrame(function () {
      return requestAnimationFrame(function () {
        image.src = Joomla.MediaManager.Edit.current.contents;
      });
    });

    // Update the angle input box
    document.getElementById('jform_rotate_a').value = angle;

    // Notify the app that a change has been made
    window.dispatchEvent(new Event('mediaManager.history.point'));
    canvas = null;
  };
  var initRotate = function initRotate(image) {
    if (!activated) {
      // The number input listener
      document.getElementById('jform_rotate_a').addEventListener('change', function (_ref) {
        var target = _ref.target;
        rotate(parseInt(target.value, 10), image);
        target.value = 0;
        // Deselect all buttons
        [].slice.call(document.querySelectorAll('#jform_rotate_distinct label')).forEach(function (element) {
          element.classList.remove('active');
          element.classList.remove('focus');
        });
      });

      // The 90 degree rotate buttons listeners
      [].slice.call(document.querySelectorAll('#jform_rotate_distinct [type=radio]')).forEach(function (element) {
        element.addEventListener('click', function (_ref2) {
          var target = _ref2.target;
          rotate(parseInt(target.value, 10), image);

          // Deselect all buttons
          [].slice.call(document.querySelectorAll('#jform_rotate_distinct label')).forEach(function (el) {
            el.classList.remove('active');
            el.classList.remove('focus');
          });
        });
      });
      activated = true;
    }
  };
  window.addEventListener('media-manager-edit-init', function () {
    // Register the Events
    Joomla.MediaManager.Edit.plugins.rotate = {
      Activate: function Activate(image) {
        return new Promise(function (resolve) {
          // Initialize
          initRotate(image);
          resolve();
        });
      },
      Deactivate: function Deactivate() {
        return new Promise(function (resolve) {
          resolve();
        });
      } /* image */
    };
  }, {
    once: true
  });

})();
