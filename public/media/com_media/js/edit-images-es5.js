(function () {
  'use strict';

  function _regeneratorRuntime() {
    _regeneratorRuntime = function () {
      return exports;
    };
    var exports = {},
      Op = Object.prototype,
      hasOwn = Op.hasOwnProperty,
      defineProperty = Object.defineProperty || function (obj, key, desc) {
        obj[key] = desc.value;
      },
      $Symbol = "function" == typeof Symbol ? Symbol : {},
      iteratorSymbol = $Symbol.iterator || "@@iterator",
      asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator",
      toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";
    function define(obj, key, value) {
      return Object.defineProperty(obj, key, {
        value: value,
        enumerable: !0,
        configurable: !0,
        writable: !0
      }), obj[key];
    }
    try {
      define({}, "");
    } catch (err) {
      define = function (obj, key, value) {
        return obj[key] = value;
      };
    }
    function wrap(innerFn, outerFn, self, tryLocsList) {
      var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator,
        generator = Object.create(protoGenerator.prototype),
        context = new Context(tryLocsList || []);
      return defineProperty(generator, "_invoke", {
        value: makeInvokeMethod(innerFn, self, context)
      }), generator;
    }
    function tryCatch(fn, obj, arg) {
      try {
        return {
          type: "normal",
          arg: fn.call(obj, arg)
        };
      } catch (err) {
        return {
          type: "throw",
          arg: err
        };
      }
    }
    exports.wrap = wrap;
    var ContinueSentinel = {};
    function Generator() {}
    function GeneratorFunction() {}
    function GeneratorFunctionPrototype() {}
    var IteratorPrototype = {};
    define(IteratorPrototype, iteratorSymbol, function () {
      return this;
    });
    var getProto = Object.getPrototypeOf,
      NativeIteratorPrototype = getProto && getProto(getProto(values([])));
    NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype);
    var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype);
    function defineIteratorMethods(prototype) {
      ["next", "throw", "return"].forEach(function (method) {
        define(prototype, method, function (arg) {
          return this._invoke(method, arg);
        });
      });
    }
    function AsyncIterator(generator, PromiseImpl) {
      function invoke(method, arg, resolve, reject) {
        var record = tryCatch(generator[method], generator, arg);
        if ("throw" !== record.type) {
          var result = record.arg,
            value = result.value;
          return value && "object" == typeof value && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) {
            invoke("next", value, resolve, reject);
          }, function (err) {
            invoke("throw", err, resolve, reject);
          }) : PromiseImpl.resolve(value).then(function (unwrapped) {
            result.value = unwrapped, resolve(result);
          }, function (error) {
            return invoke("throw", error, resolve, reject);
          });
        }
        reject(record.arg);
      }
      var previousPromise;
      defineProperty(this, "_invoke", {
        value: function (method, arg) {
          function callInvokeWithMethodAndArg() {
            return new PromiseImpl(function (resolve, reject) {
              invoke(method, arg, resolve, reject);
            });
          }
          return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
        }
      });
    }
    function makeInvokeMethod(innerFn, self, context) {
      var state = "suspendedStart";
      return function (method, arg) {
        if ("executing" === state) throw new Error("Generator is already running");
        if ("completed" === state) {
          if ("throw" === method) throw arg;
          return doneResult();
        }
        for (context.method = method, context.arg = arg;;) {
          var delegate = context.delegate;
          if (delegate) {
            var delegateResult = maybeInvokeDelegate(delegate, context);
            if (delegateResult) {
              if (delegateResult === ContinueSentinel) continue;
              return delegateResult;
            }
          }
          if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) {
            if ("suspendedStart" === state) throw state = "completed", context.arg;
            context.dispatchException(context.arg);
          } else "return" === context.method && context.abrupt("return", context.arg);
          state = "executing";
          var record = tryCatch(innerFn, self, context);
          if ("normal" === record.type) {
            if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue;
            return {
              value: record.arg,
              done: context.done
            };
          }
          "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg);
        }
      };
    }
    function maybeInvokeDelegate(delegate, context) {
      var methodName = context.method,
        method = delegate.iterator[methodName];
      if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator.return && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel;
      var record = tryCatch(method, delegate.iterator, context.arg);
      if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel;
      var info = record.arg;
      return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel);
    }
    function pushTryEntry(locs) {
      var entry = {
        tryLoc: locs[0]
      };
      1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry);
    }
    function resetTryEntry(entry) {
      var record = entry.completion || {};
      record.type = "normal", delete record.arg, entry.completion = record;
    }
    function Context(tryLocsList) {
      this.tryEntries = [{
        tryLoc: "root"
      }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0);
    }
    function values(iterable) {
      if (iterable) {
        var iteratorMethod = iterable[iteratorSymbol];
        if (iteratorMethod) return iteratorMethod.call(iterable);
        if ("function" == typeof iterable.next) return iterable;
        if (!isNaN(iterable.length)) {
          var i = -1,
            next = function next() {
              for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next;
              return next.value = undefined, next.done = !0, next;
            };
          return next.next = next;
        }
      }
      return {
        next: doneResult
      };
    }
    function doneResult() {
      return {
        value: undefined,
        done: !0
      };
    }
    return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", {
      value: GeneratorFunctionPrototype,
      configurable: !0
    }), defineProperty(GeneratorFunctionPrototype, "constructor", {
      value: GeneratorFunction,
      configurable: !0
    }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) {
      var ctor = "function" == typeof genFun && genFun.constructor;
      return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name));
    }, exports.mark = function (genFun) {
      return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun;
    }, exports.awrap = function (arg) {
      return {
        __await: arg
      };
    }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
      return this;
    }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) {
      void 0 === PromiseImpl && (PromiseImpl = Promise);
      var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
      return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) {
        return result.done ? result.value : iter.next();
      });
    }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () {
      return this;
    }), define(Gp, "toString", function () {
      return "[object Generator]";
    }), exports.keys = function (val) {
      var object = Object(val),
        keys = [];
      for (var key in object) keys.push(key);
      return keys.reverse(), function next() {
        for (; keys.length;) {
          var key = keys.pop();
          if (key in object) return next.value = key, next.done = !1, next;
        }
        return next.done = !0, next;
      };
    }, exports.values = values, Context.prototype = {
      constructor: Context,
      reset: function (skipTempReset) {
        if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined);
      },
      stop: function () {
        this.done = !0;
        var rootRecord = this.tryEntries[0].completion;
        if ("throw" === rootRecord.type) throw rootRecord.arg;
        return this.rval;
      },
      dispatchException: function (exception) {
        if (this.done) throw exception;
        var context = this;
        function handle(loc, caught) {
          return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught;
        }
        for (var i = this.tryEntries.length - 1; i >= 0; --i) {
          var entry = this.tryEntries[i],
            record = entry.completion;
          if ("root" === entry.tryLoc) return handle("end");
          if (entry.tryLoc <= this.prev) {
            var hasCatch = hasOwn.call(entry, "catchLoc"),
              hasFinally = hasOwn.call(entry, "finallyLoc");
            if (hasCatch && hasFinally) {
              if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
              if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
            } else if (hasCatch) {
              if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
            } else {
              if (!hasFinally) throw new Error("try statement without catch or finally");
              if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
            }
          }
        }
      },
      abrupt: function (type, arg) {
        for (var i = this.tryEntries.length - 1; i >= 0; --i) {
          var entry = this.tryEntries[i];
          if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
            var finallyEntry = entry;
            break;
          }
        }
        finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null);
        var record = finallyEntry ? finallyEntry.completion : {};
        return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record);
      },
      complete: function (record, afterLoc) {
        if ("throw" === record.type) throw record.arg;
        return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel;
      },
      finish: function (finallyLoc) {
        for (var i = this.tryEntries.length - 1; i >= 0; --i) {
          var entry = this.tryEntries[i];
          if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel;
        }
      },
      catch: function (tryLoc) {
        for (var i = this.tryEntries.length - 1; i >= 0; --i) {
          var entry = this.tryEntries[i];
          if (entry.tryLoc === tryLoc) {
            var record = entry.completion;
            if ("throw" === record.type) {
              var thrown = record.arg;
              resetTryEntry(entry);
            }
            return thrown;
          }
        }
        throw new Error("illegal catch attempt");
      },
      delegateYield: function (iterable, resultName, nextLoc) {
        return this.delegate = {
          iterator: values(iterable),
          resultName: resultName,
          nextLoc: nextLoc
        }, "next" === this.method && (this.arg = undefined), ContinueSentinel;
      }
    }, exports;
  }
  function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
    try {
      var info = gen[key](arg);
      var value = info.value;
    } catch (error) {
      reject(error);
      return;
    }
    if (info.done) {
      resolve(value);
    } else {
      Promise.resolve(value).then(_next, _throw);
    }
  }
  function _asyncToGenerator(fn) {
    return function () {
      var self = this,
        args = arguments;
      return new Promise(function (resolve, reject) {
        var gen = fn.apply(self, args);
        function _next(value) {
          asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
        }
        function _throw(err) {
          asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
        }
        _next(undefined);
      });
    };
  }

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  if (!Joomla) {
    throw new Error('Joomla API is not properly initialized');
  }
  Joomla.MediaManager = Joomla.MediaManager || {};
  var Edit = /*#__PURE__*/function () {
    function Edit() {
      var _this = this;
      // Get the options from Joomla.optionStorage
      this.options = Joomla.getOptions('com_media', {});
      if (!this.options) {
        throw new Error('Initialization error "edit-images.js"');
      }
      this.extension = this.options.uploadPath.split('.').pop();
      this.fileType = ['jpeg', 'jpg'].includes(this.extension) ? 'jpeg' : this.extension;
      this.options.currentUrl = new URL(window.location.href);

      // Initiate the registry
      this.original = {
        filename: this.options.uploadPath.split('/').pop(),
        extension: this.extension,
        contents: "data:image/" + this.fileType + ";base64," + this.options.contents
      };
      // eslint-disable-next-line no-promise-executor-return
      this.previousPluginDeactivated = new Promise(function (resolve) {
        return resolve;
      });
      this.history = {};
      this.current = this.original;
      this.plugins = {};
      this.baseContainer = document.getElementById('media-manager-edit-container');
      if (!this.baseContainer) {
        throw new Error('The image preview container is missing');
      }
      this.createImageContainer(this.original);
      Joomla.MediaManager.Edit = this;
      window.dispatchEvent(new CustomEvent('media-manager-edit-init'));

      // Once the DOM is ready, initialize everything
      customElements.whenDefined('joomla-tab').then( /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var tabContainer, tabsUlElement, links;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              tabContainer = document.getElementById('myTab');
              tabsUlElement = tabContainer.firstElementChild;
              links = [].slice.call(tabsUlElement.querySelectorAll('button[aria-controls]')); // Couple the tabs with the plugin objects
              links.forEach(function (link, index) {
                var tab = document.getElementById(link.getAttribute('aria-controls'));
                if (index === 0) {
                  tab.insertAdjacentElement('beforeend', _this.baseContainer);
                }
                link.addEventListener('joomla.tab.hidden', function (_ref2) {
                  var target = _ref2.target;
                  if (!target) {
                    // eslint-disable-next-line no-promise-executor-return
                    _this.previousPluginDeactivated = new Promise(function (resolve) {
                      return resolve;
                    });
                    return;
                  }
                  _this.previousPluginDeactivated = new Promise(function (resolve, reject) {
                    _this.plugins[target.getAttribute('aria-controls').replace('attrib-', '')].Deactivate(_this.imagePreview).then(resolve).catch(function (e) {
                      // eslint-disable-next-line no-console
                      console.log(e);
                      reject();
                    });
                  });
                });
                link.addEventListener('joomla.tab.shown', function (_ref3) {
                  var target = _ref3.target;
                  // Move the image container to the correct tab
                  tab.insertAdjacentElement('beforeend', _this.baseContainer);
                  _this.previousPluginDeactivated.then(function () {
                    return _this.plugins[target.getAttribute('aria-controls').replace('attrib-', '')].Activate(_this.imagePreview);
                  }).catch(function (e) {
                    // eslint-disable-next-line no-console
                    console.log(e);
                  });
                });
              });
              tabContainer.activateTab(0, false);
            case 5:
            case "end":
              return _context.stop();
          }
        }, _callee);
      })));
      this.addHistoryPoint = this.addHistoryPoint.bind(this);
      this.createImageContainer = this.createImageContainer.bind(this);
      this.Reset = this.Reset.bind(this);
      this.Undo = this.Undo.bind(this);
      this.Redo = this.Redo.bind(this);
      this.createProgressBar = this.createProgressBar.bind(this);
      this.updateProgressBar = this.updateProgressBar.bind(this);
      this.removeProgressBar = this.removeProgressBar.bind(this);
      this.upload = this.upload.bind(this);

      // Create history entry
      window.addEventListener('mediaManager.history.point', this.addHistoryPoint.bind(this));
    }

    /**
     * Creates a history snapshot
     * PRIVATE
     */
    var _proto = Edit.prototype;
    _proto.addHistoryPoint = function addHistoryPoint() {
      if (this.original !== this.current) {
        var key = Object.keys(this.history).length;
        if (this.history[key] && this.history[key - 1] && this.history[key] === this.history[key - 1]) {
          return;
        }
        this.history[key + 1] = this.current;
      }
    }

    /**
     * Creates the images for edit and preview
     * PRIVATE
     */;
    _proto.createImageContainer = function createImageContainer(data) {
      if (!data.contents) {
        throw new Error('Initialization error "edit-images.js"');
      }
      this.imagePreview = document.createElement('img');
      this.imagePreview.src = data.contents;
      this.imagePreview.id = 'image-preview';
      this.imagePreview.style.height = 'auto';
      this.imagePreview.style.maxWidth = '100%';
      this.baseContainer.appendChild(this.imagePreview);
    }

    // Reset the image to the initial state
    ;
    _proto.Reset = function Reset( /* current */
    ) {
      var _this2 = this;
      this.current.contents = "data:image/" + this.fileType + ";base64," + this.options.contents;
      this.imagePreview.setAttribute('src', this.current.contents);
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          _this2.imagePreview.setAttribute('width', _this2.imagePreview.naturalWidth);
          _this2.imagePreview.setAttribute('height', _this2.imagePreview.naturalHeight);
        });
      });
    }

    // @TODO History
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.Undo = function Undo() {}

    // @TODO History
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.Redo = function Redo() {}

    // @TODO Create the progress bar
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.createProgressBar = function createProgressBar() {}

    // @TODO Update the progress bar
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.updateProgressBar = function updateProgressBar( /* position */) {}

    // @TODO Remove the progress bar
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.removeProgressBar = function removeProgressBar() {}

    /**
     * Uploads
     * Public
     */;
    _proto.upload = function upload(url, stateChangeCallback) {
      var _this3 = this,
        _JSON$stringify;
      var format = Joomla.MediaManager.Edit.original.extension === 'jpg' ? 'jpeg' : Joomla.MediaManager.Edit.original.extension;
      if (!format) {
        // eslint-disable-next-line prefer-destructuring
        format = /data:image\/(.+);/gm.exec(Joomla.MediaManager.Edit.original.contents)[1];
      }
      if (!format) {
        throw new Error('Unable to determine image format');
      }
      this.xhr = new XMLHttpRequest();
      if (typeof stateChangeCallback === 'function') {
        this.xhr.onreadystatechange = stateChangeCallback;
      }
      this.xhr.upload.onprogress = function (e) {
        _this3.updateProgressBar(e.loaded / e.total * 100);
      };
      this.xhr.onload = function () {
        var resp;
        try {
          resp = JSON.parse(_this3.xhr.responseText);
        } catch (er) {
          resp = null;
        }
        if (resp) {
          if (_this3.xhr.status === 200) {
            if (resp.success === true) {
              _this3.removeProgressBar();
            }
            if (resp.status === '1') {
              Joomla.renderMessages({
                success: [resp.message]
              }, 'true');
              _this3.removeProgressBar();
            }
          }
        } else {
          _this3.removeProgressBar();
        }
        _this3.xhr = null;
      };
      this.xhr.onerror = function () {
        _this3.removeProgressBar();
        _this3.xhr = null;
      };
      this.xhr.open('PUT', url, true);
      this.xhr.setRequestHeader('Content-Type', 'application/json');
      this.createProgressBar();
      this.xhr.send(JSON.stringify((_JSON$stringify = {
        name: Joomla.MediaManager.Edit.options.uploadPath.split('/').pop(),
        content: Joomla.MediaManager.Edit.current.contents.replace("data:image/" + format + ";base64,", '')
      }, _JSON$stringify[Joomla.MediaManager.Edit.options.csrfToken] = 1, _JSON$stringify)));
    };
    return Edit;
  }(); // Initiate the Editor API
  // eslint-disable-next-line no-new
  new Edit();

  /**
   * Compute the current URL
   *
   * @param {boolean} isModal is the URL for a modal window
   *
   * @return {{}} the URL object
   */
  var getUrl = function getUrl(isModal) {
    var newUrl = Joomla.MediaManager.Edit.options.currentUrl;
    var params = new URLSearchParams(newUrl.search);
    params.set('view', 'media');
    params.delete('path');
    params.delete('mediatypes');
    var uploadPath = Joomla.MediaManager.Edit.options.uploadPath;
    var fileDirectory = uploadPath.split('/');
    fileDirectory.pop();
    fileDirectory = fileDirectory.join('/');

    // If we are in root add a backslash
    if (fileDirectory.endsWith(':')) {
      fileDirectory = fileDirectory + "/";
    }
    params.set('path', fileDirectory);

    // Respect the images_only URI param
    var mediaTypes = document.querySelector('input[name="mediatypes"]');
    params.set('mediatypes', mediaTypes && mediaTypes.value ? mediaTypes.value : '0');
    if (isModal) {
      params.set('tmpl', 'component');
    }
    newUrl.search = params;
    return newUrl;
  };

  // Customize the Toolbar buttons behavior
  Joomla.submitbutton = function (task) {
    var url = new URL(Joomla.MediaManager.Edit.options.apiBaseUrl + "&task=api.files&path=" + Joomla.MediaManager.Edit.options.uploadPath);
    switch (task) {
      case 'apply':
        Joomla.MediaManager.Edit.upload(url, null);
        Joomla.MediaManager.Edit.imagePreview.src = Joomla.MediaManager.Edit.current.contents;
        Joomla.MediaManager.Edit.original = Joomla.MediaManager.Edit.current;
        Joomla.MediaManager.Edit.history = {};
        _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
          var activeTab;
          return _regeneratorRuntime().wrap(function _callee2$(_context2) {
            while (1) switch (_context2.prev = _context2.next) {
              case 0:
                activeTab = [].slice.call(document.querySelectorAll('joomla-tab-element')).filter(function (tab) {
                  return tab.hasAttribute('active');
                });
                _context2.prev = 1;
                _context2.next = 4;
                return Joomla.MediaManager.Edit.plugins[activeTab[0].id.replace('attrib-', '')].Deactivate(Joomla.MediaManager.Edit.imagePreview);
              case 4:
                _context2.next = 6;
                return Joomla.MediaManager.Edit.plugins[activeTab[0].id.replace('attrib-', '')].Activate(Joomla.MediaManager.Edit.imagePreview);
              case 6:
                _context2.next = 11;
                break;
              case 8:
                _context2.prev = 8;
                _context2.t0 = _context2["catch"](1);
                // eslint-disable-next-line no-console
                console.log(_context2.t0);
              case 11:
              case "end":
                return _context2.stop();
            }
          }, _callee2, null, [[1, 8]]);
        }))();
        break;
      case 'save':
        Joomla.MediaManager.Edit.upload(url, function () {
          if (Joomla.MediaManager.Edit.xhr.readyState === XMLHttpRequest.DONE) {
            if (window.self !== window.top) {
              window.location = getUrl(true);
            } else {
              window.location = getUrl();
            }
          }
        });
        break;
      case 'cancel':
        if (window.self !== window.top) {
          window.location = getUrl(true);
        } else {
          window.location = getUrl();
        }
        break;
      case 'reset':
        Joomla.MediaManager.Edit.Reset('initial');
        break;
    }
  };

})();
