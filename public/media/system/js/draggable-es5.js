(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  // The container where the draggable will be enabled
  var url;
  var direction;
  var isNested;
  var dragElementIndex;
  var dropElementIndex;
  var container = document.querySelector('.js-draggable');
  var form;
  var formData;
  if (container) {
    /** The script expects a form with a class js-form
     *  A table with the tbody with a class js-draggable
     *                         with a data-url with the ajax request end point and
     *                         with a data-direction for asc/desc
     */
    url = container.dataset.url;
    direction = container.dataset.direction;
    isNested = container.dataset.nested;
  } else if (Joomla.getOptions('draggable-list')) {
    var options = Joomla.getOptions('draggable-list');
    container = document.querySelector(options.id);
    /**
     * This is here to make the transition to new forms easier.
     */
    if (!container.classList.contains('js-draggable')) {
      container.classList.add('js-draggable');
    }
    url = options.url;
    direction = options.direction;
    isNested = options.nested;
  }
  if (container) {
    // Get the form
    form = container.closest('form');
    // Get the form data
    formData = new FormData(form);
    formData.delete('task');
    formData.delete('order[]');

    // IOS 10 BUG
    document.addEventListener('touchstart', function () {}, false);
    var getOrderData = function getOrderData(rows, inputRows, dragIndex, dropIndex) {
      var i;
      var result = [];

      // Element is moved down
      if (dragIndex < dropIndex) {
        rows[dropIndex].value = rows[dropIndex - 1].value;
        for (i = dragIndex; i < dropIndex; i += 1) {
          if (direction === 'asc') {
            rows[i].value = parseInt(rows[i].value, 10) - 1;
          } else {
            rows[i].value = parseInt(rows[i].value, 10) + 1;
          }
        }
      } else {
        // Element is moved up
        rows[dropIndex].value = rows[dropIndex + 1].value;
        for (i = dropIndex + 1; i <= dragIndex; i += 1) {
          if (direction === 'asc') {
            rows[i].value = parseInt(rows[i].value, 10) + 1;
          } else {
            rows[i].value = parseInt(rows[i].value, 10) - 1;
          }
        }
      }
      for (i = 0; i < rows.length - 1; i += 1) {
        result.push("order[]=" + encodeURIComponent(rows[i].value));
        result.push("cid[]=" + encodeURIComponent(inputRows[i].value));
      }
      return result;
    };
    var rearrangeChildren = function rearrangeChildren($parent) {
      if (!$parent.dataset.itemId) {
        return;
      }
      var parentId = $parent.dataset.itemId;
      // Get children list. Each child row should have
      // an attribute data-parents=" 1 2 3" where the number is id of parent
      var $children = container.querySelectorAll("tr[data-parents~=\"" + parentId + "\"]");
      if ($children.length) {
        $parent.after.apply($parent, $children);
      }
    };
    var saveTheOrder = function saveTheOrder(el) {
      var orderSelector;
      var inputSelector;
      var rowSelector;
      var groupId = el.dataset.draggableGroup;
      if (groupId) {
        rowSelector = "tr[data-draggable-group=\"" + groupId + "\"]";
        orderSelector = "[data-draggable-group=\"" + groupId + "\"] [name=\"order[]\"]";
        inputSelector = "[data-draggable-group=\"" + groupId + "\"] [name=\"cid[]\"]";
      } else {
        rowSelector = 'tr';
        orderSelector = '[name="order[]"]';
        inputSelector = '[name="cid[]"]';
      }
      var rowElements = [].slice.call(container.querySelectorAll(rowSelector));
      var rows = [].slice.call(container.querySelectorAll(orderSelector));
      var inputRows = [].slice.call(container.querySelectorAll(inputSelector));
      dropElementIndex = rowElements.indexOf(el);
      if (url) {
        // Detach task field if exists
        var task = document.querySelector('[name="task"]');

        // Detach task field if exists
        if (task) {
          task.setAttribute('name', 'some__Temporary__Name__');
        }

        // Prepare the options
        var ajaxOptions = {
          url: url,
          method: 'POST',
          data: new URLSearchParams(formData).toString() + "&" + getOrderData(rows, inputRows, dragElementIndex, dropElementIndex).join('&'),
          perform: true
        };
        Joomla.request(ajaxOptions);

        // Re-Append original task field
        if (task) {
          task.setAttribute('name', 'task');
        }
      }

      // Update positions for a children of the moved item
      rearrangeChildren(el);
    };

    // eslint-disable-next-line no-undef
    dragula([container], {
      // Y axis is considered when determining where an element would be dropped
      direction: 'vertical',
      // elements are moved by default, not copied
      copy: false,
      // elements in copy-source containers can be reordered
      // copySortSource: true,
      // spilling will put the element back where it was dragged from, if this is true
      revertOnSpill: true,
      // spilling will `.remove` the element, if this is true
      // removeOnSpill: false,
      accepts: function accepts(el, target, source, sibling) {
        if (isNested) {
          if (sibling !== null) {
            return sibling.dataset.draggableGroup && sibling.dataset.draggableGroup === el.dataset.draggableGroup;
          }
          return sibling === null || sibling && sibling.tagName.toLowerCase() === 'tr';
        }
        return sibling === null || sibling && sibling.tagName.toLowerCase() === 'tr';
      },
      mirrorContainer: container
    }).on('drag', function (el) {
      var rowSelector;
      var groupId = el.dataset.draggableGroup;
      if (groupId) {
        rowSelector = "tr[data-draggable-group=\"" + groupId + "\"]";
      } else {
        rowSelector = 'tr';
      }
      var rowElements = [].slice.call(container.querySelectorAll(rowSelector));
      dragElementIndex = rowElements.indexOf(el);
    }).on('drop', function (el) {
      saveTheOrder(el);
    });
  }

})();
