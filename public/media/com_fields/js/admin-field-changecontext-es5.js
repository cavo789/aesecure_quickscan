(function () {
  'use strict';

  Joomla.fieldsChangeContext = function (context) {
    var regex = /([?;&])context[^&;]*[;&]?/;
    var url = window.location.href;
    var query = url.replace(regex, '$1').replace(/&$/, '');
    // eslint-disable-next-line
    window.location.href = (query.length > 2 ? query + '&' : '?') + (context ? 'context=' + context : '');
  };

})();
