(function () {
  'use strict';

  /*
   * Apply the predefined action for the current element
   *
   * @param {Event} event
   */
  function gridItemAction(event) {
    var item = event.target;
    if (item.nodeName === 'SPAN' && ['A', 'BUTTON'].includes(item.parentNode.nodeName)) {
      item = item.parentNode;
    }
    if (item.nodeName === 'A') {
      event.preventDefault();
    }
    if (item.hasAttribute('disabled') || !item.hasAttribute('data-item-task')) {
      return;
    }
    var itemId = item.dataset.itemId;
    var itemTask = item.dataset.itemTask;
    var itemFormId = item.dataset.itemFormId;
    if (itemFormId) {
      Joomla.listItemTask(itemId, itemTask, itemFormId);
    } else {
      Joomla.listItemTask(itemId, itemTask);
    }
  }

  /*
   * Apply the transition state for the current element
   *
   * @param {Event} event
   */
  function gridTransitionItemAction(event) {
    var item = event.target;
    if (item.nodeName !== 'SELECT' || item.hasAttribute('disabled')) {
      return;
    }
    var itemId = item.dataset.itemId;
    var itemTask = item.dataset.itemTask;
    var itemFormId = item.dataset.itemFormId;
    item.form.transition_id.value = item.value;
    if (itemFormId) {
      Joomla.listItemTask(itemId, itemTask, itemFormId);
    } else {
      Joomla.listItemTask(itemId, itemTask);
    }
  }

  /*
   * Apply the transition state for the current element
   *
   * @param {Event} event
   */
  function gridTransitionButtonAction(event) {
    var item = event.target;
    if (item.nodeName === 'SPAN' && item.parentNode.nodeName === 'BUTTON') {
      item = item.parentNode;
    }
    if (item.hasAttribute('disabled')) {
      return;
    }
    Joomla.toggleAllNextElements(item, 'd-none');
  }

  /*
   * Switch the check state for the current element
   *
   * @param {Event} event
   */
  function applyIsChecked(event) {
    var item = event.target;
    var itemFormId = item.dataset.itemFormId || '';
    if (itemFormId) {
      Joomla.isChecked(item.checked, itemFormId);
    } else {
      Joomla.isChecked(item.checked);
    }
  }

  /*
   * Set up an interactive list elements
   *
   * @param {Event} event
   */
  var setup = function setup(_ref) {
    var target = _ref.target;
    target.querySelectorAll('.js-grid-item-check-all').forEach(function (element) {
      return element.addEventListener('click', function (event) {
        return Joomla.checkAll(event.target);
      });
    });
    target.querySelectorAll('.js-grid-item-is-checked').forEach(function (element) {
      return element.addEventListener('click', applyIsChecked);
    });
    target.querySelectorAll('.js-grid-item-action').forEach(function (element) {
      return element.addEventListener('click', gridItemAction);
    });
    target.querySelectorAll('.js-grid-item-transition-action').forEach(function (element) {
      return element.addEventListener('change', gridTransitionItemAction);
    });
    target.querySelectorAll('.js-grid-button-transition-action').forEach(function (element) {
      return element.addEventListener('click', gridTransitionButtonAction);
    });
  };
  setup({
    target: document
  });
  document.addEventListener('joomla:updated', setup);

})();
