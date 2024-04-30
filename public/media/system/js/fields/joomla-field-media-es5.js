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
  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }
    return self;
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
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  if (!Joomla) {
    throw new Error('Joomla API is not properly initiated');
  }

  /**
   * Extract the extensions
   *
   * @param {*} path
   * @returns {string}
   */
  var getExtension = function getExtension(path) {
    var parts = path.split(/[#]/);
    if (parts.length > 1) {
      return parts[1].split(/[?]/)[0].split('.').pop().trim();
    }
    return path.split(/[#?]/)[0].split('.').pop().trim();
  };
  var JoomlaFieldMedia = /*#__PURE__*/function (_HTMLElement) {
    _inheritsLoose(JoomlaFieldMedia, _HTMLElement);
    function JoomlaFieldMedia() {
      var _this;
      _this = _HTMLElement.call(this) || this;
      _this.onSelected = _this.onSelected.bind(_assertThisInitialized(_this));
      _this.show = _this.show.bind(_assertThisInitialized(_this));
      _this.clearValue = _this.clearValue.bind(_assertThisInitialized(_this));
      _this.modalClose = _this.modalClose.bind(_assertThisInitialized(_this));
      _this.setValue = _this.setValue.bind(_assertThisInitialized(_this));
      _this.updatePreview = _this.updatePreview.bind(_assertThisInitialized(_this));
      _this.validateValue = _this.validateValue.bind(_assertThisInitialized(_this));
      _this.markValid = _this.markValid.bind(_assertThisInitialized(_this));
      _this.markInvalid = _this.markInvalid.bind(_assertThisInitialized(_this));
      _this.mimeType = '';
      return _this;
    }
    var _proto = JoomlaFieldMedia.prototype;
    // attributeChangedCallback(attr, oldValue, newValue) {}
    _proto.connectedCallback = function connectedCallback() {
      this.button = this.querySelector(this.buttonSelect);
      this.inputElement = this.querySelector(this.input);
      this.buttonClearEl = this.querySelector(this.buttonClear);
      this.modalElement = this.querySelector('.joomla-modal');
      this.buttonSaveSelectedElement = this.querySelector(this.buttonSaveSelected);
      this.previewElement = this.querySelector('.field-media-preview');
      if (!this.button || !this.inputElement || !this.buttonClearEl || !this.modalElement || !this.buttonSaveSelectedElement) {
        throw new Error('Misconfiguaration...');
      }
      this.button.addEventListener('click', this.show);

      // Bootstrap modal init
      if (this.modalElement && window.bootstrap && window.bootstrap.Modal && !window.bootstrap.Modal.getInstance(this.modalElement)) {
        Joomla.initialiseModal(this.modalElement, {
          isJoomla: true
        });
      }
      if (this.buttonClearEl) {
        this.buttonClearEl.addEventListener('click', this.clearValue);
      }
      this.supportedExtensions = Joomla.getOptions('media-picker', {});
      if (!Object.keys(this.supportedExtensions).length) {
        throw new Error('Joomla API is not properly initiated');
      }
      this.inputElement.removeAttribute('readonly');
      this.inputElement.addEventListener('change', this.validateValue);
      this.updatePreview();
    };
    _proto.disconnectedCallback = function disconnectedCallback() {
      if (this.button) {
        this.button.removeEventListener('click', this.show);
      }
      if (this.buttonClearEl) {
        this.buttonClearEl.removeEventListener('click', this.clearValue);
      }
      if (this.inputElement) {
        this.inputElement.removeEventListener('change', this.validateValue);
      }
    };
    _proto.onSelected = function onSelected(event) {
      event.preventDefault();
      event.stopPropagation();
      this.modalClose();
      return false;
    };
    _proto.show = function show() {
      this.modalElement.open();
      Joomla.selectedMediaFile = {};
      this.buttonSaveSelectedElement.addEventListener('click', this.onSelected);
    };
    _proto.modalClose = /*#__PURE__*/function () {
      var _modalClose = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.prev = 0;
              _context.next = 3;
              return Joomla.getMedia(Joomla.selectedMediaFile, this.inputElement, this);
            case 3:
              _context.next = 8;
              break;
            case 5:
              _context.prev = 5;
              _context.t0 = _context["catch"](0);
              Joomla.renderMessages({
                error: [Joomla.Text._('JLIB_APPLICATION_ERROR_SERVER')]
              });
            case 8:
              Joomla.selectedMediaFile = {};
              Joomla.Modal.getCurrent().close();
            case 10:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[0, 5]]);
      }));
      function modalClose() {
        return _modalClose.apply(this, arguments);
      }
      return modalClose;
    }();
    _proto.setValue = function setValue(value) {
      this.inputElement.value = value;
      this.validatedUrl = value;
      this.mimeType = Joomla.selectedMediaFile.fileType;
      this.updatePreview();

      // trigger change event both on the input and on the custom element
      this.inputElement.dispatchEvent(new Event('change'));
      this.dispatchEvent(new CustomEvent('change', {
        detail: {
          value: value
        },
        bubbles: true
      }));
    };
    _proto.validateValue = /*#__PURE__*/function () {
      var _validateValue = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(event) {
        var _this2 = this;
        var value, hashedUrl, urlParts, rest;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              value = event.target.value;
              if (!(this.validatedUrl === value || value === '')) {
                _context2.next = 3;
                break;
              }
              return _context2.abrupt("return");
            case 3:
              if (/^(http(s)?:\/\/).+$/.test(value)) {
                try {
                  fetch(value).then(function (response) {
                    if (response.status === 200) {
                      _this2.validatedUrl = value;
                      _this2.markValid();
                    } else {
                      _this2.validatedUrl = value;
                      _this2.markInvalid();
                    }
                  });
                } catch (err) {
                  this.validatedUrl = value;
                  this.markInvalid();
                }
              } else {
                if (/^\//.test(value)) {
                  value = value.substring(1);
                }
                hashedUrl = value.split('#');
                urlParts = hashedUrl[0].split('/');
                rest = urlParts.slice(1);
                fetch(Joomla.getOptions('system.paths').rootFull + "/" + value).then(function (response) {
                  return response.blob();
                }).then(function (blob) {
                  if (blob.type.includes('image')) {
                    var img = new Image();
                    img.src = URL.createObjectURL(blob);
                    img.onload = function () {
                      _this2.inputElement.value = urlParts[0] + "/" + rest.join('/') + "#joomlaImage://local-" + urlParts[0] + "/" + rest.join('/') + "?width=" + img.width + "&height=" + img.height;
                      _this2.validatedUrl = urlParts[0] + "/" + rest.join('/') + "#joomlaImage://local-" + urlParts[0] + "/" + rest.join('/') + "?width=" + img.width + "&height=" + img.height;
                      _this2.markValid();
                    };
                  } else if (blob.type.includes('audio')) {
                    _this2.mimeType = blob.type;
                    _this2.inputElement.value = value;
                    _this2.validatedUrl = value;
                    _this2.markValid();
                  } else if (blob.type.includes('video')) {
                    _this2.mimeType = blob.type;
                    _this2.inputElement.value = value;
                    _this2.validatedUrl = value;
                    _this2.markValid();
                  } else if (blob.type.includes('application/pdf')) {
                    _this2.mimeType = blob.type;
                    _this2.inputElement.value = value;
                    _this2.validatedUrl = value;
                    _this2.markValid();
                  } else {
                    _this2.validatedUrl = value;
                    _this2.markInvalid();
                  }
                }).catch(function () {
                  _this2.setValue(value);
                  _this2.validatedUrl = value;
                  _this2.markInvalid();
                });
              }
            case 4:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this);
      }));
      function validateValue(_x) {
        return _validateValue.apply(this, arguments);
      }
      return validateValue;
    }();
    _proto.markValid = function markValid() {
      this.inputElement.removeAttribute('required');
      this.inputElement.removeAttribute('pattern');
      if (document.formvalidator) {
        document.formvalidator.validate(this.inputElement);
      }
    };
    _proto.markInvalid = function markInvalid() {
      this.inputElement.setAttribute('required', '');
      this.inputElement.setAttribute('pattern', '/^(http://INVALID/).+$/');
      if (document.formvalidator) {
        document.formvalidator.validate(this.inputElement);
      }
    };
    _proto.clearValue = function clearValue() {
      this.setValue('');
      this.validatedUrl = '';
      this.inputElement.removeAttribute('required');
      this.inputElement.removeAttribute('pattern');
      if (document.formvalidator) {
        document.formvalidator.validate(this.inputElement);
      }
    };
    _proto.updatePreview = function updatePreview() {
      var _this3 = this;
      if (['true', 'static'].indexOf(this.preview) === -1 || this.preview === 'false' || !this.previewElement) {
        return;
      }

      // Reset preview
      if (this.preview) {
        var value = this.inputElement.value;
        var supportedExtensions = this.supportedExtensions;
        if (!value) {
          this.buttonClearEl.style.display = 'none';
          this.previewElement.innerHTML = Joomla.sanitizeHtml('<span class="field-media-preview-icon"></span>');
        } else {
          var type;
          this.buttonClearEl.style.display = '';
          this.previewElement.innerHTML = '';
          var ext = getExtension(value);
          if (supportedExtensions.images.includes(ext)) type = 'images';
          if (supportedExtensions.audios.includes(ext)) type = 'audios';
          if (supportedExtensions.videos.includes(ext)) type = 'videos';
          if (supportedExtensions.documents.includes(ext)) type = 'documents';
          var previewElement;
          var mediaType = {
            images: function images() {
              if (supportedExtensions.images.includes(ext)) {
                previewElement = new Image();
                previewElement.src = /http/.test(value) ? value : Joomla.getOptions('system.paths').rootFull + value;
                previewElement.setAttribute('alt', '');
              }
            },
            audios: function audios() {
              if (supportedExtensions.audios.includes(ext)) {
                previewElement = document.createElement('audio');
                previewElement.src = /http/.test(value) ? value : Joomla.getOptions('system.paths').rootFull + value;
                previewElement.setAttribute('controls', '');
              }
            },
            videos: function videos() {
              if (supportedExtensions.videos.includes(ext)) {
                previewElement = document.createElement('video');
                var previewElementSource = document.createElement('source');
                previewElementSource.src = /http/.test(value) ? value : Joomla.getOptions('system.paths').rootFull + value;
                previewElementSource.type = _this3.mimeType;
                previewElement.setAttribute('controls', '');
                previewElement.setAttribute('width', _this3.previewWidth);
                previewElement.setAttribute('height', _this3.previewHeight);
                previewElement.appendChild(previewElementSource);
              }
            },
            documents: function documents() {
              if (supportedExtensions.documents.includes(ext)) {
                previewElement = document.createElement('object');
                previewElement.data = /http/.test(value) ? value : Joomla.getOptions('system.paths').rootFull + value;
                previewElement.type = _this3.mimeType;
                previewElement.setAttribute('width', _this3.previewWidth);
                previewElement.setAttribute('height', _this3.previewHeight);
              }
            }
          };

          // @todo more checks
          if (this.givenType && ['images', 'audios', 'videos', 'documents'].includes(this.givenType)) {
            mediaType[this.givenType]();
          } else if (type && ['images', 'audios', 'videos', 'documents'].includes(type)) {
            mediaType[type]();
          } else {
            return;
          }
          this.previewElement.style.width = this.previewWidth;
          this.previewElement.appendChild(previewElement);
        }
      }
    };
    _createClass(JoomlaFieldMedia, [{
      key: "type",
      get: function get() {
        return this.getAttribute('type');
      },
      set: function set(value) {
        this.setAttribute('type', value);
      }
    }, {
      key: "basePath",
      get: function get() {
        return this.getAttribute('base-path');
      },
      set: function set(value) {
        this.setAttribute('base-path', value);
      }
    }, {
      key: "rootFolder",
      get: function get() {
        return this.getAttribute('root-folder');
      },
      set: function set(value) {
        this.setAttribute('root-folder', value);
      }
    }, {
      key: "url",
      get: function get() {
        return this.getAttribute('url');
      },
      set: function set(value) {
        this.setAttribute('url', value);
      }
    }, {
      key: "modalContainer",
      get: function get() {
        return this.getAttribute('modal-container');
      },
      set: function set(value) {
        this.setAttribute('modal-container', value);
      }
    }, {
      key: "input",
      get: function get() {
        return this.getAttribute('input');
      },
      set: function set(value) {
        this.setAttribute('input', value);
      }
    }, {
      key: "buttonSelect",
      get: function get() {
        return this.getAttribute('button-select');
      },
      set: function set(value) {
        this.setAttribute('button-select', value);
      }
    }, {
      key: "buttonClear",
      get: function get() {
        return this.getAttribute('button-clear');
      },
      set: function set(value) {
        this.setAttribute('button-clear', value);
      }
    }, {
      key: "buttonSaveSelected",
      get: function get() {
        return this.getAttribute('button-save-selected');
      },
      set: function set(value) {
        this.setAttribute('button-save-selected', value);
      }
    }, {
      key: "modalWidth",
      get: function get() {
        return parseInt(this.getAttribute('modal-width'), 10);
      },
      set: function set(value) {
        this.setAttribute('modal-width', value);
      }
    }, {
      key: "modalHeight",
      get: function get() {
        return parseInt(this.getAttribute('modal-height'), 10);
      },
      set: function set(value) {
        this.setAttribute('modal-height', value);
      }
    }, {
      key: "previewWidth",
      get: function get() {
        return parseInt(this.getAttribute('preview-width'), 10);
      },
      set: function set(value) {
        this.setAttribute('preview-width', value);
      }
    }, {
      key: "previewHeight",
      get: function get() {
        return parseInt(this.getAttribute('preview-height'), 10);
      },
      set: function set(value) {
        this.setAttribute('preview-height', value);
      }
    }, {
      key: "preview",
      get: function get() {
        return this.getAttribute('preview');
      },
      set: function set(value) {
        this.setAttribute('preview', value);
      }
    }, {
      key: "previewContainer",
      get: function get() {
        return this.getAttribute('preview-container');
      }
    }], [{
      key: "observedAttributes",
      get: function get() {
        return ['type', 'base-path', 'root-folder', 'url', 'modal-container', 'modal-width', 'modal-height', 'input', 'button-select', 'button-clear', 'button-save-selected', 'preview', 'preview-width', 'preview-height'];
      }
    }]);
    return JoomlaFieldMedia;
  }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
  customElements.define('joomla-field-media', JoomlaFieldMedia);

})();
