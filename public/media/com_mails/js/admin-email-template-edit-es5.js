(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */

  (function (document, Joomla) {
    var EmailTemplateEdit = /*#__PURE__*/function () {
      function EmailTemplateEdit(form, options) {
        // Set elements
        this.form = form;
        this.inputSubject = this.form.querySelector('#jform_subject');
        this.inputBody = this.form.querySelector('#jform_body');
        this.inputHtmlBody = this.form.querySelector('#jform_htmlbody');

        // Set options
        this.templateData = options && options.templateData ? options.templateData : {};

        // Add back reference
        this.form.EmailTemplateEdit = this;
      }
      var _proto = EmailTemplateEdit.prototype;
      _proto.setBodyValue = function setBodyValue(value) {
        if (Joomla.editors.instances[this.inputBody.id]) {
          Joomla.editors.instances[this.inputBody.id].setValue(value);
        } else {
          this.inputBody.value = value;
        }
      };
      _proto.setHtmlBodyValue = function setHtmlBodyValue(value) {
        if (Joomla.editors.instances[this.inputHtmlBody.id]) {
          Joomla.editors.instances[this.inputHtmlBody.id].setValue(value);
        } else {
          this.inputHtmlBody.value = value;
        }
      };
      _proto.insertTag = function insertTag(tag, targetField) {
        if (!tag) return false;
        var input;
        switch (targetField) {
          case 'body':
            input = this.inputBody;
            break;
          case 'htmlbody':
            input = this.inputHtmlBody;
            break;
          default:
            return false;
        }
        if (Joomla.editors.instances[input.id]) {
          Joomla.editors.instances[input.id].replaceSelection(tag);
        } else {
          input.value += " " + tag;
        }
        return true;
      };
      _proto.bindListeners = function bindListeners() {
        var _this = this;
        document.querySelector('#btnResetSubject').addEventListener('click', function (event) {
          event.preventDefault();
          _this.inputSubject.value = _this.templateData.subject ? _this.templateData.subject : '';
        });
        var btnResetBody = document.querySelector('#btnResetBody');
        if (btnResetBody) {
          btnResetBody.addEventListener('click', function (event) {
            event.preventDefault();
            _this.setBodyValue(_this.templateData.body ? _this.templateData.body : '');
          });
        }
        var btnResetHtmlBody = document.querySelector('#btnResetHtmlBody');
        if (btnResetHtmlBody) {
          btnResetHtmlBody.addEventListener('click', function (event) {
            event.preventDefault();
            _this.setHtmlBodyValue(_this.templateData.htmlbody ? _this.templateData.htmlbody : '');
          });
        }

        // Buttons for inserting a tag
        this.form.querySelectorAll('.edit-action-add-tag').forEach(function (button) {
          button.addEventListener('click', function (event) {
            event.preventDefault();
            var el = event.target;
            _this.insertTag(el.dataset.tag, el.dataset.target);
          });
        });
      };
      return EmailTemplateEdit;
    }();
    document.addEventListener('DOMContentLoaded', function () {
      var editor = new EmailTemplateEdit(document.getElementById('item-form'), Joomla.getOptions('com_mails'));
      editor.bindListeners();
    });
  })(document, Joomla);

})();
