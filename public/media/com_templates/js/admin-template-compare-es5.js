(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   *
   * @deprecated  4.3
   *              This file is deprecated and will be removed with Joomla 5.0
   */
  (function () {
    document.addEventListener('DOMContentLoaded', function () {
      var decodeHtmlspecialChars = function decodeHtmlspecialChars(text) {
        var map = {
          '&amp;': '&',
          '&#038;': '&',
          '&lt;': '<',
          '&gt;': '>',
          '&quot;': '"',
          '&#039;': "'",
          '&#8217;': '’',
          '&#8216;': '‘',
          '&#8211;': '–',
          '&#8212;': '—',
          '&#8230;': '…',
          '&#8221;': '”'
        };

        /* eslint-disable */
        return text.replace(/\&[\w\d\#]{2,5}\;/g, function (m) {
          var n = map[m];
          return n;
        });
      };
      var compare = function compare(original, changed) {
        var display = changed.nextElementSibling;
        var color = '';
        var pre = null;
        var diff = Diff.diffLines(original.innerHTML, changed.innerHTML);
        var fragment = document.createDocumentFragment();

        /* eslint-enable */

        diff.forEach(function (part) {
          if (part.added) {
            color = '#a6f3a6';
          } else if (part.removed) {
            color = '#f8cbcb';
          } else {
            color = '';
          }
          pre = document.createElement('pre');
          pre.style.backgroundColor = color;
          pre.className = 'diffview';
          pre.appendChild(document.createTextNode(decodeHtmlspecialChars(part.value)));
          fragment.appendChild(pre);
        });
        display.appendChild(fragment);
      };
      var diffs = [].slice.call(document.querySelectorAll('#original'));
      for (var i = 0, l = diffs.length; i < l; i += 1) {
        compare(diffs[i], diffs[i].nextElementSibling);
      }
    });
  })();

})();
