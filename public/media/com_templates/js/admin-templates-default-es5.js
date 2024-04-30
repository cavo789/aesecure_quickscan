(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function () {
    document.addEventListener('DOMContentLoaded', function () {
      var folders = [].concat(document.querySelectorAll('.folder-url, .component-folder-url, .plugin-folder-url, .layout-folder-url'));
      var innerLists = [].concat(document.querySelectorAll('.folder ul, .component-folder ul, .plugin-folder ul, .layout-folder ul'));
      var openLists = [].concat(document.querySelectorAll('.show > ul'));
      var fileModalFolders = [].concat(document.querySelectorAll('#fileModal .folder-url'));
      var folderModalFolders = [].concat(document.querySelectorAll('#folderModal .folder-url'));
      // Hide all the folders when the page loads
      innerLists.forEach(function (innerList) {
        innerList.classList.add('hidden');
      });

      // Show all the lists in the path of an open file
      openLists.forEach(function (openList) {
        openList.classList.remove('hidden');
      });

      // Stop the default action of anchor tag on a click event and release the inner list
      folders.forEach(function (folder) {
        folder.addEventListener('click', function (event) {
          event.preventDefault();
          var list = event.currentTarget.parentNode.querySelector('ul');
          if (!list) {
            return;
          }
          if (!list.classList.contains('hidden')) {
            list.classList.add('hidden');
          } else {
            list.classList.remove('hidden');
          }
        });
      });

      // File modal tree selector
      fileModalFolders.forEach(function (fileModalFolder) {
        fileModalFolder.addEventListener('click', function (event) {
          event.preventDefault();
          fileModalFolders.forEach(function (fileModalFold) {
            fileModalFold.classList.remove('selected');
          });
          event.currentTarget.classList.add('selected');
          var ismedia = event.currentTarget.dataset.base === 'media' ? 1 : 0;
          [].concat(document.querySelectorAll('#fileModal input.address')).forEach(function (element) {
            element.value = event.currentTarget.getAttribute('data-id');
          });
          [].concat(document.querySelectorAll('#fileModal input[name="isMedia"]')).forEach(function (el) {
            el.value = ismedia;
          });
        });
      });

      // Folder modal tree selector
      folderModalFolders.forEach(function (folderModalFolder) {
        folderModalFolder.addEventListener('click', function (event) {
          event.preventDefault();
          folderModalFolders.forEach(function (folderModalFldr) {
            folderModalFldr.classList.remove('selected');
          });
          event.currentTarget.classList.add('selected');
          var ismedia = event.currentTarget.dataset.base === 'media' ? 1 : 0;
          [].concat(document.querySelectorAll('#folderModal input.address')).forEach(function (element) {
            element.value = event.currentTarget.getAttribute('data-id');
          });
          [].concat(document.querySelectorAll('#folderModal input[name="isMedia"]')).forEach(function (el) {
            el.value = ismedia;
          });
        });
      });
      var treeContainer = document.querySelector('#treeholder .treeselect');
      var listEls = [].concat(treeContainer.querySelectorAll('.folder.show'));
      var filePathEl = document.querySelector('p.lead.hidden.path');
      if (filePathEl) {
        var filePathTmp = document.querySelector('p.lead.hidden.path').innerText;
        if (filePathTmp && filePathTmp.charAt(0) === '/') {
          filePathTmp = filePathTmp.slice(1);
          filePathTmp = filePathTmp.split('/');
          filePathTmp = filePathTmp[filePathTmp.length - 1];
          listEls.forEach(function (element, index) {
            element.querySelector('a').classList.add('active');
            if (index === listEls.length - 1) {
              var parentUl = element.querySelector('ul');
              [].concat(parentUl.querySelectorAll('li')).forEach(function (liElement) {
                var aEl = liElement.querySelector('a');
                var spanEl = aEl.querySelector('span');
                if (spanEl && spanEl.innerText.trim()) {
                  aEl.classList.add('active');
                }
              });
            }
          });
        }
      }

      // Image cropper
      var image = document.getElementById('image-crop');
      if (image) {
        var width = document.getElementById('imageWidth').value;
        var height = document.getElementById('imageHeight').value;

        // eslint-disable-next-line no-new
        new window.Cropper(image, {
          viewMode: 1,
          scalable: true,
          zoomable: false,
          movable: false,
          dragMode: 'crop',
          cropBoxMovable: true,
          cropBoxResizable: true,
          autoCrop: true,
          autoCropArea: 1,
          background: true,
          center: true,
          minCanvasWidth: width,
          minCanvasHeight: height
        });
        image.addEventListener('crop', function (e) {
          document.getElementById('x').value = e.detail.x;
          document.getElementById('y').value = e.detail.y;
          document.getElementById('w').value = e.detail.width;
          document.getElementById('h').value = e.detail.height;
        });
      }
    });
  })();

})();
