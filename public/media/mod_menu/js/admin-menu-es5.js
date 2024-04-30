(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  var allMenus = document.querySelectorAll('ul.main-nav');
  allMenus.forEach(function (menu) {
    // eslint-disable-next-line no-new, no-undef
    new MetisMenu(menu);
  });
  var wrapper = document.getElementById('wrapper');
  var sidebar = document.getElementById('sidebar-wrapper');
  var menuToggleIcon = document.getElementById('menu-collapse-icon');

  // If the sidebar doesn't exist, for example, on edit views, then remove the "closed" class
  if (!sidebar) {
    wrapper.classList.remove('closed');
  }
  if (sidebar && !sidebar.getAttribute('data-hidden')) {
    // Sidebar
    var menuToggle = document.getElementById('menu-collapse');
    var firsts = [].slice.call(sidebar.querySelectorAll('.collapse-level-1'));

    // Apply 2nd level collapse
    firsts.forEach(function (first) {
      var seconds = [].slice.call(first.querySelectorAll('.collapse-level-1'));
      seconds.forEach(function (second) {
        if (second) {
          second.classList.remove('collapse-level-1');
          second.classList.add('collapse-level-2');
        }
      });
    });

    // Toggle menu
    menuToggle.addEventListener('click', function (event) {
      event.preventDefault();
      wrapper.classList.toggle('closed');
      menuToggleIcon.classList.toggle('icon-toggle-on');
      menuToggleIcon.classList.toggle('icon-toggle-off');
      var listItems = [].slice.call(document.querySelectorAll('.main-nav > li'));
      listItems.forEach(function (item) {
        item.classList.remove('open');
      });
      var elem = document.querySelector('.child-open');
      if (elem) {
        elem.classList.remove('child-open');
      }
      window.dispatchEvent(new CustomEvent('joomla:menu-toggle', {
        detail: wrapper.classList.contains('closed') ? 'closed' : 'open',
        bubbles: true,
        cancelable: true
      }));
    });

    // Sidebar Nav
    var allLinks = wrapper.querySelectorAll('a.no-dropdown, a.collapse-arrow, .menu-dashboard > a');
    var currentUrl = window.location.href;
    var mainNav = document.querySelector('ul.main-nav');
    var menuParents = [].slice.call(mainNav.querySelectorAll('li.parent > a'));
    var subMenusClose = [].slice.call(mainNav.querySelectorAll('li.parent .close'));

    // Set active class
    allLinks.forEach(function (link) {
      if (!link.href.match(/index\.php$/) && currentUrl.indexOf(link.href) === 0 || link.href.match(/index\.php$/) && currentUrl.match(/index\.php$/)) {
        link.setAttribute('aria-current', 'page');
        link.classList.add('mm-active');

        // Auto Expand Levels
        if (!link.parentNode.classList.contains('parent')) {
          var firstLevel = link.closest('.collapse-level-1');
          var secondLevel = link.closest('.collapse-level-2');
          if (firstLevel) firstLevel.parentNode.classList.add('mm-active');
          if (firstLevel) firstLevel.classList.add('mm-show');
          if (secondLevel) secondLevel.parentNode.classList.add('mm-active');
          if (secondLevel) secondLevel.classList.add('mm-show');
        }
      }
    });

    // Child open toggle
    var openToggle = function openToggle(_ref) {
      var currentTarget = _ref.currentTarget;
      var menuItem = currentTarget.parentNode;
      if (menuItem.tagName.toLowerCase() === 'span') {
        menuItem = currentTarget.parentNode.parentNode;
      }
      if (menuItem.classList.contains('open')) {
        mainNav.classList.remove('child-open');
        menuItem.classList.remove('open');
      } else {
        var siblings = [].slice.call(menuItem.parentNode.children);
        siblings.forEach(function (sibling) {
          sibling.classList.remove('open');
        });
        wrapper.classList.remove('closed');
        if (menuToggleIcon.classList.contains('icon-toggle-off')) {
          menuToggleIcon.classList.toggle('icon-toggle-off');
          menuToggleIcon.classList.toggle('icon-toggle-on');
        }
        mainNav.classList.add('child-open');
        if (menuItem.parentNode.classList.contains('main-nav')) {
          menuItem.classList.add('open');
        }
      }
      window.dispatchEvent(new CustomEvent('joomla:menu-toggle', {
        detail: 'open',
        bubbles: true,
        cancelable: true
      }));
    };
    menuParents.forEach(function (parent) {
      parent.addEventListener('click', openToggle);
      parent.addEventListener('keyup', openToggle);
    });

    // Menu close
    subMenusClose.forEach(function (subMenu) {
      subMenu.addEventListener('click', function () {
        var menuChildsOpen = [].slice.call(mainNav.querySelectorAll('.open'));
        menuChildsOpen.forEach(function (menuChild) {
          menuChild.classList.remove('open');
        });
        mainNav.classList.remove('child-open');
      });
    });
  }

})();
