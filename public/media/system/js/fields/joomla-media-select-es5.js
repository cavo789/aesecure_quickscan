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
  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor);
    }
  }
  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }
  function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass);
  }
  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }
  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };
    return _setPrototypeOf(o, p);
  }
  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;
    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }
  function _construct(Parent, args, Class) {
    if (_isNativeReflectConstruct()) {
      _construct = Reflect.construct.bind();
    } else {
      _construct = function _construct(Parent, args, Class) {
        var a = [null];
        a.push.apply(a, args);
        var Constructor = Function.bind.apply(Parent, a);
        var instance = new Constructor();
        if (Class) _setPrototypeOf(instance, Class.prototype);
        return instance;
      };
    }
    return _construct.apply(null, arguments);
  }
  function _isNativeFunction(fn) {
    return Function.toString.call(fn).indexOf("[native code]") !== -1;
  }
  function _wrapNativeSuper(Class) {
    var _cache = typeof Map === "function" ? new Map() : undefined;
    _wrapNativeSuper = function _wrapNativeSuper(Class) {
      if (Class === null || !_isNativeFunction(Class)) return Class;
      if (typeof Class !== "function") {
        throw new TypeError("Super expression must either be null or a function");
      }
      if (typeof _cache !== "undefined") {
        if (_cache.has(Class)) return _cache.get(Class);
        _cache.set(Class, Wrapper);
      }
      function Wrapper() {
        return _construct(Class, arguments, _getPrototypeOf(this).constructor);
      }
      Wrapper.prototype = Object.create(Class.prototype, {
        constructor: {
          value: Wrapper,
          enumerable: false,
          writable: true,
          configurable: true
        }
      });
      return _setPrototypeOf(Wrapper, Class);
    };
    return _wrapNativeSuper(Class);
  }
  function _toPrimitive(input, hint) {
    if (typeof input !== "object" || input === null) return input;
    var prim = input[Symbol.toPrimitive];
    if (prim !== undefined) {
      var res = prim.call(input, hint || "default");
      if (typeof res !== "object") return res;
      throw new TypeError("@@toPrimitive must return a primitive value.");
    }
    return (hint === "string" ? String : Number)(input);
  }
  function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return typeof key === "symbol" ? key : String(key);
  }

  /**
   * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  if (!Joomla) {
    throw new Error('Joomla API is not properly initiated');
  }

  /**
   * An object holding all the information of the selected image in media manager
   * eg:
   * {
   *   extension: "png"
   *   fileType: "image/png"
   *   height: 44
   *   path: "local-images:/powered_by.png"
   *   thumb: undefined
   *   width: 294
   * }
   */
  Joomla.selectedMediaFile = {};
  var supportedExtensions = Joomla.getOptions('media-picker', {});
  if (!Object.keys(supportedExtensions).length) {
    throw new Error('No supported extensions provided');
  }

  /**
   * Event Listener that updates the Joomla.selectedMediaFile
   * to the selected file in the media manager
   */
  document.addEventListener('onMediaFileSelected', /*#__PURE__*/function () {
    var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(e) {
      var currentModal, container, optionsEl, images, audios, videos, documents, type;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            Joomla.selectedMediaFile = e.detail;
            currentModal = Joomla.Modal.getCurrent();
            container = currentModal.querySelector('.modal-body');
            if (container) {
              _context.next = 5;
              break;
            }
            return _context.abrupt("return");
          case 5:
            optionsEl = container.querySelector('joomla-field-mediamore');
            if (optionsEl) {
              optionsEl.parentNode.removeChild(optionsEl);
            }

            // No extra attributes (lazy, alt) for fields
            if (!container.closest('joomla-field-media')) {
              _context.next = 9;
              break;
            }
            return _context.abrupt("return");
          case 9:
            images = supportedExtensions.images, audios = supportedExtensions.audios, videos = supportedExtensions.videos, documents = supportedExtensions.documents;
            if (Joomla.selectedMediaFile.path) {
              if (images.includes(Joomla.selectedMediaFile.extension.toLowerCase())) {
                type = 'images';
              } else if (audios.includes(Joomla.selectedMediaFile.extension.toLowerCase())) {
                type = 'audios';
              } else if (videos.includes(Joomla.selectedMediaFile.extension.toLowerCase())) {
                type = 'videos';
              } else if (documents.includes(Joomla.selectedMediaFile.extension.toLowerCase())) {
                type = 'documents';
              }
              if (type) {
                container.insertAdjacentHTML('afterbegin', "<joomla-field-mediamore\n  parent-id=\"" + currentModal.id + "\"\n  type=\"" + type + "\"\n  summary-label=\"" + Joomla.Text._('JFIELD_MEDIA_SUMMARY_LABEL') + "\"\n  lazy-label=\"" + Joomla.Text._('JFIELD_MEDIA_LAZY_LABEL') + "\"\n  alt-label=\"" + Joomla.Text._('JFIELD_MEDIA_ALT_LABEL') + "\"\n  alt-check-label=\"" + Joomla.Text._('JFIELD_MEDIA_ALT_CHECK_LABEL') + "\"\n  alt-check-desc-label=\"" + Joomla.Text._('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL') + "\"\n  classes-label=\"" + Joomla.Text._('JFIELD_MEDIA_CLASS_LABEL') + "\"\n  figure-classes-label=\"" + Joomla.Text._('JFIELD_MEDIA_FIGURE_CLASS_LABEL') + "\"\n  figure-caption-label=\"" + Joomla.Text._('JFIELD_MEDIA_FIGURE_CAPTION_LABEL') + "\"\n  embed-check-label=\"" + Joomla.Text._('JFIELD_MEDIA_EMBED_CHECK_LABEL') + "\"\n  embed-check-desc-label=\"" + Joomla.Text._('JFIELD_MEDIA_EMBED_CHECK_DESC_LABEL') + "\"\n  download-check-label=\"" + Joomla.Text._('JFIELD_MEDIA_DOWNLOAD_CHECK_LABEL') + "\"\n  download-check-desc-label=\"" + Joomla.Text._('JFIELD_MEDIA_DOWNLOAD_CHECK_DESC_LABEL') + "\"\n  title-label=\"" + Joomla.Text._('JFIELD_MEDIA_TITLE_LABEL') + "\"\n  width-label=\"" + Joomla.Text._('JFIELD_MEDIA_WIDTH_LABEL') + "\"\n  height-label=\"" + Joomla.Text._('JFIELD_MEDIA_HEIGHT_LABEL') + "\"\n></joomla-field-mediamore>\n");
              }
            }
          case 11:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function (_x) {
      return _ref.apply(this, arguments);
    };
  }());

  /**
   * Method to check if passed param is HTMLElement
   *
   * @param o {string|HTMLElement}  Element to be checked
   *
   * @returns {boolean}
   */
  var isElement = function isElement(o) {
    return typeof HTMLElement === 'object' ? o instanceof HTMLElement : o && typeof o === 'object' && o.nodeType === 1 && typeof o.nodeName === 'string';
  };

  /**
   * Method to return the image size
   *
   * @param url {string}
   *
   * @returns {bool}
   */
  var getImageSize = function getImageSize(url) {
    return new Promise(function (resolve, reject) {
      var img = new Image();
      img.src = url;
      img.onload = function () {
        Joomla.selectedMediaFile.width = img.width;
        Joomla.selectedMediaFile.height = img.height;
        resolve(true);
      };
      img.onerror = function () {
        // eslint-disable-next-line prefer-promise-reject-errors
        reject(false);
      };
    });
  };
  var insertAsImage = /*#__PURE__*/function () {
    var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(media, editor, fieldClass) {
      var _Joomla$getOptions, rootFull, parts, attribs, isLazy, alt, appendAlt, classes, figClasses, figCaption, imageElement, currentModal;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            if (media.url) {
              _Joomla$getOptions = Joomla.getOptions('system.paths'), rootFull = _Joomla$getOptions.rootFull;
              parts = media.url.split(rootFull);
              if (parts.length > 1) {
                // eslint-disable-next-line prefer-destructuring
                Joomla.selectedMediaFile.url = parts[1];
                if (media.thumb_path) {
                  Joomla.selectedMediaFile.thumb = media.thumb_path;
                } else {
                  Joomla.selectedMediaFile.thumb = false;
                }
              } else if (media.thumb_path) {
                Joomla.selectedMediaFile.url = media.url;
                Joomla.selectedMediaFile.thumb = media.thumb_path;
              }
            } else {
              Joomla.selectedMediaFile.url = false;
            }
            if (!Joomla.selectedMediaFile.url) {
              _context2.next = 47;
              break;
            }
            isLazy = '';
            alt = '';
            appendAlt = '';
            classes = '';
            figClasses = '';
            figCaption = '';
            imageElement = '';
            if (isElement(editor)) {
              _context2.next = 35;
              break;
            }
            currentModal = fieldClass.closest('.modal-content');
            attribs = currentModal.querySelector('joomla-field-mediamore');
            if (!attribs) {
              _context2.next = 30;
              break;
            }
            if (attribs.getAttribute('alt-check') === 'true') {
              appendAlt = ' alt=""';
            }
            alt = attribs.getAttribute('alt-value') ? " alt=\"" + attribs.getAttribute('alt-value') + "\"" : appendAlt;
            classes = attribs.getAttribute('img-classes') ? " class=\"" + attribs.getAttribute('img-classes') + "\"" : '';
            figClasses = attribs.getAttribute('fig-classes') ? " class=\"image " + attribs.getAttribute('fig-classes') + "\"" : ' class="image"';
            figCaption = attribs.getAttribute('fig-caption') ? "" + attribs.getAttribute('fig-caption') : '';
            if (!(attribs.getAttribute('is-lazy') === 'true')) {
              _context2.next = 30;
              break;
            }
            isLazy = " loading=\"lazy\" width=\"" + Joomla.selectedMediaFile.width + "\" height=\"" + Joomla.selectedMediaFile.height + "\"";
            if (!(Joomla.selectedMediaFile.width === 0 || Joomla.selectedMediaFile.height === 0)) {
              _context2.next = 30;
              break;
            }
            _context2.prev = 21;
            _context2.next = 24;
            return getImageSize(Joomla.selectedMediaFile.url);
          case 24:
            isLazy = " loading=\"lazy\" width=\"" + Joomla.selectedMediaFile.width + "\" height=\"" + Joomla.selectedMediaFile.height + "\"";
            _context2.next = 30;
            break;
          case 27:
            _context2.prev = 27;
            _context2.t0 = _context2["catch"](21);
            isLazy = '';
          case 30:
            if (figCaption) {
              imageElement = "<figure" + figClasses + "><img src=\"" + Joomla.selectedMediaFile.url + "\"" + classes + isLazy + alt + " data-path=\"" + Joomla.selectedMediaFile.path + "\"/><figcaption>" + figCaption + "</figcaption></figure>";
            } else {
              imageElement = "<img src=\"" + Joomla.selectedMediaFile.url + "\"" + classes + isLazy + alt + " data-path=\"" + Joomla.selectedMediaFile.path + "\"/>";
            }
            if (attribs) {
              attribs.parentNode.removeChild(attribs);
            }
            Joomla.editors.instances[editor].replaceSelection(imageElement);
            _context2.next = 47;
            break;
          case 35:
            if (!(Joomla.selectedMediaFile.width === 0 || Joomla.selectedMediaFile.height === 0)) {
              _context2.next = 45;
              break;
            }
            _context2.prev = 36;
            _context2.next = 39;
            return getImageSize(Joomla.selectedMediaFile.url);
          case 39:
            _context2.next = 45;
            break;
          case 41:
            _context2.prev = 41;
            _context2.t1 = _context2["catch"](36);
            Joomla.selectedMediaFile.height = 0;
            Joomla.selectedMediaFile.width = 0;
          case 45:
            fieldClass.markValid();
            fieldClass.setValue(Joomla.selectedMediaFile.url + "#joomlaImage://" + media.path.replace(':', '') + "?width=" + Joomla.selectedMediaFile.width + "&height=" + Joomla.selectedMediaFile.height);
          case 47:
          case "end":
            return _context2.stop();
        }
      }, _callee2, null, [[21, 27], [36, 41]]);
    }));
    return function insertAsImage(_x2, _x3, _x4) {
      return _ref2.apply(this, arguments);
    };
  }();
  var insertAsOther = function insertAsOther(media, editor, fieldClass, type) {
    if (media.url) {
      var _Joomla$getOptions2 = Joomla.getOptions('system.paths'),
        rootFull = _Joomla$getOptions2.rootFull;
      var parts = media.url.split(rootFull);
      if (parts.length > 1) {
        // eslint-disable-next-line prefer-destructuring
        Joomla.selectedMediaFile.url = parts[1];
      } else {
        Joomla.selectedMediaFile.url = media.url;
      }
    } else {
      Joomla.selectedMediaFile.url = false;
    }
    var attribs;
    if (Joomla.selectedMediaFile.url) {
      // Available Only inside an editor
      if (!isElement(editor)) {
        var outputText;
        var currentModal = fieldClass.closest('.modal-content');
        attribs = currentModal.querySelector('joomla-field-mediamore');
        if (attribs) {
          var embedable = attribs.getAttribute('embed-it');
          if (embedable && embedable === 'true') {
            if (type === 'audios') {
              outputText = "<audio controls src=\"" + Joomla.selectedMediaFile.url + "\"></audio>";
            }
            if (type === 'documents') {
              // @todo use ${Joomla.selectedMediaFile.filetype} in type
              var title = attribs.getAttribute('title');
              outputText = "<object type=\"application/" + Joomla.selectedMediaFile.extension + "\" data=\"" + Joomla.selectedMediaFile.url + "\" " + (title ? "title=\"" + title + "\"" : '') + " width=\"" + attribs.getAttribute('width') + "\" height=\"" + attribs.getAttribute('height') + "\">\n  " + Joomla.Text._('JFIELD_MEDIA_UNSUPPORTED').replace('{tag}', "<a download href=\"" + Joomla.selectedMediaFile.url + "\">").replace(/{extension}/g, Joomla.selectedMediaFile.extension) + "\n</object>";
            }
            if (type === 'videos') {
              outputText = "<video controls width=\"" + attribs.getAttribute('width') + "\" height=\"" + attribs.getAttribute('height') + "\">\n  <source src=\"" + Joomla.selectedMediaFile.url + "\" type=\"" + Joomla.selectedMediaFile.fileType + "\">\n</video>";
            }
          } else if (Joomla.editors.instances[editor].getSelection() !== '') {
            outputText = "<a download href=\"" + Joomla.selectedMediaFile.url + "\">" + Joomla.editors.instances[editor].getSelection() + "</a>";
          } else {
            var name = /([\w-]+)\./.exec(Joomla.selectedMediaFile.url);
            outputText = "<a download href=\"" + Joomla.selectedMediaFile.url + "\">" + Joomla.Text._('JFIELD_MEDIA_DOWNLOAD_FILE').replace('{file}', name[1]) + "</a>";
          }
        }
        if (attribs) {
          attribs.parentNode.removeChild(attribs);
        }
        Joomla.editors.instances[editor].replaceSelection(outputText);
      } else {
        fieldClass.markValid();
        fieldClass.givenType = type;
        fieldClass.setValue(Joomla.selectedMediaFile.url);
      }
    }
  };

  /**
   * Method to append the image in an editor or a field
   *
   * @param {{}} resp
   * @param {string|HTMLElement} editor
   * @param {string} fieldClass
   */
  var execTransform = /*#__PURE__*/function () {
    var _ref3 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3(resp, editor, fieldClass) {
      var media, images, audios, videos, documents;
      return _regeneratorRuntime().wrap(function _callee3$(_context3) {
        while (1) switch (_context3.prev = _context3.next) {
          case 0:
            if (!(resp.success === true)) {
              _context3.next = 12;
              break;
            }
            media = resp.data[0];
            images = supportedExtensions.images, audios = supportedExtensions.audios, videos = supportedExtensions.videos, documents = supportedExtensions.documents;
            if (!(Joomla.selectedMediaFile.extension && images.includes(media.extension.toLowerCase()))) {
              _context3.next = 5;
              break;
            }
            return _context3.abrupt("return", insertAsImage(media, editor, fieldClass));
          case 5:
            if (!(Joomla.selectedMediaFile.extension && audios.includes(media.extension.toLowerCase()))) {
              _context3.next = 7;
              break;
            }
            return _context3.abrupt("return", insertAsOther(media, editor, fieldClass, 'audios'));
          case 7:
            if (!(Joomla.selectedMediaFile.extension && documents.includes(media.extension.toLowerCase()))) {
              _context3.next = 9;
              break;
            }
            return _context3.abrupt("return", insertAsOther(media, editor, fieldClass, 'documents'));
          case 9:
            if (!(Joomla.selectedMediaFile.extension && videos.includes(media.extension.toLowerCase()))) {
              _context3.next = 11;
              break;
            }
            return _context3.abrupt("return", insertAsOther(media, editor, fieldClass, 'videos'));
          case 11:
            return _context3.abrupt("return", '');
          case 12:
            return _context3.abrupt("return", '');
          case 13:
          case "end":
            return _context3.stop();
        }
      }, _callee3);
    }));
    return function execTransform(_x5, _x6, _x7) {
      return _ref3.apply(this, arguments);
    };
  }();

  /**
   * Method that resolves the real url for the selected media file
   *
   * @param data        {object}         The data for the detail
   * @param editor      {string|object}  The data for the detail
   * @param fieldClass  {HTMLElement}    The fieldClass for the detail
   *
   * @returns {void}
   */
  Joomla.getMedia = function (data, editor, fieldClass) {
    return new Promise(function (resolve, reject) {
      if (!data || typeof data === 'object' && (!data.path || data.path === '')) {
        Joomla.selectedMediaFile = {};
        resolve({
          resp: {
            success: false
          }
        });
        return;
      }

      // Compile the url
      var url = new URL(Joomla.getOptions('media-picker-api').apiBaseUrl ? Joomla.getOptions('media-picker-api').apiBaseUrl : Joomla.getOptions('system.paths').baseFull + "index.php?option=com_media&format=json");
      url.searchParams.append('task', 'api.files');
      url.searchParams.append('url', true);
      url.searchParams.append('path', data.path);
      url.searchParams.append('mediatypes', '0,1,2,3');
      url.searchParams.append(Joomla.getOptions('csrf.token'), 1);
      fetch(url, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      }).then(function (response) {
        return response.json();
      }).then( /*#__PURE__*/function () {
        var _ref4 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4(response) {
          return _regeneratorRuntime().wrap(function _callee4$(_context4) {
            while (1) switch (_context4.prev = _context4.next) {
              case 0:
                _context4.t0 = resolve;
                _context4.next = 3;
                return execTransform(response, editor, fieldClass);
              case 3:
                _context4.t1 = _context4.sent;
                return _context4.abrupt("return", (0, _context4.t0)(_context4.t1));
              case 5:
              case "end":
                return _context4.stop();
            }
          }, _callee4);
        }));
        return function (_x8) {
          return _ref4.apply(this, arguments);
        };
      }()).catch(function (error) {
        return reject(error);
      });
    });
  };

  // For B/C purposes
  Joomla.getImage = Joomla.getMedia;

  /**
   * A simple Custom Element for adding alt text and controlling
   * the lazy loading on a selected image
   *
   * Will be rendered only for editor content images
   * Attributes:
   * - parent-id: the id of the parent media field {string}
   * - lazy-label: The text for the checkbox label {string}
   * - alt-label: The text for the alt label {string}
   * - is-lazy: The value for the lazyloading (calculated, defaults to 'true') {string}
   * - alt-value: The value for the alt text (calculated, defaults to '') {string}
   */
  var JoomlaFieldMediaOptions = /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(JoomlaFieldMediaOptions, _HTMLElement);
    function JoomlaFieldMediaOptions() {
      return _HTMLElement.apply(this, arguments) || this;
    }
    var _proto = JoomlaFieldMediaOptions.prototype;
    _proto.connectedCallback = function connectedCallback() {
      var _this = this;
      if (this.type === 'images') {
        this.innerHTML = "<details open>\n<summary>" + this.summarytext + "</summary>\n<div class=\"\">\n  <div class=\"form-group\">\n    <div class=\"input-group\">\n      <label class=\"input-group-text\" for=\"" + this.parentId + "-alt\">" + this.alttext + "</label>\n      <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-alt\" data-is=\"alt-value\" />\n    </div>\n  </div>\n  <div class=\"form-group\">\n    <div class=\"form-check\">\n      <input class=\"form-check-input\" type=\"checkbox\" id=\"" + this.parentId + "-alt-check\">\n      <label class=\"form-check-label\" for=\"" + this.parentId + "-alt-check\">" + this.altchecktext + "</label>\n      <div><small class=\"form-text\">" + this.altcheckdesctext + "</small></div>\n    </div>\n  </div>\n  <div class=\"form-group\">\n    <div class=\"form-check\">\n      <input class=\"form-check-input\" type=\"checkbox\" id=\"" + this.parentId + "-lazy\" checked>\n      <label class=\"form-check-label\" for=\"" + this.parentId + "-lazy\">" + this.lazytext + "</label>\n    </div>\n  </div>\n  <div class=\"form-group\">\n    <div class=\"input-group\">\n      <label class=\"input-group-text\" for=\"" + this.parentId + "-classes\">" + this.classestext + "</label>\n      <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-classes\" data-is=\"img-classes\"/>\n    </div>\n  </div>\n  <div class=\"form-group\">\n    <div class=\"input-group\">\n      <label class=\"input-group-text\" for=\"" + this.parentId + "-figclasses\">" + this.figclassestext + "</label>\n      <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-figclasses\" data-is=\"fig-classes\"/>\n    </div>\n  </div>\n  <div class=\"form-group\">\n    <div class=\"input-group\">\n      <label class=\"input-group-text\" for=\"" + this.parentId + "-figcaption\">" + this.figcaptiontext + "</label>\n      <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-figcaption\" data-is=\"fig-caption\"/>\n    </div>\n  </div>\n</div>\n</details>";
        this.lazyInputFn = this.lazyInputFn.bind(this);
        this.altCheckFn = this.altCheckFn.bind(this);
        this.inputFn = this.inputFn.bind(this);

        // Add event listeners
        this.lazyInput = this.querySelector("#" + this.parentId + "-lazy");
        this.lazyInput.addEventListener('change', this.lazyInputFn);
        this.altCheck = this.querySelector("#" + this.parentId + "-alt-check");
        this.altCheck.addEventListener('input', this.altCheckFn);
        [].slice.call(this.querySelectorAll('input[type="text"]')).map(function (el) {
          el.addEventListener('input', _this.inputFn);
          var is = el.dataset.is;
          if (is) {
            _this.setAttribute(is, el.value.replace(/"/g, '&quot;'));
          }
          return el;
        });

        // Set initial values
        this.setAttribute('is-lazy', !!this.lazyInput.checked);
        this.setAttribute('alt-check', false);
      } else if (['audios', 'videos', 'documents'].includes(this.type)) {
        this.innerHTML = "<details open>\n<summary>" + this.summarytext + "</summary>\n<div class=\"\">\n  <div class=\"form-group\">\n    <div class=\"form-check\">\n      <input class=\"form-check-input radio\" type=\"radio\" name=\"flexRadioDefault\" id=\"" + this.parentId + "-embed-check-2\" value=\"0\" checked>\n      <label class=\"form-check-label\" for=\"" + this.parentId + "-embed-check-2\">\n        " + this.downloadchecktext + "\n        <div><small class=\"form-text\">" + this.downloadcheckdesctext + "</small></div>\n      </label>\n    </div>\n    <div class=\"form-check\">\n      <input class=\"form-check-input radio\" type=\"radio\" name=\"flexRadioDefault\" id=\"" + this.parentId + "-embed-check-1\" value=\"1\">\n      <label class=\"form-check-label\" for=\"" + this.parentId + "-embed-check-1\">\n        " + this.embedchecktext + "\n        <div><small class=\"form-text\">" + this.embedcheckdesctext + "</small></div>\n      </label>\n    </div>\n  </div>\n  <div class=\"toggable-parts\" style=\"display: none\">\n    <div style=\"display: " + (this.type === 'audios' ? 'none' : 'block') + "\">\n      <div class=\"form-group\">\n        <div class=\"input-group\">\n          <label class=\"input-group-text\" for=\"" + this.parentId + "-width\">" + this.widthtext + "</label>\n          <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-width\" value=\"800\" data-is=\"width\"/>\n        </div>\n      </div>\n      <div class=\"form-group\">\n        <div class=\"input-group\">\n          <label class=\"input-group-text\" for=\"" + this.parentId + "-height\">" + this.heighttext + "</label>\n          <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-height\" value=\"600\" data-is=\"height\"/>\n        </div>\n      </div>\n      <div style=\"display: " + (this.type === 'document' ? 'block' : 'none') + "\">\n        <div class=\"form-group\">\n          <div class=\"input-group\">\n            <label class=\"input-group-text\" for=\"" + this.parentId + "-title\">" + this.titletext + "</label>\n            <input class=\"form-control\" type=\"text\" id=\"" + this.parentId + "-title\" value=\"\" data-is=\"title\"/>\n          </div>\n        </div>\n    </div>\n  </div>\n</div>\n</details>";
        this.embedInputFn = this.embedInputFn.bind(this);
        this.inputFn = this.inputFn.bind(this);
        [].slice.call(this.querySelectorAll('.form-check-input.radio')).map(function (el) {
          return el.addEventListener('input', _this.embedInputFn);
        });
        this.setAttribute('embed-it', false);
        [].slice.call(this.querySelectorAll('input[type="text"]')).map(function (el) {
          el.addEventListener('input', _this.inputFn);
          var is = el.dataset.is;
          if (is) {
            _this.setAttribute(is, el.value.replace(/"/g, '&quot;'));
          }
          return el;
        });
      }
    };
    _proto.disconnectedCallback = function disconnectedCallback() {
      var _this2 = this;
      if (this.type === 'image') {
        this.lazyInput.removeEventListener('input', this.lazyInputFn);
        this.altInput.removeEventListener('input', this.inputFn);
        this.altCheck.removeEventListener('input', this.altCheckFn);
      }
      if (['audio', 'video', 'document'].includes(this.type)) {
        [].slice.call(this.querySelectorAll('.form-check-input.radio')).map(function (el) {
          return el.removeEventListener('input', _this2.embedInputFn);
        });
        [].slice.call(this.querySelectorAll('input[type="text"]')).map(function (el) {
          return el.removeEventListener('input', _this2.embedInputFn);
        });
      }
      this.innerHTML = '';
    };
    _proto.lazyInputFn = function lazyInputFn(e) {
      this.setAttribute('is-lazy', !!e.target.checked);
    };
    _proto.altCheckFn = function altCheckFn(e) {
      this.setAttribute('alt-check', !!e.target.checked);
    };
    _proto.inputFn = function inputFn(e) {
      var is = e.target.dataset.is;
      if (is) {
        this.setAttribute(is, e.target.value.replace(/"/g, '&quot;'));
      }
    };
    _proto.embedInputFn = function embedInputFn(e) {
      var value = e.target.value;
      this.setAttribute('embed-it', value !== '0');
      var toggable = this.querySelector('.toggable-parts');
      if (toggable) {
        if (toggable.style.display !== 'block') {
          toggable.style.display = 'block';
        } else {
          toggable.style.display = 'none';
        }
      }
    };
    _createClass(JoomlaFieldMediaOptions, [{
      key: "type",
      get: function get() {
        return this.getAttribute('type');
      }
    }, {
      key: "parentId",
      get: function get() {
        return this.getAttribute('parent-id');
      }
    }, {
      key: "lazytext",
      get: function get() {
        return this.getAttribute('lazy-label');
      }
    }, {
      key: "alttext",
      get: function get() {
        return this.getAttribute('alt-label');
      }
    }, {
      key: "altchecktext",
      get: function get() {
        return this.getAttribute('alt-check-label');
      }
    }, {
      key: "altcheckdesctext",
      get: function get() {
        return this.getAttribute('alt-check-desc-label');
      }
    }, {
      key: "embedchecktext",
      get: function get() {
        return this.getAttribute('embed-check-label');
      }
    }, {
      key: "embedcheckdesctext",
      get: function get() {
        return this.getAttribute('embed-check-desc-label');
      }
    }, {
      key: "downloadchecktext",
      get: function get() {
        return this.getAttribute('download-check-label');
      }
    }, {
      key: "downloadcheckdesctext",
      get: function get() {
        return this.getAttribute('download-check-desc-label');
      }
    }, {
      key: "classestext",
      get: function get() {
        return this.getAttribute('classes-label');
      }
    }, {
      key: "figclassestext",
      get: function get() {
        return this.getAttribute('figure-classes-label');
      }
    }, {
      key: "figcaptiontext",
      get: function get() {
        return this.getAttribute('figure-caption-label');
      }
    }, {
      key: "summarytext",
      get: function get() {
        return this.getAttribute('summary-label');
      }
    }, {
      key: "widthtext",
      get: function get() {
        return this.getAttribute('width-label');
      }
    }, {
      key: "heighttext",
      get: function get() {
        return this.getAttribute('height-label');
      }
    }, {
      key: "titletext",
      get: function get() {
        return this.getAttribute('title-label');
      }
    }]);
    return JoomlaFieldMediaOptions;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
  customElements.define('joomla-field-mediamore', JoomlaFieldMediaOptions);

})();
