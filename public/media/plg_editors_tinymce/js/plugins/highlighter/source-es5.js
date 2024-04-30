(function () {
  'use strict';

  /**
   * source.js
   *
   * Original code by Arjan Haverkamp
   * Copyright 2013-2015 Arjan Haverkamp (arjan@webgear.nl)
   */
  if (!window.parent.Joomla || typeof window.parent.Joomla.getOptions !== 'function') {
    throw new Error('Joomla API not found');
  }

  // Get the base path for CodeMirror
  var rootPath = window.parent.Joomla.getOptions('system.paths').rootFull;
  var cmPath = rootPath + "/media/vendor/codemirror";

  // CodeMirror settings
  var CMsettings = {
    indentOnInit: true,
    config: {
      mode: 'htmlmixed',
      theme: 'default',
      lineNumbers: true,
      lineWrapping: true,
      indentUnit: 2,
      tabSize: 2,
      indentWithTabs: true,
      matchBrackets: true,
      saveCursorPosition: true,
      styleActiveLine: true
    },
    jsFiles: [// Default JS files
    cmPath + "/lib/codemirror.min.js", cmPath + "/addon/edit/matchbrackets.min.js", cmPath + "/mode/xml/xml.min.js", cmPath + "/mode/javascript/javascript.min.js", cmPath + "/mode/css/css.min.js", cmPath + "/mode/htmlmixed/htmlmixed.min.js", cmPath + "/addon/dialog/dialog.min.js", cmPath + "/addon/search/searchcursor.min.js", cmPath + "/addon/search/search.min.js", cmPath + "/addon/selection/active-line.min.js"],
    cssFiles: [// Default CSS files
    cmPath + "/lib/codemirror.css", cmPath + "/addon/dialog/dialog.css"]
  };

  // Declare some variables:
  var tinymce; // Reference to TinyMCE
  var editor; // Reference to TinyMCE editor
  var codemirror; // CodeMirror instance
  var chr = 0; // Unused utf-8 character, placeholder for cursor
  var isMac = /macintosh|mac os/i.test(navigator.userAgent);

  // Utility function to load CodeMirror script files
  var loadScript = function loadScript(url) {
    return new Promise(function (resolve, reject) {
      var script = document.createElement('script');
      script.src = url;
      script.onload = function () {
        return resolve();
      };
      script.onerror = function () {
        return reject(new Error("Failed to load the script " + url));
      };
      document.head.appendChild(script);
    });
  };

  /**
   * Find the depth level
   */
  var findDepth = function findDepth(haystack, needle) {
    var idx = haystack.indexOf(needle);
    var depth = 0;
    for (var x = idx - 1; x >= 0; x -= 1) {
      switch (haystack.charAt(x)) {
        case '<':
          depth -= 1;
          break;
        case '>':
          depth += 1;
          break;
        case '&':
          depth += 1;
          break;
      }
    }
    return depth;
  };

  /**
   * This function is called by plugin.js, when user clicks 'Ok' button
   */
  window.tinymceHighlighterSubmit = function () {
    var cc = '&#x0;';
    var _codemirror = codemirror,
      isDirty = _codemirror.isDirty;
    var _codemirror2 = codemirror,
      doc = _codemirror2.doc;
    if (doc.somethingSelected()) {
      // Clear selection:
      doc.setCursor(doc.getCursor());
    }

    // Insert cursor placeholder (&#x0;)
    doc.replaceSelection(cc);
    var pos = codemirror.getCursor();
    var curLineHTML = doc.getLine(pos.line);
    if (findDepth(curLineHTML, cc) !== 0) {
      // Cursor is inside a <tag>, don't set cursor:
      curLineHTML = curLineHTML.replace(cc, '');
      doc.replaceRange(curLineHTML, window.CodeMirror.Pos(pos.line, 0), window.CodeMirror.Pos(pos.line));
    }

    // Submit HTML to TinyMCE:
    // [FIX] Cursor position inside JS, style or &nbps;
    // Workaround to fix cursor position if inside script tag
    var code = codemirror.getValue();

    /* Regex to check if inside script or style tags */
    var ccScript = new RegExp("<script(.*?)>(.*?)" + cc + "(.*?)</script>", 'ms');
    var ccStyle = new RegExp("<style(.*?)>(.*?)" + cc + "(.*?)</style>", 'ms');

    /* Regex to check if in beginning or end or if between < & > */
    var ccLocationCheck = new RegExp("<[^>]*(" + cc + ").*>|^(" + cc + ")|(" + cc + ")$");
    if (code.search(ccScript) !== -1 || code.search(ccStyle) !== -1 || code.search(ccLocationCheck) !== -1) {
      editor.setContent(code.replace(cc, ''));
    } else {
      editor.setContent(code.replace(cc, '<span id="CmCaReT"></span>'));
    }
    editor.isNotDirty = !isDirty;
    if (isDirty) {
      editor.nodeChanged();
    }

    // Set cursor:
    var el = editor.dom.select('span#CmCaReT')[0];
    if (el) {
      editor.selection.scrollIntoView(el);
      editor.selection.setCursorLocation(el, 0);
      editor.dom.remove(el);
    }
  };

  /**
   * Listen for the escape key and close the modal
   *
   * @param {Event} evt
   */
  document.addEventListener('keydown', function (evt) {
    var event = evt || window.event;
    var isEscape = false;
    if ('key' in event) isEscape = event.key === 'Escape' || event.key === 'Esc';else isEscape = event.keyCode === 27;
    if (isEscape) tinymce.activeEditor.windowManager.close();
  });

  /**
   * Append some help text in the modal footer
   */
  var start = function start() {
    // Initialise (on load)
    if (typeof window.CodeMirror !== 'function') {
      throw new Error("CodeMirror not found in \"" + CMsettings.path + "\", aborting...");
    }

    // Create legend for keyboard shortcuts for find & replace:
    var footer = window.parent.document.querySelectorAll('.tox-dialog__footer')[0];
    var div = window.parent.document.createElement('div');
    var td1 = '<td style="font-size:11px;background:#777;color:#fff;padding:0 4px">';
    var td2 = '<td style="font-size:11px;padding-right:5px">';
    div.innerHTML = "\n<table cellspacing=\"0\" cellpadding=\"0\" style=\"border-spacing:4px\">\n  <tr>\n    " + td1 + (isMac ? '&#8984;-F' : 'Ctrl-F</td>') + td2 + tinymce.translate('Start search') + "</td>\n    " + td1 + (isMac ? '&#8984;-G' : 'Ctrl-G') + "</td>\n    " + td2 + tinymce.translate('Find next') + "</td>\n    " + td1 + (isMac ? '&#8984;-Alt-F' : 'Shift-Ctrl-F') + "</td>\n    " + td2 + tinymce.translate('Find previous') + "</td>\n  </tr>\n  <tr>\n    " + td1 + (isMac ? '&#8984;-Alt-F' : 'Shift-Ctrl-F') + "</td>\n    " + td2 + tinymce.translate('Replace') + "</td>\n    " + td1 + (isMac ? 'Shift-&#8984;-Alt-F' : 'Shift-Ctrl-R') + "</td>\n    " + td2 + tinymce.translate('Replace all') + "</td>\n  </tr>\n</table>";
    footer.insertAdjacentElement('afterbegin', div);

    // Set CodeMirror cursor and bookmark to same position as cursor was in TinyMCE:
    var html = editor.getContent({
      source_view: true
    });

    // [FIX] #6 z-index issue with table panel and source code dialog
    //  editor.selection.getBookmark();

    html = html.replace(/<span\s+style="display: none;"\s+class="CmCaReT"([^>]*)>([^<]*)<\/span>/gm, String.fromCharCode(chr));
    editor.dom.remove(editor.dom.select('.CmCaReT'));

    // Hide TinyMCE toolbar panels, [FIX] #6 z-index issue with table panel and source code dialog
    // https://github.com/christiaan/tinymce-codemirror/issues/6
    tinymce.each(editor.contextToolbars, function (toolbar) {
      if (toolbar.panel) {
        toolbar.panel.hide();
      }
    });
    window.CodeMirror.defineInitHook(function (inst) {
      // Move cursor to correct position:
      inst.focus();
      var cursor = inst.getSearchCursor(String.fromCharCode(chr), false);
      if (cursor.findNext()) {
        inst.setCursor(cursor.to());
        cursor.replace('');
      }

      // Indent all code, if so requested:
      if (editor.settings.codemirror.indentOnInit) {
        var last = inst.lineCount();
        inst.operation(function () {
          // eslint-disable-next-line no-plusplus
          for (var i = 0; i < last; ++i) {
            inst.indentLine(i);
          }
        });
      }
    });
    CMsettings.config.value = html;

    // Instantiate CodeMirror:
    codemirror = window.CodeMirror(document.body, CMsettings.config);
    codemirror.isDirty = false;
    codemirror.on('change', function (inst) {
      inst.isDirty = true;
    });
    codemirror.setSize('100%', '100%');
    codemirror.refresh();
  };

  // Initialise
  tinymce = window.parent.tinymce;
  if (!tinymce) {
    throw new Error('tinyMCE not found');
  }
  editor = tinymce.activeEditor;
  var userSettings = editor.settings.codemirror;
  if (userSettings.fullscreen) {
    CMsettings.jsFiles.push(cmPath + "/addon/display/fullscreen.min.js");
    CMsettings.cssFiles.push(cmPath + "/addon/display/fullscreen.css");
  }

  // Merge config
  CMsettings = Object.assign({}, CMsettings, userSettings);

  // Append the stylesheets
  CMsettings.cssFiles.forEach(function (css) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = css;
    document.head.appendChild(link);
  });

  /**
   * Append javascript files ensuring the order of execution.
   * Then execute the start function.
   */
  CMsettings.jsFiles.reduce(function (p, item) {
    return p.then(function () {
      return loadScript(item);
    });
  }, Promise.resolve(true)).then(function () {
    // Borrowed from codemirror.js themeChanged function. Sets the theme's class names to the html element.
    // Without this, the background color outside of the codemirror wrapper element remains white.
    // [TMP] commented temporary, cause JS error: Uncaught TypeError: Cannot read property 'replace' of undefined
    if (CMsettings.config.theme) {
      document.documentElement.className += CMsettings.config.theme.replace(/(^|\s)\s*/g, ' cm-s-');
    }
    start();
  });

})();
