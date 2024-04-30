(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (Awesomplete, Joomla, window, document) {
    if (!Joomla) {
      throw new Error('core.js was not properly initialised');
    }

    // Handle the autocomplete
    var onInputChange = function onInputChange(_ref) {
      var target = _ref.target;
      if (target.value.length > 1) {
        target.awesomplete.list = [];
        Joomla.request({
          url: Joomla.getOptions('finder-search').url + "&q=" + target.value,
          promise: true
        }).then(function (xhr) {
          var response;
          try {
            response = JSON.parse(xhr.responseText);
          } catch (e) {
            Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr, 'parsererror'));
            return;
          }
          if (Object.prototype.toString.call(response.suggestions) === '[object Array]') {
            target.awesomplete.list = response.suggestions;
          }
        }).catch(function (xhr) {
          Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr));
        });
      }
    };

    // Handle the submit
    var onSubmit = function onSubmit(event) {
      event.stopPropagation();
      var advanced = event.target.querySelector('.js-finder-advanced');

      // Disable select boxes with no value selected.
      if (advanced) {
        var fields = [].slice.call(advanced.querySelectorAll('select'));
        fields.forEach(function (field) {
          if (!field.value) {
            field.setAttribute('disabled', 'disabled');
          }
        });
      }
    };

    // Submits the form programmatically
    var submitForm = function submitForm(event) {
      var form = event.target.closest('form');
      if (form) {
        form.submit();
      }
    };

    // The boot sequence
    var onBoot = function onBoot() {
      var searchWords = [].slice.call(document.querySelectorAll('.js-finder-search-query'));
      searchWords.forEach(function (searchword) {
        // Handle the auto suggestion
        if (Joomla.getOptions('finder-search')) {
          searchword.awesomplete = new Awesomplete(searchword);

          // If the current value is empty, set the previous value.
          searchword.addEventListener('input', onInputChange);
          var advanced = searchword.closest('form').querySelector('.js-finder-advanced');

          // Do not submit the form on suggestion selection, in case of advanced form.
          if (!advanced) {
            searchword.addEventListener('awesomplete-selectcomplete', submitForm);
          }
        }
      });
      var forms = [].slice.call(document.querySelectorAll('.js-finder-searchform'));
      forms.forEach(function (form) {
        form.addEventListener('submit', onSubmit);
      });

      // Cleanup
      document.removeEventListener('DOMContentLoaded', onBoot);
    };
    document.addEventListener('DOMContentLoaded', onBoot);
  })(window.Awesomplete, window.Joomla, window, document);

})();
