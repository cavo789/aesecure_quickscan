(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  /**
   * Debounce
   * https://gist.github.com/nmsdvid/8807205
   *
   * @param { function } callback  The callback function to be executed
   * @param { int }  time      The time to wait before firing the callback
   * @param { int }  interval  The interval
   */
  // eslint-disable-next-line no-param-reassign, no-return-assign, default-param-last
  var debounce = function debounce(callback, time, interval) {
    if (time === void 0) {
      time = 250;
    }
    return function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      return clearTimeout(interval, interval = setTimeout.apply(void 0, [callback, time].concat(args)));
    };
  };
  (function (window, document, Joomla) {
    Joomla.unpublishModule = function (element) {
      // Get variables
      var baseUrl = 'index.php?option=com_modules&task=modules.unpublish&format=json';
      var id = element.getAttribute('data-module-id');
      Joomla.request({
        url: baseUrl + "&cid=" + id,
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        onSuccess: function onSuccess() {
          var wrapper = element.closest('.module-wrapper');
          wrapper.parentNode.removeChild(wrapper);
          Joomla.renderMessages({
            message: [Joomla.Text._('COM_CPANEL_UNPUBLISH_MODULE_SUCCESS')]
          });
        },
        onError: function onError() {
          Joomla.renderMessages({
            error: [Joomla.Text._('COM_CPANEL_UNPUBLISH_MODULE_ERROR')]
          });
        }
      });
    };
    var onBoot = function onBoot() {
      var cpanelModules = document.getElementById('content');
      if (cpanelModules) {
        var links = [].slice.call(cpanelModules.querySelectorAll('.unpublish-module'));
        links.forEach(function (link) {
          link.addEventListener('click', function (_ref) {
            var target = _ref.target;
            return Joomla.unpublishModule(target);
          });
        });
      }

      // Cleanup
      document.removeEventListener('DOMContentLoaded', onBoot);
    };

    // Initialise
    document.addEventListener('DOMContentLoaded', onBoot);

    // Masonry layout for cpanel cards
    var MasonryLayout = {
      $gridBox: null,
      gridAutoRows: 0,
      gridRowGap: 10,
      // Calculate "grid-row-end" property
      resizeGridItem: function resizeGridItem($cell, rowHeight, rowGap) {
        var $content = $cell.querySelector('.card');
        if ($content) {
          var contentHeight = $content.getBoundingClientRect().height + rowGap;
          var rowSpan = Math.ceil(contentHeight / (rowHeight + rowGap));
          $cell.style.gridRowEnd = "span " + rowSpan;
        }
      },
      // Check a size of every cell in the grid
      resizeAllGridItems: function resizeAllGridItems() {
        var _this = this;
        var $gridCells = [].slice.call(this.$gridBox.children);
        $gridCells.forEach(function ($cell) {
          _this.resizeGridItem($cell, _this.gridAutoRows, _this.gridRowGap);
        });
      },
      initialise: function initialise() {
        var _this2 = this;
        this.$gridBox = document.querySelector('#cpanel-modules .card-columns');
        var gridStyle = window.getComputedStyle(this.$gridBox);
        this.gridAutoRows = parseInt(gridStyle.getPropertyValue('grid-auto-rows'), 10) || this.gridAutoRows;
        this.gridRowGap = parseInt(gridStyle.getPropertyValue('grid-row-gap'), 10) || this.gridRowGap;
        this.resizeAllGridItems();

        // Recheck the layout after all content (fonts and images) is loaded.
        window.addEventListener('load', function () {
          return _this2.resizeAllGridItems();
        });

        // Recheck the layout when the menu is toggled
        window.addEventListener('joomla:menu-toggle', function () {
          // 300ms is animation time, need to wait for the animation to end
          setTimeout(function () {
            return _this2.resizeAllGridItems();
          }, 330);
        });

        // Watch on window resize
        window.addEventListener('resize', debounce(function () {
          return _this2.resizeAllGridItems();
        }, 50));
      }
    };

    // Initialise Masonry layout at the very beginning, to avoid jumping.
    // We can do this because the script is deferred.
    MasonryLayout.initialise();
  })(window, document, window.Joomla);

})();
