(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (Joomla) {
    Joomla.submitbutton = function (task, type) {
      if (task === 'item.setType' || task === 'item.setMenuType') {
        if (task === 'item.setType') {
          var list = [].slice.call(document.querySelectorAll('#item-form input[name="jform[type]"]'));
          list.forEach(function (item) {
            item.value = type;
          });
          document.getElementById('fieldtype').value = 'type';
        } else {
          var _list = [].slice.call(document.querySelectorAll('#item-form input[name="jform[menutype]"]'));
          _list.forEach(function (item) {
            item.value = type;
          });
        }
        Joomla.submitform('item.setType', document.getElementById('item-form'));
      } else if (task === 'item.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
        Joomla.submitform(task, document.getElementById('item-form'));
      } else {
        // special case for modal popups validation response
        var _list2 = [].slice.call(document.querySelectorAll('#item-form .modal-value.invalid'));
        _list2.forEach(function (field) {
          var idReversed = field.getAttribute('id').split('').reverse().join('');
          var separatorLocation = idReversed.indexOf('_');
          var nameId = idReversed.substr(separatorLocation).split('').reverse().join('') + "name";
          document.getElementById(nameId).classList.add('invalid');
        });
      }
    };
    var onChange = function onChange(_ref) {
      var target = _ref.target;
      var menuType = target.value;
      Joomla.request({
        url: "index.php?option=com_menus&task=item.getParentItem&menutype=" + menuType,
        headers: {
          'Content-Type': 'application/json'
        },
        onSuccess: function onSuccess(response) {
          var data = JSON.parse(response);
          var fancySelect = document.getElementById('jform_parent_id').closest('joomla-field-fancy-select');
          fancySelect.choicesInstance.clearChoices();
          fancySelect.choicesInstance.setChoices([{
            id: '1',
            text: Joomla.Text._('JGLOBAL_ROOT_PARENT')
          }], 'id', 'text', false);
          data.forEach(function (value) {
            var option = {};
            option.innerText = value.title;
            option.id = value.id;
            fancySelect.choicesInstance.setChoices([option], 'id', 'innerText', false);
          });
          fancySelect.choicesInstance.setChoiceByValue('1');
          var newEvent = document.createEvent('HTMLEvents');
          newEvent.initEvent('change', true, false);
          document.getElementById('jform_parent_id').dispatchEvent(newEvent);
        },
        onError: function onError(xhr) {
          Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr));
        }
      });
    };
    if (!Joomla || typeof Joomla.request !== 'function') {
      throw new Error('core.js was not properly initialised');
    }
    var element = document.getElementById('jform_menutype');
    if (element) {
      element.addEventListener('change', onChange);
    }

    // Menu type Login Form specific
    document.getElementById('item-form').addEventListener('submit', function () {
      if (document.getElementById('jform_params_login_redirect_url') && document.getElementById('jform_params_logout_redirect_url')) {
        // Login
        if (!document.getElementById('jform_params_login_redirect_url').closest('.control-group').classList.contains('hidden')) {
          document.getElementById('jform_params_login_redirect_menuitem_id').value = '';
        }
        if (!document.getElementById('jform_params_login_redirect_menuitem_name').closest('.control-group').classList.contains('hidden')) {
          document.getElementById('jform_params_login_redirect_url').value = '';
        }

        // Logout
        if (!document.getElementById('jform_params_logout_redirect_url').closest('.control-group').classList.contains('hidden')) {
          document.getElementById('jform_params_logout_redirect_menuitem_id').value = '';
        }
        if (!document.getElementById('jform_params_logout_redirect_menuitem_id').closest('.control-group').classList.contains('hidden')) {
          document.getElementById('jform_params_logout_redirect_url').value = '';
        }
      }
    });
  })(Joomla);

})();
