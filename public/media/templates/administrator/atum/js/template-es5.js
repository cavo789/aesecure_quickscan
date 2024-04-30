(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  if (!Joomla) {
    throw new Error('Joomla API is not initialized');
  }
  var getCookie = function getCookie() {
    return document.cookie.length && document.cookie.split('; ').find(function (row) {
      return row.startsWith('atumSidebarState=');
    }).split('=')[1];
  };
  var mobile = window.matchMedia('(max-width: 992px)');
  var small = window.matchMedia('(max-width: 575.98px)');
  var tablet = window.matchMedia('(min-width: 576px) and (max-width:991.98px)');
  var menu = document.querySelector('.sidebar-menu');
  var sidebarNav = [].slice.call(document.querySelectorAll('.sidebar-nav'));
  var subhead = document.querySelector('#subhead-container');
  var wrapper = document.querySelector('.wrapper');
  var sidebarWrapper = document.querySelector('.sidebar-wrapper');
  var logo = document.querySelector('.logo');
  var isLogin = document.querySelector('body.com_login');
  var menuToggleIcon = document.getElementById('menu-collapse-icon');
  var navDropDownIcon = document.querySelectorAll('.nav-item.dropdown span[class*="icon-angle-"]');
  var headerTitleArea = document.querySelector('#header .header-title');
  var headerItemsArea = document.querySelector('#header .header-items');
  var headerExpandedItems = [].slice.call(headerItemsArea.children).filter(function (element) {
    return element.classList.contains('header-item');
  });
  var headerCondensedItemContainer = document.getElementById('header-more-items');
  var headerCondensedItems = [].slice.call(headerCondensedItemContainer.querySelectorAll('.header-dd-item'));
  var headerTitleWidth = headerTitleArea.getBoundingClientRect().width;
  var headerItemWidths = headerExpandedItems.map(function (element) {
    return element.getBoundingClientRect().width;
  });

  // Get the ellipsis button width
  headerCondensedItemContainer.classList.remove('d-none');
  // eslint-disable-next-line no-unused-expressions
  headerCondensedItemContainer.paddingTop;
  var ellipsisWidth = headerCondensedItemContainer.getBoundingClientRect().width;
  headerCondensedItemContainer.classList.add('d-none');

  /**
   * Shrink or extend the logo, depending on sidebar
   *
   * @param {string} [change] is the sidebar 'open' or 'closed'
   *
   * @since   4.0.0
   */
  function changeLogo(change) {
    if (!logo || isLogin) {
      return;
    }
    if (small.matches) {
      logo.classList.add('small');
      return;
    }
    var state = change || getCookie();
    if (state === 'closed') {
      logo.classList.add('small');
    } else {
      logo.classList.remove('small');
    }
    if (menuToggleIcon) {
      if (wrapper.classList.contains('closed')) {
        menuToggleIcon.classList.add('icon-toggle-on');
        menuToggleIcon.classList.remove('icon-toggle-off');
      } else {
        menuToggleIcon.classList.remove('icon-toggle-on');
        menuToggleIcon.classList.add('icon-toggle-off');
      }
    }
  }

  /**
   * toggle arrow icon between down and up depending on position of the nav header
   *
   * @param {string} [positionTop] set if the nav header positioned to the 'top' otherwise 'bottom'
   *
   * @since   4.0.0
   */
  function toggleArrowIcon(positionTop) {
    var remIcon = positionTop ? 'icon-angle-up' : 'icon-angle-down';
    var addIcon = positionTop ? 'icon-angle-down' : 'icon-angle-up';
    if (!navDropDownIcon) {
      return;
    }
    navDropDownIcon.forEach(function (item) {
      item.classList.remove(remIcon);
      item.classList.add(addIcon);
    });
  }

  /**
   *
   * @param {[]} arr
   * @returns {Number}
   */
  var getSum = function getSum(arr) {
    return arr.reduce(function (a, b) {
      return Number(a) + Number(b);
    }, 0);
  };

  /**
   * put elements that are too much in the header in a dropdown
   *
   * @since   4.0.0
   */
  function headerItemsInDropdown() {
    headerTitleWidth = headerTitleArea.getBoundingClientRect().width;
    var minViable = headerTitleWidth + ellipsisWidth;
    var totalHeaderItemWidths = 50 + getSum(headerItemWidths);
    if (headerTitleWidth + totalHeaderItemWidths < document.body.getBoundingClientRect().width) {
      headerExpandedItems.map(function (element) {
        return element.classList.remove('d-none');
      });
      headerCondensedItemContainer.classList.add('d-none');
    } else {
      headerCondensedItemContainer.classList.remove('d-none');
      headerCondensedItems.map(function (el) {
        return el.classList.add('d-none');
      });
      headerCondensedItemContainer.classList.remove('d-none');
      headerItemWidths.forEach(function (width, index) {
        var tempArr = headerItemWidths.slice(index, headerItemWidths.length);
        if (minViable + getSum(tempArr) < document.body.getBoundingClientRect().width) {
          return;
        }
        if (headerExpandedItems[index].children && !headerExpandedItems[index].children[0].classList.contains('dropdown')) {
          headerExpandedItems[index].classList.add('d-none');
          headerCondensedItems[index].classList.remove('d-none');
        }
      });
    }
  }

  /**
   * Change appearance for mobile devices
   *
   * @since   4.0.0
   */
  function setMobile() {
    if (small.matches) {
      toggleArrowIcon();
      if (menu) {
        wrapper.classList.remove('closed');
      }
    } else {
      toggleArrowIcon('top');
    }
    if (tablet.matches && menu) {
      wrapper.classList.add('closed');
    }
    if (small.matches) {
      sidebarNav.map(function (el) {
        return el.classList.add('collapse');
      });
      if (subhead) subhead.classList.add('collapse');
      if (sidebarWrapper) sidebarWrapper.classList.add('collapse');
    } else {
      sidebarNav.map(function (el) {
        return el.classList.remove('collapse');
      });
      if (subhead) subhead.classList.remove('collapse');
      if (sidebarWrapper) sidebarWrapper.classList.remove('collapse');
    }
    changeLogo('closed');
  }

  /**
   * Change appearance for mobile devices
   *
   * @since   4.0.0
   */
  function setDesktop() {
    if (!sidebarWrapper) {
      changeLogo('closed');
    } else {
      changeLogo(getCookie() || 'open');
      sidebarWrapper.classList.remove('collapse');
    }
    sidebarNav.map(function (el) {
      return el.classList.remove('collapse');
    });
    if (subhead) subhead.classList.remove('collapse');
    toggleArrowIcon('top');
  }

  /**
   * React on resizing window
   *
   * @since   4.0.0
   */
  function reactToResize() {
    window.addEventListener('resize', function () {
      if (mobile.matches) {
        setMobile();
      } else {
        setDesktop();
      }
      headerItemsInDropdown();
    });
  }

  /**
   * Subhead gets white background when user scrolls down
   *
   * @since   4.0.0
   */
  function subheadScrolling() {
    if (subhead) {
      document.addEventListener('scroll', function () {
        if (window.scrollY > 0) {
          subhead.classList.add('shadow-sm');
        } else {
          subhead.classList.remove('shadow-sm');
        }
      });
    }
  }

  // Initialize
  headerItemsInDropdown();
  reactToResize();
  subheadScrolling();
  if (small.matches) {
    changeLogo('closed');
    if (subhead) {
      subhead.classList.remove('show');
      subhead.classList.add('collapse');
    }
  }
  if (!navigator.cookieEnabled) {
    Joomla.renderMessages({
      error: [Joomla.Text._('JGLOBAL_WARNCOOKIES')]
    }, undefined, false, 6000);
  }
  window.addEventListener('joomla:menu-toggle', function (event) {
    headerItemsInDropdown();
    document.cookie = "atumSidebarState=" + event.detail + ";";
    if (mobile.matches) {
      changeLogo('closed');
    } else {
      changeLogo(event.detail);
    }
  });

})();
