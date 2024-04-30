(function () {
  'use strict';

  /**
   * plugin.js
   *
   * Original code by Arjan Haverkamp
   * Copyright 2013-2015 Arjan Haverkamp (arjan@webgear.nl)
   */
  window.tinymce.PluginManager.add('highlightPlus', function (editor, url) {
    var showSourceEditor = function showSourceEditor() {
      editor.focus();
      editor.selection.collapse(true);
      if (!editor.settings.codemirror) editor.settings.codemirror = {};

      // Insert caret marker
      if (editor.settings.codemirror && editor.settings.codemirror.saveCursorPosition) {
        editor.selection.setContent('<span style="display: none;" class="CmCaReT">&#x0;</span>');
      }
      var codemirrorWidth = 800;
      if (editor.settings.codemirror.width) {
        codemirrorWidth = editor.settings.codemirror.width;
      }
      var codemirrorHeight = 550;
      if (editor.settings.codemirror.height) {
        codemirrorHeight = editor.settings.codemirror.height;
      }
      var buttonsConfig = [{
        type: 'custom',
        text: 'Ok',
        name: 'codemirrorOk',
        primary: true
      }, {
        type: 'cancel',
        text: 'Cancel',
        name: 'codemirrorCancel'
      }];
      var config = {
        title: 'Source code',
        url: url + "/source.html",
        width: codemirrorWidth,
        height: codemirrorHeight,
        resizable: true,
        maximizable: true,
        fullScreen: editor.settings.codemirror.fullscreen,
        saveCursorPosition: false,
        buttons: buttonsConfig
      };
      config.onAction = function (dialogApi, actionData) {
        if (actionData.name === 'codemirrorOk') {
          var doc = document.querySelectorAll('.tox-dialog__body-iframe iframe')[0];
          doc.contentWindow.tinymceHighlighterSubmit();
          editor.undoManager.add();
          // eslint-disable-next-line no-use-before-define
          win.close();
        }
      };
      var win = editor.windowManager.openUrl(config);
      if (editor.settings.codemirror.fullscreen) {
        win.fullscreen(true);
      }
    };
    editor.ui.registry.addButton('code', {
      icon: 'sourcecode',
      title: 'Source code+',
      tooltip: 'Source code+',
      onAction: showSourceEditor
    });
    editor.ui.registry.addMenuItem('code', {
      icon: 'sourcecode',
      text: 'Source code+',
      onAction: showSourceEditor,
      context: 'tools'
    });
  });

})();
