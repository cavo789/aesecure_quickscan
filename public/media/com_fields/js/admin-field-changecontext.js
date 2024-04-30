Joomla.fieldsChangeContext = context => {

  const regex = /([?;&])context[^&;]*[;&]?/;
  const url = window.location.href;
  const query = url.replace(regex, '$1').replace(/&$/, '');
  // eslint-disable-next-line
  window.location.href = (query.length > 2 ? query + '&' : '?') + (context ? 'context=' + context : '');
};
