(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function () {
    function topLevelMouseOver(el, settings) {
      var ulChild = el.querySelector('ul');
      if (ulChild) {
        ulChild.setAttribute('aria-hidden', 'false');
        ulChild.classList.add(settings.menuHoverClass);
      }
    }
    function topLevelMouseOut(el, settings) {
      var ulChild = el.querySelector('ul');
      if (ulChild) {
        ulChild.setAttribute('aria-hidden', 'true');
        ulChild.classList.remove(settings.menuHoverClass);
      }
    }
    function setupNavigation(nav) {
      var settings = {
        menuHoverClass: 'show-menu',
        dir: 'ltr'
      };
      var topLevelChilds = nav.querySelectorAll(':scope > li');

      // Set tabIndex to -1 so that top_level_childs can't receive focus until menu is open
      topLevelChilds.forEach(function (topLevelEl) {
        var linkEl = topLevelEl.querySelector('a');
        if (linkEl) {
          linkEl.tabIndex = '0';
          linkEl.addEventListener('mouseover', topLevelMouseOver(topLevelEl, settings));
          linkEl.addEventListener('mouseout', topLevelMouseOut(topLevelEl, settings));
        }
        var spanEl = topLevelEl.querySelector('span');
        if (spanEl) {
          spanEl.tabIndex = '0';
          spanEl.addEventListener('mouseover', topLevelMouseOver(topLevelEl, settings));
          spanEl.addEventListener('mouseout', topLevelMouseOut(topLevelEl, settings));
        }
        topLevelEl.addEventListener('mouseover', function (_ref) {
          var target = _ref.target;
          var ulChild = target.querySelector('ul');
          if (ulChild) {
            ulChild.setAttribute('aria-hidden', 'false');
            ulChild.classList.add(settings.menuHoverClass);
          }
        });
        topLevelEl.addEventListener('mouseout', function (_ref2) {
          var target = _ref2.target;
          var ulChild = target.querySelector('ul');
          if (ulChild) {
            ulChild.setAttribute('aria-hidden', 'true');
            ulChild.classList.remove(settings.menuHoverClass);
          }
        });
        topLevelEl.addEventListener('focus', function (_ref3) {
          var target = _ref3.target;
          var ulChild = target.querySelector('ul');
          if (ulChild) {
            ulChild.setAttribute('aria-hidden', 'true');
            ulChild.classList.add(settings.menuHoverClass);
          }
        });
        topLevelEl.addEventListener('blur', function (_ref4) {
          var target = _ref4.target;
          var ulChild = target.querySelector('ul');
          if (ulChild) {
            ulChild.setAttribute('aria-hidden', 'false');
            ulChild.classList.remove(settings.menuHoverClass);
          }
        });
        topLevelEl.addEventListener('keydown', function (event) {
          var keyName = event.key;
          var curEl = event.target;
          var curLiEl = curEl.parentElement;
          var curUlEl = curLiEl.parentElement;
          var prevLiEl = curLiEl.previousElementSibling;
          var nextLiEl = curLiEl.nextElementSibling;
          if (!prevLiEl) {
            prevLiEl = curUlEl.children[curUlEl.children.length - 1];
          }
          if (!nextLiEl) {
            var _curUlEl$children = curUlEl.children;
            nextLiEl = _curUlEl$children[0];
          }
          switch (keyName) {
            case 'ArrowLeft':
              event.preventDefault();
              if (settings.dir === 'rtl') {
                nextLiEl.children[0].focus();
              } else {
                prevLiEl.children[0].focus();
              }
              break;
            case 'ArrowRight':
              event.preventDefault();
              if (settings.dir === 'rtl') {
                prevLiEl.children[0].focus();
              } else {
                nextLiEl.children[0].focus();
              }
              break;
            case 'ArrowUp':
              {
                event.preventDefault();
                var parent = curLiEl.parentElement.parentElement;
                if (parent.nodeName === 'LI') {
                  parent.children[0].focus();
                } else {
                  prevLiEl.children[0].focus();
                }
                break;
              }
            case 'ArrowDown':
              event.preventDefault();
              if (curLiEl.classList.contains('parent')) {
                var child = curLiEl.querySelector('ul');
                if (child != null) {
                  var childLi = child.querySelector('li');
                  childLi.children[0].focus();
                } else {
                  nextLiEl.children[0].focus();
                }
              } else {
                nextLiEl.children[0].focus();
              }
              break;
          }
        });
      });
    }
    document.addEventListener('DOMContentLoaded', function () {
      var navs = document.querySelectorAll('.nav');
      [].forEach.call(navs, function (nav) {
        setupNavigation(nav);
      });
    });
  })();

})();
