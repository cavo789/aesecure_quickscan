var JoomlaMediaManager = (function () {
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
  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }
  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
    return arr2;
  }
  function _createForOfIteratorHelperLoose(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
    if (it) return (it = it.call(o)).next.bind(it);
    if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
      if (it) o = it;
      var i = 0;
      return function () {
        if (i >= o.length) return {
          done: true
        };
        return {
          done: false,
          value: o[i++]
        };
      };
    }
    throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
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

  var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  var check = function (it) {
    return it && it.Math == Math && it;
  };

  // https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
  var global$u =
    // eslint-disable-next-line es/no-global-this -- safe
    check(typeof globalThis == 'object' && globalThis) ||
    check(typeof window == 'object' && window) ||
    // eslint-disable-next-line no-restricted-globals -- safe
    check(typeof self == 'object' && self) ||
    check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
    // eslint-disable-next-line no-new-func -- fallback
    (function () { return this; })() || Function('return this')();

  var objectGetOwnPropertyDescriptor = {};

  var fails$I = function (exec) {
    try {
      return !!exec();
    } catch (error) {
      return true;
    }
  };

  var fails$H = fails$I;

  // Detect IE8's incomplete defineProperty implementation
  var descriptors = !fails$H(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
  });

  var fails$G = fails$I;

  var functionBindNative = !fails$G(function () {
    // eslint-disable-next-line es/no-function-prototype-bind -- safe
    var test = (function () { /* empty */ }).bind();
    // eslint-disable-next-line no-prototype-builtins -- safe
    return typeof test != 'function' || test.hasOwnProperty('prototype');
  });

  var NATIVE_BIND$3 = functionBindNative;

  var call$u = Function.prototype.call;

  var functionCall = NATIVE_BIND$3 ? call$u.bind(call$u) : function () {
    return call$u.apply(call$u, arguments);
  };

  var objectPropertyIsEnumerable = {};

  var $propertyIsEnumerable$2 = {}.propertyIsEnumerable;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getOwnPropertyDescriptor$8 = Object.getOwnPropertyDescriptor;

  // Nashorn ~ JDK8 bug
  var NASHORN_BUG = getOwnPropertyDescriptor$8 && !$propertyIsEnumerable$2.call({ 1: 2 }, 1);

  // `Object.prototype.propertyIsEnumerable` method implementation
  // https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
  objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
    var descriptor = getOwnPropertyDescriptor$8(this, V);
    return !!descriptor && descriptor.enumerable;
  } : $propertyIsEnumerable$2;

  var createPropertyDescriptor$7 = function (bitmap, value) {
    return {
      enumerable: !(bitmap & 1),
      configurable: !(bitmap & 2),
      writable: !(bitmap & 4),
      value: value
    };
  };

  var NATIVE_BIND$2 = functionBindNative;

  var FunctionPrototype$3 = Function.prototype;
  var call$t = FunctionPrototype$3.call;
  var uncurryThisWithBind = NATIVE_BIND$2 && FunctionPrototype$3.bind.bind(call$t, call$t);

  var functionUncurryThis = NATIVE_BIND$2 ? uncurryThisWithBind : function (fn) {
    return function () {
      return call$t.apply(fn, arguments);
    };
  };

  var uncurryThis$I = functionUncurryThis;

  var toString$l = uncurryThis$I({}.toString);
  var stringSlice$c = uncurryThis$I(''.slice);

  var classofRaw$2 = function (it) {
    return stringSlice$c(toString$l(it), 8, -1);
  };

  var uncurryThis$H = functionUncurryThis;
  var fails$F = fails$I;
  var classof$d = classofRaw$2;

  var $Object$5 = Object;
  var split$3 = uncurryThis$H(''.split);

  // fallback for non-array-like ES3 and non-enumerable old V8 strings
  var indexedObject = fails$F(function () {
    // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
    // eslint-disable-next-line no-prototype-builtins -- safe
    return !$Object$5('z').propertyIsEnumerable(0);
  }) ? function (it) {
    return classof$d(it) == 'String' ? split$3(it, '') : $Object$5(it);
  } : $Object$5;

  // we can't use just `it == null` since of `document.all` special case
  // https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
  var isNullOrUndefined$b = function (it) {
    return it === null || it === undefined;
  };

  var isNullOrUndefined$a = isNullOrUndefined$b;

  var $TypeError$h = TypeError;

  // `RequireObjectCoercible` abstract operation
  // https://tc39.es/ecma262/#sec-requireobjectcoercible
  var requireObjectCoercible$d = function (it) {
    if (isNullOrUndefined$a(it)) throw $TypeError$h("Can't call method on " + it);
    return it;
  };

  // toObject with fallback for non-array-like ES3 strings
  var IndexedObject$3 = indexedObject;
  var requireObjectCoercible$c = requireObjectCoercible$d;

  var toIndexedObject$a = function (it) {
    return IndexedObject$3(requireObjectCoercible$c(it));
  };

  var documentAll$2 = typeof document == 'object' && document.all;

  // https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
  // eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
  var IS_HTMLDDA = typeof documentAll$2 == 'undefined' && documentAll$2 !== undefined;

  var documentAll_1 = {
    all: documentAll$2,
    IS_HTMLDDA: IS_HTMLDDA
  };

  var $documentAll$1 = documentAll_1;

  var documentAll$1 = $documentAll$1.all;

  // `IsCallable` abstract operation
  // https://tc39.es/ecma262/#sec-iscallable
  var isCallable$t = $documentAll$1.IS_HTMLDDA ? function (argument) {
    return typeof argument == 'function' || argument === documentAll$1;
  } : function (argument) {
    return typeof argument == 'function';
  };

  var isCallable$s = isCallable$t;
  var $documentAll = documentAll_1;

  var documentAll = $documentAll.all;

  var isObject$q = $documentAll.IS_HTMLDDA ? function (it) {
    return typeof it == 'object' ? it !== null : isCallable$s(it) || it === documentAll;
  } : function (it) {
    return typeof it == 'object' ? it !== null : isCallable$s(it);
  };

  var global$t = global$u;
  var isCallable$r = isCallable$t;

  var aFunction = function (argument) {
    return isCallable$r(argument) ? argument : undefined;
  };

  var getBuiltIn$a = function (namespace, method) {
    return arguments.length < 2 ? aFunction(global$t[namespace]) : global$t[namespace] && global$t[namespace][method];
  };

  var uncurryThis$G = functionUncurryThis;

  var objectIsPrototypeOf = uncurryThis$G({}.isPrototypeOf);

  var engineUserAgent = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

  var global$s = global$u;
  var userAgent$5 = engineUserAgent;

  var process$4 = global$s.process;
  var Deno$1 = global$s.Deno;
  var versions = process$4 && process$4.versions || Deno$1 && Deno$1.version;
  var v8 = versions && versions.v8;
  var match, version$1;

  if (v8) {
    match = v8.split('.');
    // in old Chrome, versions of V8 isn't V8 = Chrome / 10
    // but their correct versions are not interesting for us
    version$1 = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
  }

  // BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
  // so check `userAgent` even if `.v8` exists, but 0
  if (!version$1 && userAgent$5) {
    match = userAgent$5.match(/Edge\/(\d+)/);
    if (!match || match[1] >= 74) {
      match = userAgent$5.match(/Chrome\/(\d+)/);
      if (match) version$1 = +match[1];
    }
  }

  var engineV8Version = version$1;

  /* eslint-disable es/no-symbol -- required for testing */

  var V8_VERSION$3 = engineV8Version;
  var fails$E = fails$I;

  // eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
  var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$E(function () {
    var symbol = Symbol();
    // Chrome 38 Symbol has incorrect toString conversion
    // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
    return !String(symbol) || !(Object(symbol) instanceof Symbol) ||
      // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
      !Symbol.sham && V8_VERSION$3 && V8_VERSION$3 < 41;
  });

  /* eslint-disable es/no-symbol -- required for testing */

  var NATIVE_SYMBOL$6 = symbolConstructorDetection;

  var useSymbolAsUid = NATIVE_SYMBOL$6
    && !Symbol.sham
    && typeof Symbol.iterator == 'symbol';

  var getBuiltIn$9 = getBuiltIn$a;
  var isCallable$q = isCallable$t;
  var isPrototypeOf$6 = objectIsPrototypeOf;
  var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

  var $Object$4 = Object;

  var isSymbol$6 = USE_SYMBOL_AS_UID$1 ? function (it) {
    return typeof it == 'symbol';
  } : function (it) {
    var $Symbol = getBuiltIn$9('Symbol');
    return isCallable$q($Symbol) && isPrototypeOf$6($Symbol.prototype, $Object$4(it));
  };

  var $String$6 = String;

  var tryToString$6 = function (argument) {
    try {
      return $String$6(argument);
    } catch (error) {
      return 'Object';
    }
  };

  var isCallable$p = isCallable$t;
  var tryToString$5 = tryToString$6;

  var $TypeError$g = TypeError;

  // `Assert: IsCallable(argument) is true`
  var aCallable$9 = function (argument) {
    if (isCallable$p(argument)) return argument;
    throw $TypeError$g(tryToString$5(argument) + ' is not a function');
  };

  var aCallable$8 = aCallable$9;
  var isNullOrUndefined$9 = isNullOrUndefined$b;

  // `GetMethod` abstract operation
  // https://tc39.es/ecma262/#sec-getmethod
  var getMethod$7 = function (V, P) {
    var func = V[P];
    return isNullOrUndefined$9(func) ? undefined : aCallable$8(func);
  };

  var call$s = functionCall;
  var isCallable$o = isCallable$t;
  var isObject$p = isObject$q;

  var $TypeError$f = TypeError;

  // `OrdinaryToPrimitive` abstract operation
  // https://tc39.es/ecma262/#sec-ordinarytoprimitive
  var ordinaryToPrimitive$1 = function (input, pref) {
    var fn, val;
    if (pref === 'string' && isCallable$o(fn = input.toString) && !isObject$p(val = call$s(fn, input))) return val;
    if (isCallable$o(fn = input.valueOf) && !isObject$p(val = call$s(fn, input))) return val;
    if (pref !== 'string' && isCallable$o(fn = input.toString) && !isObject$p(val = call$s(fn, input))) return val;
    throw $TypeError$f("Can't convert object to primitive value");
  };

  var shared$7 = {exports: {}};

  var isPure = false;

  var global$r = global$u;

  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var defineProperty$9 = Object.defineProperty;

  var defineGlobalProperty$3 = function (key, value) {
    try {
      defineProperty$9(global$r, key, { value: value, configurable: true, writable: true });
    } catch (error) {
      global$r[key] = value;
    } return value;
  };

  var global$q = global$u;
  var defineGlobalProperty$2 = defineGlobalProperty$3;

  var SHARED = '__core-js_shared__';
  var store$4 = global$q[SHARED] || defineGlobalProperty$2(SHARED, {});

  var sharedStore = store$4;

  var store$3 = sharedStore;

  (shared$7.exports = function (key, value) {
    return store$3[key] || (store$3[key] = value !== undefined ? value : {});
  })('versions', []).push({
    version: '3.29.0',
    mode: 'global',
    copyright: '© 2014-2023 Denis Pushkarev (zloirock.ru)',
    license: 'https://github.com/zloirock/core-js/blob/v3.29.0/LICENSE',
    source: 'https://github.com/zloirock/core-js'
  });

  var requireObjectCoercible$b = requireObjectCoercible$d;

  var $Object$3 = Object;

  // `ToObject` abstract operation
  // https://tc39.es/ecma262/#sec-toobject
  var toObject$e = function (argument) {
    return $Object$3(requireObjectCoercible$b(argument));
  };

  var uncurryThis$F = functionUncurryThis;
  var toObject$d = toObject$e;

  var hasOwnProperty$2 = uncurryThis$F({}.hasOwnProperty);

  // `HasOwnProperty` abstract operation
  // https://tc39.es/ecma262/#sec-hasownproperty
  // eslint-disable-next-line es/no-object-hasown -- safe
  var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
    return hasOwnProperty$2(toObject$d(it), key);
  };

  var uncurryThis$E = functionUncurryThis;

  var id$2 = 0;
  var postfix = Math.random();
  var toString$k = uncurryThis$E(1.0.toString);

  var uid$6 = function (key) {
    return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$k(++id$2 + postfix, 36);
  };

  var global$p = global$u;
  var shared$6 = shared$7.exports;
  var hasOwn$n = hasOwnProperty_1;
  var uid$5 = uid$6;
  var NATIVE_SYMBOL$5 = symbolConstructorDetection;
  var USE_SYMBOL_AS_UID = useSymbolAsUid;

  var Symbol$1 = global$p.Symbol;
  var WellKnownSymbolsStore$1 = shared$6('wks');
  var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$1['for'] || Symbol$1 : Symbol$1 && Symbol$1.withoutSetter || uid$5;

  var wellKnownSymbol$r = function (name) {
    if (!hasOwn$n(WellKnownSymbolsStore$1, name)) {
      WellKnownSymbolsStore$1[name] = NATIVE_SYMBOL$5 && hasOwn$n(Symbol$1, name)
        ? Symbol$1[name]
        : createWellKnownSymbol('Symbol.' + name);
    } return WellKnownSymbolsStore$1[name];
  };

  var call$r = functionCall;
  var isObject$o = isObject$q;
  var isSymbol$5 = isSymbol$6;
  var getMethod$6 = getMethod$7;
  var ordinaryToPrimitive = ordinaryToPrimitive$1;
  var wellKnownSymbol$q = wellKnownSymbol$r;

  var $TypeError$e = TypeError;
  var TO_PRIMITIVE = wellKnownSymbol$q('toPrimitive');

  // `ToPrimitive` abstract operation
  // https://tc39.es/ecma262/#sec-toprimitive
  var toPrimitive$2 = function (input, pref) {
    if (!isObject$o(input) || isSymbol$5(input)) return input;
    var exoticToPrim = getMethod$6(input, TO_PRIMITIVE);
    var result;
    if (exoticToPrim) {
      if (pref === undefined) pref = 'default';
      result = call$r(exoticToPrim, input, pref);
      if (!isObject$o(result) || isSymbol$5(result)) return result;
      throw $TypeError$e("Can't convert object to primitive value");
    }
    if (pref === undefined) pref = 'number';
    return ordinaryToPrimitive(input, pref);
  };

  var toPrimitive$1 = toPrimitive$2;
  var isSymbol$4 = isSymbol$6;

  // `ToPropertyKey` abstract operation
  // https://tc39.es/ecma262/#sec-topropertykey
  var toPropertyKey$5 = function (argument) {
    var key = toPrimitive$1(argument, 'string');
    return isSymbol$4(key) ? key : key + '';
  };

  var global$o = global$u;
  var isObject$n = isObject$q;

  var document$3 = global$o.document;
  // typeof document.createElement is 'object' in old IE
  var EXISTS$1 = isObject$n(document$3) && isObject$n(document$3.createElement);

  var documentCreateElement$2 = function (it) {
    return EXISTS$1 ? document$3.createElement(it) : {};
  };

  var DESCRIPTORS$l = descriptors;
  var fails$D = fails$I;
  var createElement$1 = documentCreateElement$2;

  // Thanks to IE8 for its funny defineProperty
  var ie8DomDefine = !DESCRIPTORS$l && !fails$D(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty(createElement$1('div'), 'a', {
      get: function () { return 7; }
    }).a != 7;
  });

  var DESCRIPTORS$k = descriptors;
  var call$q = functionCall;
  var propertyIsEnumerableModule$2 = objectPropertyIsEnumerable;
  var createPropertyDescriptor$6 = createPropertyDescriptor$7;
  var toIndexedObject$9 = toIndexedObject$a;
  var toPropertyKey$4 = toPropertyKey$5;
  var hasOwn$m = hasOwnProperty_1;
  var IE8_DOM_DEFINE$1 = ie8DomDefine;

  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var $getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

  // `Object.getOwnPropertyDescriptor` method
  // https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
  objectGetOwnPropertyDescriptor.f = DESCRIPTORS$k ? $getOwnPropertyDescriptor$2 : function getOwnPropertyDescriptor(O, P) {
    O = toIndexedObject$9(O);
    P = toPropertyKey$4(P);
    if (IE8_DOM_DEFINE$1) try {
      return $getOwnPropertyDescriptor$2(O, P);
    } catch (error) { /* empty */ }
    if (hasOwn$m(O, P)) return createPropertyDescriptor$6(!call$q(propertyIsEnumerableModule$2.f, O, P), O[P]);
  };

  var objectDefineProperty = {};

  var DESCRIPTORS$j = descriptors;
  var fails$C = fails$I;

  // V8 ~ Chrome 36-
  // https://bugs.chromium.org/p/v8/issues/detail?id=3334
  var v8PrototypeDefineBug = DESCRIPTORS$j && fails$C(function () {
    // eslint-disable-next-line es/no-object-defineproperty -- required for testing
    return Object.defineProperty(function () { /* empty */ }, 'prototype', {
      value: 42,
      writable: false
    }).prototype != 42;
  });

  var isObject$m = isObject$q;

  var $String$5 = String;
  var $TypeError$d = TypeError;

  // `Assert: Type(argument) is Object`
  var anObject$r = function (argument) {
    if (isObject$m(argument)) return argument;
    throw $TypeError$d($String$5(argument) + ' is not an object');
  };

  var DESCRIPTORS$i = descriptors;
  var IE8_DOM_DEFINE = ie8DomDefine;
  var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
  var anObject$q = anObject$r;
  var toPropertyKey$3 = toPropertyKey$5;

  var $TypeError$c = TypeError;
  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var $defineProperty$1 = Object.defineProperty;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;
  var ENUMERABLE = 'enumerable';
  var CONFIGURABLE$1 = 'configurable';
  var WRITABLE = 'writable';

  // `Object.defineProperty` method
  // https://tc39.es/ecma262/#sec-object.defineproperty
  objectDefineProperty.f = DESCRIPTORS$i ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
    anObject$q(O);
    P = toPropertyKey$3(P);
    anObject$q(Attributes);
    if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
      var current = $getOwnPropertyDescriptor$1(O, P);
      if (current && current[WRITABLE]) {
        O[P] = Attributes.value;
        Attributes = {
          configurable: CONFIGURABLE$1 in Attributes ? Attributes[CONFIGURABLE$1] : current[CONFIGURABLE$1],
          enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
          writable: false
        };
      }
    } return $defineProperty$1(O, P, Attributes);
  } : $defineProperty$1 : function defineProperty(O, P, Attributes) {
    anObject$q(O);
    P = toPropertyKey$3(P);
    anObject$q(Attributes);
    if (IE8_DOM_DEFINE) try {
      return $defineProperty$1(O, P, Attributes);
    } catch (error) { /* empty */ }
    if ('get' in Attributes || 'set' in Attributes) throw $TypeError$c('Accessors not supported');
    if ('value' in Attributes) O[P] = Attributes.value;
    return O;
  };

  var DESCRIPTORS$h = descriptors;
  var definePropertyModule$7 = objectDefineProperty;
  var createPropertyDescriptor$5 = createPropertyDescriptor$7;

  var createNonEnumerableProperty$6 = DESCRIPTORS$h ? function (object, key, value) {
    return definePropertyModule$7.f(object, key, createPropertyDescriptor$5(1, value));
  } : function (object, key, value) {
    object[key] = value;
    return object;
  };

  var makeBuiltIn$3 = {exports: {}};

  var DESCRIPTORS$g = descriptors;
  var hasOwn$l = hasOwnProperty_1;

  var FunctionPrototype$2 = Function.prototype;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getDescriptor = DESCRIPTORS$g && Object.getOwnPropertyDescriptor;

  var EXISTS = hasOwn$l(FunctionPrototype$2, 'name');
  // additional protection from minified / mangled / dropped function names
  var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
  var CONFIGURABLE = EXISTS && (!DESCRIPTORS$g || (DESCRIPTORS$g && getDescriptor(FunctionPrototype$2, 'name').configurable));

  var functionName = {
    EXISTS: EXISTS,
    PROPER: PROPER,
    CONFIGURABLE: CONFIGURABLE
  };

  var uncurryThis$D = functionUncurryThis;
  var isCallable$n = isCallable$t;
  var store$2 = sharedStore;

  var functionToString$1 = uncurryThis$D(Function.toString);

  // this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
  if (!isCallable$n(store$2.inspectSource)) {
    store$2.inspectSource = function (it) {
      return functionToString$1(it);
    };
  }

  var inspectSource$3 = store$2.inspectSource;

  var global$n = global$u;
  var isCallable$m = isCallable$t;

  var WeakMap$2 = global$n.WeakMap;

  var weakMapBasicDetection = isCallable$m(WeakMap$2) && /native code/.test(String(WeakMap$2));

  var shared$5 = shared$7.exports;
  var uid$4 = uid$6;

  var keys$1 = shared$5('keys');

  var sharedKey$4 = function (key) {
    return keys$1[key] || (keys$1[key] = uid$4(key));
  };

  var hiddenKeys$6 = {};

  var NATIVE_WEAK_MAP$1 = weakMapBasicDetection;
  var global$m = global$u;
  var isObject$l = isObject$q;
  var createNonEnumerableProperty$5 = createNonEnumerableProperty$6;
  var hasOwn$k = hasOwnProperty_1;
  var shared$4 = sharedStore;
  var sharedKey$3 = sharedKey$4;
  var hiddenKeys$5 = hiddenKeys$6;

  var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
  var TypeError$6 = global$m.TypeError;
  var WeakMap$1 = global$m.WeakMap;
  var set$5, get$3, has$2;

  var enforce = function (it) {
    return has$2(it) ? get$3(it) : set$5(it, {});
  };

  var getterFor = function (TYPE) {
    return function (it) {
      var state;
      if (!isObject$l(it) || (state = get$3(it)).type !== TYPE) {
        throw TypeError$6('Incompatible receiver, ' + TYPE + ' required');
      } return state;
    };
  };

  if (NATIVE_WEAK_MAP$1 || shared$4.state) {
    var store$1 = shared$4.state || (shared$4.state = new WeakMap$1());
    /* eslint-disable no-self-assign -- prototype methods protection */
    store$1.get = store$1.get;
    store$1.has = store$1.has;
    store$1.set = store$1.set;
    /* eslint-enable no-self-assign -- prototype methods protection */
    set$5 = function (it, metadata) {
      if (store$1.has(it)) throw TypeError$6(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      store$1.set(it, metadata);
      return metadata;
    };
    get$3 = function (it) {
      return store$1.get(it) || {};
    };
    has$2 = function (it) {
      return store$1.has(it);
    };
  } else {
    var STATE = sharedKey$3('state');
    hiddenKeys$5[STATE] = true;
    set$5 = function (it, metadata) {
      if (hasOwn$k(it, STATE)) throw TypeError$6(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      createNonEnumerableProperty$5(it, STATE, metadata);
      return metadata;
    };
    get$3 = function (it) {
      return hasOwn$k(it, STATE) ? it[STATE] : {};
    };
    has$2 = function (it) {
      return hasOwn$k(it, STATE);
    };
  }

  var internalState = {
    set: set$5,
    get: get$3,
    has: has$2,
    enforce: enforce,
    getterFor: getterFor
  };

  var uncurryThis$C = functionUncurryThis;
  var fails$B = fails$I;
  var isCallable$l = isCallable$t;
  var hasOwn$j = hasOwnProperty_1;
  var DESCRIPTORS$f = descriptors;
  var CONFIGURABLE_FUNCTION_NAME$1 = functionName.CONFIGURABLE;
  var inspectSource$2 = inspectSource$3;
  var InternalStateModule$8 = internalState;

  var enforceInternalState$1 = InternalStateModule$8.enforce;
  var getInternalState$4 = InternalStateModule$8.get;
  var $String$4 = String;
  // eslint-disable-next-line es/no-object-defineproperty -- safe
  var defineProperty$8 = Object.defineProperty;
  var stringSlice$b = uncurryThis$C(''.slice);
  var replace$9 = uncurryThis$C(''.replace);
  var join$3 = uncurryThis$C([].join);

  var CONFIGURABLE_LENGTH = DESCRIPTORS$f && !fails$B(function () {
    return defineProperty$8(function () { /* empty */ }, 'length', { value: 8 }).length !== 8;
  });

  var TEMPLATE = String(String).split('String');

  var makeBuiltIn$2 = makeBuiltIn$3.exports = function (value, name, options) {
    if (stringSlice$b($String$4(name), 0, 7) === 'Symbol(') {
      name = '[' + replace$9($String$4(name), /^Symbol\(([^)]*)\)/, '$1') + ']';
    }
    if (options && options.getter) name = 'get ' + name;
    if (options && options.setter) name = 'set ' + name;
    if (!hasOwn$j(value, 'name') || (CONFIGURABLE_FUNCTION_NAME$1 && value.name !== name)) {
      if (DESCRIPTORS$f) defineProperty$8(value, 'name', { value: name, configurable: true });
      else value.name = name;
    }
    if (CONFIGURABLE_LENGTH && options && hasOwn$j(options, 'arity') && value.length !== options.arity) {
      defineProperty$8(value, 'length', { value: options.arity });
    }
    try {
      if (options && hasOwn$j(options, 'constructor') && options.constructor) {
        if (DESCRIPTORS$f) defineProperty$8(value, 'prototype', { writable: false });
      // in V8 ~ Chrome 53, prototypes of some methods, like `Array.prototype.values`, are non-writable
      } else if (value.prototype) value.prototype = undefined;
    } catch (error) { /* empty */ }
    var state = enforceInternalState$1(value);
    if (!hasOwn$j(state, 'source')) {
      state.source = join$3(TEMPLATE, typeof name == 'string' ? name : '');
    } return value;
  };

  // add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
  // eslint-disable-next-line no-extend-native -- required
  Function.prototype.toString = makeBuiltIn$2(function toString() {
    return isCallable$l(this) && getInternalState$4(this).source || inspectSource$2(this);
  }, 'toString');

  var isCallable$k = isCallable$t;
  var definePropertyModule$6 = objectDefineProperty;
  var makeBuiltIn$1 = makeBuiltIn$3.exports;
  var defineGlobalProperty$1 = defineGlobalProperty$3;

  var defineBuiltIn$e = function (O, key, value, options) {
    if (!options) options = {};
    var simple = options.enumerable;
    var name = options.name !== undefined ? options.name : key;
    if (isCallable$k(value)) makeBuiltIn$1(value, name, options);
    if (options.global) {
      if (simple) O[key] = value;
      else defineGlobalProperty$1(key, value);
    } else {
      try {
        if (!options.unsafe) delete O[key];
        else if (O[key]) simple = true;
      } catch (error) { /* empty */ }
      if (simple) O[key] = value;
      else definePropertyModule$6.f(O, key, {
        value: value,
        enumerable: false,
        configurable: !options.nonConfigurable,
        writable: !options.nonWritable
      });
    } return O;
  };

  var objectGetOwnPropertyNames = {};

  var ceil = Math.ceil;
  var floor$5 = Math.floor;

  // `Math.trunc` method
  // https://tc39.es/ecma262/#sec-math.trunc
  // eslint-disable-next-line es/no-math-trunc -- safe
  var mathTrunc = Math.trunc || function trunc(x) {
    var n = +x;
    return (n > 0 ? floor$5 : ceil)(n);
  };

  var trunc = mathTrunc;

  // `ToIntegerOrInfinity` abstract operation
  // https://tc39.es/ecma262/#sec-tointegerorinfinity
  var toIntegerOrInfinity$8 = function (argument) {
    var number = +argument;
    // eslint-disable-next-line no-self-compare -- NaN check
    return number !== number || number === 0 ? 0 : trunc(number);
  };

  var toIntegerOrInfinity$7 = toIntegerOrInfinity$8;

  var max$4 = Math.max;
  var min$6 = Math.min;

  // Helper for a popular repeating case of the spec:
  // Let integer be ? ToInteger(index).
  // If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
  var toAbsoluteIndex$5 = function (index, length) {
    var integer = toIntegerOrInfinity$7(index);
    return integer < 0 ? max$4(integer + length, 0) : min$6(integer, length);
  };

  var toIntegerOrInfinity$6 = toIntegerOrInfinity$8;

  var min$5 = Math.min;

  // `ToLength` abstract operation
  // https://tc39.es/ecma262/#sec-tolength
  var toLength$6 = function (argument) {
    return argument > 0 ? min$5(toIntegerOrInfinity$6(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
  };

  var toLength$5 = toLength$6;

  // `LengthOfArrayLike` abstract operation
  // https://tc39.es/ecma262/#sec-lengthofarraylike
  var lengthOfArrayLike$c = function (obj) {
    return toLength$5(obj.length);
  };

  var toIndexedObject$8 = toIndexedObject$a;
  var toAbsoluteIndex$4 = toAbsoluteIndex$5;
  var lengthOfArrayLike$b = lengthOfArrayLike$c;

  // `Array.prototype.{ indexOf, includes }` methods implementation
  var createMethod$4 = function (IS_INCLUDES) {
    return function ($this, el, fromIndex) {
      var O = toIndexedObject$8($this);
      var length = lengthOfArrayLike$b(O);
      var index = toAbsoluteIndex$4(fromIndex, length);
      var value;
      // Array#includes uses SameValueZero equality algorithm
      // eslint-disable-next-line no-self-compare -- NaN check
      if (IS_INCLUDES && el != el) while (length > index) {
        value = O[index++];
        // eslint-disable-next-line no-self-compare -- NaN check
        if (value != value) return true;
      // Array#indexOf ignores holes, Array#includes - not
      } else for (;length > index; index++) {
        if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
      } return !IS_INCLUDES && -1;
    };
  };

  var arrayIncludes = {
    // `Array.prototype.includes` method
    // https://tc39.es/ecma262/#sec-array.prototype.includes
    includes: createMethod$4(true),
    // `Array.prototype.indexOf` method
    // https://tc39.es/ecma262/#sec-array.prototype.indexof
    indexOf: createMethod$4(false)
  };

  var uncurryThis$B = functionUncurryThis;
  var hasOwn$i = hasOwnProperty_1;
  var toIndexedObject$7 = toIndexedObject$a;
  var indexOf$1 = arrayIncludes.indexOf;
  var hiddenKeys$4 = hiddenKeys$6;

  var push$a = uncurryThis$B([].push);

  var objectKeysInternal = function (object, names) {
    var O = toIndexedObject$7(object);
    var i = 0;
    var result = [];
    var key;
    for (key in O) !hasOwn$i(hiddenKeys$4, key) && hasOwn$i(O, key) && push$a(result, key);
    // Don't enum bug & hidden keys
    while (names.length > i) if (hasOwn$i(O, key = names[i++])) {
      ~indexOf$1(result, key) || push$a(result, key);
    }
    return result;
  };

  // IE8- don't enum bug keys
  var enumBugKeys$3 = [
    'constructor',
    'hasOwnProperty',
    'isPrototypeOf',
    'propertyIsEnumerable',
    'toLocaleString',
    'toString',
    'valueOf'
  ];

  var internalObjectKeys$1 = objectKeysInternal;
  var enumBugKeys$2 = enumBugKeys$3;

  var hiddenKeys$3 = enumBugKeys$2.concat('length', 'prototype');

  // `Object.getOwnPropertyNames` method
  // https://tc39.es/ecma262/#sec-object.getownpropertynames
  // eslint-disable-next-line es/no-object-getownpropertynames -- safe
  objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
    return internalObjectKeys$1(O, hiddenKeys$3);
  };

  var objectGetOwnPropertySymbols = {};

  // eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
  objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

  var getBuiltIn$8 = getBuiltIn$a;
  var uncurryThis$A = functionUncurryThis;
  var getOwnPropertyNamesModule$2 = objectGetOwnPropertyNames;
  var getOwnPropertySymbolsModule$3 = objectGetOwnPropertySymbols;
  var anObject$p = anObject$r;

  var concat$2 = uncurryThis$A([].concat);

  // all object keys, includes non-enumerable and symbols
  var ownKeys$3 = getBuiltIn$8('Reflect', 'ownKeys') || function ownKeys(it) {
    var keys = getOwnPropertyNamesModule$2.f(anObject$p(it));
    var getOwnPropertySymbols = getOwnPropertySymbolsModule$3.f;
    return getOwnPropertySymbols ? concat$2(keys, getOwnPropertySymbols(it)) : keys;
  };

  var hasOwn$h = hasOwnProperty_1;
  var ownKeys$2 = ownKeys$3;
  var getOwnPropertyDescriptorModule$3 = objectGetOwnPropertyDescriptor;
  var definePropertyModule$5 = objectDefineProperty;

  var copyConstructorProperties$3 = function (target, source, exceptions) {
    var keys = ownKeys$2(source);
    var defineProperty = definePropertyModule$5.f;
    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule$3.f;
    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      if (!hasOwn$h(target, key) && !(exceptions && hasOwn$h(exceptions, key))) {
        defineProperty(target, key, getOwnPropertyDescriptor(source, key));
      }
    }
  };

  var fails$A = fails$I;
  var isCallable$j = isCallable$t;

  var replacement = /#|\.prototype\./;

  var isForced$4 = function (feature, detection) {
    var value = data[normalize(feature)];
    return value == POLYFILL ? true
      : value == NATIVE ? false
      : isCallable$j(detection) ? fails$A(detection)
      : !!detection;
  };

  var normalize = isForced$4.normalize = function (string) {
    return String(string).replace(replacement, '.').toLowerCase();
  };

  var data = isForced$4.data = {};
  var NATIVE = isForced$4.NATIVE = 'N';
  var POLYFILL = isForced$4.POLYFILL = 'P';

  var isForced_1 = isForced$4;

  var global$l = global$u;
  var getOwnPropertyDescriptor$7 = objectGetOwnPropertyDescriptor.f;
  var createNonEnumerableProperty$4 = createNonEnumerableProperty$6;
  var defineBuiltIn$d = defineBuiltIn$e;
  var defineGlobalProperty = defineGlobalProperty$3;
  var copyConstructorProperties$2 = copyConstructorProperties$3;
  var isForced$3 = isForced_1;

  /*
    options.target         - name of the target object
    options.global         - target is the global object
    options.stat           - export as static methods of target
    options.proto          - export as prototype methods of target
    options.real           - real prototype method for the `pure` version
    options.forced         - export even if the native feature is available
    options.bind           - bind methods to the target, required for the `pure` version
    options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
    options.unsafe         - use the simple assignment of property instead of delete + defineProperty
    options.sham           - add a flag to not completely full polyfills
    options.enumerable     - export as enumerable property
    options.dontCallGetSet - prevent calling a getter on target
    options.name           - the .name of the function if it does not match the key
  */
  var _export = function (options, source) {
    var TARGET = options.target;
    var GLOBAL = options.global;
    var STATIC = options.stat;
    var FORCED, target, key, targetProperty, sourceProperty, descriptor;
    if (GLOBAL) {
      target = global$l;
    } else if (STATIC) {
      target = global$l[TARGET] || defineGlobalProperty(TARGET, {});
    } else {
      target = (global$l[TARGET] || {}).prototype;
    }
    if (target) for (key in source) {
      sourceProperty = source[key];
      if (options.dontCallGetSet) {
        descriptor = getOwnPropertyDescriptor$7(target, key);
        targetProperty = descriptor && descriptor.value;
      } else targetProperty = target[key];
      FORCED = isForced$3(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
      // contained in target
      if (!FORCED && targetProperty !== undefined) {
        if (typeof sourceProperty == typeof targetProperty) continue;
        copyConstructorProperties$2(sourceProperty, targetProperty);
      }
      // add a flag to not completely full polyfills
      if (options.sham || (targetProperty && targetProperty.sham)) {
        createNonEnumerableProperty$4(sourceProperty, 'sham', true);
      }
      defineBuiltIn$d(target, key, sourceProperty, options);
    }
  };

  var internalObjectKeys = objectKeysInternal;
  var enumBugKeys$1 = enumBugKeys$3;

  // `Object.keys` method
  // https://tc39.es/ecma262/#sec-object.keys
  // eslint-disable-next-line es/no-object-keys -- safe
  var objectKeys$4 = Object.keys || function keys(O) {
    return internalObjectKeys(O, enumBugKeys$1);
  };

  var DESCRIPTORS$e = descriptors;
  var uncurryThis$z = functionUncurryThis;
  var call$p = functionCall;
  var fails$z = fails$I;
  var objectKeys$3 = objectKeys$4;
  var getOwnPropertySymbolsModule$2 = objectGetOwnPropertySymbols;
  var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
  var toObject$c = toObject$e;
  var IndexedObject$2 = indexedObject;

  // eslint-disable-next-line es/no-object-assign -- safe
  var $assign = Object.assign;
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  var defineProperty$7 = Object.defineProperty;
  var concat$1 = uncurryThis$z([].concat);

  // `Object.assign` method
  // https://tc39.es/ecma262/#sec-object.assign
  var objectAssign = !$assign || fails$z(function () {
    // should have correct order of operations (Edge bug)
    if (DESCRIPTORS$e && $assign({ b: 1 }, $assign(defineProperty$7({}, 'a', {
      enumerable: true,
      get: function () {
        defineProperty$7(this, 'b', {
          value: 3,
          enumerable: false
        });
      }
    }), { b: 2 })).b !== 1) return true;
    // should work with symbols and should have deterministic property order (V8 bug)
    var A = {};
    var B = {};
    // eslint-disable-next-line es/no-symbol -- safe
    var symbol = Symbol();
    var alphabet = 'abcdefghijklmnopqrst';
    A[symbol] = 7;
    alphabet.split('').forEach(function (chr) { B[chr] = chr; });
    return $assign({}, A)[symbol] != 7 || objectKeys$3($assign({}, B)).join('') != alphabet;
  }) ? function assign(target, source) { // eslint-disable-line no-unused-vars -- required for `.length`
    var T = toObject$c(target);
    var argumentsLength = arguments.length;
    var index = 1;
    var getOwnPropertySymbols = getOwnPropertySymbolsModule$2.f;
    var propertyIsEnumerable = propertyIsEnumerableModule$1.f;
    while (argumentsLength > index) {
      var S = IndexedObject$2(arguments[index++]);
      var keys = getOwnPropertySymbols ? concat$1(objectKeys$3(S), getOwnPropertySymbols(S)) : objectKeys$3(S);
      var length = keys.length;
      var j = 0;
      var key;
      while (length > j) {
        key = keys[j++];
        if (!DESCRIPTORS$e || call$p(propertyIsEnumerable, S, key)) T[key] = S[key];
      }
    } return T;
  } : $assign;

  var $$V = _export;
  var assign$1 = objectAssign;

  // `Object.assign` method
  // https://tc39.es/ecma262/#sec-object.assign
  // eslint-disable-next-line es/no-object-assign -- required for testing
  $$V({ target: 'Object', stat: true, arity: 2, forced: Object.assign !== assign$1 }, {
    assign: assign$1
  });

  var wellKnownSymbol$p = wellKnownSymbol$r;

  var TO_STRING_TAG$3 = wellKnownSymbol$p('toStringTag');
  var test$1 = {};

  test$1[TO_STRING_TAG$3] = 'z';

  var toStringTagSupport = String(test$1) === '[object z]';

  var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
  var isCallable$i = isCallable$t;
  var classofRaw$1 = classofRaw$2;
  var wellKnownSymbol$o = wellKnownSymbol$r;

  var TO_STRING_TAG$2 = wellKnownSymbol$o('toStringTag');
  var $Object$2 = Object;

  // ES3 wrong here
  var CORRECT_ARGUMENTS = classofRaw$1(function () { return arguments; }()) == 'Arguments';

  // fallback for IE11 Script Access Denied error
  var tryGet = function (it, key) {
    try {
      return it[key];
    } catch (error) { /* empty */ }
  };

  // getting tag from ES6+ `Object.prototype.toString`
  var classof$c = TO_STRING_TAG_SUPPORT$2 ? classofRaw$1 : function (it) {
    var O, tag, result;
    return it === undefined ? 'Undefined' : it === null ? 'Null'
      // @@toStringTag case
      : typeof (tag = tryGet(O = $Object$2(it), TO_STRING_TAG$2)) == 'string' ? tag
      // builtinTag case
      : CORRECT_ARGUMENTS ? classofRaw$1(O)
      // ES3 arguments fallback
      : (result = classofRaw$1(O)) == 'Object' && isCallable$i(O.callee) ? 'Arguments' : result;
  };

  var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
  var classof$b = classof$c;

  // `Object.prototype.toString` method implementation
  // https://tc39.es/ecma262/#sec-object.prototype.tostring
  var objectToString$2 = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
    return '[object ' + classof$b(this) + ']';
  };

  var TO_STRING_TAG_SUPPORT = toStringTagSupport;
  var defineBuiltIn$c = defineBuiltIn$e;
  var toString$j = objectToString$2;

  // `Object.prototype.toString` method
  // https://tc39.es/ecma262/#sec-object.prototype.tostring
  if (!TO_STRING_TAG_SUPPORT) {
    defineBuiltIn$c(Object.prototype, 'toString', toString$j, { unsafe: true });
  }

  var classof$a = classofRaw$2;

  // `IsArray` abstract operation
  // https://tc39.es/ecma262/#sec-isarray
  // eslint-disable-next-line es/no-array-isarray -- safe
  var isArray$a = Array.isArray || function isArray(argument) {
    return classof$a(argument) == 'Array';
  };

  var uncurryThis$y = functionUncurryThis;
  var fails$y = fails$I;
  var isCallable$h = isCallable$t;
  var classof$9 = classof$c;
  var getBuiltIn$7 = getBuiltIn$a;
  var inspectSource$1 = inspectSource$3;

  var noop$1 = function () { /* empty */ };
  var empty = [];
  var construct = getBuiltIn$7('Reflect', 'construct');
  var constructorRegExp = /^\s*(?:class|function)\b/;
  var exec$5 = uncurryThis$y(constructorRegExp.exec);
  var INCORRECT_TO_STRING = !constructorRegExp.exec(noop$1);

  var isConstructorModern = function isConstructor(argument) {
    if (!isCallable$h(argument)) return false;
    try {
      construct(noop$1, empty, argument);
      return true;
    } catch (error) {
      return false;
    }
  };

  var isConstructorLegacy = function isConstructor(argument) {
    if (!isCallable$h(argument)) return false;
    switch (classof$9(argument)) {
      case 'AsyncFunction':
      case 'GeneratorFunction':
      case 'AsyncGeneratorFunction': return false;
    }
    try {
      // we can't check .prototype since constructors produced by .bind haven't it
      // `Function#toString` throws on some built-it function in some legacy engines
      // (for example, `DOMQuad` and similar in FF41-)
      return INCORRECT_TO_STRING || !!exec$5(constructorRegExp, inspectSource$1(argument));
    } catch (error) {
      return true;
    }
  };

  isConstructorLegacy.sham = true;

  // `IsConstructor` abstract operation
  // https://tc39.es/ecma262/#sec-isconstructor
  var isConstructor$4 = !construct || fails$y(function () {
    var called;
    return isConstructorModern(isConstructorModern.call)
      || !isConstructorModern(Object)
      || !isConstructorModern(function () { called = true; })
      || called;
  }) ? isConstructorLegacy : isConstructorModern;

  var toPropertyKey$2 = toPropertyKey$5;
  var definePropertyModule$4 = objectDefineProperty;
  var createPropertyDescriptor$4 = createPropertyDescriptor$7;

  var createProperty$5 = function (object, key, value) {
    var propertyKey = toPropertyKey$2(key);
    if (propertyKey in object) definePropertyModule$4.f(object, propertyKey, createPropertyDescriptor$4(0, value));
    else object[propertyKey] = value;
  };

  var fails$x = fails$I;
  var wellKnownSymbol$n = wellKnownSymbol$r;
  var V8_VERSION$2 = engineV8Version;

  var SPECIES$6 = wellKnownSymbol$n('species');

  var arrayMethodHasSpeciesSupport$5 = function (METHOD_NAME) {
    // We can't use this feature detection in V8 since it causes
    // deoptimization and serious performance degradation
    // https://github.com/zloirock/core-js/issues/677
    return V8_VERSION$2 >= 51 || !fails$x(function () {
      var array = [];
      var constructor = array.constructor = {};
      constructor[SPECIES$6] = function () {
        return { foo: 1 };
      };
      return array[METHOD_NAME](Boolean).foo !== 1;
    });
  };

  var uncurryThis$x = functionUncurryThis;

  var arraySlice$6 = uncurryThis$x([].slice);

  var $$U = _export;
  var isArray$9 = isArray$a;
  var isConstructor$3 = isConstructor$4;
  var isObject$k = isObject$q;
  var toAbsoluteIndex$3 = toAbsoluteIndex$5;
  var lengthOfArrayLike$a = lengthOfArrayLike$c;
  var toIndexedObject$6 = toIndexedObject$a;
  var createProperty$4 = createProperty$5;
  var wellKnownSymbol$m = wellKnownSymbol$r;
  var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$5;
  var nativeSlice = arraySlice$6;

  var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$4('slice');

  var SPECIES$5 = wellKnownSymbol$m('species');
  var $Array$3 = Array;
  var max$3 = Math.max;

  // `Array.prototype.slice` method
  // https://tc39.es/ecma262/#sec-array.prototype.slice
  // fallback for not array-like ES3 strings and DOM objects
  $$U({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
    slice: function slice(start, end) {
      var O = toIndexedObject$6(this);
      var length = lengthOfArrayLike$a(O);
      var k = toAbsoluteIndex$3(start, length);
      var fin = toAbsoluteIndex$3(end === undefined ? length : end, length);
      // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
      var Constructor, result, n;
      if (isArray$9(O)) {
        Constructor = O.constructor;
        // cross-realm fallback
        if (isConstructor$3(Constructor) && (Constructor === $Array$3 || isArray$9(Constructor.prototype))) {
          Constructor = undefined;
        } else if (isObject$k(Constructor)) {
          Constructor = Constructor[SPECIES$5];
          if (Constructor === null) Constructor = undefined;
        }
        if (Constructor === $Array$3 || Constructor === undefined) {
          return nativeSlice(O, k, fin);
        }
      }
      result = new (Constructor === undefined ? $Array$3 : Constructor)(max$3(fin - k, 0));
      for (n = 0; k < fin; k++, n++) if (k in O) createProperty$4(result, n, O[k]);
      result.length = n;
      return result;
    }
  });

  // `SameValue` abstract operation
  // https://tc39.es/ecma262/#sec-samevalue
  // eslint-disable-next-line es/no-object-is -- safe
  var sameValue$1 = Object.is || function is(x, y) {
    // eslint-disable-next-line no-self-compare -- NaN check
    return x === y ? x !== 0 || 1 / x === 1 / y : x != x && y != y;
  };

  var $$T = _export;
  var is = sameValue$1;

  // `Object.is` method
  // https://tc39.es/ecma262/#sec-object.is
  $$T({ target: 'Object', stat: true }, {
    is: is
  });

  var objectDefineProperties = {};

  var DESCRIPTORS$d = descriptors;
  var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
  var definePropertyModule$3 = objectDefineProperty;
  var anObject$o = anObject$r;
  var toIndexedObject$5 = toIndexedObject$a;
  var objectKeys$2 = objectKeys$4;

  // `Object.defineProperties` method
  // https://tc39.es/ecma262/#sec-object.defineproperties
  // eslint-disable-next-line es/no-object-defineproperties -- safe
  objectDefineProperties.f = DESCRIPTORS$d && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
    anObject$o(O);
    var props = toIndexedObject$5(Properties);
    var keys = objectKeys$2(Properties);
    var length = keys.length;
    var index = 0;
    var key;
    while (length > index) definePropertyModule$3.f(O, key = keys[index++], props[key]);
    return O;
  };

  var getBuiltIn$6 = getBuiltIn$a;

  var html$2 = getBuiltIn$6('document', 'documentElement');

  /* global ActiveXObject -- old IE, WSH */

  var anObject$n = anObject$r;
  var definePropertiesModule$1 = objectDefineProperties;
  var enumBugKeys = enumBugKeys$3;
  var hiddenKeys$2 = hiddenKeys$6;
  var html$1 = html$2;
  var documentCreateElement$1 = documentCreateElement$2;
  var sharedKey$2 = sharedKey$4;

  var GT = '>';
  var LT = '<';
  var PROTOTYPE$1 = 'prototype';
  var SCRIPT = 'script';
  var IE_PROTO$1 = sharedKey$2('IE_PROTO');

  var EmptyConstructor = function () { /* empty */ };

  var scriptTag = function (content) {
    return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
  };

  // Create object with fake `null` prototype: use ActiveX Object with cleared prototype
  var NullProtoObjectViaActiveX = function (activeXDocument) {
    activeXDocument.write(scriptTag(''));
    activeXDocument.close();
    var temp = activeXDocument.parentWindow.Object;
    activeXDocument = null; // avoid memory leak
    return temp;
  };

  // Create object with fake `null` prototype: use iframe Object with cleared prototype
  var NullProtoObjectViaIFrame = function () {
    // Thrash, waste and sodomy: IE GC bug
    var iframe = documentCreateElement$1('iframe');
    var JS = 'java' + SCRIPT + ':';
    var iframeDocument;
    iframe.style.display = 'none';
    html$1.appendChild(iframe);
    // https://github.com/zloirock/core-js/issues/475
    iframe.src = String(JS);
    iframeDocument = iframe.contentWindow.document;
    iframeDocument.open();
    iframeDocument.write(scriptTag('document.F=Object'));
    iframeDocument.close();
    return iframeDocument.F;
  };

  // Check for document.domain and active x support
  // No need to use active x approach when document.domain is not set
  // see https://github.com/es-shims/es5-shim/issues/150
  // variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
  // avoid IE GC bug
  var activeXDocument;
  var NullProtoObject = function () {
    try {
      activeXDocument = new ActiveXObject('htmlfile');
    } catch (error) { /* ignore */ }
    NullProtoObject = typeof document != 'undefined'
      ? document.domain && activeXDocument
        ? NullProtoObjectViaActiveX(activeXDocument) // old IE
        : NullProtoObjectViaIFrame()
      : NullProtoObjectViaActiveX(activeXDocument); // WSH
    var length = enumBugKeys.length;
    while (length--) delete NullProtoObject[PROTOTYPE$1][enumBugKeys[length]];
    return NullProtoObject();
  };

  hiddenKeys$2[IE_PROTO$1] = true;

  // `Object.create` method
  // https://tc39.es/ecma262/#sec-object.create
  // eslint-disable-next-line es/no-object-create -- safe
  var objectCreate = Object.create || function create(O, Properties) {
    var result;
    if (O !== null) {
      EmptyConstructor[PROTOTYPE$1] = anObject$n(O);
      result = new EmptyConstructor();
      EmptyConstructor[PROTOTYPE$1] = null;
      // add "__proto__" for Object.getPrototypeOf polyfill
      result[IE_PROTO$1] = O;
    } else result = NullProtoObject();
    return Properties === undefined ? result : definePropertiesModule$1.f(result, Properties);
  };

  var wellKnownSymbol$l = wellKnownSymbol$r;
  var create$4 = objectCreate;
  var defineProperty$6 = objectDefineProperty.f;

  var UNSCOPABLES = wellKnownSymbol$l('unscopables');
  var ArrayPrototype$1 = Array.prototype;

  // Array.prototype[@@unscopables]
  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  if (ArrayPrototype$1[UNSCOPABLES] == undefined) {
    defineProperty$6(ArrayPrototype$1, UNSCOPABLES, {
      configurable: true,
      value: create$4(null)
    });
  }

  // add a key to Array.prototype[@@unscopables]
  var addToUnscopables$6 = function (key) {
    ArrayPrototype$1[UNSCOPABLES][key] = true;
  };

  var iterators = {};

  var fails$w = fails$I;

  var correctPrototypeGetter = !fails$w(function () {
    function F() { /* empty */ }
    F.prototype.constructor = null;
    // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
    return Object.getPrototypeOf(new F()) !== F.prototype;
  });

  var hasOwn$g = hasOwnProperty_1;
  var isCallable$g = isCallable$t;
  var toObject$b = toObject$e;
  var sharedKey$1 = sharedKey$4;
  var CORRECT_PROTOTYPE_GETTER$2 = correctPrototypeGetter;

  var IE_PROTO = sharedKey$1('IE_PROTO');
  var $Object$1 = Object;
  var ObjectPrototype$1 = $Object$1.prototype;

  // `Object.getPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.getprototypeof
  // eslint-disable-next-line es/no-object-getprototypeof -- safe
  var objectGetPrototypeOf$1 = CORRECT_PROTOTYPE_GETTER$2 ? $Object$1.getPrototypeOf : function (O) {
    var object = toObject$b(O);
    if (hasOwn$g(object, IE_PROTO)) return object[IE_PROTO];
    var constructor = object.constructor;
    if (isCallable$g(constructor) && object instanceof constructor) {
      return constructor.prototype;
    } return object instanceof $Object$1 ? ObjectPrototype$1 : null;
  };

  var fails$v = fails$I;
  var isCallable$f = isCallable$t;
  var isObject$j = isObject$q;
  var getPrototypeOf$3 = objectGetPrototypeOf$1;
  var defineBuiltIn$b = defineBuiltIn$e;
  var wellKnownSymbol$k = wellKnownSymbol$r;

  var ITERATOR$7 = wellKnownSymbol$k('iterator');
  var BUGGY_SAFARI_ITERATORS$1 = false;

  // `%IteratorPrototype%` object
  // https://tc39.es/ecma262/#sec-%iteratorprototype%-object
  var IteratorPrototype$2, PrototypeOfArrayIteratorPrototype, arrayIterator;

  /* eslint-disable es/no-array-prototype-keys -- safe */
  if ([].keys) {
    arrayIterator = [].keys();
    // Safari 8 has buggy iterators w/o `next`
    if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
    else {
      PrototypeOfArrayIteratorPrototype = getPrototypeOf$3(getPrototypeOf$3(arrayIterator));
      if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$2 = PrototypeOfArrayIteratorPrototype;
    }
  }

  var NEW_ITERATOR_PROTOTYPE = !isObject$j(IteratorPrototype$2) || fails$v(function () {
    var test = {};
    // FF44- legacy iterators case
    return IteratorPrototype$2[ITERATOR$7].call(test) !== test;
  });

  if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$2 = {};

  // `%IteratorPrototype%[@@iterator]()` method
  // https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
  if (!isCallable$f(IteratorPrototype$2[ITERATOR$7])) {
    defineBuiltIn$b(IteratorPrototype$2, ITERATOR$7, function () {
      return this;
    });
  }

  var iteratorsCore = {
    IteratorPrototype: IteratorPrototype$2,
    BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
  };

  var defineProperty$5 = objectDefineProperty.f;
  var hasOwn$f = hasOwnProperty_1;
  var wellKnownSymbol$j = wellKnownSymbol$r;

  var TO_STRING_TAG$1 = wellKnownSymbol$j('toStringTag');

  var setToStringTag$8 = function (target, TAG, STATIC) {
    if (target && !STATIC) target = target.prototype;
    if (target && !hasOwn$f(target, TO_STRING_TAG$1)) {
      defineProperty$5(target, TO_STRING_TAG$1, { configurable: true, value: TAG });
    }
  };

  var IteratorPrototype$1 = iteratorsCore.IteratorPrototype;
  var create$3 = objectCreate;
  var createPropertyDescriptor$3 = createPropertyDescriptor$7;
  var setToStringTag$7 = setToStringTag$8;
  var Iterators$4 = iterators;

  var returnThis$1 = function () { return this; };

  var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
    var TO_STRING_TAG = NAME + ' Iterator';
    IteratorConstructor.prototype = create$3(IteratorPrototype$1, { next: createPropertyDescriptor$3(+!ENUMERABLE_NEXT, next) });
    setToStringTag$7(IteratorConstructor, TO_STRING_TAG, false);
    Iterators$4[TO_STRING_TAG] = returnThis$1;
    return IteratorConstructor;
  };

  var uncurryThis$w = functionUncurryThis;
  var aCallable$7 = aCallable$9;

  var functionUncurryThisAccessor = function (object, key, method) {
    try {
      // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
      return uncurryThis$w(aCallable$7(Object.getOwnPropertyDescriptor(object, key)[method]));
    } catch (error) { /* empty */ }
  };

  var isCallable$e = isCallable$t;

  var $String$3 = String;
  var $TypeError$b = TypeError;

  var aPossiblePrototype$1 = function (argument) {
    if (typeof argument == 'object' || isCallable$e(argument)) return argument;
    throw $TypeError$b("Can't set " + $String$3(argument) + ' as a prototype');
  };

  /* eslint-disable no-proto -- safe */

  var uncurryThisAccessor = functionUncurryThisAccessor;
  var anObject$m = anObject$r;
  var aPossiblePrototype = aPossiblePrototype$1;

  // `Object.setPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.setprototypeof
  // Works with __proto__ only. Old v8 can't work with null proto objects.
  // eslint-disable-next-line es/no-object-setprototypeof -- safe
  var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
    var CORRECT_SETTER = false;
    var test = {};
    var setter;
    try {
      setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
      setter(test, []);
      CORRECT_SETTER = test instanceof Array;
    } catch (error) { /* empty */ }
    return function setPrototypeOf(O, proto) {
      anObject$m(O);
      aPossiblePrototype(proto);
      if (CORRECT_SETTER) setter(O, proto);
      else O.__proto__ = proto;
      return O;
    };
  }() : undefined);

  var $$S = _export;
  var call$o = functionCall;
  var FunctionName = functionName;
  var isCallable$d = isCallable$t;
  var createIteratorConstructor$1 = iteratorCreateConstructor;
  var getPrototypeOf$2 = objectGetPrototypeOf$1;
  var setPrototypeOf$2 = objectSetPrototypeOf;
  var setToStringTag$6 = setToStringTag$8;
  var createNonEnumerableProperty$3 = createNonEnumerableProperty$6;
  var defineBuiltIn$a = defineBuiltIn$e;
  var wellKnownSymbol$i = wellKnownSymbol$r;
  var Iterators$3 = iterators;
  var IteratorsCore = iteratorsCore;

  var PROPER_FUNCTION_NAME$2 = FunctionName.PROPER;
  var CONFIGURABLE_FUNCTION_NAME = FunctionName.CONFIGURABLE;
  var IteratorPrototype = IteratorsCore.IteratorPrototype;
  var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
  var ITERATOR$6 = wellKnownSymbol$i('iterator');
  var KEYS = 'keys';
  var VALUES = 'values';
  var ENTRIES = 'entries';

  var returnThis = function () { return this; };

  var iteratorDefine = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
    createIteratorConstructor$1(IteratorConstructor, NAME, next);

    var getIterationMethod = function (KIND) {
      if (KIND === DEFAULT && defaultIterator) return defaultIterator;
      if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];
      switch (KIND) {
        case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
        case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
        case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
      } return function () { return new IteratorConstructor(this); };
    };

    var TO_STRING_TAG = NAME + ' Iterator';
    var INCORRECT_VALUES_NAME = false;
    var IterablePrototype = Iterable.prototype;
    var nativeIterator = IterablePrototype[ITERATOR$6]
      || IterablePrototype['@@iterator']
      || DEFAULT && IterablePrototype[DEFAULT];
    var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
    var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
    var CurrentIteratorPrototype, methods, KEY;

    // fix native
    if (anyNativeIterator) {
      CurrentIteratorPrototype = getPrototypeOf$2(anyNativeIterator.call(new Iterable()));
      if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
        if (getPrototypeOf$2(CurrentIteratorPrototype) !== IteratorPrototype) {
          if (setPrototypeOf$2) {
            setPrototypeOf$2(CurrentIteratorPrototype, IteratorPrototype);
          } else if (!isCallable$d(CurrentIteratorPrototype[ITERATOR$6])) {
            defineBuiltIn$a(CurrentIteratorPrototype, ITERATOR$6, returnThis);
          }
        }
        // Set @@toStringTag to native iterators
        setToStringTag$6(CurrentIteratorPrototype, TO_STRING_TAG, true);
      }
    }

    // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
    if (PROPER_FUNCTION_NAME$2 && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
      if (CONFIGURABLE_FUNCTION_NAME) {
        createNonEnumerableProperty$3(IterablePrototype, 'name', VALUES);
      } else {
        INCORRECT_VALUES_NAME = true;
        defaultIterator = function values() { return call$o(nativeIterator, this); };
      }
    }

    // export additional methods
    if (DEFAULT) {
      methods = {
        values: getIterationMethod(VALUES),
        keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
        entries: getIterationMethod(ENTRIES)
      };
      if (FORCED) for (KEY in methods) {
        if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
          defineBuiltIn$a(IterablePrototype, KEY, methods[KEY]);
        }
      } else $$S({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
    }

    // define iterator
    if (IterablePrototype[ITERATOR$6] !== defaultIterator) {
      defineBuiltIn$a(IterablePrototype, ITERATOR$6, defaultIterator, { name: DEFAULT });
    }
    Iterators$3[NAME] = defaultIterator;

    return methods;
  };

  // `CreateIterResultObject` abstract operation
  // https://tc39.es/ecma262/#sec-createiterresultobject
  var createIterResultObject$3 = function (value, done) {
    return { value: value, done: done };
  };

  var toIndexedObject$4 = toIndexedObject$a;
  var addToUnscopables$5 = addToUnscopables$6;
  var Iterators$2 = iterators;
  var InternalStateModule$7 = internalState;
  var defineProperty$4 = objectDefineProperty.f;
  var defineIterator$2 = iteratorDefine;
  var createIterResultObject$2 = createIterResultObject$3;
  var DESCRIPTORS$c = descriptors;

  var ARRAY_ITERATOR = 'Array Iterator';
  var setInternalState$7 = InternalStateModule$7.set;
  var getInternalState$3 = InternalStateModule$7.getterFor(ARRAY_ITERATOR);

  // `Array.prototype.entries` method
  // https://tc39.es/ecma262/#sec-array.prototype.entries
  // `Array.prototype.keys` method
  // https://tc39.es/ecma262/#sec-array.prototype.keys
  // `Array.prototype.values` method
  // https://tc39.es/ecma262/#sec-array.prototype.values
  // `Array.prototype[@@iterator]` method
  // https://tc39.es/ecma262/#sec-array.prototype-@@iterator
  // `CreateArrayIterator` internal method
  // https://tc39.es/ecma262/#sec-createarrayiterator
  var es_array_iterator = defineIterator$2(Array, 'Array', function (iterated, kind) {
    setInternalState$7(this, {
      type: ARRAY_ITERATOR,
      target: toIndexedObject$4(iterated), // target
      index: 0,                          // next index
      kind: kind                         // kind
    });
  // `%ArrayIteratorPrototype%.next` method
  // https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
  }, function () {
    var state = getInternalState$3(this);
    var target = state.target;
    var kind = state.kind;
    var index = state.index++;
    if (!target || index >= target.length) {
      state.target = undefined;
      return createIterResultObject$2(undefined, true);
    }
    if (kind == 'keys') return createIterResultObject$2(index, false);
    if (kind == 'values') return createIterResultObject$2(target[index], false);
    return createIterResultObject$2([index, target[index]], false);
  }, 'values');

  // argumentsList[@@iterator] is %ArrayProto_values%
  // https://tc39.es/ecma262/#sec-createunmappedargumentsobject
  // https://tc39.es/ecma262/#sec-createmappedargumentsobject
  var values = Iterators$2.Arguments = Iterators$2.Array;

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$5('keys');
  addToUnscopables$5('values');
  addToUnscopables$5('entries');

  // V8 ~ Chrome 45- bug
  if (DESCRIPTORS$c && values.name !== 'values') try {
    defineProperty$4(values, 'name', { value: 'values' });
  } catch (error) { /* empty */ }

  var internalMetadata = {exports: {}};

  var objectGetOwnPropertyNamesExternal = {};

  var toAbsoluteIndex$2 = toAbsoluteIndex$5;
  var lengthOfArrayLike$9 = lengthOfArrayLike$c;
  var createProperty$3 = createProperty$5;

  var $Array$2 = Array;
  var max$2 = Math.max;

  var arraySliceSimple = function (O, start, end) {
    var length = lengthOfArrayLike$9(O);
    var k = toAbsoluteIndex$2(start, length);
    var fin = toAbsoluteIndex$2(end === undefined ? length : end, length);
    var result = $Array$2(max$2(fin - k, 0));
    for (var n = 0; k < fin; k++, n++) createProperty$3(result, n, O[k]);
    result.length = n;
    return result;
  };

  /* eslint-disable es/no-object-getownpropertynames -- safe */

  var classof$8 = classofRaw$2;
  var toIndexedObject$3 = toIndexedObject$a;
  var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
  var arraySlice$5 = arraySliceSimple;

  var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
    ? Object.getOwnPropertyNames(window) : [];

  var getWindowNames = function (it) {
    try {
      return $getOwnPropertyNames$1(it);
    } catch (error) {
      return arraySlice$5(windowNames);
    }
  };

  // fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
  objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
    return windowNames && classof$8(it) == 'Window'
      ? getWindowNames(it)
      : $getOwnPropertyNames$1(toIndexedObject$3(it));
  };

  // FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
  var fails$u = fails$I;

  var arrayBufferNonExtensible = fails$u(function () {
    if (typeof ArrayBuffer == 'function') {
      var buffer = new ArrayBuffer(8);
      // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
      if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
    }
  });

  var fails$t = fails$I;
  var isObject$i = isObject$q;
  var classof$7 = classofRaw$2;
  var ARRAY_BUFFER_NON_EXTENSIBLE = arrayBufferNonExtensible;

  // eslint-disable-next-line es/no-object-isextensible -- safe
  var $isExtensible$1 = Object.isExtensible;
  var FAILS_ON_PRIMITIVES$4 = fails$t(function () { $isExtensible$1(1); });

  // `Object.isExtensible` method
  // https://tc39.es/ecma262/#sec-object.isextensible
  var objectIsExtensible = (FAILS_ON_PRIMITIVES$4 || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
    if (!isObject$i(it)) return false;
    if (ARRAY_BUFFER_NON_EXTENSIBLE && classof$7(it) == 'ArrayBuffer') return false;
    return $isExtensible$1 ? $isExtensible$1(it) : true;
  } : $isExtensible$1;

  var fails$s = fails$I;

  var freezing = !fails$s(function () {
    // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
    return Object.isExtensible(Object.preventExtensions({}));
  });

  var $$R = _export;
  var uncurryThis$v = functionUncurryThis;
  var hiddenKeys$1 = hiddenKeys$6;
  var isObject$h = isObject$q;
  var hasOwn$e = hasOwnProperty_1;
  var defineProperty$3 = objectDefineProperty.f;
  var getOwnPropertyNamesModule$1 = objectGetOwnPropertyNames;
  var getOwnPropertyNamesExternalModule = objectGetOwnPropertyNamesExternal;
  var isExtensible$1 = objectIsExtensible;
  var uid$3 = uid$6;
  var FREEZING$2 = freezing;

  var REQUIRED = false;
  var METADATA = uid$3('meta');
  var id$1 = 0;

  var setMetadata = function (it) {
    defineProperty$3(it, METADATA, { value: {
      objectID: 'O' + id$1++, // object ID
      weakData: {}          // weak collections IDs
    } });
  };

  var fastKey$1 = function (it, create) {
    // return a primitive with prefix
    if (!isObject$h(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
    if (!hasOwn$e(it, METADATA)) {
      // can't set metadata to uncaught frozen object
      if (!isExtensible$1(it)) return 'F';
      // not necessary to add metadata
      if (!create) return 'E';
      // add missing metadata
      setMetadata(it);
    // return object ID
    } return it[METADATA].objectID;
  };

  var getWeakData$1 = function (it, create) {
    if (!hasOwn$e(it, METADATA)) {
      // can't set metadata to uncaught frozen object
      if (!isExtensible$1(it)) return true;
      // not necessary to add metadata
      if (!create) return false;
      // add missing metadata
      setMetadata(it);
    // return the store of weak collections IDs
    } return it[METADATA].weakData;
  };

  // add metadata on freeze-family methods calling
  var onFreeze$1 = function (it) {
    if (FREEZING$2 && REQUIRED && isExtensible$1(it) && !hasOwn$e(it, METADATA)) setMetadata(it);
    return it;
  };

  var enable = function () {
    meta.enable = function () { /* empty */ };
    REQUIRED = true;
    var getOwnPropertyNames = getOwnPropertyNamesModule$1.f;
    var splice = uncurryThis$v([].splice);
    var test = {};
    test[METADATA] = 1;

    // prevent exposing of metadata key
    if (getOwnPropertyNames(test).length) {
      getOwnPropertyNamesModule$1.f = function (it) {
        var result = getOwnPropertyNames(it);
        for (var i = 0, length = result.length; i < length; i++) {
          if (result[i] === METADATA) {
            splice(result, i, 1);
            break;
          }
        } return result;
      };

      $$R({ target: 'Object', stat: true, forced: true }, {
        getOwnPropertyNames: getOwnPropertyNamesExternalModule.f
      });
    }
  };

  var meta = internalMetadata.exports = {
    enable: enable,
    fastKey: fastKey$1,
    getWeakData: getWeakData$1,
    onFreeze: onFreeze$1
  };

  hiddenKeys$1[METADATA] = true;

  var classofRaw = classofRaw$2;
  var uncurryThis$u = functionUncurryThis;

  var functionUncurryThisClause = function (fn) {
    // Nashorn bug:
    //   https://github.com/zloirock/core-js/issues/1128
    //   https://github.com/zloirock/core-js/issues/1130
    if (classofRaw(fn) === 'Function') return uncurryThis$u(fn);
  };

  var uncurryThis$t = functionUncurryThisClause;
  var aCallable$6 = aCallable$9;
  var NATIVE_BIND$1 = functionBindNative;

  var bind$a = uncurryThis$t(uncurryThis$t.bind);

  // optional / simple context binding
  var functionBindContext = function (fn, that) {
    aCallable$6(fn);
    return that === undefined ? fn : NATIVE_BIND$1 ? bind$a(fn, that) : function (/* ...args */) {
      return fn.apply(that, arguments);
    };
  };

  var wellKnownSymbol$h = wellKnownSymbol$r;
  var Iterators$1 = iterators;

  var ITERATOR$5 = wellKnownSymbol$h('iterator');
  var ArrayPrototype = Array.prototype;

  // check on default Array iterator
  var isArrayIteratorMethod$2 = function (it) {
    return it !== undefined && (Iterators$1.Array === it || ArrayPrototype[ITERATOR$5] === it);
  };

  var classof$6 = classof$c;
  var getMethod$5 = getMethod$7;
  var isNullOrUndefined$8 = isNullOrUndefined$b;
  var Iterators = iterators;
  var wellKnownSymbol$g = wellKnownSymbol$r;

  var ITERATOR$4 = wellKnownSymbol$g('iterator');

  var getIteratorMethod$4 = function (it) {
    if (!isNullOrUndefined$8(it)) return getMethod$5(it, ITERATOR$4)
      || getMethod$5(it, '@@iterator')
      || Iterators[classof$6(it)];
  };

  var call$n = functionCall;
  var aCallable$5 = aCallable$9;
  var anObject$l = anObject$r;
  var tryToString$4 = tryToString$6;
  var getIteratorMethod$3 = getIteratorMethod$4;

  var $TypeError$a = TypeError;

  var getIterator$3 = function (argument, usingIterator) {
    var iteratorMethod = arguments.length < 2 ? getIteratorMethod$3(argument) : usingIterator;
    if (aCallable$5(iteratorMethod)) return anObject$l(call$n(iteratorMethod, argument));
    throw $TypeError$a(tryToString$4(argument) + ' is not iterable');
  };

  var call$m = functionCall;
  var anObject$k = anObject$r;
  var getMethod$4 = getMethod$7;

  var iteratorClose$2 = function (iterator, kind, value) {
    var innerResult, innerError;
    anObject$k(iterator);
    try {
      innerResult = getMethod$4(iterator, 'return');
      if (!innerResult) {
        if (kind === 'throw') throw value;
        return value;
      }
      innerResult = call$m(innerResult, iterator);
    } catch (error) {
      innerError = true;
      innerResult = error;
    }
    if (kind === 'throw') throw value;
    if (innerError) throw innerResult;
    anObject$k(innerResult);
    return value;
  };

  var bind$9 = functionBindContext;
  var call$l = functionCall;
  var anObject$j = anObject$r;
  var tryToString$3 = tryToString$6;
  var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
  var lengthOfArrayLike$8 = lengthOfArrayLike$c;
  var isPrototypeOf$5 = objectIsPrototypeOf;
  var getIterator$2 = getIterator$3;
  var getIteratorMethod$2 = getIteratorMethod$4;
  var iteratorClose$1 = iteratorClose$2;

  var $TypeError$9 = TypeError;

  var Result = function (stopped, result) {
    this.stopped = stopped;
    this.result = result;
  };

  var ResultPrototype = Result.prototype;

  var iterate$5 = function (iterable, unboundFunction, options) {
    var that = options && options.that;
    var AS_ENTRIES = !!(options && options.AS_ENTRIES);
    var IS_RECORD = !!(options && options.IS_RECORD);
    var IS_ITERATOR = !!(options && options.IS_ITERATOR);
    var INTERRUPTED = !!(options && options.INTERRUPTED);
    var fn = bind$9(unboundFunction, that);
    var iterator, iterFn, index, length, result, next, step;

    var stop = function (condition) {
      if (iterator) iteratorClose$1(iterator, 'normal', condition);
      return new Result(true, condition);
    };

    var callFn = function (value) {
      if (AS_ENTRIES) {
        anObject$j(value);
        return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
      } return INTERRUPTED ? fn(value, stop) : fn(value);
    };

    if (IS_RECORD) {
      iterator = iterable.iterator;
    } else if (IS_ITERATOR) {
      iterator = iterable;
    } else {
      iterFn = getIteratorMethod$2(iterable);
      if (!iterFn) throw $TypeError$9(tryToString$3(iterable) + ' is not iterable');
      // optimisation for array iterators
      if (isArrayIteratorMethod$1(iterFn)) {
        for (index = 0, length = lengthOfArrayLike$8(iterable); length > index; index++) {
          result = callFn(iterable[index]);
          if (result && isPrototypeOf$5(ResultPrototype, result)) return result;
        } return new Result(false);
      }
      iterator = getIterator$2(iterable, iterFn);
    }

    next = IS_RECORD ? iterable.next : iterator.next;
    while (!(step = call$l(next, iterator)).done) {
      try {
        result = callFn(step.value);
      } catch (error) {
        iteratorClose$1(iterator, 'throw', error);
      }
      if (typeof result == 'object' && result && isPrototypeOf$5(ResultPrototype, result)) return result;
    } return new Result(false);
  };

  var isPrototypeOf$4 = objectIsPrototypeOf;

  var $TypeError$8 = TypeError;

  var anInstance$6 = function (it, Prototype) {
    if (isPrototypeOf$4(Prototype, it)) return it;
    throw $TypeError$8('Incorrect invocation');
  };

  var wellKnownSymbol$f = wellKnownSymbol$r;

  var ITERATOR$3 = wellKnownSymbol$f('iterator');
  var SAFE_CLOSING = false;

  try {
    var called = 0;
    var iteratorWithReturn = {
      next: function () {
        return { done: !!called++ };
      },
      'return': function () {
        SAFE_CLOSING = true;
      }
    };
    iteratorWithReturn[ITERATOR$3] = function () {
      return this;
    };
    // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
    Array.from(iteratorWithReturn, function () { throw 2; });
  } catch (error) { /* empty */ }

  var checkCorrectnessOfIteration$3 = function (exec, SKIP_CLOSING) {
    if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
    var ITERATION_SUPPORT = false;
    try {
      var object = {};
      object[ITERATOR$3] = function () {
        return {
          next: function () {
            return { done: ITERATION_SUPPORT = true };
          }
        };
      };
      exec(object);
    } catch (error) { /* empty */ }
    return ITERATION_SUPPORT;
  };

  var isCallable$c = isCallable$t;
  var isObject$g = isObject$q;
  var setPrototypeOf$1 = objectSetPrototypeOf;

  // makes subclassing work correct for wrapped built-ins
  var inheritIfRequired$2 = function ($this, dummy, Wrapper) {
    var NewTarget, NewTargetPrototype;
    if (
      // it can work only with native `setPrototypeOf`
      setPrototypeOf$1 &&
      // we haven't completely correct pre-ES6 way for getting `new.target`, so use this
      isCallable$c(NewTarget = dummy.constructor) &&
      NewTarget !== Wrapper &&
      isObject$g(NewTargetPrototype = NewTarget.prototype) &&
      NewTargetPrototype !== Wrapper.prototype
    ) setPrototypeOf$1($this, NewTargetPrototype);
    return $this;
  };

  var $$Q = _export;
  var global$k = global$u;
  var uncurryThis$s = functionUncurryThis;
  var isForced$2 = isForced_1;
  var defineBuiltIn$9 = defineBuiltIn$e;
  var InternalMetadataModule$1 = internalMetadata.exports;
  var iterate$4 = iterate$5;
  var anInstance$5 = anInstance$6;
  var isCallable$b = isCallable$t;
  var isNullOrUndefined$7 = isNullOrUndefined$b;
  var isObject$f = isObject$q;
  var fails$r = fails$I;
  var checkCorrectnessOfIteration$2 = checkCorrectnessOfIteration$3;
  var setToStringTag$5 = setToStringTag$8;
  var inheritIfRequired$1 = inheritIfRequired$2;

  var collection$3 = function (CONSTRUCTOR_NAME, wrapper, common) {
    var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
    var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
    var ADDER = IS_MAP ? 'set' : 'add';
    var NativeConstructor = global$k[CONSTRUCTOR_NAME];
    var NativePrototype = NativeConstructor && NativeConstructor.prototype;
    var Constructor = NativeConstructor;
    var exported = {};

    var fixMethod = function (KEY) {
      var uncurriedNativeMethod = uncurryThis$s(NativePrototype[KEY]);
      defineBuiltIn$9(NativePrototype, KEY,
        KEY == 'add' ? function add(value) {
          uncurriedNativeMethod(this, value === 0 ? 0 : value);
          return this;
        } : KEY == 'delete' ? function (key) {
          return IS_WEAK && !isObject$f(key) ? false : uncurriedNativeMethod(this, key === 0 ? 0 : key);
        } : KEY == 'get' ? function get(key) {
          return IS_WEAK && !isObject$f(key) ? undefined : uncurriedNativeMethod(this, key === 0 ? 0 : key);
        } : KEY == 'has' ? function has(key) {
          return IS_WEAK && !isObject$f(key) ? false : uncurriedNativeMethod(this, key === 0 ? 0 : key);
        } : function set(key, value) {
          uncurriedNativeMethod(this, key === 0 ? 0 : key, value);
          return this;
        }
      );
    };

    var REPLACE = isForced$2(
      CONSTRUCTOR_NAME,
      !isCallable$b(NativeConstructor) || !(IS_WEAK || NativePrototype.forEach && !fails$r(function () {
        new NativeConstructor().entries().next();
      }))
    );

    if (REPLACE) {
      // create collection constructor
      Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
      InternalMetadataModule$1.enable();
    } else if (isForced$2(CONSTRUCTOR_NAME, true)) {
      var instance = new Constructor();
      // early implementations not supports chaining
      var HASNT_CHAINING = instance[ADDER](IS_WEAK ? {} : -0, 1) != instance;
      // V8 ~ Chromium 40- weak-collections throws on primitives, but should return false
      var THROWS_ON_PRIMITIVES = fails$r(function () { instance.has(1); });
      // most early implementations doesn't supports iterables, most modern - not close it correctly
      // eslint-disable-next-line no-new -- required for testing
      var ACCEPT_ITERABLES = checkCorrectnessOfIteration$2(function (iterable) { new NativeConstructor(iterable); });
      // for early implementations -0 and +0 not the same
      var BUGGY_ZERO = !IS_WEAK && fails$r(function () {
        // V8 ~ Chromium 42- fails only with 5+ elements
        var $instance = new NativeConstructor();
        var index = 5;
        while (index--) $instance[ADDER](index, index);
        return !$instance.has(-0);
      });

      if (!ACCEPT_ITERABLES) {
        Constructor = wrapper(function (dummy, iterable) {
          anInstance$5(dummy, NativePrototype);
          var that = inheritIfRequired$1(new NativeConstructor(), dummy, Constructor);
          if (!isNullOrUndefined$7(iterable)) iterate$4(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
          return that;
        });
        Constructor.prototype = NativePrototype;
        NativePrototype.constructor = Constructor;
      }

      if (THROWS_ON_PRIMITIVES || BUGGY_ZERO) {
        fixMethod('delete');
        fixMethod('has');
        IS_MAP && fixMethod('get');
      }

      if (BUGGY_ZERO || HASNT_CHAINING) fixMethod(ADDER);

      // weak collections should not contains .clear method
      if (IS_WEAK && NativePrototype.clear) delete NativePrototype.clear;
    }

    exported[CONSTRUCTOR_NAME] = Constructor;
    $$Q({ global: true, constructor: true, forced: Constructor != NativeConstructor }, exported);

    setToStringTag$5(Constructor, CONSTRUCTOR_NAME);

    if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

    return Constructor;
  };

  var makeBuiltIn = makeBuiltIn$3.exports;
  var defineProperty$2 = objectDefineProperty;

  var defineBuiltInAccessor$7 = function (target, name, descriptor) {
    if (descriptor.get) makeBuiltIn(descriptor.get, name, { getter: true });
    if (descriptor.set) makeBuiltIn(descriptor.set, name, { setter: true });
    return defineProperty$2.f(target, name, descriptor);
  };

  var defineBuiltIn$8 = defineBuiltIn$e;

  var defineBuiltIns$4 = function (target, src, options) {
    for (var key in src) defineBuiltIn$8(target, key, src[key], options);
    return target;
  };

  var getBuiltIn$5 = getBuiltIn$a;
  var defineBuiltInAccessor$6 = defineBuiltInAccessor$7;
  var wellKnownSymbol$e = wellKnownSymbol$r;
  var DESCRIPTORS$b = descriptors;

  var SPECIES$4 = wellKnownSymbol$e('species');

  var setSpecies$2 = function (CONSTRUCTOR_NAME) {
    var Constructor = getBuiltIn$5(CONSTRUCTOR_NAME);

    if (DESCRIPTORS$b && Constructor && !Constructor[SPECIES$4]) {
      defineBuiltInAccessor$6(Constructor, SPECIES$4, {
        configurable: true,
        get: function () { return this; }
      });
    }
  };

  var create$2 = objectCreate;
  var defineBuiltInAccessor$5 = defineBuiltInAccessor$7;
  var defineBuiltIns$3 = defineBuiltIns$4;
  var bind$8 = functionBindContext;
  var anInstance$4 = anInstance$6;
  var isNullOrUndefined$6 = isNullOrUndefined$b;
  var iterate$3 = iterate$5;
  var defineIterator$1 = iteratorDefine;
  var createIterResultObject$1 = createIterResultObject$3;
  var setSpecies$1 = setSpecies$2;
  var DESCRIPTORS$a = descriptors;
  var fastKey = internalMetadata.exports.fastKey;
  var InternalStateModule$6 = internalState;

  var setInternalState$6 = InternalStateModule$6.set;
  var internalStateGetterFor$1 = InternalStateModule$6.getterFor;

  var collectionStrong$2 = {
    getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
      var Constructor = wrapper(function (that, iterable) {
        anInstance$4(that, Prototype);
        setInternalState$6(that, {
          type: CONSTRUCTOR_NAME,
          index: create$2(null),
          first: undefined,
          last: undefined,
          size: 0
        });
        if (!DESCRIPTORS$a) that.size = 0;
        if (!isNullOrUndefined$6(iterable)) iterate$3(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
      });

      var Prototype = Constructor.prototype;

      var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

      var define = function (that, key, value) {
        var state = getInternalState(that);
        var entry = getEntry(that, key);
        var previous, index;
        // change existing entry
        if (entry) {
          entry.value = value;
        // create new entry
        } else {
          state.last = entry = {
            index: index = fastKey(key, true),
            key: key,
            value: value,
            previous: previous = state.last,
            next: undefined,
            removed: false
          };
          if (!state.first) state.first = entry;
          if (previous) previous.next = entry;
          if (DESCRIPTORS$a) state.size++;
          else that.size++;
          // add to index
          if (index !== 'F') state.index[index] = entry;
        } return that;
      };

      var getEntry = function (that, key) {
        var state = getInternalState(that);
        // fast case
        var index = fastKey(key);
        var entry;
        if (index !== 'F') return state.index[index];
        // frozen object case
        for (entry = state.first; entry; entry = entry.next) {
          if (entry.key == key) return entry;
        }
      };

      defineBuiltIns$3(Prototype, {
        // `{ Map, Set }.prototype.clear()` methods
        // https://tc39.es/ecma262/#sec-map.prototype.clear
        // https://tc39.es/ecma262/#sec-set.prototype.clear
        clear: function clear() {
          var that = this;
          var state = getInternalState(that);
          var data = state.index;
          var entry = state.first;
          while (entry) {
            entry.removed = true;
            if (entry.previous) entry.previous = entry.previous.next = undefined;
            delete data[entry.index];
            entry = entry.next;
          }
          state.first = state.last = undefined;
          if (DESCRIPTORS$a) state.size = 0;
          else that.size = 0;
        },
        // `{ Map, Set }.prototype.delete(key)` methods
        // https://tc39.es/ecma262/#sec-map.prototype.delete
        // https://tc39.es/ecma262/#sec-set.prototype.delete
        'delete': function (key) {
          var that = this;
          var state = getInternalState(that);
          var entry = getEntry(that, key);
          if (entry) {
            var next = entry.next;
            var prev = entry.previous;
            delete state.index[entry.index];
            entry.removed = true;
            if (prev) prev.next = next;
            if (next) next.previous = prev;
            if (state.first == entry) state.first = next;
            if (state.last == entry) state.last = prev;
            if (DESCRIPTORS$a) state.size--;
            else that.size--;
          } return !!entry;
        },
        // `{ Map, Set }.prototype.forEach(callbackfn, thisArg = undefined)` methods
        // https://tc39.es/ecma262/#sec-map.prototype.foreach
        // https://tc39.es/ecma262/#sec-set.prototype.foreach
        forEach: function forEach(callbackfn /* , that = undefined */) {
          var state = getInternalState(this);
          var boundFunction = bind$8(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          var entry;
          while (entry = entry ? entry.next : state.first) {
            boundFunction(entry.value, entry.key, this);
            // revert to the last existing entry
            while (entry && entry.removed) entry = entry.previous;
          }
        },
        // `{ Map, Set}.prototype.has(key)` methods
        // https://tc39.es/ecma262/#sec-map.prototype.has
        // https://tc39.es/ecma262/#sec-set.prototype.has
        has: function has(key) {
          return !!getEntry(this, key);
        }
      });

      defineBuiltIns$3(Prototype, IS_MAP ? {
        // `Map.prototype.get(key)` method
        // https://tc39.es/ecma262/#sec-map.prototype.get
        get: function get(key) {
          var entry = getEntry(this, key);
          return entry && entry.value;
        },
        // `Map.prototype.set(key, value)` method
        // https://tc39.es/ecma262/#sec-map.prototype.set
        set: function set(key, value) {
          return define(this, key === 0 ? 0 : key, value);
        }
      } : {
        // `Set.prototype.add(value)` method
        // https://tc39.es/ecma262/#sec-set.prototype.add
        add: function add(value) {
          return define(this, value = value === 0 ? 0 : value, value);
        }
      });
      if (DESCRIPTORS$a) defineBuiltInAccessor$5(Prototype, 'size', {
        configurable: true,
        get: function () {
          return getInternalState(this).size;
        }
      });
      return Constructor;
    },
    setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
      var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
      var getInternalCollectionState = internalStateGetterFor$1(CONSTRUCTOR_NAME);
      var getInternalIteratorState = internalStateGetterFor$1(ITERATOR_NAME);
      // `{ Map, Set }.prototype.{ keys, values, entries, @@iterator }()` methods
      // https://tc39.es/ecma262/#sec-map.prototype.entries
      // https://tc39.es/ecma262/#sec-map.prototype.keys
      // https://tc39.es/ecma262/#sec-map.prototype.values
      // https://tc39.es/ecma262/#sec-map.prototype-@@iterator
      // https://tc39.es/ecma262/#sec-set.prototype.entries
      // https://tc39.es/ecma262/#sec-set.prototype.keys
      // https://tc39.es/ecma262/#sec-set.prototype.values
      // https://tc39.es/ecma262/#sec-set.prototype-@@iterator
      defineIterator$1(Constructor, CONSTRUCTOR_NAME, function (iterated, kind) {
        setInternalState$6(this, {
          type: ITERATOR_NAME,
          target: iterated,
          state: getInternalCollectionState(iterated),
          kind: kind,
          last: undefined
        });
      }, function () {
        var state = getInternalIteratorState(this);
        var kind = state.kind;
        var entry = state.last;
        // revert to the last existing entry
        while (entry && entry.removed) entry = entry.previous;
        // get next entry
        if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
          // or finish the iteration
          state.target = undefined;
          return createIterResultObject$1(undefined, true);
        }
        // return step by kind
        if (kind == 'keys') return createIterResultObject$1(entry.key, false);
        if (kind == 'values') return createIterResultObject$1(entry.value, false);
        return createIterResultObject$1([entry.key, entry.value], false);
      }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

      // `{ Map, Set }.prototype[@@species]` accessors
      // https://tc39.es/ecma262/#sec-get-map-@@species
      // https://tc39.es/ecma262/#sec-get-set-@@species
      setSpecies$1(CONSTRUCTOR_NAME);
    }
  };

  var collection$2 = collection$3;
  var collectionStrong$1 = collectionStrong$2;

  // `Set` constructor
  // https://tc39.es/ecma262/#sec-set-objects
  collection$2('Set', function (init) {
    return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
  }, collectionStrong$1);

  var classof$5 = classof$c;

  var $String$2 = String;

  var toString$i = function (argument) {
    if (classof$5(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
    return $String$2(argument);
  };

  var uncurryThis$r = functionUncurryThis;
  var toIntegerOrInfinity$5 = toIntegerOrInfinity$8;
  var toString$h = toString$i;
  var requireObjectCoercible$a = requireObjectCoercible$d;

  var charAt$7 = uncurryThis$r(''.charAt);
  var charCodeAt$3 = uncurryThis$r(''.charCodeAt);
  var stringSlice$a = uncurryThis$r(''.slice);

  var createMethod$3 = function (CONVERT_TO_STRING) {
    return function ($this, pos) {
      var S = toString$h(requireObjectCoercible$a($this));
      var position = toIntegerOrInfinity$5(pos);
      var size = S.length;
      var first, second;
      if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
      first = charCodeAt$3(S, position);
      return first < 0xD800 || first > 0xDBFF || position + 1 === size
        || (second = charCodeAt$3(S, position + 1)) < 0xDC00 || second > 0xDFFF
          ? CONVERT_TO_STRING
            ? charAt$7(S, position)
            : first
          : CONVERT_TO_STRING
            ? stringSlice$a(S, position, position + 2)
            : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
    };
  };

  var stringMultibyte = {
    // `String.prototype.codePointAt` method
    // https://tc39.es/ecma262/#sec-string.prototype.codepointat
    codeAt: createMethod$3(false),
    // `String.prototype.at` method
    // https://github.com/mathiasbynens/String.prototype.at
    charAt: createMethod$3(true)
  };

  var charAt$6 = stringMultibyte.charAt;
  var toString$g = toString$i;
  var InternalStateModule$5 = internalState;
  var defineIterator = iteratorDefine;
  var createIterResultObject = createIterResultObject$3;

  var STRING_ITERATOR = 'String Iterator';
  var setInternalState$5 = InternalStateModule$5.set;
  var getInternalState$2 = InternalStateModule$5.getterFor(STRING_ITERATOR);

  // `String.prototype[@@iterator]` method
  // https://tc39.es/ecma262/#sec-string.prototype-@@iterator
  defineIterator(String, 'String', function (iterated) {
    setInternalState$5(this, {
      type: STRING_ITERATOR,
      string: toString$g(iterated),
      index: 0
    });
  // `%StringIteratorPrototype%.next` method
  // https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
  }, function next() {
    var state = getInternalState$2(this);
    var string = state.string;
    var index = state.index;
    var point;
    if (index >= string.length) return createIterResultObject(undefined, true);
    point = charAt$6(string, index);
    state.index += point.length;
    return createIterResultObject(point, false);
  });

  // iterable DOM collections
  // flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
  var domIterables = {
    CSSRuleList: 0,
    CSSStyleDeclaration: 0,
    CSSValueList: 0,
    ClientRectList: 0,
    DOMRectList: 0,
    DOMStringList: 0,
    DOMTokenList: 1,
    DataTransferItemList: 0,
    FileList: 0,
    HTMLAllCollection: 0,
    HTMLCollection: 0,
    HTMLFormElement: 0,
    HTMLSelectElement: 0,
    MediaList: 0,
    MimeTypeArray: 0,
    NamedNodeMap: 0,
    NodeList: 1,
    PaintRequestList: 0,
    Plugin: 0,
    PluginArray: 0,
    SVGLengthList: 0,
    SVGNumberList: 0,
    SVGPathSegList: 0,
    SVGPointList: 0,
    SVGStringList: 0,
    SVGTransformList: 0,
    SourceBufferList: 0,
    StyleSheetList: 0,
    TextTrackCueList: 0,
    TextTrackList: 0,
    TouchList: 0
  };

  // in old WebKit versions, `element.classList` is not an instance of global `DOMTokenList`
  var documentCreateElement = documentCreateElement$2;

  var classList = documentCreateElement('span').classList;
  var DOMTokenListPrototype$2 = classList && classList.constructor && classList.constructor.prototype;

  var domTokenListPrototype = DOMTokenListPrototype$2 === Object.prototype ? undefined : DOMTokenListPrototype$2;

  var global$j = global$u;
  var DOMIterables$1 = domIterables;
  var DOMTokenListPrototype$1 = domTokenListPrototype;
  var ArrayIteratorMethods = es_array_iterator;
  var createNonEnumerableProperty$2 = createNonEnumerableProperty$6;
  var wellKnownSymbol$d = wellKnownSymbol$r;

  var ITERATOR$2 = wellKnownSymbol$d('iterator');
  var TO_STRING_TAG = wellKnownSymbol$d('toStringTag');
  var ArrayValues = ArrayIteratorMethods.values;

  var handlePrototype$1 = function (CollectionPrototype, COLLECTION_NAME) {
    if (CollectionPrototype) {
      // some Chrome versions have non-configurable methods on DOMTokenList
      if (CollectionPrototype[ITERATOR$2] !== ArrayValues) try {
        createNonEnumerableProperty$2(CollectionPrototype, ITERATOR$2, ArrayValues);
      } catch (error) {
        CollectionPrototype[ITERATOR$2] = ArrayValues;
      }
      if (!CollectionPrototype[TO_STRING_TAG]) {
        createNonEnumerableProperty$2(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
      }
      if (DOMIterables$1[COLLECTION_NAME]) for (var METHOD_NAME in ArrayIteratorMethods) {
        // some Chrome versions have non-configurable methods on DOMTokenList
        if (CollectionPrototype[METHOD_NAME] !== ArrayIteratorMethods[METHOD_NAME]) try {
          createNonEnumerableProperty$2(CollectionPrototype, METHOD_NAME, ArrayIteratorMethods[METHOD_NAME]);
        } catch (error) {
          CollectionPrototype[METHOD_NAME] = ArrayIteratorMethods[METHOD_NAME];
        }
      }
    }
  };

  for (var COLLECTION_NAME$1 in DOMIterables$1) {
    handlePrototype$1(global$j[COLLECTION_NAME$1] && global$j[COLLECTION_NAME$1].prototype, COLLECTION_NAME$1);
  }

  handlePrototype$1(DOMTokenListPrototype$1, 'DOMTokenList');

  var isArray$8 = isArray$a;
  var isConstructor$2 = isConstructor$4;
  var isObject$e = isObject$q;
  var wellKnownSymbol$c = wellKnownSymbol$r;

  var SPECIES$3 = wellKnownSymbol$c('species');
  var $Array$1 = Array;

  // a part of `ArraySpeciesCreate` abstract operation
  // https://tc39.es/ecma262/#sec-arrayspeciescreate
  var arraySpeciesConstructor$1 = function (originalArray) {
    var C;
    if (isArray$8(originalArray)) {
      C = originalArray.constructor;
      // cross-realm fallback
      if (isConstructor$2(C) && (C === $Array$1 || isArray$8(C.prototype))) C = undefined;
      else if (isObject$e(C)) {
        C = C[SPECIES$3];
        if (C === null) C = undefined;
      }
    } return C === undefined ? $Array$1 : C;
  };

  var arraySpeciesConstructor = arraySpeciesConstructor$1;

  // `ArraySpeciesCreate` abstract operation
  // https://tc39.es/ecma262/#sec-arrayspeciescreate
  var arraySpeciesCreate$4 = function (originalArray, length) {
    return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
  };

  var bind$7 = functionBindContext;
  var uncurryThis$q = functionUncurryThis;
  var IndexedObject$1 = indexedObject;
  var toObject$a = toObject$e;
  var lengthOfArrayLike$7 = lengthOfArrayLike$c;
  var arraySpeciesCreate$3 = arraySpeciesCreate$4;

  var push$9 = uncurryThis$q([].push);

  // `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
  var createMethod$2 = function (TYPE) {
    var IS_MAP = TYPE == 1;
    var IS_FILTER = TYPE == 2;
    var IS_SOME = TYPE == 3;
    var IS_EVERY = TYPE == 4;
    var IS_FIND_INDEX = TYPE == 6;
    var IS_FILTER_REJECT = TYPE == 7;
    var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
    return function ($this, callbackfn, that, specificCreate) {
      var O = toObject$a($this);
      var self = IndexedObject$1(O);
      var boundFunction = bind$7(callbackfn, that);
      var length = lengthOfArrayLike$7(self);
      var index = 0;
      var create = specificCreate || arraySpeciesCreate$3;
      var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
      var value, result;
      for (;length > index; index++) if (NO_HOLES || index in self) {
        value = self[index];
        result = boundFunction(value, index, O);
        if (TYPE) {
          if (IS_MAP) target[index] = result; // map
          else if (result) switch (TYPE) {
            case 3: return true;              // some
            case 5: return value;             // find
            case 6: return index;             // findIndex
            case 2: push$9(target, value);      // filter
          } else switch (TYPE) {
            case 4: return false;             // every
            case 7: push$9(target, value);      // filterReject
          }
        }
      }
      return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
    };
  };

  var arrayIteration = {
    // `Array.prototype.forEach` method
    // https://tc39.es/ecma262/#sec-array.prototype.foreach
    forEach: createMethod$2(0),
    // `Array.prototype.map` method
    // https://tc39.es/ecma262/#sec-array.prototype.map
    map: createMethod$2(1),
    // `Array.prototype.filter` method
    // https://tc39.es/ecma262/#sec-array.prototype.filter
    filter: createMethod$2(2),
    // `Array.prototype.some` method
    // https://tc39.es/ecma262/#sec-array.prototype.some
    some: createMethod$2(3),
    // `Array.prototype.every` method
    // https://tc39.es/ecma262/#sec-array.prototype.every
    every: createMethod$2(4),
    // `Array.prototype.find` method
    // https://tc39.es/ecma262/#sec-array.prototype.find
    find: createMethod$2(5),
    // `Array.prototype.findIndex` method
    // https://tc39.es/ecma262/#sec-array.prototype.findIndex
    findIndex: createMethod$2(6),
    // `Array.prototype.filterReject` method
    // https://github.com/tc39/proposal-array-filtering
    filterReject: createMethod$2(7)
  };

  var uncurryThis$p = functionUncurryThis;
  var defineBuiltIns$2 = defineBuiltIns$4;
  var getWeakData = internalMetadata.exports.getWeakData;
  var anInstance$3 = anInstance$6;
  var anObject$i = anObject$r;
  var isNullOrUndefined$5 = isNullOrUndefined$b;
  var isObject$d = isObject$q;
  var iterate$2 = iterate$5;
  var ArrayIterationModule = arrayIteration;
  var hasOwn$d = hasOwnProperty_1;
  var InternalStateModule$4 = internalState;

  var setInternalState$4 = InternalStateModule$4.set;
  var internalStateGetterFor = InternalStateModule$4.getterFor;
  var find$1 = ArrayIterationModule.find;
  var findIndex = ArrayIterationModule.findIndex;
  var splice$1 = uncurryThis$p([].splice);
  var id = 0;

  // fallback for uncaught frozen keys
  var uncaughtFrozenStore = function (state) {
    return state.frozen || (state.frozen = new UncaughtFrozenStore());
  };

  var UncaughtFrozenStore = function () {
    this.entries = [];
  };

  var findUncaughtFrozen = function (store, key) {
    return find$1(store.entries, function (it) {
      return it[0] === key;
    });
  };

  UncaughtFrozenStore.prototype = {
    get: function (key) {
      var entry = findUncaughtFrozen(this, key);
      if (entry) return entry[1];
    },
    has: function (key) {
      return !!findUncaughtFrozen(this, key);
    },
    set: function (key, value) {
      var entry = findUncaughtFrozen(this, key);
      if (entry) entry[1] = value;
      else this.entries.push([key, value]);
    },
    'delete': function (key) {
      var index = findIndex(this.entries, function (it) {
        return it[0] === key;
      });
      if (~index) splice$1(this.entries, index, 1);
      return !!~index;
    }
  };

  var collectionWeak$1 = {
    getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
      var Constructor = wrapper(function (that, iterable) {
        anInstance$3(that, Prototype);
        setInternalState$4(that, {
          type: CONSTRUCTOR_NAME,
          id: id++,
          frozen: undefined
        });
        if (!isNullOrUndefined$5(iterable)) iterate$2(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
      });

      var Prototype = Constructor.prototype;

      var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

      var define = function (that, key, value) {
        var state = getInternalState(that);
        var data = getWeakData(anObject$i(key), true);
        if (data === true) uncaughtFrozenStore(state).set(key, value);
        else data[state.id] = value;
        return that;
      };

      defineBuiltIns$2(Prototype, {
        // `{ WeakMap, WeakSet }.prototype.delete(key)` methods
        // https://tc39.es/ecma262/#sec-weakmap.prototype.delete
        // https://tc39.es/ecma262/#sec-weakset.prototype.delete
        'delete': function (key) {
          var state = getInternalState(this);
          if (!isObject$d(key)) return false;
          var data = getWeakData(key);
          if (data === true) return uncaughtFrozenStore(state)['delete'](key);
          return data && hasOwn$d(data, state.id) && delete data[state.id];
        },
        // `{ WeakMap, WeakSet }.prototype.has(key)` methods
        // https://tc39.es/ecma262/#sec-weakmap.prototype.has
        // https://tc39.es/ecma262/#sec-weakset.prototype.has
        has: function has(key) {
          var state = getInternalState(this);
          if (!isObject$d(key)) return false;
          var data = getWeakData(key);
          if (data === true) return uncaughtFrozenStore(state).has(key);
          return data && hasOwn$d(data, state.id);
        }
      });

      defineBuiltIns$2(Prototype, IS_MAP ? {
        // `WeakMap.prototype.get(key)` method
        // https://tc39.es/ecma262/#sec-weakmap.prototype.get
        get: function get(key) {
          var state = getInternalState(this);
          if (isObject$d(key)) {
            var data = getWeakData(key);
            if (data === true) return uncaughtFrozenStore(state).get(key);
            return data ? data[state.id] : undefined;
          }
        },
        // `WeakMap.prototype.set(key, value)` method
        // https://tc39.es/ecma262/#sec-weakmap.prototype.set
        set: function set(key, value) {
          return define(this, key, value);
        }
      } : {
        // `WeakSet.prototype.add(value)` method
        // https://tc39.es/ecma262/#sec-weakset.prototype.add
        add: function add(value) {
          return define(this, value, true);
        }
      });

      return Constructor;
    }
  };

  var FREEZING$1 = freezing;
  var global$i = global$u;
  var uncurryThis$o = functionUncurryThis;
  var defineBuiltIns$1 = defineBuiltIns$4;
  var InternalMetadataModule = internalMetadata.exports;
  var collection$1 = collection$3;
  var collectionWeak = collectionWeak$1;
  var isObject$c = isObject$q;
  var enforceInternalState = internalState.enforce;
  var fails$q = fails$I;
  var NATIVE_WEAK_MAP = weakMapBasicDetection;

  var $Object = Object;
  // eslint-disable-next-line es/no-array-isarray -- safe
  var isArray$7 = Array.isArray;
  // eslint-disable-next-line es/no-object-isextensible -- safe
  var isExtensible = $Object.isExtensible;
  // eslint-disable-next-line es/no-object-isfrozen -- safe
  var isFrozen = $Object.isFrozen;
  // eslint-disable-next-line es/no-object-issealed -- safe
  var isSealed = $Object.isSealed;
  // eslint-disable-next-line es/no-object-freeze -- safe
  var freeze = $Object.freeze;
  // eslint-disable-next-line es/no-object-seal -- safe
  var seal = $Object.seal;

  var FROZEN = {};
  var SEALED = {};
  var IS_IE11 = !global$i.ActiveXObject && 'ActiveXObject' in global$i;
  var InternalWeakMap;

  var wrapper = function (init) {
    return function WeakMap() {
      return init(this, arguments.length ? arguments[0] : undefined);
    };
  };

  // `WeakMap` constructor
  // https://tc39.es/ecma262/#sec-weakmap-constructor
  var $WeakMap = collection$1('WeakMap', wrapper, collectionWeak);
  var WeakMapPrototype = $WeakMap.prototype;
  var nativeSet = uncurryThis$o(WeakMapPrototype.set);

  // Chakra Edge bug: adding frozen arrays to WeakMap unfreeze them
  var hasMSEdgeFreezingBug = function () {
    return FREEZING$1 && fails$q(function () {
      var frozenArray = freeze([]);
      nativeSet(new $WeakMap(), frozenArray, 1);
      return !isFrozen(frozenArray);
    });
  };

  // IE11 WeakMap frozen keys fix
  // We can't use feature detection because it crash some old IE builds
  // https://github.com/zloirock/core-js/issues/485
  if (NATIVE_WEAK_MAP) if (IS_IE11) {
    InternalWeakMap = collectionWeak.getConstructor(wrapper, 'WeakMap', true);
    InternalMetadataModule.enable();
    var nativeDelete = uncurryThis$o(WeakMapPrototype['delete']);
    var nativeHas = uncurryThis$o(WeakMapPrototype.has);
    var nativeGet = uncurryThis$o(WeakMapPrototype.get);
    defineBuiltIns$1(WeakMapPrototype, {
      'delete': function (key) {
        if (isObject$c(key) && !isExtensible(key)) {
          var state = enforceInternalState(this);
          if (!state.frozen) state.frozen = new InternalWeakMap();
          return nativeDelete(this, key) || state.frozen['delete'](key);
        } return nativeDelete(this, key);
      },
      has: function has(key) {
        if (isObject$c(key) && !isExtensible(key)) {
          var state = enforceInternalState(this);
          if (!state.frozen) state.frozen = new InternalWeakMap();
          return nativeHas(this, key) || state.frozen.has(key);
        } return nativeHas(this, key);
      },
      get: function get(key) {
        if (isObject$c(key) && !isExtensible(key)) {
          var state = enforceInternalState(this);
          if (!state.frozen) state.frozen = new InternalWeakMap();
          return nativeHas(this, key) ? nativeGet(this, key) : state.frozen.get(key);
        } return nativeGet(this, key);
      },
      set: function set(key, value) {
        if (isObject$c(key) && !isExtensible(key)) {
          var state = enforceInternalState(this);
          if (!state.frozen) state.frozen = new InternalWeakMap();
          nativeHas(this, key) ? nativeSet(this, key, value) : state.frozen.set(key, value);
        } else nativeSet(this, key, value);
        return this;
      }
    });
  // Chakra Edge frozen keys fix
  } else if (hasMSEdgeFreezingBug()) {
    defineBuiltIns$1(WeakMapPrototype, {
      set: function set(key, value) {
        var arrayIntegrityLevel;
        if (isArray$7(key)) {
          if (isFrozen(key)) arrayIntegrityLevel = FROZEN;
          else if (isSealed(key)) arrayIntegrityLevel = SEALED;
        }
        nativeSet(this, key, value);
        if (arrayIntegrityLevel == FROZEN) freeze(key);
        if (arrayIntegrityLevel == SEALED) seal(key);
        return this;
      }
    });
  }

  var wellKnownSymbolWrapped = {};

  var wellKnownSymbol$b = wellKnownSymbol$r;

  wellKnownSymbolWrapped.f = wellKnownSymbol$b;

  var global$h = global$u;

  var path$2 = global$h;

  var path$1 = path$2;
  var hasOwn$c = hasOwnProperty_1;
  var wrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;
  var defineProperty$1 = objectDefineProperty.f;

  var wellKnownSymbolDefine = function (NAME) {
    var Symbol = path$1.Symbol || (path$1.Symbol = {});
    if (!hasOwn$c(Symbol, NAME)) defineProperty$1(Symbol, NAME, {
      value: wrappedWellKnownSymbolModule$1.f(NAME)
    });
  };

  var call$k = functionCall;
  var getBuiltIn$4 = getBuiltIn$a;
  var wellKnownSymbol$a = wellKnownSymbol$r;
  var defineBuiltIn$7 = defineBuiltIn$e;

  var symbolDefineToPrimitive = function () {
    var Symbol = getBuiltIn$4('Symbol');
    var SymbolPrototype = Symbol && Symbol.prototype;
    var valueOf = SymbolPrototype && SymbolPrototype.valueOf;
    var TO_PRIMITIVE = wellKnownSymbol$a('toPrimitive');

    if (SymbolPrototype && !SymbolPrototype[TO_PRIMITIVE]) {
      // `Symbol.prototype[@@toPrimitive]` method
      // https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
      // eslint-disable-next-line no-unused-vars -- required for .length
      defineBuiltIn$7(SymbolPrototype, TO_PRIMITIVE, function (hint) {
        return call$k(valueOf, this);
      }, { arity: 1 });
    }
  };

  var $$P = _export;
  var global$g = global$u;
  var call$j = functionCall;
  var uncurryThis$n = functionUncurryThis;
  var DESCRIPTORS$9 = descriptors;
  var NATIVE_SYMBOL$4 = symbolConstructorDetection;
  var fails$p = fails$I;
  var hasOwn$b = hasOwnProperty_1;
  var isPrototypeOf$3 = objectIsPrototypeOf;
  var anObject$h = anObject$r;
  var toIndexedObject$2 = toIndexedObject$a;
  var toPropertyKey$1 = toPropertyKey$5;
  var $toString$3 = toString$i;
  var createPropertyDescriptor$2 = createPropertyDescriptor$7;
  var nativeObjectCreate = objectCreate;
  var objectKeys$1 = objectKeys$4;
  var getOwnPropertyNamesModule = objectGetOwnPropertyNames;
  var getOwnPropertyNamesExternal = objectGetOwnPropertyNamesExternal;
  var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
  var getOwnPropertyDescriptorModule$2 = objectGetOwnPropertyDescriptor;
  var definePropertyModule$2 = objectDefineProperty;
  var definePropertiesModule = objectDefineProperties;
  var propertyIsEnumerableModule = objectPropertyIsEnumerable;
  var defineBuiltIn$6 = defineBuiltIn$e;
  var defineBuiltInAccessor$4 = defineBuiltInAccessor$7;
  var shared$3 = shared$7.exports;
  var sharedKey = sharedKey$4;
  var hiddenKeys = hiddenKeys$6;
  var uid$2 = uid$6;
  var wellKnownSymbol$9 = wellKnownSymbol$r;
  var wrappedWellKnownSymbolModule = wellKnownSymbolWrapped;
  var defineWellKnownSymbol$1 = wellKnownSymbolDefine;
  var defineSymbolToPrimitive = symbolDefineToPrimitive;
  var setToStringTag$4 = setToStringTag$8;
  var InternalStateModule$3 = internalState;
  var $forEach$1 = arrayIteration.forEach;

  var HIDDEN = sharedKey('hidden');
  var SYMBOL = 'Symbol';
  var PROTOTYPE = 'prototype';

  var setInternalState$3 = InternalStateModule$3.set;
  var getInternalState$1 = InternalStateModule$3.getterFor(SYMBOL);

  var ObjectPrototype = Object[PROTOTYPE];
  var $Symbol = global$g.Symbol;
  var SymbolPrototype$1 = $Symbol && $Symbol[PROTOTYPE];
  var TypeError$5 = global$g.TypeError;
  var QObject = global$g.QObject;
  var nativeGetOwnPropertyDescriptor = getOwnPropertyDescriptorModule$2.f;
  var nativeDefineProperty = definePropertyModule$2.f;
  var nativeGetOwnPropertyNames = getOwnPropertyNamesExternal.f;
  var nativePropertyIsEnumerable = propertyIsEnumerableModule.f;
  var push$8 = uncurryThis$n([].push);

  var AllSymbols = shared$3('symbols');
  var ObjectPrototypeSymbols = shared$3('op-symbols');
  var WellKnownSymbolsStore = shared$3('wks');

  // Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
  var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

  // fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
  var setSymbolDescriptor = DESCRIPTORS$9 && fails$p(function () {
    return nativeObjectCreate(nativeDefineProperty({}, 'a', {
      get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
    })).a != 7;
  }) ? function (O, P, Attributes) {
    var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor(ObjectPrototype, P);
    if (ObjectPrototypeDescriptor) delete ObjectPrototype[P];
    nativeDefineProperty(O, P, Attributes);
    if (ObjectPrototypeDescriptor && O !== ObjectPrototype) {
      nativeDefineProperty(ObjectPrototype, P, ObjectPrototypeDescriptor);
    }
  } : nativeDefineProperty;

  var wrap = function (tag, description) {
    var symbol = AllSymbols[tag] = nativeObjectCreate(SymbolPrototype$1);
    setInternalState$3(symbol, {
      type: SYMBOL,
      tag: tag,
      description: description
    });
    if (!DESCRIPTORS$9) symbol.description = description;
    return symbol;
  };

  var $defineProperty = function defineProperty(O, P, Attributes) {
    if (O === ObjectPrototype) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
    anObject$h(O);
    var key = toPropertyKey$1(P);
    anObject$h(Attributes);
    if (hasOwn$b(AllSymbols, key)) {
      if (!Attributes.enumerable) {
        if (!hasOwn$b(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor$2(1, {}));
        O[HIDDEN][key] = true;
      } else {
        if (hasOwn$b(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
        Attributes = nativeObjectCreate(Attributes, { enumerable: createPropertyDescriptor$2(0, false) });
      } return setSymbolDescriptor(O, key, Attributes);
    } return nativeDefineProperty(O, key, Attributes);
  };

  var $defineProperties = function defineProperties(O, Properties) {
    anObject$h(O);
    var properties = toIndexedObject$2(Properties);
    var keys = objectKeys$1(properties).concat($getOwnPropertySymbols(properties));
    $forEach$1(keys, function (key) {
      if (!DESCRIPTORS$9 || call$j($propertyIsEnumerable$1, properties, key)) $defineProperty(O, key, properties[key]);
    });
    return O;
  };

  var $create = function create(O, Properties) {
    return Properties === undefined ? nativeObjectCreate(O) : $defineProperties(nativeObjectCreate(O), Properties);
  };

  var $propertyIsEnumerable$1 = function propertyIsEnumerable(V) {
    var P = toPropertyKey$1(V);
    var enumerable = call$j(nativePropertyIsEnumerable, this, P);
    if (this === ObjectPrototype && hasOwn$b(AllSymbols, P) && !hasOwn$b(ObjectPrototypeSymbols, P)) return false;
    return enumerable || !hasOwn$b(this, P) || !hasOwn$b(AllSymbols, P) || hasOwn$b(this, HIDDEN) && this[HIDDEN][P]
      ? enumerable : true;
  };

  var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
    var it = toIndexedObject$2(O);
    var key = toPropertyKey$1(P);
    if (it === ObjectPrototype && hasOwn$b(AllSymbols, key) && !hasOwn$b(ObjectPrototypeSymbols, key)) return;
    var descriptor = nativeGetOwnPropertyDescriptor(it, key);
    if (descriptor && hasOwn$b(AllSymbols, key) && !(hasOwn$b(it, HIDDEN) && it[HIDDEN][key])) {
      descriptor.enumerable = true;
    }
    return descriptor;
  };

  var $getOwnPropertyNames = function getOwnPropertyNames(O) {
    var names = nativeGetOwnPropertyNames(toIndexedObject$2(O));
    var result = [];
    $forEach$1(names, function (key) {
      if (!hasOwn$b(AllSymbols, key) && !hasOwn$b(hiddenKeys, key)) push$8(result, key);
    });
    return result;
  };

  var $getOwnPropertySymbols = function (O) {
    var IS_OBJECT_PROTOTYPE = O === ObjectPrototype;
    var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject$2(O));
    var result = [];
    $forEach$1(names, function (key) {
      if (hasOwn$b(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || hasOwn$b(ObjectPrototype, key))) {
        push$8(result, AllSymbols[key]);
      }
    });
    return result;
  };

  // `Symbol` constructor
  // https://tc39.es/ecma262/#sec-symbol-constructor
  if (!NATIVE_SYMBOL$4) {
    $Symbol = function Symbol() {
      if (isPrototypeOf$3(SymbolPrototype$1, this)) throw TypeError$5('Symbol is not a constructor');
      var description = !arguments.length || arguments[0] === undefined ? undefined : $toString$3(arguments[0]);
      var tag = uid$2(description);
      var setter = function (value) {
        if (this === ObjectPrototype) call$j(setter, ObjectPrototypeSymbols, value);
        if (hasOwn$b(this, HIDDEN) && hasOwn$b(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
        setSymbolDescriptor(this, tag, createPropertyDescriptor$2(1, value));
      };
      if (DESCRIPTORS$9 && USE_SETTER) setSymbolDescriptor(ObjectPrototype, tag, { configurable: true, set: setter });
      return wrap(tag, description);
    };

    SymbolPrototype$1 = $Symbol[PROTOTYPE];

    defineBuiltIn$6(SymbolPrototype$1, 'toString', function toString() {
      return getInternalState$1(this).tag;
    });

    defineBuiltIn$6($Symbol, 'withoutSetter', function (description) {
      return wrap(uid$2(description), description);
    });

    propertyIsEnumerableModule.f = $propertyIsEnumerable$1;
    definePropertyModule$2.f = $defineProperty;
    definePropertiesModule.f = $defineProperties;
    getOwnPropertyDescriptorModule$2.f = $getOwnPropertyDescriptor;
    getOwnPropertyNamesModule.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
    getOwnPropertySymbolsModule$1.f = $getOwnPropertySymbols;

    wrappedWellKnownSymbolModule.f = function (name) {
      return wrap(wellKnownSymbol$9(name), name);
    };

    if (DESCRIPTORS$9) {
      // https://github.com/tc39/proposal-Symbol-description
      defineBuiltInAccessor$4(SymbolPrototype$1, 'description', {
        configurable: true,
        get: function description() {
          return getInternalState$1(this).description;
        }
      });
      {
        defineBuiltIn$6(ObjectPrototype, 'propertyIsEnumerable', $propertyIsEnumerable$1, { unsafe: true });
      }
    }
  }

  $$P({ global: true, constructor: true, wrap: true, forced: !NATIVE_SYMBOL$4, sham: !NATIVE_SYMBOL$4 }, {
    Symbol: $Symbol
  });

  $forEach$1(objectKeys$1(WellKnownSymbolsStore), function (name) {
    defineWellKnownSymbol$1(name);
  });

  $$P({ target: SYMBOL, stat: true, forced: !NATIVE_SYMBOL$4 }, {
    useSetter: function () { USE_SETTER = true; },
    useSimple: function () { USE_SETTER = false; }
  });

  $$P({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$4, sham: !DESCRIPTORS$9 }, {
    // `Object.create` method
    // https://tc39.es/ecma262/#sec-object.create
    create: $create,
    // `Object.defineProperty` method
    // https://tc39.es/ecma262/#sec-object.defineproperty
    defineProperty: $defineProperty,
    // `Object.defineProperties` method
    // https://tc39.es/ecma262/#sec-object.defineproperties
    defineProperties: $defineProperties,
    // `Object.getOwnPropertyDescriptor` method
    // https://tc39.es/ecma262/#sec-object.getownpropertydescriptors
    getOwnPropertyDescriptor: $getOwnPropertyDescriptor
  });

  $$P({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$4 }, {
    // `Object.getOwnPropertyNames` method
    // https://tc39.es/ecma262/#sec-object.getownpropertynames
    getOwnPropertyNames: $getOwnPropertyNames
  });

  // `Symbol.prototype[@@toPrimitive]` method
  // https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
  defineSymbolToPrimitive();

  // `Symbol.prototype[@@toStringTag]` property
  // https://tc39.es/ecma262/#sec-symbol.prototype-@@tostringtag
  setToStringTag$4($Symbol, SYMBOL);

  hiddenKeys[HIDDEN] = true;

  var NATIVE_SYMBOL$3 = symbolConstructorDetection;

  /* eslint-disable es/no-symbol -- safe */
  var symbolRegistryDetection = NATIVE_SYMBOL$3 && !!Symbol['for'] && !!Symbol.keyFor;

  var $$O = _export;
  var getBuiltIn$3 = getBuiltIn$a;
  var hasOwn$a = hasOwnProperty_1;
  var toString$f = toString$i;
  var shared$2 = shared$7.exports;
  var NATIVE_SYMBOL_REGISTRY$1 = symbolRegistryDetection;

  var StringToSymbolRegistry = shared$2('string-to-symbol-registry');
  var SymbolToStringRegistry$1 = shared$2('symbol-to-string-registry');

  // `Symbol.for` method
  // https://tc39.es/ecma262/#sec-symbol.for
  $$O({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY$1 }, {
    'for': function (key) {
      var string = toString$f(key);
      if (hasOwn$a(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
      var symbol = getBuiltIn$3('Symbol')(string);
      StringToSymbolRegistry[string] = symbol;
      SymbolToStringRegistry$1[symbol] = string;
      return symbol;
    }
  });

  var $$N = _export;
  var hasOwn$9 = hasOwnProperty_1;
  var isSymbol$3 = isSymbol$6;
  var tryToString$2 = tryToString$6;
  var shared$1 = shared$7.exports;
  var NATIVE_SYMBOL_REGISTRY = symbolRegistryDetection;

  var SymbolToStringRegistry = shared$1('symbol-to-string-registry');

  // `Symbol.keyFor` method
  // https://tc39.es/ecma262/#sec-symbol.keyfor
  $$N({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY }, {
    keyFor: function keyFor(sym) {
      if (!isSymbol$3(sym)) throw TypeError(tryToString$2(sym) + ' is not a symbol');
      if (hasOwn$9(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
    }
  });

  var NATIVE_BIND = functionBindNative;

  var FunctionPrototype$1 = Function.prototype;
  var apply$4 = FunctionPrototype$1.apply;
  var call$i = FunctionPrototype$1.call;

  // eslint-disable-next-line es/no-reflect -- safe
  var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND ? call$i.bind(apply$4) : function () {
    return call$i.apply(apply$4, arguments);
  });

  var uncurryThis$m = functionUncurryThis;
  var isArray$6 = isArray$a;
  var isCallable$a = isCallable$t;
  var classof$4 = classofRaw$2;
  var toString$e = toString$i;

  var push$7 = uncurryThis$m([].push);

  var getJsonReplacerFunction = function (replacer) {
    if (isCallable$a(replacer)) return replacer;
    if (!isArray$6(replacer)) return;
    var rawLength = replacer.length;
    var keys = [];
    for (var i = 0; i < rawLength; i++) {
      var element = replacer[i];
      if (typeof element == 'string') push$7(keys, element);
      else if (typeof element == 'number' || classof$4(element) == 'Number' || classof$4(element) == 'String') push$7(keys, toString$e(element));
    }
    var keysLength = keys.length;
    var root = true;
    return function (key, value) {
      if (root) {
        root = false;
        return value;
      }
      if (isArray$6(this)) return value;
      for (var j = 0; j < keysLength; j++) if (keys[j] === key) return value;
    };
  };

  var $$M = _export;
  var getBuiltIn$2 = getBuiltIn$a;
  var apply$3 = functionApply;
  var call$h = functionCall;
  var uncurryThis$l = functionUncurryThis;
  var fails$o = fails$I;
  var isCallable$9 = isCallable$t;
  var isSymbol$2 = isSymbol$6;
  var arraySlice$4 = arraySlice$6;
  var getReplacerFunction = getJsonReplacerFunction;
  var NATIVE_SYMBOL$2 = symbolConstructorDetection;

  var $String$1 = String;
  var $stringify$1 = getBuiltIn$2('JSON', 'stringify');
  var exec$4 = uncurryThis$l(/./.exec);
  var charAt$5 = uncurryThis$l(''.charAt);
  var charCodeAt$2 = uncurryThis$l(''.charCodeAt);
  var replace$8 = uncurryThis$l(''.replace);
  var numberToString$1 = uncurryThis$l(1.0.toString);

  var tester = /[\uD800-\uDFFF]/g;
  var low = /^[\uD800-\uDBFF]$/;
  var hi = /^[\uDC00-\uDFFF]$/;

  var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL$2 || fails$o(function () {
    var symbol = getBuiltIn$2('Symbol')();
    // MS Edge converts symbol values to JSON as {}
    return $stringify$1([symbol]) != '[null]'
      // WebKit converts symbol values to JSON as null
      || $stringify$1({ a: symbol }) != '{}'
      // V8 throws on boxed symbols
      || $stringify$1(Object(symbol)) != '{}';
  });

  // https://github.com/tc39/proposal-well-formed-stringify
  var ILL_FORMED_UNICODE = fails$o(function () {
    return $stringify$1('\uDF06\uD834') !== '"\\udf06\\ud834"'
      || $stringify$1('\uDEAD') !== '"\\udead"';
  });

  var stringifyWithSymbolsFix = function (it, replacer) {
    var args = arraySlice$4(arguments);
    var $replacer = getReplacerFunction(replacer);
    if (!isCallable$9($replacer) && (it === undefined || isSymbol$2(it))) return; // IE8 returns string on undefined
    args[1] = function (key, value) {
      // some old implementations (like WebKit) could pass numbers as keys
      if (isCallable$9($replacer)) value = call$h($replacer, this, $String$1(key), value);
      if (!isSymbol$2(value)) return value;
    };
    return apply$3($stringify$1, null, args);
  };

  var fixIllFormed = function (match, offset, string) {
    var prev = charAt$5(string, offset - 1);
    var next = charAt$5(string, offset + 1);
    if ((exec$4(low, match) && !exec$4(hi, next)) || (exec$4(hi, match) && !exec$4(low, prev))) {
      return '\\u' + numberToString$1(charCodeAt$2(match, 0), 16);
    } return match;
  };

  if ($stringify$1) {
    // `JSON.stringify` method
    // https://tc39.es/ecma262/#sec-json.stringify
    $$M({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
      // eslint-disable-next-line no-unused-vars -- required for `.length`
      stringify: function stringify(it, replacer, space) {
        var args = arraySlice$4(arguments);
        var result = apply$3(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify$1, null, args);
        return ILL_FORMED_UNICODE && typeof result == 'string' ? replace$8(result, tester, fixIllFormed) : result;
      }
    });
  }

  var $$L = _export;
  var NATIVE_SYMBOL$1 = symbolConstructorDetection;
  var fails$n = fails$I;
  var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
  var toObject$9 = toObject$e;

  // V8 ~ Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
  // https://bugs.chromium.org/p/v8/issues/detail?id=3443
  var FORCED$5 = !NATIVE_SYMBOL$1 || fails$n(function () { getOwnPropertySymbolsModule.f(1); });

  // `Object.getOwnPropertySymbols` method
  // https://tc39.es/ecma262/#sec-object.getownpropertysymbols
  $$L({ target: 'Object', stat: true, forced: FORCED$5 }, {
    getOwnPropertySymbols: function getOwnPropertySymbols(it) {
      var $getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
      return $getOwnPropertySymbols ? $getOwnPropertySymbols(toObject$9(it)) : [];
    }
  });

  var $$K = _export;
  var DESCRIPTORS$8 = descriptors;
  var global$f = global$u;
  var uncurryThis$k = functionUncurryThis;
  var hasOwn$8 = hasOwnProperty_1;
  var isCallable$8 = isCallable$t;
  var isPrototypeOf$2 = objectIsPrototypeOf;
  var toString$d = toString$i;
  var defineBuiltInAccessor$3 = defineBuiltInAccessor$7;
  var copyConstructorProperties$1 = copyConstructorProperties$3;

  var NativeSymbol = global$f.Symbol;
  var SymbolPrototype = NativeSymbol && NativeSymbol.prototype;

  if (DESCRIPTORS$8 && isCallable$8(NativeSymbol) && (!('description' in SymbolPrototype) ||
    // Safari 12 bug
    NativeSymbol().description !== undefined
  )) {
    var EmptyStringDescriptionStore = {};
    // wrap Symbol constructor for correct work with undefined description
    var SymbolWrapper = function Symbol() {
      var description = arguments.length < 1 || arguments[0] === undefined ? undefined : toString$d(arguments[0]);
      var result = isPrototypeOf$2(SymbolPrototype, this)
        ? new NativeSymbol(description)
        // in Edge 13, String(Symbol(undefined)) === 'Symbol(undefined)'
        : description === undefined ? NativeSymbol() : NativeSymbol(description);
      if (description === '') EmptyStringDescriptionStore[result] = true;
      return result;
    };

    copyConstructorProperties$1(SymbolWrapper, NativeSymbol);
    SymbolWrapper.prototype = SymbolPrototype;
    SymbolPrototype.constructor = SymbolWrapper;

    var NATIVE_SYMBOL = String(NativeSymbol('test')) == 'Symbol(test)';
    var thisSymbolValue = uncurryThis$k(SymbolPrototype.valueOf);
    var symbolDescriptiveString = uncurryThis$k(SymbolPrototype.toString);
    var regexp = /^Symbol\((.*)\)[^)]+$/;
    var replace$7 = uncurryThis$k(''.replace);
    var stringSlice$9 = uncurryThis$k(''.slice);

    defineBuiltInAccessor$3(SymbolPrototype, 'description', {
      configurable: true,
      get: function description() {
        var symbol = thisSymbolValue(this);
        if (hasOwn$8(EmptyStringDescriptionStore, symbol)) return '';
        var string = symbolDescriptiveString(symbol);
        var desc = NATIVE_SYMBOL ? stringSlice$9(string, 7, -1) : replace$7(string, regexp, '$1');
        return desc === '' ? undefined : desc;
      }
    });

    $$K({ global: true, constructor: true, forced: true }, {
      Symbol: SymbolWrapper
    });
  }

  var collection = collection$3;
  var collectionStrong = collectionStrong$2;

  // `Map` constructor
  // https://tc39.es/ecma262/#sec-map-objects
  collection('Map', function (init) {
    return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
  }, collectionStrong);

  var $TypeError$7 = TypeError;
  var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

  var doesNotExceedSafeInteger$3 = function (it) {
    if (it > MAX_SAFE_INTEGER) throw $TypeError$7('Maximum allowed index exceeded');
    return it;
  };

  var $$J = _export;
  var fails$m = fails$I;
  var isArray$5 = isArray$a;
  var isObject$b = isObject$q;
  var toObject$8 = toObject$e;
  var lengthOfArrayLike$6 = lengthOfArrayLike$c;
  var doesNotExceedSafeInteger$2 = doesNotExceedSafeInteger$3;
  var createProperty$2 = createProperty$5;
  var arraySpeciesCreate$2 = arraySpeciesCreate$4;
  var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$5;
  var wellKnownSymbol$8 = wellKnownSymbol$r;
  var V8_VERSION$1 = engineV8Version;

  var IS_CONCAT_SPREADABLE = wellKnownSymbol$8('isConcatSpreadable');

  // We can't use this feature detection in V8 since it causes
  // deoptimization and serious performance degradation
  // https://github.com/zloirock/core-js/issues/679
  var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION$1 >= 51 || !fails$m(function () {
    var array = [];
    array[IS_CONCAT_SPREADABLE] = false;
    return array.concat()[0] !== array;
  });

  var isConcatSpreadable = function (O) {
    if (!isObject$b(O)) return false;
    var spreadable = O[IS_CONCAT_SPREADABLE];
    return spreadable !== undefined ? !!spreadable : isArray$5(O);
  };

  var FORCED$4 = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport$3('concat');

  // `Array.prototype.concat` method
  // https://tc39.es/ecma262/#sec-array.prototype.concat
  // with adding support of @@isConcatSpreadable and @@species
  $$J({ target: 'Array', proto: true, arity: 1, forced: FORCED$4 }, {
    // eslint-disable-next-line no-unused-vars -- required for `.length`
    concat: function concat(arg) {
      var O = toObject$8(this);
      var A = arraySpeciesCreate$2(O, 0);
      var n = 0;
      var i, k, length, len, E;
      for (i = -1, length = arguments.length; i < length; i++) {
        E = i === -1 ? O : arguments[i];
        if (isConcatSpreadable(E)) {
          len = lengthOfArrayLike$6(E);
          doesNotExceedSafeInteger$2(n + len);
          for (k = 0; k < len; k++, n++) if (k in E) createProperty$2(A, n, E[k]);
        } else {
          doesNotExceedSafeInteger$2(n + 1);
          createProperty$2(A, n++, E);
        }
      }
      A.length = n;
      return A;
    }
  });

  var fails$l = fails$I;

  var arrayMethodIsStrict$3 = function (METHOD_NAME, argument) {
    var method = [][METHOD_NAME];
    return !!method && fails$l(function () {
      // eslint-disable-next-line no-useless-call -- required for testing
      method.call(null, argument || function () { return 1; }, 1);
    });
  };

  var $forEach = arrayIteration.forEach;
  var arrayMethodIsStrict$2 = arrayMethodIsStrict$3;

  var STRICT_METHOD$1 = arrayMethodIsStrict$2('forEach');

  // `Array.prototype.forEach` method implementation
  // https://tc39.es/ecma262/#sec-array.prototype.foreach
  var arrayForEach = !STRICT_METHOD$1 ? function forEach(callbackfn /* , thisArg */) {
    return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  // eslint-disable-next-line es/no-array-prototype-foreach -- safe
  } : [].forEach;

  var global$e = global$u;
  var DOMIterables = domIterables;
  var DOMTokenListPrototype = domTokenListPrototype;
  var forEach = arrayForEach;
  var createNonEnumerableProperty$1 = createNonEnumerableProperty$6;

  var handlePrototype = function (CollectionPrototype) {
    // some Chrome versions have non-configurable methods on DOMTokenList
    if (CollectionPrototype && CollectionPrototype.forEach !== forEach) try {
      createNonEnumerableProperty$1(CollectionPrototype, 'forEach', forEach);
    } catch (error) {
      CollectionPrototype.forEach = forEach;
    }
  };

  for (var COLLECTION_NAME in DOMIterables) {
    if (DOMIterables[COLLECTION_NAME]) {
      handlePrototype(global$e[COLLECTION_NAME] && global$e[COLLECTION_NAME].prototype);
    }
  }

  handlePrototype(DOMTokenListPrototype);

  var $$I = _export;
  var $filter = arrayIteration.filter;
  var arrayMethodHasSpeciesSupport$2 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$2('filter');

  // `Array.prototype.filter` method
  // https://tc39.es/ecma262/#sec-array.prototype.filter
  // with adding support of @@species
  $$I({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
    filter: function filter(callbackfn /* , thisArg */) {
      return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var $$H = _export;
  var $map = arrayIteration.map;
  var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('map');

  // `Array.prototype.map` method
  // https://tc39.es/ecma262/#sec-array.prototype.map
  // with adding support of @@species
  $$H({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
    map: function map(callbackfn /* , thisArg */) {
      return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var $$G = _export;
  var fails$k = fails$I;
  var getOwnPropertyNames$1 = objectGetOwnPropertyNamesExternal.f;

  // eslint-disable-next-line es/no-object-getownpropertynames -- required for testing
  var FAILS_ON_PRIMITIVES$3 = fails$k(function () { return !Object.getOwnPropertyNames(1); });

  // `Object.getOwnPropertyNames` method
  // https://tc39.es/ecma262/#sec-object.getownpropertynames
  $$G({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$3 }, {
    getOwnPropertyNames: getOwnPropertyNames$1
  });

  var hasOwn$7 = hasOwnProperty_1;

  var isDataDescriptor$2 = function (descriptor) {
    return descriptor !== undefined && (hasOwn$7(descriptor, 'value') || hasOwn$7(descriptor, 'writable'));
  };

  var $$F = _export;
  var call$g = functionCall;
  var isObject$a = isObject$q;
  var anObject$g = anObject$r;
  var isDataDescriptor$1 = isDataDescriptor$2;
  var getOwnPropertyDescriptorModule$1 = objectGetOwnPropertyDescriptor;
  var getPrototypeOf$1 = objectGetPrototypeOf$1;

  // `Reflect.get` method
  // https://tc39.es/ecma262/#sec-reflect.get
  function get$2(target, propertyKey /* , receiver */) {
    var receiver = arguments.length < 3 ? target : arguments[2];
    var descriptor, prototype;
    if (anObject$g(target) === receiver) return target[propertyKey];
    descriptor = getOwnPropertyDescriptorModule$1.f(target, propertyKey);
    if (descriptor) return isDataDescriptor$1(descriptor)
      ? descriptor.value
      : descriptor.get === undefined ? undefined : call$g(descriptor.get, receiver);
    if (isObject$a(prototype = getPrototypeOf$1(target))) return get$2(prototype, propertyKey, receiver);
  }

  $$F({ target: 'Reflect', stat: true }, {
    get: get$2
  });

  var $$E = _export;
  var global$d = global$u;
  var setToStringTag$3 = setToStringTag$8;

  $$E({ global: true }, { Reflect: {} });

  // Reflect[@@toStringTag] property
  // https://tc39.es/ecma262/#sec-reflect-@@tostringtag
  setToStringTag$3(global$d.Reflect, 'Reflect', true);

  var uncurryThis$j = functionUncurryThis;

  // `thisNumberValue` abstract operation
  // https://tc39.es/ecma262/#sec-thisnumbervalue
  var thisNumberValue$2 = uncurryThis$j(1.0.valueOf);

  // a string of all valid unicode whitespaces
  var whitespaces$2 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
    '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

  var uncurryThis$i = functionUncurryThis;
  var requireObjectCoercible$9 = requireObjectCoercible$d;
  var toString$c = toString$i;
  var whitespaces$1 = whitespaces$2;

  var replace$6 = uncurryThis$i(''.replace);
  var ltrim = RegExp('^[' + whitespaces$1 + ']+');
  var rtrim = RegExp('(^|[^' + whitespaces$1 + '])[' + whitespaces$1 + ']+$');

  // `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
  var createMethod$1 = function (TYPE) {
    return function ($this) {
      var string = toString$c(requireObjectCoercible$9($this));
      if (TYPE & 1) string = replace$6(string, ltrim, '');
      if (TYPE & 2) string = replace$6(string, rtrim, '$1');
      return string;
    };
  };

  var stringTrim = {
    // `String.prototype.{ trimLeft, trimStart }` methods
    // https://tc39.es/ecma262/#sec-string.prototype.trimstart
    start: createMethod$1(1),
    // `String.prototype.{ trimRight, trimEnd }` methods
    // https://tc39.es/ecma262/#sec-string.prototype.trimend
    end: createMethod$1(2),
    // `String.prototype.trim` method
    // https://tc39.es/ecma262/#sec-string.prototype.trim
    trim: createMethod$1(3)
  };

  var $$D = _export;
  var IS_PURE$1 = isPure;
  var DESCRIPTORS$7 = descriptors;
  var global$c = global$u;
  var path = path$2;
  var uncurryThis$h = functionUncurryThis;
  var isForced$1 = isForced_1;
  var hasOwn$6 = hasOwnProperty_1;
  var inheritIfRequired = inheritIfRequired$2;
  var isPrototypeOf$1 = objectIsPrototypeOf;
  var isSymbol$1 = isSymbol$6;
  var toPrimitive = toPrimitive$2;
  var fails$j = fails$I;
  var getOwnPropertyNames = objectGetOwnPropertyNames.f;
  var getOwnPropertyDescriptor$6 = objectGetOwnPropertyDescriptor.f;
  var defineProperty = objectDefineProperty.f;
  var thisNumberValue$1 = thisNumberValue$2;
  var trim = stringTrim.trim;

  var NUMBER = 'Number';
  var NativeNumber = global$c[NUMBER];
  path[NUMBER];
  var NumberPrototype = NativeNumber.prototype;
  var TypeError$4 = global$c.TypeError;
  var stringSlice$8 = uncurryThis$h(''.slice);
  var charCodeAt$1 = uncurryThis$h(''.charCodeAt);

  // `ToNumeric` abstract operation
  // https://tc39.es/ecma262/#sec-tonumeric
  var toNumeric = function (value) {
    var primValue = toPrimitive(value, 'number');
    return typeof primValue == 'bigint' ? primValue : toNumber$3(primValue);
  };

  // `ToNumber` abstract operation
  // https://tc39.es/ecma262/#sec-tonumber
  var toNumber$3 = function (argument) {
    var it = toPrimitive(argument, 'number');
    var first, third, radix, maxCode, digits, length, index, code;
    if (isSymbol$1(it)) throw TypeError$4('Cannot convert a Symbol value to a number');
    if (typeof it == 'string' && it.length > 2) {
      it = trim(it);
      first = charCodeAt$1(it, 0);
      if (first === 43 || first === 45) {
        third = charCodeAt$1(it, 2);
        if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
      } else if (first === 48) {
        switch (charCodeAt$1(it, 1)) {
          case 66: case 98: radix = 2; maxCode = 49; break; // fast equal of /^0b[01]+$/i
          case 79: case 111: radix = 8; maxCode = 55; break; // fast equal of /^0o[0-7]+$/i
          default: return +it;
        }
        digits = stringSlice$8(it, 2);
        length = digits.length;
        for (index = 0; index < length; index++) {
          code = charCodeAt$1(digits, index);
          // parseInt parses a string to a first unavailable symbol
          // but ToNumber should return NaN if a string contains unavailable symbols
          if (code < 48 || code > maxCode) return NaN;
        } return parseInt(digits, radix);
      }
    } return +it;
  };

  var FORCED$3 = isForced$1(NUMBER, !NativeNumber(' 0o1') || !NativeNumber('0b1') || NativeNumber('+0x1'));

  var calledWithNew = function (dummy) {
    // includes check on 1..constructor(foo) case
    return isPrototypeOf$1(NumberPrototype, dummy) && fails$j(function () { thisNumberValue$1(dummy); });
  };

  // `Number` constructor
  // https://tc39.es/ecma262/#sec-number-constructor
  var NumberWrapper = function Number(value) {
    var n = arguments.length < 1 ? 0 : NativeNumber(toNumeric(value));
    return calledWithNew(this) ? inheritIfRequired(Object(n), this, NumberWrapper) : n;
  };

  NumberWrapper.prototype = NumberPrototype;
  if (FORCED$3 && !IS_PURE$1) NumberPrototype.constructor = NumberWrapper;

  $$D({ global: true, constructor: true, wrap: true, forced: FORCED$3 }, {
    Number: NumberWrapper
  });

  // Use `internal/copy-constructor-properties` helper in `core-js@4`
  var copyConstructorProperties = function (target, source) {
    for (var keys = DESCRIPTORS$7 ? getOwnPropertyNames(source) : (
      // ES3:
      'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
      // ES2015 (in case, if modules with ES2015 Number statics required before):
      'EPSILON,MAX_SAFE_INTEGER,MIN_SAFE_INTEGER,isFinite,isInteger,isNaN,isSafeInteger,parseFloat,parseInt,' +
      // ESNext
      'fromString,range'
    ).split(','), j = 0, key; keys.length > j; j++) {
      if (hasOwn$6(source, key = keys[j]) && !hasOwn$6(target, key)) {
        defineProperty(target, key, getOwnPropertyDescriptor$6(source, key));
      }
    }
  };
  if (FORCED$3 || IS_PURE$1) copyConstructorProperties(path[NUMBER], NativeNumber);

  var $$C = _export;
  var call$f = functionCall;
  var anObject$f = anObject$r;
  var isObject$9 = isObject$q;
  var isDataDescriptor = isDataDescriptor$2;
  var fails$i = fails$I;
  var definePropertyModule$1 = objectDefineProperty;
  var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
  var getPrototypeOf = objectGetPrototypeOf$1;
  var createPropertyDescriptor$1 = createPropertyDescriptor$7;

  // `Reflect.set` method
  // https://tc39.es/ecma262/#sec-reflect.set
  function set$4(target, propertyKey, V /* , receiver */) {
    var receiver = arguments.length < 4 ? target : arguments[3];
    var ownDescriptor = getOwnPropertyDescriptorModule.f(anObject$f(target), propertyKey);
    var existingDescriptor, prototype, setter;
    if (!ownDescriptor) {
      if (isObject$9(prototype = getPrototypeOf(target))) {
        return set$4(prototype, propertyKey, V, receiver);
      }
      ownDescriptor = createPropertyDescriptor$1(0);
    }
    if (isDataDescriptor(ownDescriptor)) {
      if (ownDescriptor.writable === false || !isObject$9(receiver)) return false;
      if (existingDescriptor = getOwnPropertyDescriptorModule.f(receiver, propertyKey)) {
        if (existingDescriptor.get || existingDescriptor.set || existingDescriptor.writable === false) return false;
        existingDescriptor.value = V;
        definePropertyModule$1.f(receiver, propertyKey, existingDescriptor);
      } else definePropertyModule$1.f(receiver, propertyKey, createPropertyDescriptor$1(0, V));
    } else {
      setter = ownDescriptor.set;
      if (setter === undefined) return false;
      call$f(setter, receiver, V);
    } return true;
  }

  // MS Edge 17-18 Reflect.set allows setting the property to object
  // with non-writable property on the prototype
  var MS_EDGE_BUG = fails$i(function () {
    var Constructor = function () { /* empty */ };
    var object = definePropertyModule$1.f(new Constructor(), 'a', { configurable: true });
    // eslint-disable-next-line es/no-reflect -- required for testing
    return Reflect.set(Constructor.prototype, 'a', 1, object) !== false;
  });

  $$C({ target: 'Reflect', stat: true, forced: MS_EDGE_BUG }, {
    set: set$4
  });

  var $$B = _export;
  var anObject$e = anObject$r;
  var getOwnPropertyDescriptor$5 = objectGetOwnPropertyDescriptor.f;

  // `Reflect.deleteProperty` method
  // https://tc39.es/ecma262/#sec-reflect.deleteproperty
  $$B({ target: 'Reflect', stat: true }, {
    deleteProperty: function deleteProperty(target, propertyKey) {
      var descriptor = getOwnPropertyDescriptor$5(anObject$e(target), propertyKey);
      return descriptor && !descriptor.configurable ? false : delete target[propertyKey];
    }
  });

  var $$A = _export;

  // `Reflect.has` method
  // https://tc39.es/ecma262/#sec-reflect.has
  $$A({ target: 'Reflect', stat: true }, {
    has: function has(target, propertyKey) {
      return propertyKey in target;
    }
  });

  var $$z = _export;
  var ownKeys$1 = ownKeys$3;

  // `Reflect.ownKeys` method
  // https://tc39.es/ecma262/#sec-reflect.ownkeys
  $$z({ target: 'Reflect', stat: true }, {
    ownKeys: ownKeys$1
  });

  var $$y = _export;
  var anObject$d = anObject$r;
  var objectGetPrototypeOf = objectGetPrototypeOf$1;
  var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter;

  // `Reflect.getPrototypeOf` method
  // https://tc39.es/ecma262/#sec-reflect.getprototypeof
  $$y({ target: 'Reflect', stat: true, sham: !CORRECT_PROTOTYPE_GETTER$1 }, {
    getPrototypeOf: function getPrototypeOf(target) {
      return objectGetPrototypeOf(anObject$d(target));
    }
  });

  var defineWellKnownSymbol = wellKnownSymbolDefine;

  // `Symbol.iterator` well-known symbol
  // https://tc39.es/ecma262/#sec-symbol.iterator
  defineWellKnownSymbol('iterator');

  var $$x = _export;
  var $isExtensible = objectIsExtensible;

  // `Object.isExtensible` method
  // https://tc39.es/ecma262/#sec-object.isextensible
  // eslint-disable-next-line es/no-object-isextensible -- safe
  $$x({ target: 'Object', stat: true, forced: Object.isExtensible !== $isExtensible }, {
    isExtensible: $isExtensible
  });

  var anObject$c = anObject$r;

  // `RegExp.prototype.flags` getter implementation
  // https://tc39.es/ecma262/#sec-get-regexp.prototype.flags
  var regexpFlags$1 = function () {
    var that = anObject$c(this);
    var result = '';
    if (that.hasIndices) result += 'd';
    if (that.global) result += 'g';
    if (that.ignoreCase) result += 'i';
    if (that.multiline) result += 'm';
    if (that.dotAll) result += 's';
    if (that.unicode) result += 'u';
    if (that.unicodeSets) result += 'v';
    if (that.sticky) result += 'y';
    return result;
  };

  var fails$h = fails$I;
  var global$b = global$u;

  // babel-minify and Closure Compiler transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError
  var $RegExp$2 = global$b.RegExp;

  var UNSUPPORTED_Y$2 = fails$h(function () {
    var re = $RegExp$2('a', 'y');
    re.lastIndex = 2;
    return re.exec('abcd') != null;
  });

  // UC Browser bug
  // https://github.com/zloirock/core-js/issues/1008
  var MISSED_STICKY = UNSUPPORTED_Y$2 || fails$h(function () {
    return !$RegExp$2('a', 'y').sticky;
  });

  var BROKEN_CARET = UNSUPPORTED_Y$2 || fails$h(function () {
    // https://bugzilla.mozilla.org/show_bug.cgi?id=773687
    var re = $RegExp$2('^r', 'gy');
    re.lastIndex = 2;
    return re.exec('str') != null;
  });

  var regexpStickyHelpers = {
    BROKEN_CARET: BROKEN_CARET,
    MISSED_STICKY: MISSED_STICKY,
    UNSUPPORTED_Y: UNSUPPORTED_Y$2
  };

  var fails$g = fails$I;
  var global$a = global$u;

  // babel-minify and Closure Compiler transpiles RegExp('.', 's') -> /./s and it causes SyntaxError
  var $RegExp$1 = global$a.RegExp;

  var regexpUnsupportedDotAll = fails$g(function () {
    var re = $RegExp$1('.', 's');
    return !(re.dotAll && re.exec('\n') && re.flags === 's');
  });

  var fails$f = fails$I;
  var global$9 = global$u;

  // babel-minify and Closure Compiler transpiles RegExp('(?<a>b)', 'g') -> /(?<a>b)/g and it causes SyntaxError
  var $RegExp = global$9.RegExp;

  var regexpUnsupportedNcg = fails$f(function () {
    var re = $RegExp('(?<a>b)', 'g');
    return re.exec('b').groups.a !== 'b' ||
      'b'.replace(re, '$<a>c') !== 'bc';
  });

  /* eslint-disable regexp/no-empty-capturing-group, regexp/no-empty-group, regexp/no-lazy-ends -- testing */
  /* eslint-disable regexp/no-useless-quantifier -- testing */
  var call$e = functionCall;
  var uncurryThis$g = functionUncurryThis;
  var toString$b = toString$i;
  var regexpFlags = regexpFlags$1;
  var stickyHelpers$1 = regexpStickyHelpers;
  var shared = shared$7.exports;
  var create$1 = objectCreate;
  var getInternalState = internalState.get;
  var UNSUPPORTED_DOT_ALL = regexpUnsupportedDotAll;
  var UNSUPPORTED_NCG = regexpUnsupportedNcg;

  var nativeReplace = shared('native-string-replace', String.prototype.replace);
  var nativeExec = RegExp.prototype.exec;
  var patchedExec = nativeExec;
  var charAt$4 = uncurryThis$g(''.charAt);
  var indexOf = uncurryThis$g(''.indexOf);
  var replace$5 = uncurryThis$g(''.replace);
  var stringSlice$7 = uncurryThis$g(''.slice);

  var UPDATES_LAST_INDEX_WRONG = (function () {
    var re1 = /a/;
    var re2 = /b*/g;
    call$e(nativeExec, re1, 'a');
    call$e(nativeExec, re2, 'a');
    return re1.lastIndex !== 0 || re2.lastIndex !== 0;
  })();

  var UNSUPPORTED_Y$1 = stickyHelpers$1.BROKEN_CARET;

  // nonparticipating capturing group, copied from es5-shim's String#split patch.
  var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

  var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED || UNSUPPORTED_Y$1 || UNSUPPORTED_DOT_ALL || UNSUPPORTED_NCG;

  if (PATCH) {
    patchedExec = function exec(string) {
      var re = this;
      var state = getInternalState(re);
      var str = toString$b(string);
      var raw = state.raw;
      var result, reCopy, lastIndex, match, i, object, group;

      if (raw) {
        raw.lastIndex = re.lastIndex;
        result = call$e(patchedExec, raw, str);
        re.lastIndex = raw.lastIndex;
        return result;
      }

      var groups = state.groups;
      var sticky = UNSUPPORTED_Y$1 && re.sticky;
      var flags = call$e(regexpFlags, re);
      var source = re.source;
      var charsAdded = 0;
      var strCopy = str;

      if (sticky) {
        flags = replace$5(flags, 'y', '');
        if (indexOf(flags, 'g') === -1) {
          flags += 'g';
        }

        strCopy = stringSlice$7(str, re.lastIndex);
        // Support anchored sticky behavior.
        if (re.lastIndex > 0 && (!re.multiline || re.multiline && charAt$4(str, re.lastIndex - 1) !== '\n')) {
          source = '(?: ' + source + ')';
          strCopy = ' ' + strCopy;
          charsAdded++;
        }
        // ^(? + rx + ) is needed, in combination with some str slicing, to
        // simulate the 'y' flag.
        reCopy = new RegExp('^(?:' + source + ')', flags);
      }

      if (NPCG_INCLUDED) {
        reCopy = new RegExp('^' + source + '$(?!\\s)', flags);
      }
      if (UPDATES_LAST_INDEX_WRONG) lastIndex = re.lastIndex;

      match = call$e(nativeExec, sticky ? reCopy : re, strCopy);

      if (sticky) {
        if (match) {
          match.input = stringSlice$7(match.input, charsAdded);
          match[0] = stringSlice$7(match[0], charsAdded);
          match.index = re.lastIndex;
          re.lastIndex += match[0].length;
        } else re.lastIndex = 0;
      } else if (UPDATES_LAST_INDEX_WRONG && match) {
        re.lastIndex = re.global ? match.index + match[0].length : lastIndex;
      }
      if (NPCG_INCLUDED && match && match.length > 1) {
        // Fix browsers whose `exec` methods don't consistently return `undefined`
        // for NPCG, like IE8. NOTE: This doesn't work for /(.?)?/
        call$e(nativeReplace, match[0], reCopy, function () {
          for (i = 1; i < arguments.length - 2; i++) {
            if (arguments[i] === undefined) match[i] = undefined;
          }
        });
      }

      if (match && groups) {
        match.groups = object = create$1(null);
        for (i = 0; i < groups.length; i++) {
          group = groups[i];
          object[group[0]] = match[group[1]];
        }
      }

      return match;
    };
  }

  var regexpExec$3 = patchedExec;

  var $$w = _export;
  var exec$3 = regexpExec$3;

  // `RegExp.prototype.exec` method
  // https://tc39.es/ecma262/#sec-regexp.prototype.exec
  $$w({ target: 'RegExp', proto: true, forced: /./.exec !== exec$3 }, {
    exec: exec$3
  });

  // TODO: Remove from `core-js@4` since it's moved to entry points

  var uncurryThis$f = functionUncurryThisClause;
  var defineBuiltIn$5 = defineBuiltIn$e;
  var regexpExec$2 = regexpExec$3;
  var fails$e = fails$I;
  var wellKnownSymbol$7 = wellKnownSymbol$r;
  var createNonEnumerableProperty = createNonEnumerableProperty$6;

  var SPECIES$2 = wellKnownSymbol$7('species');
  var RegExpPrototype$2 = RegExp.prototype;

  var fixRegexpWellKnownSymbolLogic = function (KEY, exec, FORCED, SHAM) {
    var SYMBOL = wellKnownSymbol$7(KEY);

    var DELEGATES_TO_SYMBOL = !fails$e(function () {
      // String methods call symbol-named RegEp methods
      var O = {};
      O[SYMBOL] = function () { return 7; };
      return ''[KEY](O) != 7;
    });

    var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL && !fails$e(function () {
      // Symbol-named RegExp methods call .exec
      var execCalled = false;
      var re = /a/;

      if (KEY === 'split') {
        // We can't use real regex here since it causes deoptimization
        // and serious performance degradation in V8
        // https://github.com/zloirock/core-js/issues/306
        re = {};
        // RegExp[@@split] doesn't call the regex's exec method, but first creates
        // a new one. We need to return the patched regex when creating the new one.
        re.constructor = {};
        re.constructor[SPECIES$2] = function () { return re; };
        re.flags = '';
        re[SYMBOL] = /./[SYMBOL];
      }

      re.exec = function () { execCalled = true; return null; };

      re[SYMBOL]('');
      return !execCalled;
    });

    if (
      !DELEGATES_TO_SYMBOL ||
      !DELEGATES_TO_EXEC ||
      FORCED
    ) {
      var uncurriedNativeRegExpMethod = uncurryThis$f(/./[SYMBOL]);
      var methods = exec(SYMBOL, ''[KEY], function (nativeMethod, regexp, str, arg2, forceStringMethod) {
        var uncurriedNativeMethod = uncurryThis$f(nativeMethod);
        var $exec = regexp.exec;
        if ($exec === regexpExec$2 || $exec === RegExpPrototype$2.exec) {
          if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
            // The native String method already delegates to @@method (this
            // polyfilled function), leasing to infinite recursion.
            // We avoid it by directly calling the native @@method method.
            return { done: true, value: uncurriedNativeRegExpMethod(regexp, str, arg2) };
          }
          return { done: true, value: uncurriedNativeMethod(str, regexp, arg2) };
        }
        return { done: false };
      });

      defineBuiltIn$5(String.prototype, KEY, methods[0]);
      defineBuiltIn$5(RegExpPrototype$2, SYMBOL, methods[1]);
    }

    if (SHAM) createNonEnumerableProperty(RegExpPrototype$2[SYMBOL], 'sham', true);
  };

  var isObject$8 = isObject$q;
  var classof$3 = classofRaw$2;
  var wellKnownSymbol$6 = wellKnownSymbol$r;

  var MATCH$1 = wellKnownSymbol$6('match');

  // `IsRegExp` abstract operation
  // https://tc39.es/ecma262/#sec-isregexp
  var isRegexp = function (it) {
    var isRegExp;
    return isObject$8(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof$3(it) == 'RegExp');
  };

  var isConstructor$1 = isConstructor$4;
  var tryToString$1 = tryToString$6;

  var $TypeError$6 = TypeError;

  // `Assert: IsConstructor(argument) is true`
  var aConstructor$1 = function (argument) {
    if (isConstructor$1(argument)) return argument;
    throw $TypeError$6(tryToString$1(argument) + ' is not a constructor');
  };

  var anObject$b = anObject$r;
  var aConstructor = aConstructor$1;
  var isNullOrUndefined$4 = isNullOrUndefined$b;
  var wellKnownSymbol$5 = wellKnownSymbol$r;

  var SPECIES$1 = wellKnownSymbol$5('species');

  // `SpeciesConstructor` abstract operation
  // https://tc39.es/ecma262/#sec-speciesconstructor
  var speciesConstructor$2 = function (O, defaultConstructor) {
    var C = anObject$b(O).constructor;
    var S;
    return C === undefined || isNullOrUndefined$4(S = anObject$b(C)[SPECIES$1]) ? defaultConstructor : aConstructor(S);
  };

  var charAt$3 = stringMultibyte.charAt;

  // `AdvanceStringIndex` abstract operation
  // https://tc39.es/ecma262/#sec-advancestringindex
  var advanceStringIndex$3 = function (S, index, unicode) {
    return index + (unicode ? charAt$3(S, index).length : 1);
  };

  var call$d = functionCall;
  var anObject$a = anObject$r;
  var isCallable$7 = isCallable$t;
  var classof$2 = classofRaw$2;
  var regexpExec$1 = regexpExec$3;

  var $TypeError$5 = TypeError;

  // `RegExpExec` abstract operation
  // https://tc39.es/ecma262/#sec-regexpexec
  var regexpExecAbstract = function (R, S) {
    var exec = R.exec;
    if (isCallable$7(exec)) {
      var result = call$d(exec, R, S);
      if (result !== null) anObject$a(result);
      return result;
    }
    if (classof$2(R) === 'RegExp') return call$d(regexpExec$1, R, S);
    throw $TypeError$5('RegExp#exec called on incompatible receiver');
  };

  var apply$2 = functionApply;
  var call$c = functionCall;
  var uncurryThis$e = functionUncurryThis;
  var fixRegExpWellKnownSymbolLogic$3 = fixRegexpWellKnownSymbolLogic;
  var anObject$9 = anObject$r;
  var isNullOrUndefined$3 = isNullOrUndefined$b;
  var isRegExp$1 = isRegexp;
  var requireObjectCoercible$8 = requireObjectCoercible$d;
  var speciesConstructor$1 = speciesConstructor$2;
  var advanceStringIndex$2 = advanceStringIndex$3;
  var toLength$4 = toLength$6;
  var toString$a = toString$i;
  var getMethod$3 = getMethod$7;
  var arraySlice$3 = arraySliceSimple;
  var callRegExpExec = regexpExecAbstract;
  var regexpExec = regexpExec$3;
  var stickyHelpers = regexpStickyHelpers;
  var fails$d = fails$I;

  var UNSUPPORTED_Y = stickyHelpers.UNSUPPORTED_Y;
  var MAX_UINT32 = 0xFFFFFFFF;
  var min$4 = Math.min;
  var $push = [].push;
  var exec$2 = uncurryThis$e(/./.exec);
  var push$6 = uncurryThis$e($push);
  var stringSlice$6 = uncurryThis$e(''.slice);

  // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
  // Weex JS has frozen built-in prototypes, so use try / catch wrapper
  var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = !fails$d(function () {
    // eslint-disable-next-line regexp/no-empty-group -- required for testing
    var re = /(?:)/;
    var originalExec = re.exec;
    re.exec = function () { return originalExec.apply(this, arguments); };
    var result = 'ab'.split(re);
    return result.length !== 2 || result[0] !== 'a' || result[1] !== 'b';
  });

  // @@split logic
  fixRegExpWellKnownSymbolLogic$3('split', function (SPLIT, nativeSplit, maybeCallNative) {
    var internalSplit;
    if (
      'abbc'.split(/(b)*/)[1] == 'c' ||
      // eslint-disable-next-line regexp/no-empty-group -- required for testing
      'test'.split(/(?:)/, -1).length != 4 ||
      'ab'.split(/(?:ab)*/).length != 2 ||
      '.'.split(/(.?)(.?)/).length != 4 ||
      // eslint-disable-next-line regexp/no-empty-capturing-group, regexp/no-empty-group -- required for testing
      '.'.split(/()()/).length > 1 ||
      ''.split(/.?/).length
    ) {
      // based on es5-shim implementation, need to rework it
      internalSplit = function (separator, limit) {
        var string = toString$a(requireObjectCoercible$8(this));
        var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
        if (lim === 0) return [];
        if (separator === undefined) return [string];
        // If `separator` is not a regex, use native split
        if (!isRegExp$1(separator)) {
          return call$c(nativeSplit, string, separator, lim);
        }
        var output = [];
        var flags = (separator.ignoreCase ? 'i' : '') +
                    (separator.multiline ? 'm' : '') +
                    (separator.unicode ? 'u' : '') +
                    (separator.sticky ? 'y' : '');
        var lastLastIndex = 0;
        // Make `global` and avoid `lastIndex` issues by working with a copy
        var separatorCopy = new RegExp(separator.source, flags + 'g');
        var match, lastIndex, lastLength;
        while (match = call$c(regexpExec, separatorCopy, string)) {
          lastIndex = separatorCopy.lastIndex;
          if (lastIndex > lastLastIndex) {
            push$6(output, stringSlice$6(string, lastLastIndex, match.index));
            if (match.length > 1 && match.index < string.length) apply$2($push, output, arraySlice$3(match, 1));
            lastLength = match[0].length;
            lastLastIndex = lastIndex;
            if (output.length >= lim) break;
          }
          if (separatorCopy.lastIndex === match.index) separatorCopy.lastIndex++; // Avoid an infinite loop
        }
        if (lastLastIndex === string.length) {
          if (lastLength || !exec$2(separatorCopy, '')) push$6(output, '');
        } else push$6(output, stringSlice$6(string, lastLastIndex));
        return output.length > lim ? arraySlice$3(output, 0, lim) : output;
      };
    // Chakra, V8
    } else if ('0'.split(undefined, 0).length) {
      internalSplit = function (separator, limit) {
        return separator === undefined && limit === 0 ? [] : call$c(nativeSplit, this, separator, limit);
      };
    } else internalSplit = nativeSplit;

    return [
      // `String.prototype.split` method
      // https://tc39.es/ecma262/#sec-string.prototype.split
      function split(separator, limit) {
        var O = requireObjectCoercible$8(this);
        var splitter = isNullOrUndefined$3(separator) ? undefined : getMethod$3(separator, SPLIT);
        return splitter
          ? call$c(splitter, separator, O, limit)
          : call$c(internalSplit, toString$a(O), separator, limit);
      },
      // `RegExp.prototype[@@split]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@split
      //
      // NOTE: This cannot be properly polyfilled in engines that don't support
      // the 'y' flag.
      function (string, limit) {
        var rx = anObject$9(this);
        var S = toString$a(string);
        var res = maybeCallNative(internalSplit, rx, S, limit, internalSplit !== nativeSplit);

        if (res.done) return res.value;

        var C = speciesConstructor$1(rx, RegExp);

        var unicodeMatching = rx.unicode;
        var flags = (rx.ignoreCase ? 'i' : '') +
                    (rx.multiline ? 'm' : '') +
                    (rx.unicode ? 'u' : '') +
                    (UNSUPPORTED_Y ? 'g' : 'y');

        // ^(? + rx + ) is needed, in combination with some S slicing, to
        // simulate the 'y' flag.
        var splitter = new C(UNSUPPORTED_Y ? '^(?:' + rx.source + ')' : rx, flags);
        var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
        if (lim === 0) return [];
        if (S.length === 0) return callRegExpExec(splitter, S) === null ? [S] : [];
        var p = 0;
        var q = 0;
        var A = [];
        while (q < S.length) {
          splitter.lastIndex = UNSUPPORTED_Y ? 0 : q;
          var z = callRegExpExec(splitter, UNSUPPORTED_Y ? stringSlice$6(S, q) : S);
          var e;
          if (
            z === null ||
            (e = min$4(toLength$4(splitter.lastIndex + (UNSUPPORTED_Y ? q : 0)), S.length)) === p
          ) {
            q = advanceStringIndex$2(S, q, unicodeMatching);
          } else {
            push$6(A, stringSlice$6(S, p, q));
            if (A.length === lim) return A;
            for (var i = 1; i <= z.length - 1; i++) {
              push$6(A, z[i]);
              if (A.length === lim) return A;
            }
            q = p = e;
          }
        }
        push$6(A, stringSlice$6(S, p));
        return A;
      }
    ];
  }, !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC, UNSUPPORTED_Y);

  var uncurryThis$d = functionUncurryThis;
  var toObject$7 = toObject$e;

  var floor$4 = Math.floor;
  var charAt$2 = uncurryThis$d(''.charAt);
  var replace$4 = uncurryThis$d(''.replace);
  var stringSlice$5 = uncurryThis$d(''.slice);
  // eslint-disable-next-line redos/no-vulnerable -- safe
  var SUBSTITUTION_SYMBOLS = /\$([$&'`]|\d{1,2}|<[^>]*>)/g;
  var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&'`]|\d{1,2})/g;

  // `GetSubstitution` abstract operation
  // https://tc39.es/ecma262/#sec-getsubstitution
  var getSubstitution$1 = function (matched, str, position, captures, namedCaptures, replacement) {
    var tailPos = position + matched.length;
    var m = captures.length;
    var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;
    if (namedCaptures !== undefined) {
      namedCaptures = toObject$7(namedCaptures);
      symbols = SUBSTITUTION_SYMBOLS;
    }
    return replace$4(replacement, symbols, function (match, ch) {
      var capture;
      switch (charAt$2(ch, 0)) {
        case '$': return '$';
        case '&': return matched;
        case '`': return stringSlice$5(str, 0, position);
        case "'": return stringSlice$5(str, tailPos);
        case '<':
          capture = namedCaptures[stringSlice$5(ch, 1, -1)];
          break;
        default: // \d\d?
          var n = +ch;
          if (n === 0) return match;
          if (n > m) {
            var f = floor$4(n / 10);
            if (f === 0) return match;
            if (f <= m) return captures[f - 1] === undefined ? charAt$2(ch, 1) : captures[f - 1] + charAt$2(ch, 1);
            return match;
          }
          capture = captures[n - 1];
      }
      return capture === undefined ? '' : capture;
    });
  };

  var apply$1 = functionApply;
  var call$b = functionCall;
  var uncurryThis$c = functionUncurryThis;
  var fixRegExpWellKnownSymbolLogic$2 = fixRegexpWellKnownSymbolLogic;
  var fails$c = fails$I;
  var anObject$8 = anObject$r;
  var isCallable$6 = isCallable$t;
  var isNullOrUndefined$2 = isNullOrUndefined$b;
  var toIntegerOrInfinity$4 = toIntegerOrInfinity$8;
  var toLength$3 = toLength$6;
  var toString$9 = toString$i;
  var requireObjectCoercible$7 = requireObjectCoercible$d;
  var advanceStringIndex$1 = advanceStringIndex$3;
  var getMethod$2 = getMethod$7;
  var getSubstitution = getSubstitution$1;
  var regExpExec$3 = regexpExecAbstract;
  var wellKnownSymbol$4 = wellKnownSymbol$r;

  var REPLACE = wellKnownSymbol$4('replace');
  var max$1 = Math.max;
  var min$3 = Math.min;
  var concat = uncurryThis$c([].concat);
  var push$5 = uncurryThis$c([].push);
  var stringIndexOf$1 = uncurryThis$c(''.indexOf);
  var stringSlice$4 = uncurryThis$c(''.slice);

  var maybeToString = function (it) {
    return it === undefined ? it : String(it);
  };

  // IE <= 11 replaces $0 with the whole match, as if it was $&
  // https://stackoverflow.com/questions/6024666/getting-ie-to-replace-a-regex-with-the-literal-string-0
  var REPLACE_KEEPS_$0 = (function () {
    // eslint-disable-next-line regexp/prefer-escape-replacement-dollar-char -- required for testing
    return 'a'.replace(/./, '$0') === '$0';
  })();

  // Safari <= 13.0.3(?) substitutes nth capture where n>m with an empty string
  var REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE = (function () {
    if (/./[REPLACE]) {
      return /./[REPLACE]('a', '$0') === '';
    }
    return false;
  })();

  var REPLACE_SUPPORTS_NAMED_GROUPS = !fails$c(function () {
    var re = /./;
    re.exec = function () {
      var result = [];
      result.groups = { a: '7' };
      return result;
    };
    // eslint-disable-next-line regexp/no-useless-dollar-replacements -- false positive
    return ''.replace(re, '$<a>') !== '7';
  });

  // @@replace logic
  fixRegExpWellKnownSymbolLogic$2('replace', function (_, nativeReplace, maybeCallNative) {
    var UNSAFE_SUBSTITUTE = REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE ? '$' : '$0';

    return [
      // `String.prototype.replace` method
      // https://tc39.es/ecma262/#sec-string.prototype.replace
      function replace(searchValue, replaceValue) {
        var O = requireObjectCoercible$7(this);
        var replacer = isNullOrUndefined$2(searchValue) ? undefined : getMethod$2(searchValue, REPLACE);
        return replacer
          ? call$b(replacer, searchValue, O, replaceValue)
          : call$b(nativeReplace, toString$9(O), searchValue, replaceValue);
      },
      // `RegExp.prototype[@@replace]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@replace
      function (string, replaceValue) {
        var rx = anObject$8(this);
        var S = toString$9(string);

        if (
          typeof replaceValue == 'string' &&
          stringIndexOf$1(replaceValue, UNSAFE_SUBSTITUTE) === -1 &&
          stringIndexOf$1(replaceValue, '$<') === -1
        ) {
          var res = maybeCallNative(nativeReplace, rx, S, replaceValue);
          if (res.done) return res.value;
        }

        var functionalReplace = isCallable$6(replaceValue);
        if (!functionalReplace) replaceValue = toString$9(replaceValue);

        var global = rx.global;
        if (global) {
          var fullUnicode = rx.unicode;
          rx.lastIndex = 0;
        }
        var results = [];
        while (true) {
          var result = regExpExec$3(rx, S);
          if (result === null) break;

          push$5(results, result);
          if (!global) break;

          var matchStr = toString$9(result[0]);
          if (matchStr === '') rx.lastIndex = advanceStringIndex$1(S, toLength$3(rx.lastIndex), fullUnicode);
        }

        var accumulatedResult = '';
        var nextSourcePosition = 0;
        for (var i = 0; i < results.length; i++) {
          result = results[i];

          var matched = toString$9(result[0]);
          var position = max$1(min$3(toIntegerOrInfinity$4(result.index), S.length), 0);
          var captures = [];
          // NOTE: This is equivalent to
          //   captures = result.slice(1).map(maybeToString)
          // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
          // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
          // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.
          for (var j = 1; j < result.length; j++) push$5(captures, maybeToString(result[j]));
          var namedCaptures = result.groups;
          if (functionalReplace) {
            var replacerArgs = concat([matched], captures, position, S);
            if (namedCaptures !== undefined) push$5(replacerArgs, namedCaptures);
            var replacement = toString$9(apply$1(replaceValue, undefined, replacerArgs));
          } else {
            replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
          }
          if (position >= nextSourcePosition) {
            accumulatedResult += stringSlice$4(S, nextSourcePosition, position) + replacement;
            nextSourcePosition = position + matched.length;
          }
        }
        return accumulatedResult + stringSlice$4(S, nextSourcePosition);
      }
    ];
  }, !REPLACE_SUPPORTS_NAMED_GROUPS || !REPLACE_KEEPS_$0 || REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE);

  var PROPER_FUNCTION_NAME$1 = functionName.PROPER;
  var fails$b = fails$I;
  var whitespaces = whitespaces$2;

  var non = '\u200B\u0085\u180E';

  // check that a method works with the correct list
  // of whitespaces and has a correct name
  var stringTrimForced = function (METHOD_NAME) {
    return fails$b(function () {
      return !!whitespaces[METHOD_NAME]()
        || non[METHOD_NAME]() !== non
        || (PROPER_FUNCTION_NAME$1 && whitespaces[METHOD_NAME].name !== METHOD_NAME);
    });
  };

  var $$v = _export;
  var $trim = stringTrim.trim;
  var forcedStringTrimMethod = stringTrimForced;

  // `String.prototype.trim` method
  // https://tc39.es/ecma262/#sec-string.prototype.trim
  $$v({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
    trim: function trim() {
      return $trim(this);
    }
  });

  var call$a = functionCall;
  var hasOwn$5 = hasOwnProperty_1;
  var isPrototypeOf = objectIsPrototypeOf;
  var regExpFlags = regexpFlags$1;

  var RegExpPrototype$1 = RegExp.prototype;

  var regexpGetFlags = function (R) {
    var flags = R.flags;
    return flags === undefined && !('flags' in RegExpPrototype$1) && !hasOwn$5(R, 'flags') && isPrototypeOf(RegExpPrototype$1, R)
      ? call$a(regExpFlags, R) : flags;
  };

  var PROPER_FUNCTION_NAME = functionName.PROPER;
  var defineBuiltIn$4 = defineBuiltIn$e;
  var anObject$7 = anObject$r;
  var $toString$2 = toString$i;
  var fails$a = fails$I;
  var getRegExpFlags = regexpGetFlags;

  var TO_STRING = 'toString';
  var RegExpPrototype = RegExp.prototype;
  var nativeToString = RegExpPrototype[TO_STRING];

  var NOT_GENERIC = fails$a(function () { return nativeToString.call({ source: 'a', flags: 'b' }) != '/a/b'; });
  // FF44- RegExp#toString has a wrong name
  var INCORRECT_NAME = PROPER_FUNCTION_NAME && nativeToString.name != TO_STRING;

  // `RegExp.prototype.toString` method
  // https://tc39.es/ecma262/#sec-regexp.prototype.tostring
  if (NOT_GENERIC || INCORRECT_NAME) {
    defineBuiltIn$4(RegExp.prototype, TO_STRING, function toString() {
      var R = anObject$7(this);
      var pattern = $toString$2(R.source);
      var flags = $toString$2(getRegExpFlags(R));
      return '/' + pattern + '/' + flags;
    }, { unsafe: true });
  }

  // TODO: Remove from `core-js@4` since it's moved to entry points

  var $$u = _export;
  var call$9 = functionCall;
  var isCallable$5 = isCallable$t;
  var anObject$6 = anObject$r;
  var toString$8 = toString$i;

  var DELEGATES_TO_EXEC = function () {
    var execCalled = false;
    var re = /[ac]/;
    re.exec = function () {
      execCalled = true;
      return /./.exec.apply(this, arguments);
    };
    return re.test('abc') === true && execCalled;
  }();

  var nativeTest = /./.test;

  // `RegExp.prototype.test` method
  // https://tc39.es/ecma262/#sec-regexp.prototype.test
  $$u({ target: 'RegExp', proto: true, forced: !DELEGATES_TO_EXEC }, {
    test: function (S) {
      var R = anObject$6(this);
      var string = toString$8(S);
      var exec = R.exec;
      if (!isCallable$5(exec)) return call$9(nativeTest, R, string);
      var result = call$9(exec, R, string);
      if (result === null) return false;
      anObject$6(result);
      return true;
    }
  });

  var isRegExp = isRegexp;

  var $TypeError$4 = TypeError;

  var notARegexp = function (it) {
    if (isRegExp(it)) {
      throw $TypeError$4("The method doesn't accept regular expressions");
    } return it;
  };

  var wellKnownSymbol$3 = wellKnownSymbol$r;

  var MATCH = wellKnownSymbol$3('match');

  var correctIsRegexpLogic = function (METHOD_NAME) {
    var regexp = /./;
    try {
      '/./'[METHOD_NAME](regexp);
    } catch (error1) {
      try {
        regexp[MATCH] = false;
        return '/./'[METHOD_NAME](regexp);
      } catch (error2) { /* empty */ }
    } return false;
  };

  var $$t = _export;
  var uncurryThis$b = functionUncurryThisClause;
  var getOwnPropertyDescriptor$4 = objectGetOwnPropertyDescriptor.f;
  var toLength$2 = toLength$6;
  var toString$7 = toString$i;
  var notARegExp$2 = notARegexp;
  var requireObjectCoercible$6 = requireObjectCoercible$d;
  var correctIsRegExpLogic$2 = correctIsRegexpLogic;

  // eslint-disable-next-line es/no-string-prototype-startswith -- safe
  var nativeStartsWith = uncurryThis$b(''.startsWith);
  var stringSlice$3 = uncurryThis$b(''.slice);
  var min$2 = Math.min;

  var CORRECT_IS_REGEXP_LOGIC$1 = correctIsRegExpLogic$2('startsWith');
  // https://github.com/zloirock/core-js/pull/702
  var MDN_POLYFILL_BUG$1 = !CORRECT_IS_REGEXP_LOGIC$1 && !!function () {
    var descriptor = getOwnPropertyDescriptor$4(String.prototype, 'startsWith');
    return descriptor && !descriptor.writable;
  }();

  // `String.prototype.startsWith` method
  // https://tc39.es/ecma262/#sec-string.prototype.startswith
  $$t({ target: 'String', proto: true, forced: !MDN_POLYFILL_BUG$1 && !CORRECT_IS_REGEXP_LOGIC$1 }, {
    startsWith: function startsWith(searchString /* , position = 0 */) {
      var that = toString$7(requireObjectCoercible$6(this));
      notARegExp$2(searchString);
      var index = toLength$2(min$2(arguments.length > 1 ? arguments[1] : undefined, that.length));
      var search = toString$7(searchString);
      return nativeStartsWith
        ? nativeStartsWith(that, search, index)
        : stringSlice$3(that, index, index + search.length) === search;
    }
  });

  var DESCRIPTORS$6 = descriptors;
  var isArray$4 = isArray$a;

  var $TypeError$3 = TypeError;
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getOwnPropertyDescriptor$3 = Object.getOwnPropertyDescriptor;

  // Safari < 13 does not throw an error in this case
  var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$6 && !function () {
    // makes no sense without proper strict mode support
    if (this !== undefined) return true;
    try {
      // eslint-disable-next-line es/no-object-defineproperty -- safe
      Object.defineProperty([], 'length', { writable: false }).length = 1;
    } catch (error) {
      return error instanceof TypeError;
    }
  }();

  var arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
    if (isArray$4(O) && !getOwnPropertyDescriptor$3(O, 'length').writable) {
      throw $TypeError$3('Cannot set read only .length');
    } return O.length = length;
  } : function (O, length) {
    return O.length = length;
  };

  var tryToString = tryToString$6;

  var $TypeError$2 = TypeError;

  var deletePropertyOrThrow$2 = function (O, P) {
    if (!delete O[P]) throw $TypeError$2('Cannot delete property ' + tryToString(P) + ' of ' + tryToString(O));
  };

  var $$s = _export;
  var toObject$6 = toObject$e;
  var toAbsoluteIndex$1 = toAbsoluteIndex$5;
  var toIntegerOrInfinity$3 = toIntegerOrInfinity$8;
  var lengthOfArrayLike$5 = lengthOfArrayLike$c;
  var setArrayLength = arraySetLength;
  var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$3;
  var arraySpeciesCreate$1 = arraySpeciesCreate$4;
  var createProperty$1 = createProperty$5;
  var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
  var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');

  var max = Math.max;
  var min$1 = Math.min;

  // `Array.prototype.splice` method
  // https://tc39.es/ecma262/#sec-array.prototype.splice
  // with adding support of @@species
  $$s({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
    splice: function splice(start, deleteCount /* , ...items */) {
      var O = toObject$6(this);
      var len = lengthOfArrayLike$5(O);
      var actualStart = toAbsoluteIndex$1(start, len);
      var argumentsLength = arguments.length;
      var insertCount, actualDeleteCount, A, k, from, to;
      if (argumentsLength === 0) {
        insertCount = actualDeleteCount = 0;
      } else if (argumentsLength === 1) {
        insertCount = 0;
        actualDeleteCount = len - actualStart;
      } else {
        insertCount = argumentsLength - 2;
        actualDeleteCount = min$1(max(toIntegerOrInfinity$3(deleteCount), 0), len - actualStart);
      }
      doesNotExceedSafeInteger$1(len + insertCount - actualDeleteCount);
      A = arraySpeciesCreate$1(O, actualDeleteCount);
      for (k = 0; k < actualDeleteCount; k++) {
        from = actualStart + k;
        if (from in O) createProperty$1(A, k, O[from]);
      }
      A.length = actualDeleteCount;
      if (insertCount < actualDeleteCount) {
        for (k = actualStart; k < len - actualDeleteCount; k++) {
          from = k + actualDeleteCount;
          to = k + insertCount;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow$1(O, to);
        }
        for (k = len; k > len - actualDeleteCount + insertCount; k--) deletePropertyOrThrow$1(O, k - 1);
      } else if (insertCount > actualDeleteCount) {
        for (k = len - actualDeleteCount; k > actualStart; k--) {
          from = k + actualDeleteCount - 1;
          to = k + insertCount - 1;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow$1(O, to);
        }
      }
      for (k = 0; k < insertCount; k++) {
        O[k + actualStart] = arguments[k + 2];
      }
      setArrayLength(O, len - actualDeleteCount + insertCount);
      return A;
    }
  });

  var $$r = _export;
  var global$8 = global$u;

  // `globalThis` object
  // https://tc39.es/ecma262/#sec-globalthis
  $$r({ global: true, forced: global$8.globalThis !== global$8 }, {
    globalThis: global$8
  });

  var classof$1 = classofRaw$2;

  var engineIsNode = typeof process != 'undefined' && classof$1(process) == 'process';

  var $TypeError$1 = TypeError;

  var validateArgumentsLength$3 = function (passed, required) {
    if (passed < required) throw $TypeError$1('Not enough arguments');
    return passed;
  };

  var userAgent$4 = engineUserAgent;

  // eslint-disable-next-line redos/no-vulnerable -- safe
  var engineIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent$4);

  var global$7 = global$u;
  var apply = functionApply;
  var bind$6 = functionBindContext;
  var isCallable$4 = isCallable$t;
  var hasOwn$4 = hasOwnProperty_1;
  var fails$9 = fails$I;
  var html = html$2;
  var arraySlice$2 = arraySlice$6;
  var createElement = documentCreateElement$2;
  var validateArgumentsLength$2 = validateArgumentsLength$3;
  var IS_IOS$1 = engineIsIos;
  var IS_NODE$3 = engineIsNode;

  var set$3 = global$7.setImmediate;
  var clear$1 = global$7.clearImmediate;
  var process$3 = global$7.process;
  var Dispatch = global$7.Dispatch;
  var Function$1 = global$7.Function;
  var MessageChannel = global$7.MessageChannel;
  var String$1 = global$7.String;
  var counter = 0;
  var queue$3 = {};
  var ONREADYSTATECHANGE = 'onreadystatechange';
  var $location, defer, channel, port;

  fails$9(function () {
    // Deno throws a ReferenceError on `location` access without `--location` flag
    $location = global$7.location;
  });

  var run = function (id) {
    if (hasOwn$4(queue$3, id)) {
      var fn = queue$3[id];
      delete queue$3[id];
      fn();
    }
  };

  var runner = function (id) {
    return function () {
      run(id);
    };
  };

  var eventListener = function (event) {
    run(event.data);
  };

  var globalPostMessageDefer = function (id) {
    // old engines have not location.origin
    global$7.postMessage(String$1(id), $location.protocol + '//' + $location.host);
  };

  // Node.js 0.9+ & IE10+ has setImmediate, otherwise:
  if (!set$3 || !clear$1) {
    set$3 = function setImmediate(handler) {
      validateArgumentsLength$2(arguments.length, 1);
      var fn = isCallable$4(handler) ? handler : Function$1(handler);
      var args = arraySlice$2(arguments, 1);
      queue$3[++counter] = function () {
        apply(fn, undefined, args);
      };
      defer(counter);
      return counter;
    };
    clear$1 = function clearImmediate(id) {
      delete queue$3[id];
    };
    // Node.js 0.8-
    if (IS_NODE$3) {
      defer = function (id) {
        process$3.nextTick(runner(id));
      };
    // Sphere (JS game engine) Dispatch API
    } else if (Dispatch && Dispatch.now) {
      defer = function (id) {
        Dispatch.now(runner(id));
      };
    // Browsers with MessageChannel, includes WebWorkers
    // except iOS - https://github.com/zloirock/core-js/issues/624
    } else if (MessageChannel && !IS_IOS$1) {
      channel = new MessageChannel();
      port = channel.port2;
      channel.port1.onmessage = eventListener;
      defer = bind$6(port.postMessage, port);
    // Browsers with postMessage, skip WebWorkers
    // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
    } else if (
      global$7.addEventListener &&
      isCallable$4(global$7.postMessage) &&
      !global$7.importScripts &&
      $location && $location.protocol !== 'file:' &&
      !fails$9(globalPostMessageDefer)
    ) {
      defer = globalPostMessageDefer;
      global$7.addEventListener('message', eventListener, false);
    // IE8-
    } else if (ONREADYSTATECHANGE in createElement('script')) {
      defer = function (id) {
        html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
          html.removeChild(this);
          run(id);
        };
      };
    // Rest old browsers
    } else {
      defer = function (id) {
        setTimeout(runner(id), 0);
      };
    }
  }

  var task$1 = {
    set: set$3,
    clear: clear$1
  };

  var Queue$2 = function () {
    this.head = null;
    this.tail = null;
  };

  Queue$2.prototype = {
    add: function (item) {
      var entry = { item: item, next: null };
      var tail = this.tail;
      if (tail) tail.next = entry;
      else this.head = entry;
      this.tail = entry;
    },
    get: function () {
      var entry = this.head;
      if (entry) {
        var next = this.head = entry.next;
        if (next === null) this.tail = null;
        return entry.item;
      }
    }
  };

  var queue$2 = Queue$2;

  var userAgent$3 = engineUserAgent;

  var engineIsIosPebble = /ipad|iphone|ipod/i.test(userAgent$3) && typeof Pebble != 'undefined';

  var userAgent$2 = engineUserAgent;

  var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent$2);

  var global$6 = global$u;
  var bind$5 = functionBindContext;
  var getOwnPropertyDescriptor$2 = objectGetOwnPropertyDescriptor.f;
  var macrotask = task$1.set;
  var Queue$1 = queue$2;
  var IS_IOS = engineIsIos;
  var IS_IOS_PEBBLE = engineIsIosPebble;
  var IS_WEBOS_WEBKIT = engineIsWebosWebkit;
  var IS_NODE$2 = engineIsNode;

  var MutationObserver = global$6.MutationObserver || global$6.WebKitMutationObserver;
  var document$2 = global$6.document;
  var process$2 = global$6.process;
  var Promise$1 = global$6.Promise;
  // Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
  var queueMicrotaskDescriptor = getOwnPropertyDescriptor$2(global$6, 'queueMicrotask');
  var microtask$1 = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;
  var notify$2, toggle, node, promise, then;

  // modern engines have queueMicrotask method
  if (!microtask$1) {
    var queue$1 = new Queue$1();

    var flush = function () {
      var parent, fn;
      if (IS_NODE$2 && (parent = process$2.domain)) parent.exit();
      while (fn = queue$1.get()) try {
        fn();
      } catch (error) {
        if (queue$1.head) notify$2();
        throw error;
      }
      if (parent) parent.enter();
    };

    // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
    // also except WebOS Webkit https://github.com/zloirock/core-js/issues/898
    if (!IS_IOS && !IS_NODE$2 && !IS_WEBOS_WEBKIT && MutationObserver && document$2) {
      toggle = true;
      node = document$2.createTextNode('');
      new MutationObserver(flush).observe(node, { characterData: true });
      notify$2 = function () {
        node.data = toggle = !toggle;
      };
    // environments with maybe non-completely correct, but existent Promise
    } else if (!IS_IOS_PEBBLE && Promise$1 && Promise$1.resolve) {
      // Promise.resolve without an argument throws an error in LG WebOS 2
      promise = Promise$1.resolve(undefined);
      // workaround of WebKit ~ iOS Safari 10.1 bug
      promise.constructor = Promise$1;
      then = bind$5(promise.then, promise);
      notify$2 = function () {
        then(flush);
      };
    // Node.js without promises
    } else if (IS_NODE$2) {
      notify$2 = function () {
        process$2.nextTick(flush);
      };
    // for other environments - macrotask based on:
    // - setImmediate
    // - MessageChannel
    // - window.postMessage
    // - onreadystatechange
    // - setTimeout
    } else {
      // `webpack` dev server bug on IE global methods - use bind(fn, global)
      macrotask = bind$5(macrotask, global$6);
      notify$2 = function () {
        macrotask(flush);
      };
    }

    microtask$1 = function (fn) {
      if (!queue$1.head) notify$2();
      queue$1.add(fn);
    };
  }

  var microtask_1 = microtask$1;

  var hostReportErrors$1 = function (a, b) {
    try {
      // eslint-disable-next-line no-console -- safe
      arguments.length == 1 ? console.error(a) : console.error(a, b);
    } catch (error) { /* empty */ }
  };

  var perform$3 = function (exec) {
    try {
      return { error: false, value: exec() };
    } catch (error) {
      return { error: true, value: error };
    }
  };

  var global$5 = global$u;

  var promiseNativeConstructor = global$5.Promise;

  /* global Deno -- Deno case */

  var engineIsDeno = typeof Deno == 'object' && Deno && typeof Deno.version == 'object';

  var IS_DENO$1 = engineIsDeno;
  var IS_NODE$1 = engineIsNode;

  var engineIsBrowser = !IS_DENO$1 && !IS_NODE$1
    && typeof window == 'object'
    && typeof document == 'object';

  var global$4 = global$u;
  var NativePromiseConstructor$3 = promiseNativeConstructor;
  var isCallable$3 = isCallable$t;
  var isForced = isForced_1;
  var inspectSource = inspectSource$3;
  var wellKnownSymbol$2 = wellKnownSymbol$r;
  var IS_BROWSER = engineIsBrowser;
  var IS_DENO = engineIsDeno;
  var V8_VERSION = engineV8Version;

  NativePromiseConstructor$3 && NativePromiseConstructor$3.prototype;
  var SPECIES = wellKnownSymbol$2('species');
  var SUBCLASSING = false;
  var NATIVE_PROMISE_REJECTION_EVENT$1 = isCallable$3(global$4.PromiseRejectionEvent);

  var FORCED_PROMISE_CONSTRUCTOR$5 = isForced('Promise', function () {
    var PROMISE_CONSTRUCTOR_SOURCE = inspectSource(NativePromiseConstructor$3);
    var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(NativePromiseConstructor$3);
    // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
    // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
    // We can't detect it synchronously, so just check versions
    if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION === 66) return true;
    // We can't use @@species feature detection in V8 since it causes
    // deoptimization and performance degradation
    // https://github.com/zloirock/core-js/issues/679
    if (!V8_VERSION || V8_VERSION < 51 || !/native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) {
      // Detect correctness of subclassing with @@species support
      var promise = new NativePromiseConstructor$3(function (resolve) { resolve(1); });
      var FakePromise = function (exec) {
        exec(function () { /* empty */ }, function () { /* empty */ });
      };
      var constructor = promise.constructor = {};
      constructor[SPECIES] = FakePromise;
      SUBCLASSING = promise.then(function () { /* empty */ }) instanceof FakePromise;
      if (!SUBCLASSING) return true;
    // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    } return !GLOBAL_CORE_JS_PROMISE && (IS_BROWSER || IS_DENO) && !NATIVE_PROMISE_REJECTION_EVENT$1;
  });

  var promiseConstructorDetection = {
    CONSTRUCTOR: FORCED_PROMISE_CONSTRUCTOR$5,
    REJECTION_EVENT: NATIVE_PROMISE_REJECTION_EVENT$1,
    SUBCLASSING: SUBCLASSING
  };

  var newPromiseCapability$2 = {};

  var aCallable$4 = aCallable$9;

  var $TypeError = TypeError;

  var PromiseCapability = function (C) {
    var resolve, reject;
    this.promise = new C(function ($$resolve, $$reject) {
      if (resolve !== undefined || reject !== undefined) throw $TypeError('Bad Promise constructor');
      resolve = $$resolve;
      reject = $$reject;
    });
    this.resolve = aCallable$4(resolve);
    this.reject = aCallable$4(reject);
  };

  // `NewPromiseCapability` abstract operation
  // https://tc39.es/ecma262/#sec-newpromisecapability
  newPromiseCapability$2.f = function (C) {
    return new PromiseCapability(C);
  };

  var $$q = _export;
  var IS_NODE = engineIsNode;
  var global$3 = global$u;
  var call$8 = functionCall;
  var defineBuiltIn$3 = defineBuiltIn$e;
  var setPrototypeOf = objectSetPrototypeOf;
  var setToStringTag$2 = setToStringTag$8;
  var setSpecies = setSpecies$2;
  var aCallable$3 = aCallable$9;
  var isCallable$2 = isCallable$t;
  var isObject$7 = isObject$q;
  var anInstance$2 = anInstance$6;
  var speciesConstructor = speciesConstructor$2;
  var task = task$1.set;
  var microtask = microtask_1;
  var hostReportErrors = hostReportErrors$1;
  var perform$2 = perform$3;
  var Queue = queue$2;
  var InternalStateModule$2 = internalState;
  var NativePromiseConstructor$2 = promiseNativeConstructor;
  var PromiseConstructorDetection = promiseConstructorDetection;
  var newPromiseCapabilityModule$3 = newPromiseCapability$2;

  var PROMISE = 'Promise';
  var FORCED_PROMISE_CONSTRUCTOR$4 = PromiseConstructorDetection.CONSTRUCTOR;
  var NATIVE_PROMISE_REJECTION_EVENT = PromiseConstructorDetection.REJECTION_EVENT;
  var NATIVE_PROMISE_SUBCLASSING = PromiseConstructorDetection.SUBCLASSING;
  var getInternalPromiseState = InternalStateModule$2.getterFor(PROMISE);
  var setInternalState$2 = InternalStateModule$2.set;
  var NativePromisePrototype$1 = NativePromiseConstructor$2 && NativePromiseConstructor$2.prototype;
  var PromiseConstructor = NativePromiseConstructor$2;
  var PromisePrototype = NativePromisePrototype$1;
  var TypeError$3 = global$3.TypeError;
  var document$1 = global$3.document;
  var process$1 = global$3.process;
  var newPromiseCapability$1 = newPromiseCapabilityModule$3.f;
  var newGenericPromiseCapability = newPromiseCapability$1;

  var DISPATCH_EVENT = !!(document$1 && document$1.createEvent && global$3.dispatchEvent);
  var UNHANDLED_REJECTION = 'unhandledrejection';
  var REJECTION_HANDLED = 'rejectionhandled';
  var PENDING = 0;
  var FULFILLED = 1;
  var REJECTED = 2;
  var HANDLED = 1;
  var UNHANDLED = 2;

  var Internal, OwnPromiseCapability, PromiseWrapper, nativeThen;

  // helpers
  var isThenable = function (it) {
    var then;
    return isObject$7(it) && isCallable$2(then = it.then) ? then : false;
  };

  var callReaction = function (reaction, state) {
    var value = state.value;
    var ok = state.state == FULFILLED;
    var handler = ok ? reaction.ok : reaction.fail;
    var resolve = reaction.resolve;
    var reject = reaction.reject;
    var domain = reaction.domain;
    var result, then, exited;
    try {
      if (handler) {
        if (!ok) {
          if (state.rejection === UNHANDLED) onHandleUnhandled(state);
          state.rejection = HANDLED;
        }
        if (handler === true) result = value;
        else {
          if (domain) domain.enter();
          result = handler(value); // can throw
          if (domain) {
            domain.exit();
            exited = true;
          }
        }
        if (result === reaction.promise) {
          reject(TypeError$3('Promise-chain cycle'));
        } else if (then = isThenable(result)) {
          call$8(then, result, resolve, reject);
        } else resolve(result);
      } else reject(value);
    } catch (error) {
      if (domain && !exited) domain.exit();
      reject(error);
    }
  };

  var notify$1 = function (state, isReject) {
    if (state.notified) return;
    state.notified = true;
    microtask(function () {
      var reactions = state.reactions;
      var reaction;
      while (reaction = reactions.get()) {
        callReaction(reaction, state);
      }
      state.notified = false;
      if (isReject && !state.rejection) onUnhandled(state);
    });
  };

  var dispatchEvent = function (name, promise, reason) {
    var event, handler;
    if (DISPATCH_EVENT) {
      event = document$1.createEvent('Event');
      event.promise = promise;
      event.reason = reason;
      event.initEvent(name, false, true);
      global$3.dispatchEvent(event);
    } else event = { promise: promise, reason: reason };
    if (!NATIVE_PROMISE_REJECTION_EVENT && (handler = global$3['on' + name])) handler(event);
    else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
  };

  var onUnhandled = function (state) {
    call$8(task, global$3, function () {
      var promise = state.facade;
      var value = state.value;
      var IS_UNHANDLED = isUnhandled(state);
      var result;
      if (IS_UNHANDLED) {
        result = perform$2(function () {
          if (IS_NODE) {
            process$1.emit('unhandledRejection', value, promise);
          } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
        });
        // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
        state.rejection = IS_NODE || isUnhandled(state) ? UNHANDLED : HANDLED;
        if (result.error) throw result.value;
      }
    });
  };

  var isUnhandled = function (state) {
    return state.rejection !== HANDLED && !state.parent;
  };

  var onHandleUnhandled = function (state) {
    call$8(task, global$3, function () {
      var promise = state.facade;
      if (IS_NODE) {
        process$1.emit('rejectionHandled', promise);
      } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
    });
  };

  var bind$4 = function (fn, state, unwrap) {
    return function (value) {
      fn(state, value, unwrap);
    };
  };

  var internalReject = function (state, value, unwrap) {
    if (state.done) return;
    state.done = true;
    if (unwrap) state = unwrap;
    state.value = value;
    state.state = REJECTED;
    notify$1(state, true);
  };

  var internalResolve = function (state, value, unwrap) {
    if (state.done) return;
    state.done = true;
    if (unwrap) state = unwrap;
    try {
      if (state.facade === value) throw TypeError$3("Promise can't be resolved itself");
      var then = isThenable(value);
      if (then) {
        microtask(function () {
          var wrapper = { done: false };
          try {
            call$8(then, value,
              bind$4(internalResolve, wrapper, state),
              bind$4(internalReject, wrapper, state)
            );
          } catch (error) {
            internalReject(wrapper, error, state);
          }
        });
      } else {
        state.value = value;
        state.state = FULFILLED;
        notify$1(state, false);
      }
    } catch (error) {
      internalReject({ done: false }, error, state);
    }
  };

  // constructor polyfill
  if (FORCED_PROMISE_CONSTRUCTOR$4) {
    // 25.4.3.1 Promise(executor)
    PromiseConstructor = function Promise(executor) {
      anInstance$2(this, PromisePrototype);
      aCallable$3(executor);
      call$8(Internal, this);
      var state = getInternalPromiseState(this);
      try {
        executor(bind$4(internalResolve, state), bind$4(internalReject, state));
      } catch (error) {
        internalReject(state, error);
      }
    };

    PromisePrototype = PromiseConstructor.prototype;

    // eslint-disable-next-line no-unused-vars -- required for `.length`
    Internal = function Promise(executor) {
      setInternalState$2(this, {
        type: PROMISE,
        done: false,
        notified: false,
        parent: false,
        reactions: new Queue(),
        rejection: false,
        state: PENDING,
        value: undefined
      });
    };

    // `Promise.prototype.then` method
    // https://tc39.es/ecma262/#sec-promise.prototype.then
    Internal.prototype = defineBuiltIn$3(PromisePrototype, 'then', function then(onFulfilled, onRejected) {
      var state = getInternalPromiseState(this);
      var reaction = newPromiseCapability$1(speciesConstructor(this, PromiseConstructor));
      state.parent = true;
      reaction.ok = isCallable$2(onFulfilled) ? onFulfilled : true;
      reaction.fail = isCallable$2(onRejected) && onRejected;
      reaction.domain = IS_NODE ? process$1.domain : undefined;
      if (state.state == PENDING) state.reactions.add(reaction);
      else microtask(function () {
        callReaction(reaction, state);
      });
      return reaction.promise;
    });

    OwnPromiseCapability = function () {
      var promise = new Internal();
      var state = getInternalPromiseState(promise);
      this.promise = promise;
      this.resolve = bind$4(internalResolve, state);
      this.reject = bind$4(internalReject, state);
    };

    newPromiseCapabilityModule$3.f = newPromiseCapability$1 = function (C) {
      return C === PromiseConstructor || C === PromiseWrapper
        ? new OwnPromiseCapability(C)
        : newGenericPromiseCapability(C);
    };

    if (isCallable$2(NativePromiseConstructor$2) && NativePromisePrototype$1 !== Object.prototype) {
      nativeThen = NativePromisePrototype$1.then;

      if (!NATIVE_PROMISE_SUBCLASSING) {
        // make `Promise#then` return a polyfilled `Promise` for native promise-based APIs
        defineBuiltIn$3(NativePromisePrototype$1, 'then', function then(onFulfilled, onRejected) {
          var that = this;
          return new PromiseConstructor(function (resolve, reject) {
            call$8(nativeThen, that, resolve, reject);
          }).then(onFulfilled, onRejected);
        // https://github.com/zloirock/core-js/issues/640
        }, { unsafe: true });
      }

      // make `.constructor === Promise` work for native promise-based APIs
      try {
        delete NativePromisePrototype$1.constructor;
      } catch (error) { /* empty */ }

      // make `instanceof Promise` work for native promise-based APIs
      if (setPrototypeOf) {
        setPrototypeOf(NativePromisePrototype$1, PromisePrototype);
      }
    }
  }

  $$q({ global: true, constructor: true, wrap: true, forced: FORCED_PROMISE_CONSTRUCTOR$4 }, {
    Promise: PromiseConstructor
  });

  setToStringTag$2(PromiseConstructor, PROMISE, false);
  setSpecies(PROMISE);

  var NativePromiseConstructor$1 = promiseNativeConstructor;
  var checkCorrectnessOfIteration$1 = checkCorrectnessOfIteration$3;
  var FORCED_PROMISE_CONSTRUCTOR$3 = promiseConstructorDetection.CONSTRUCTOR;

  var promiseStaticsIncorrectIteration = FORCED_PROMISE_CONSTRUCTOR$3 || !checkCorrectnessOfIteration$1(function (iterable) {
    NativePromiseConstructor$1.all(iterable).then(undefined, function () { /* empty */ });
  });

  var $$p = _export;
  var call$7 = functionCall;
  var aCallable$2 = aCallable$9;
  var newPromiseCapabilityModule$2 = newPromiseCapability$2;
  var perform$1 = perform$3;
  var iterate$1 = iterate$5;
  var PROMISE_STATICS_INCORRECT_ITERATION$1 = promiseStaticsIncorrectIteration;

  // `Promise.all` method
  // https://tc39.es/ecma262/#sec-promise.all
  $$p({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$1 }, {
    all: function all(iterable) {
      var C = this;
      var capability = newPromiseCapabilityModule$2.f(C);
      var resolve = capability.resolve;
      var reject = capability.reject;
      var result = perform$1(function () {
        var $promiseResolve = aCallable$2(C.resolve);
        var values = [];
        var counter = 0;
        var remaining = 1;
        iterate$1(iterable, function (promise) {
          var index = counter++;
          var alreadyCalled = false;
          remaining++;
          call$7($promiseResolve, C, promise).then(function (value) {
            if (alreadyCalled) return;
            alreadyCalled = true;
            values[index] = value;
            --remaining || resolve(values);
          }, reject);
        });
        --remaining || resolve(values);
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$o = _export;
  var FORCED_PROMISE_CONSTRUCTOR$2 = promiseConstructorDetection.CONSTRUCTOR;
  var NativePromiseConstructor = promiseNativeConstructor;
  var getBuiltIn$1 = getBuiltIn$a;
  var isCallable$1 = isCallable$t;
  var defineBuiltIn$2 = defineBuiltIn$e;

  var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

  // `Promise.prototype.catch` method
  // https://tc39.es/ecma262/#sec-promise.prototype.catch
  $$o({ target: 'Promise', proto: true, forced: FORCED_PROMISE_CONSTRUCTOR$2, real: true }, {
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });

  // makes sure that native promise-based APIs `Promise#catch` properly works with patched `Promise#then`
  if (isCallable$1(NativePromiseConstructor)) {
    var method = getBuiltIn$1('Promise').prototype['catch'];
    if (NativePromisePrototype['catch'] !== method) {
      defineBuiltIn$2(NativePromisePrototype, 'catch', method, { unsafe: true });
    }
  }

  var $$n = _export;
  var call$6 = functionCall;
  var aCallable$1 = aCallable$9;
  var newPromiseCapabilityModule$1 = newPromiseCapability$2;
  var perform = perform$3;
  var iterate = iterate$5;
  var PROMISE_STATICS_INCORRECT_ITERATION = promiseStaticsIncorrectIteration;

  // `Promise.race` method
  // https://tc39.es/ecma262/#sec-promise.race
  $$n({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
    race: function race(iterable) {
      var C = this;
      var capability = newPromiseCapabilityModule$1.f(C);
      var reject = capability.reject;
      var result = perform(function () {
        var $promiseResolve = aCallable$1(C.resolve);
        iterate(iterable, function (promise) {
          call$6($promiseResolve, C, promise).then(capability.resolve, reject);
        });
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$m = _export;
  var call$5 = functionCall;
  var newPromiseCapabilityModule = newPromiseCapability$2;
  var FORCED_PROMISE_CONSTRUCTOR$1 = promiseConstructorDetection.CONSTRUCTOR;

  // `Promise.reject` method
  // https://tc39.es/ecma262/#sec-promise.reject
  $$m({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR$1 }, {
    reject: function reject(r) {
      var capability = newPromiseCapabilityModule.f(this);
      call$5(capability.reject, undefined, r);
      return capability.promise;
    }
  });

  var anObject$5 = anObject$r;
  var isObject$6 = isObject$q;
  var newPromiseCapability = newPromiseCapability$2;

  var promiseResolve$1 = function (C, x) {
    anObject$5(C);
    if (isObject$6(x) && x.constructor === C) return x;
    var promiseCapability = newPromiseCapability.f(C);
    var resolve = promiseCapability.resolve;
    resolve(x);
    return promiseCapability.promise;
  };

  var $$l = _export;
  var getBuiltIn = getBuiltIn$a;
  var FORCED_PROMISE_CONSTRUCTOR = promiseConstructorDetection.CONSTRUCTOR;
  var promiseResolve = promiseResolve$1;

  getBuiltIn('Promise');

  // `Promise.resolve` method
  // https://tc39.es/ecma262/#sec-promise.resolve
  $$l({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR }, {
    resolve: function resolve(x) {
      return promiseResolve(this, x);
    }
  });

  var $$k = _export;
  var $includes = arrayIncludes.includes;
  var fails$8 = fails$I;
  var addToUnscopables$4 = addToUnscopables$6;

  // FF99+ bug
  var BROKEN_ON_SPARSE = fails$8(function () {
    // eslint-disable-next-line es/no-array-prototype-includes -- detection
    return !Array(1).includes();
  });

  // `Array.prototype.includes` method
  // https://tc39.es/ecma262/#sec-array.prototype.includes
  $$k({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
    includes: function includes(el /* , fromIndex = 0 */) {
      return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$4('includes');

  var $$j = _export;
  var uncurryThis$a = functionUncurryThis;
  var notARegExp$1 = notARegexp;
  var requireObjectCoercible$5 = requireObjectCoercible$d;
  var toString$6 = toString$i;
  var correctIsRegExpLogic$1 = correctIsRegexpLogic;

  var stringIndexOf = uncurryThis$a(''.indexOf);

  // `String.prototype.includes` method
  // https://tc39.es/ecma262/#sec-string.prototype.includes
  $$j({ target: 'String', proto: true, forced: !correctIsRegExpLogic$1('includes') }, {
    includes: function includes(searchString /* , position = 0 */) {
      return !!~stringIndexOf(
        toString$6(requireObjectCoercible$5(this)),
        toString$6(notARegExp$1(searchString)),
        arguments.length > 1 ? arguments[1] : undefined
      );
    }
  });

  var arraySlice$1 = arraySliceSimple;

  var floor$3 = Math.floor;

  var mergeSort = function (array, comparefn) {
    var length = array.length;
    var middle = floor$3(length / 2);
    return length < 8 ? insertionSort(array, comparefn) : merge$1(
      array,
      mergeSort(arraySlice$1(array, 0, middle), comparefn),
      mergeSort(arraySlice$1(array, middle), comparefn),
      comparefn
    );
  };

  var insertionSort = function (array, comparefn) {
    var length = array.length;
    var i = 1;
    var element, j;

    while (i < length) {
      j = i;
      element = array[i];
      while (j && comparefn(array[j - 1], element) > 0) {
        array[j] = array[--j];
      }
      if (j !== i++) array[j] = element;
    } return array;
  };

  var merge$1 = function (array, left, right, comparefn) {
    var llength = left.length;
    var rlength = right.length;
    var lindex = 0;
    var rindex = 0;

    while (lindex < llength || rindex < rlength) {
      array[lindex + rindex] = (lindex < llength && rindex < rlength)
        ? comparefn(left[lindex], right[rindex]) <= 0 ? left[lindex++] : right[rindex++]
        : lindex < llength ? left[lindex++] : right[rindex++];
    } return array;
  };

  var arraySort$1 = mergeSort;

  var userAgent$1 = engineUserAgent;

  var firefox = userAgent$1.match(/firefox\/(\d+)/i);

  var engineFfVersion = !!firefox && +firefox[1];

  var UA = engineUserAgent;

  var engineIsIeOrEdge = /MSIE|Trident/.test(UA);

  var userAgent = engineUserAgent;

  var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

  var engineWebkitVersion = !!webkit && +webkit[1];

  var $$i = _export;
  var uncurryThis$9 = functionUncurryThis;
  var aCallable = aCallable$9;
  var toObject$5 = toObject$e;
  var lengthOfArrayLike$4 = lengthOfArrayLike$c;
  var deletePropertyOrThrow = deletePropertyOrThrow$2;
  var toString$5 = toString$i;
  var fails$7 = fails$I;
  var internalSort = arraySort$1;
  var arrayMethodIsStrict$1 = arrayMethodIsStrict$3;
  var FF = engineFfVersion;
  var IE_OR_EDGE = engineIsIeOrEdge;
  var V8 = engineV8Version;
  var WEBKIT = engineWebkitVersion;

  var test = [];
  var nativeSort = uncurryThis$9(test.sort);
  var push$4 = uncurryThis$9(test.push);

  // IE8-
  var FAILS_ON_UNDEFINED = fails$7(function () {
    test.sort(undefined);
  });
  // V8 bug
  var FAILS_ON_NULL = fails$7(function () {
    test.sort(null);
  });
  // Old WebKit
  var STRICT_METHOD = arrayMethodIsStrict$1('sort');

  var STABLE_SORT = !fails$7(function () {
    // feature detection can be too slow, so check engines versions
    if (V8) return V8 < 70;
    if (FF && FF > 3) return;
    if (IE_OR_EDGE) return true;
    if (WEBKIT) return WEBKIT < 603;

    var result = '';
    var code, chr, value, index;

    // generate an array with more 512 elements (Chakra and old V8 fails only in this case)
    for (code = 65; code < 76; code++) {
      chr = String.fromCharCode(code);

      switch (code) {
        case 66: case 69: case 70: case 72: value = 3; break;
        case 68: case 71: value = 4; break;
        default: value = 2;
      }

      for (index = 0; index < 47; index++) {
        test.push({ k: chr + index, v: value });
      }
    }

    test.sort(function (a, b) { return b.v - a.v; });

    for (index = 0; index < test.length; index++) {
      chr = test[index].k.charAt(0);
      if (result.charAt(result.length - 1) !== chr) result += chr;
    }

    return result !== 'DGBEFHACIJK';
  });

  var FORCED$2 = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD || !STABLE_SORT;

  var getSortCompare = function (comparefn) {
    return function (x, y) {
      if (y === undefined) return -1;
      if (x === undefined) return 1;
      if (comparefn !== undefined) return +comparefn(x, y) || 0;
      return toString$5(x) > toString$5(y) ? 1 : -1;
    };
  };

  // `Array.prototype.sort` method
  // https://tc39.es/ecma262/#sec-array.prototype.sort
  $$i({ target: 'Array', proto: true, forced: FORCED$2 }, {
    sort: function sort(comparefn) {
      if (comparefn !== undefined) aCallable(comparefn);

      var array = toObject$5(this);

      if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

      var items = [];
      var arrayLength = lengthOfArrayLike$4(array);
      var itemsLength, index;

      for (index = 0; index < arrayLength; index++) {
        if (index in array) push$4(items, array[index]);
      }

      internalSort(items, getSortCompare(comparefn));

      itemsLength = lengthOfArrayLike$4(items);
      index = 0;

      while (index < itemsLength) array[index] = items[index++];
      while (index < arrayLength) deletePropertyOrThrow(array, index++);

      return array;
    }
  });

  var $$h = _export;
  var toObject$4 = toObject$e;
  var nativeKeys = objectKeys$4;
  var fails$6 = fails$I;

  var FAILS_ON_PRIMITIVES$2 = fails$6(function () { nativeKeys(1); });

  // `Object.keys` method
  // https://tc39.es/ecma262/#sec-object.keys
  $$h({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2 }, {
    keys: function keys(it) {
      return nativeKeys(toObject$4(it));
    }
  });

  var toObject$3 = toObject$e;
  var toAbsoluteIndex = toAbsoluteIndex$5;
  var lengthOfArrayLike$3 = lengthOfArrayLike$c;

  // `Array.prototype.fill` method implementation
  // https://tc39.es/ecma262/#sec-array.prototype.fill
  var arrayFill = function fill(value /* , start = 0, end = @length */) {
    var O = toObject$3(this);
    var length = lengthOfArrayLike$3(O);
    var argumentsLength = arguments.length;
    var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
    var end = argumentsLength > 2 ? arguments[2] : undefined;
    var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
    while (endPos > index) O[index++] = value;
    return O;
  };

  var $$g = _export;
  var fill = arrayFill;
  var addToUnscopables$3 = addToUnscopables$6;

  // `Array.prototype.fill` method
  // https://tc39.es/ecma262/#sec-array.prototype.fill
  $$g({ target: 'Array', proto: true }, {
    fill: fill
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$3('fill');

  var anObject$4 = anObject$r;
  var iteratorClose = iteratorClose$2;

  // call something on iterator step with safe closing on error
  var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
    try {
      return ENTRIES ? fn(anObject$4(value)[0], value[1]) : fn(value);
    } catch (error) {
      iteratorClose(iterator, 'throw', error);
    }
  };

  var bind$3 = functionBindContext;
  var call$4 = functionCall;
  var toObject$2 = toObject$e;
  var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
  var isArrayIteratorMethod = isArrayIteratorMethod$2;
  var isConstructor = isConstructor$4;
  var lengthOfArrayLike$2 = lengthOfArrayLike$c;
  var createProperty = createProperty$5;
  var getIterator$1 = getIterator$3;
  var getIteratorMethod$1 = getIteratorMethod$4;

  var $Array = Array;

  // `Array.from` method implementation
  // https://tc39.es/ecma262/#sec-array.from
  var arrayFrom$1 = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
    var O = toObject$2(arrayLike);
    var IS_CONSTRUCTOR = isConstructor(this);
    var argumentsLength = arguments.length;
    var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    if (mapping) mapfn = bind$3(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
    var iteratorMethod = getIteratorMethod$1(O);
    var index = 0;
    var length, result, step, iterator, next, value;
    // if the target is not iterable or it's an array with the default iterator - use a simple case
    if (iteratorMethod && !(this === $Array && isArrayIteratorMethod(iteratorMethod))) {
      iterator = getIterator$1(O, iteratorMethod);
      next = iterator.next;
      result = IS_CONSTRUCTOR ? new this() : [];
      for (;!(step = call$4(next, iterator)).done; index++) {
        value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
        createProperty(result, index, value);
      }
    } else {
      length = lengthOfArrayLike$2(O);
      result = IS_CONSTRUCTOR ? new this(length) : $Array(length);
      for (;length > index; index++) {
        value = mapping ? mapfn(O[index], index) : O[index];
        createProperty(result, index, value);
      }
    }
    result.length = index;
    return result;
  };

  var $$f = _export;
  var from = arrayFrom$1;
  var checkCorrectnessOfIteration = checkCorrectnessOfIteration$3;

  var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
    // eslint-disable-next-line es/no-array-from -- required for testing
    Array.from(iterable);
  });

  // `Array.from` method
  // https://tc39.es/ecma262/#sec-array.from
  $$f({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
    from: from
  });

  var DESCRIPTORS$5 = descriptors;
  var FUNCTION_NAME_EXISTS = functionName.EXISTS;
  var uncurryThis$8 = functionUncurryThis;
  var defineBuiltInAccessor$2 = defineBuiltInAccessor$7;

  var FunctionPrototype = Function.prototype;
  var functionToString = uncurryThis$8(FunctionPrototype.toString);
  var nameRE = /function\b(?:\s|\/\*[\S\s]*?\*\/|\/\/[^\n\r]*[\n\r]+)*([^\s(/]*)/;
  var regExpExec$2 = uncurryThis$8(nameRE.exec);
  var NAME = 'name';

  // Function instances `.name` property
  // https://tc39.es/ecma262/#sec-function-instances-name
  if (DESCRIPTORS$5 && !FUNCTION_NAME_EXISTS) {
    defineBuiltInAccessor$2(FunctionPrototype, NAME, {
      configurable: true,
      get: function () {
        try {
          return regExpExec$2(nameRE, functionToString(this))[1];
        } catch (error) {
          return '';
        }
      }
    });
  }

  var $$e = _export;
  var DESCRIPTORS$4 = descriptors;
  var anObject$3 = anObject$r;
  var toPropertyKey = toPropertyKey$5;
  var definePropertyModule = objectDefineProperty;
  var fails$5 = fails$I;

  // MS Edge has broken Reflect.defineProperty - throwing instead of returning false
  var ERROR_INSTEAD_OF_FALSE = fails$5(function () {
    // eslint-disable-next-line es/no-reflect -- required for testing
    Reflect.defineProperty(definePropertyModule.f({}, 1, { value: 1 }), 1, { value: 2 });
  });

  // `Reflect.defineProperty` method
  // https://tc39.es/ecma262/#sec-reflect.defineproperty
  $$e({ target: 'Reflect', stat: true, forced: ERROR_INSTEAD_OF_FALSE, sham: !DESCRIPTORS$4 }, {
    defineProperty: function defineProperty(target, propertyKey, attributes) {
      anObject$3(target);
      var key = toPropertyKey(propertyKey);
      anObject$3(attributes);
      try {
        definePropertyModule.f(target, key, attributes);
        return true;
      } catch (error) {
        return false;
      }
    }
  });

  var call$3 = functionCall;
  var fixRegExpWellKnownSymbolLogic$1 = fixRegexpWellKnownSymbolLogic;
  var anObject$2 = anObject$r;
  var isNullOrUndefined$1 = isNullOrUndefined$b;
  var toLength$1 = toLength$6;
  var toString$4 = toString$i;
  var requireObjectCoercible$4 = requireObjectCoercible$d;
  var getMethod$1 = getMethod$7;
  var advanceStringIndex = advanceStringIndex$3;
  var regExpExec$1 = regexpExecAbstract;

  // @@match logic
  fixRegExpWellKnownSymbolLogic$1('match', function (MATCH, nativeMatch, maybeCallNative) {
    return [
      // `String.prototype.match` method
      // https://tc39.es/ecma262/#sec-string.prototype.match
      function match(regexp) {
        var O = requireObjectCoercible$4(this);
        var matcher = isNullOrUndefined$1(regexp) ? undefined : getMethod$1(regexp, MATCH);
        return matcher ? call$3(matcher, regexp, O) : new RegExp(regexp)[MATCH](toString$4(O));
      },
      // `RegExp.prototype[@@match]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@match
      function (string) {
        var rx = anObject$2(this);
        var S = toString$4(string);
        var res = maybeCallNative(nativeMatch, rx, S);

        if (res.done) return res.value;

        if (!rx.global) return regExpExec$1(rx, S);

        var fullUnicode = rx.unicode;
        rx.lastIndex = 0;
        var A = [];
        var n = 0;
        var result;
        while ((result = regExpExec$1(rx, S)) !== null) {
          var matchStr = toString$4(result[0]);
          A[n] = matchStr;
          if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength$1(rx.lastIndex), fullUnicode);
          n++;
        }
        return n === 0 ? null : A;
      }
    ];
  });

  var $$d = _export;
  var $findIndex = arrayIteration.findIndex;
  var addToUnscopables$2 = addToUnscopables$6;

  var FIND_INDEX = 'findIndex';
  var SKIPS_HOLES$1 = true;

  // Shouldn't skip holes
  if (FIND_INDEX in []) Array(1)[FIND_INDEX](function () { SKIPS_HOLES$1 = false; });

  // `Array.prototype.findIndex` method
  // https://tc39.es/ecma262/#sec-array.prototype.findindex
  $$d({ target: 'Array', proto: true, forced: SKIPS_HOLES$1 }, {
    findIndex: function findIndex(callbackfn /* , that = undefined */) {
      return $findIndex(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$2(FIND_INDEX);

  var uncurryThis$7 = functionUncurryThis;
  var requireObjectCoercible$3 = requireObjectCoercible$d;
  var toString$3 = toString$i;

  var quot = /"/g;
  var replace$3 = uncurryThis$7(''.replace);

  // `CreateHTML` abstract operation
  // https://tc39.es/ecma262/#sec-createhtml
  var createHtml = function (string, tag, attribute, value) {
    var S = toString$3(requireObjectCoercible$3(string));
    var p1 = '<' + tag;
    if (attribute !== '') p1 += ' ' + attribute + '="' + replace$3(toString$3(value), quot, '&quot;') + '"';
    return p1 + '>' + S + '</' + tag + '>';
  };

  var fails$4 = fails$I;

  // check the existence of a method, lowercase
  // of a tag and escaping quotes in arguments
  var stringHtmlForced = function (METHOD_NAME) {
    return fails$4(function () {
      var test = ''[METHOD_NAME]('"');
      return test !== test.toLowerCase() || test.split('"').length > 3;
    });
  };

  var $$c = _export;
  var createHTML = createHtml;
  var forcedStringHTMLMethod = stringHtmlForced;

  // `String.prototype.anchor` method
  // https://tc39.es/ecma262/#sec-string.prototype.anchor
  $$c({ target: 'String', proto: true, forced: forcedStringHTMLMethod('anchor') }, {
    anchor: function anchor(name) {
      return createHTML(this, 'a', 'name', name);
    }
  });

  var $$b = _export;
  var uncurryThis$6 = functionUncurryThis;
  var IndexedObject = indexedObject;
  var toIndexedObject$1 = toIndexedObject$a;
  var arrayMethodIsStrict = arrayMethodIsStrict$3;

  var nativeJoin = uncurryThis$6([].join);

  var ES3_STRINGS = IndexedObject != Object;
  var FORCED$1 = ES3_STRINGS || !arrayMethodIsStrict('join', ',');

  // `Array.prototype.join` method
  // https://tc39.es/ecma262/#sec-array.prototype.join
  $$b({ target: 'Array', proto: true, forced: FORCED$1 }, {
    join: function join(separator) {
      return nativeJoin(toIndexedObject$1(this), separator === undefined ? ',' : separator);
    }
  });

  var toIntegerOrInfinity$2 = toIntegerOrInfinity$8;
  var toString$2 = toString$i;
  var requireObjectCoercible$2 = requireObjectCoercible$d;

  var $RangeError$2 = RangeError;

  // `String.prototype.repeat` method implementation
  // https://tc39.es/ecma262/#sec-string.prototype.repeat
  var stringRepeat = function repeat(count) {
    var str = toString$2(requireObjectCoercible$2(this));
    var result = '';
    var n = toIntegerOrInfinity$2(count);
    if (n < 0 || n == Infinity) throw $RangeError$2('Wrong number of repetitions');
    for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
    return result;
  };

  var $$a = _export;
  var uncurryThis$5 = functionUncurryThis;
  var toIntegerOrInfinity$1 = toIntegerOrInfinity$8;
  var thisNumberValue = thisNumberValue$2;
  var $repeat = stringRepeat;
  var fails$3 = fails$I;

  var $RangeError$1 = RangeError;
  var $String = String;
  var floor$2 = Math.floor;
  var repeat = uncurryThis$5($repeat);
  var stringSlice$2 = uncurryThis$5(''.slice);
  var nativeToFixed = uncurryThis$5(1.0.toFixed);

  var pow$1 = function (x, n, acc) {
    return n === 0 ? acc : n % 2 === 1 ? pow$1(x, n - 1, acc * x) : pow$1(x * x, n / 2, acc);
  };

  var log = function (x) {
    var n = 0;
    var x2 = x;
    while (x2 >= 4096) {
      n += 12;
      x2 /= 4096;
    }
    while (x2 >= 2) {
      n += 1;
      x2 /= 2;
    } return n;
  };

  var multiply = function (data, n, c) {
    var index = -1;
    var c2 = c;
    while (++index < 6) {
      c2 += n * data[index];
      data[index] = c2 % 1e7;
      c2 = floor$2(c2 / 1e7);
    }
  };

  var divide = function (data, n) {
    var index = 6;
    var c = 0;
    while (--index >= 0) {
      c += data[index];
      data[index] = floor$2(c / n);
      c = (c % n) * 1e7;
    }
  };

  var dataToString = function (data) {
    var index = 6;
    var s = '';
    while (--index >= 0) {
      if (s !== '' || index === 0 || data[index] !== 0) {
        var t = $String(data[index]);
        s = s === '' ? t : s + repeat('0', 7 - t.length) + t;
      }
    } return s;
  };

  var FORCED = fails$3(function () {
    return nativeToFixed(0.00008, 3) !== '0.000' ||
      nativeToFixed(0.9, 0) !== '1' ||
      nativeToFixed(1.255, 2) !== '1.25' ||
      nativeToFixed(1000000000000000128.0, 0) !== '1000000000000000128';
  }) || !fails$3(function () {
    // V8 ~ Android 4.3-
    nativeToFixed({});
  });

  // `Number.prototype.toFixed` method
  // https://tc39.es/ecma262/#sec-number.prototype.tofixed
  $$a({ target: 'Number', proto: true, forced: FORCED }, {
    toFixed: function toFixed(fractionDigits) {
      var number = thisNumberValue(this);
      var fractDigits = toIntegerOrInfinity$1(fractionDigits);
      var data = [0, 0, 0, 0, 0, 0];
      var sign = '';
      var result = '0';
      var e, z, j, k;

      // TODO: ES2018 increased the maximum number of fraction digits to 100, need to improve the implementation
      if (fractDigits < 0 || fractDigits > 20) throw $RangeError$1('Incorrect fraction digits');
      // eslint-disable-next-line no-self-compare -- NaN check
      if (number != number) return 'NaN';
      if (number <= -1e21 || number >= 1e21) return $String(number);
      if (number < 0) {
        sign = '-';
        number = -number;
      }
      if (number > 1e-21) {
        e = log(number * pow$1(2, 69, 1)) - 69;
        z = e < 0 ? number * pow$1(2, -e, 1) : number / pow$1(2, e, 1);
        z *= 0x10000000000000;
        e = 52 - e;
        if (e > 0) {
          multiply(data, 0, z);
          j = fractDigits;
          while (j >= 7) {
            multiply(data, 1e7, 0);
            j -= 7;
          }
          multiply(data, pow$1(10, j, 1), 0);
          j = e - 1;
          while (j >= 23) {
            divide(data, 1 << 23);
            j -= 23;
          }
          divide(data, 1 << j);
          multiply(data, 1, 1);
          divide(data, 2);
          result = dataToString(data);
        } else {
          multiply(data, 0, z);
          multiply(data, 1 << -e, 0);
          result = dataToString(data) + repeat('0', fractDigits);
        }
      }
      if (fractDigits > 0) {
        k = result.length;
        result = sign + (k <= fractDigits
          ? '0.' + repeat('0', fractDigits - k) + result
          : stringSlice$2(result, 0, k - fractDigits) + '.' + stringSlice$2(result, k - fractDigits));
      } else {
        result = sign + result;
      } return result;
    }
  });

  var fails$2 = fails$I;
  var wellKnownSymbol$1 = wellKnownSymbol$r;
  var DESCRIPTORS$3 = descriptors;
  var IS_PURE = isPure;

  var ITERATOR$1 = wellKnownSymbol$1('iterator');

  var urlConstructorDetection = !fails$2(function () {
    // eslint-disable-next-line unicorn/relative-url-style -- required for testing
    var url = new URL('b?a=1&b=2&c=3', 'http://a');
    var searchParams = url.searchParams;
    var result = '';
    url.pathname = 'c%20d';
    searchParams.forEach(function (value, key) {
      searchParams['delete']('b');
      result += key + value;
    });
    return (IS_PURE && !url.toJSON)
      || (!searchParams.size && (IS_PURE || !DESCRIPTORS$3))
      || !searchParams.sort
      || url.href !== 'http://a/c%20d?a=1&c=3'
      || searchParams.get('c') !== '3'
      || String(new URLSearchParams('?a=1')) !== 'a=1'
      || !searchParams[ITERATOR$1]
      // throws in Edge
      || new URL('https://a@b').username !== 'a'
      || new URLSearchParams(new URLSearchParams('a=b')).get('a') !== 'b'
      // not punycoded in Edge
      || new URL('http://тест').host !== 'xn--e1aybc'
      // not escaped in Chrome 62-
      || new URL('http://a#б').hash !== '#%D0%B1'
      // fails in Chrome 66-
      || result !== 'a1c3'
      // throws in Safari
      || new URL('http://x', undefined).host !== 'x';
  });

  // based on https://github.com/bestiejs/punycode.js/blob/master/punycode.js
  var uncurryThis$4 = functionUncurryThis;

  var maxInt = 2147483647; // aka. 0x7FFFFFFF or 2^31-1
  var base = 36;
  var tMin = 1;
  var tMax = 26;
  var skew = 38;
  var damp = 700;
  var initialBias = 72;
  var initialN = 128; // 0x80
  var delimiter = '-'; // '\x2D'
  var regexNonASCII = /[^\0-\u007E]/; // non-ASCII chars
  var regexSeparators = /[.\u3002\uFF0E\uFF61]/g; // RFC 3490 separators
  var OVERFLOW_ERROR = 'Overflow: input needs wider integers to process';
  var baseMinusTMin = base - tMin;

  var $RangeError = RangeError;
  var exec$1 = uncurryThis$4(regexSeparators.exec);
  var floor$1 = Math.floor;
  var fromCharCode = String.fromCharCode;
  var charCodeAt = uncurryThis$4(''.charCodeAt);
  var join$2 = uncurryThis$4([].join);
  var push$3 = uncurryThis$4([].push);
  var replace$2 = uncurryThis$4(''.replace);
  var split$2 = uncurryThis$4(''.split);
  var toLowerCase$1 = uncurryThis$4(''.toLowerCase);

  /**
   * Creates an array containing the numeric code points of each Unicode
   * character in the string. While JavaScript uses UCS-2 internally,
   * this function will convert a pair of surrogate halves (each of which
   * UCS-2 exposes as separate characters) into a single code point,
   * matching UTF-16.
   */
  var ucs2decode = function (string) {
    var output = [];
    var counter = 0;
    var length = string.length;
    while (counter < length) {
      var value = charCodeAt(string, counter++);
      if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
        // It's a high surrogate, and there is a next character.
        var extra = charCodeAt(string, counter++);
        if ((extra & 0xFC00) == 0xDC00) { // Low surrogate.
          push$3(output, ((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
        } else {
          // It's an unmatched surrogate; only append this code unit, in case the
          // next code unit is the high surrogate of a surrogate pair.
          push$3(output, value);
          counter--;
        }
      } else {
        push$3(output, value);
      }
    }
    return output;
  };

  /**
   * Converts a digit/integer into a basic code point.
   */
  var digitToBasic = function (digit) {
    //  0..25 map to ASCII a..z or A..Z
    // 26..35 map to ASCII 0..9
    return digit + 22 + 75 * (digit < 26);
  };

  /**
   * Bias adaptation function as per section 3.4 of RFC 3492.
   * https://tools.ietf.org/html/rfc3492#section-3.4
   */
  var adapt = function (delta, numPoints, firstTime) {
    var k = 0;
    delta = firstTime ? floor$1(delta / damp) : delta >> 1;
    delta += floor$1(delta / numPoints);
    while (delta > baseMinusTMin * tMax >> 1) {
      delta = floor$1(delta / baseMinusTMin);
      k += base;
    }
    return floor$1(k + (baseMinusTMin + 1) * delta / (delta + skew));
  };

  /**
   * Converts a string of Unicode symbols (e.g. a domain name label) to a
   * Punycode string of ASCII-only symbols.
   */
  var encode = function (input) {
    var output = [];

    // Convert the input in UCS-2 to an array of Unicode code points.
    input = ucs2decode(input);

    // Cache the length.
    var inputLength = input.length;

    // Initialize the state.
    var n = initialN;
    var delta = 0;
    var bias = initialBias;
    var i, currentValue;

    // Handle the basic code points.
    for (i = 0; i < input.length; i++) {
      currentValue = input[i];
      if (currentValue < 0x80) {
        push$3(output, fromCharCode(currentValue));
      }
    }

    var basicLength = output.length; // number of basic code points.
    var handledCPCount = basicLength; // number of code points that have been handled;

    // Finish the basic string with a delimiter unless it's empty.
    if (basicLength) {
      push$3(output, delimiter);
    }

    // Main encoding loop:
    while (handledCPCount < inputLength) {
      // All non-basic code points < n have been handled already. Find the next larger one:
      var m = maxInt;
      for (i = 0; i < input.length; i++) {
        currentValue = input[i];
        if (currentValue >= n && currentValue < m) {
          m = currentValue;
        }
      }

      // Increase `delta` enough to advance the decoder's <n,i> state to <m,0>, but guard against overflow.
      var handledCPCountPlusOne = handledCPCount + 1;
      if (m - n > floor$1((maxInt - delta) / handledCPCountPlusOne)) {
        throw $RangeError(OVERFLOW_ERROR);
      }

      delta += (m - n) * handledCPCountPlusOne;
      n = m;

      for (i = 0; i < input.length; i++) {
        currentValue = input[i];
        if (currentValue < n && ++delta > maxInt) {
          throw $RangeError(OVERFLOW_ERROR);
        }
        if (currentValue == n) {
          // Represent delta as a generalized variable-length integer.
          var q = delta;
          var k = base;
          while (true) {
            var t = k <= bias ? tMin : (k >= bias + tMax ? tMax : k - bias);
            if (q < t) break;
            var qMinusT = q - t;
            var baseMinusT = base - t;
            push$3(output, fromCharCode(digitToBasic(t + qMinusT % baseMinusT)));
            q = floor$1(qMinusT / baseMinusT);
            k += base;
          }

          push$3(output, fromCharCode(digitToBasic(q)));
          bias = adapt(delta, handledCPCountPlusOne, handledCPCount == basicLength);
          delta = 0;
          handledCPCount++;
        }
      }

      delta++;
      n++;
    }
    return join$2(output, '');
  };

  var stringPunycodeToAscii = function (input) {
    var encoded = [];
    var labels = split$2(replace$2(toLowerCase$1(input), regexSeparators, '\u002E'), '.');
    var i, label;
    for (i = 0; i < labels.length; i++) {
      label = labels[i];
      push$3(encoded, exec$1(regexNonASCII, label) ? 'xn--' + encode(label) : label);
    }
    return join$2(encoded, '.');
  };

  // TODO: in core-js@4, move /modules/ dependencies to public entries for better optimization by tools like `preset-env`

  var $$9 = _export;
  var global$2 = global$u;
  var call$2 = functionCall;
  var uncurryThis$3 = functionUncurryThis;
  var DESCRIPTORS$2 = descriptors;
  var USE_NATIVE_URL$1 = urlConstructorDetection;
  var defineBuiltIn$1 = defineBuiltIn$e;
  var defineBuiltInAccessor$1 = defineBuiltInAccessor$7;
  var defineBuiltIns = defineBuiltIns$4;
  var setToStringTag$1 = setToStringTag$8;
  var createIteratorConstructor = iteratorCreateConstructor;
  var InternalStateModule$1 = internalState;
  var anInstance$1 = anInstance$6;
  var isCallable = isCallable$t;
  var hasOwn$3 = hasOwnProperty_1;
  var bind$2 = functionBindContext;
  var classof = classof$c;
  var anObject$1 = anObject$r;
  var isObject$5 = isObject$q;
  var $toString$1 = toString$i;
  var create = objectCreate;
  var createPropertyDescriptor = createPropertyDescriptor$7;
  var getIterator = getIterator$3;
  var getIteratorMethod = getIteratorMethod$4;
  var validateArgumentsLength$1 = validateArgumentsLength$3;
  var wellKnownSymbol = wellKnownSymbol$r;
  var arraySort = arraySort$1;

  var ITERATOR = wellKnownSymbol('iterator');
  var URL_SEARCH_PARAMS = 'URLSearchParams';
  var URL_SEARCH_PARAMS_ITERATOR = URL_SEARCH_PARAMS + 'Iterator';
  var setInternalState$1 = InternalStateModule$1.set;
  var getInternalParamsState = InternalStateModule$1.getterFor(URL_SEARCH_PARAMS);
  var getInternalIteratorState = InternalStateModule$1.getterFor(URL_SEARCH_PARAMS_ITERATOR);
  // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
  var getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

  // Avoid NodeJS experimental warning
  var safeGetBuiltIn = function (name) {
    if (!DESCRIPTORS$2) return global$2[name];
    var descriptor = getOwnPropertyDescriptor$1(global$2, name);
    return descriptor && descriptor.value;
  };

  var nativeFetch = safeGetBuiltIn('fetch');
  var NativeRequest = safeGetBuiltIn('Request');
  var Headers = safeGetBuiltIn('Headers');
  var RequestPrototype = NativeRequest && NativeRequest.prototype;
  var HeadersPrototype = Headers && Headers.prototype;
  var RegExp$1 = global$2.RegExp;
  var TypeError$2 = global$2.TypeError;
  var decodeURIComponent = global$2.decodeURIComponent;
  var encodeURIComponent$1 = global$2.encodeURIComponent;
  var charAt$1 = uncurryThis$3(''.charAt);
  var join$1 = uncurryThis$3([].join);
  var push$2 = uncurryThis$3([].push);
  var replace$1 = uncurryThis$3(''.replace);
  var shift$1 = uncurryThis$3([].shift);
  var splice = uncurryThis$3([].splice);
  var split$1 = uncurryThis$3(''.split);
  var stringSlice$1 = uncurryThis$3(''.slice);

  var plus = /\+/g;
  var sequences = Array(4);

  var percentSequence = function (bytes) {
    return sequences[bytes - 1] || (sequences[bytes - 1] = RegExp$1('((?:%[\\da-f]{2}){' + bytes + '})', 'gi'));
  };

  var percentDecode = function (sequence) {
    try {
      return decodeURIComponent(sequence);
    } catch (error) {
      return sequence;
    }
  };

  var deserialize = function (it) {
    var result = replace$1(it, plus, ' ');
    var bytes = 4;
    try {
      return decodeURIComponent(result);
    } catch (error) {
      while (bytes) {
        result = replace$1(result, percentSequence(bytes--), percentDecode);
      }
      return result;
    }
  };

  var find = /[!'()~]|%20/g;

  var replacements = {
    '!': '%21',
    "'": '%27',
    '(': '%28',
    ')': '%29',
    '~': '%7E',
    '%20': '+'
  };

  var replacer$1 = function (match) {
    return replacements[match];
  };

  var serialize = function (it) {
    return replace$1(encodeURIComponent$1(it), find, replacer$1);
  };

  var URLSearchParamsIterator = createIteratorConstructor(function Iterator(params, kind) {
    setInternalState$1(this, {
      type: URL_SEARCH_PARAMS_ITERATOR,
      iterator: getIterator(getInternalParamsState(params).entries),
      kind: kind
    });
  }, 'Iterator', function next() {
    var state = getInternalIteratorState(this);
    var kind = state.kind;
    var step = state.iterator.next();
    var entry = step.value;
    if (!step.done) {
      step.value = kind === 'keys' ? entry.key : kind === 'values' ? entry.value : [entry.key, entry.value];
    } return step;
  }, true);

  var URLSearchParamsState = function (init) {
    this.entries = [];
    this.url = null;

    if (init !== undefined) {
      if (isObject$5(init)) this.parseObject(init);
      else this.parseQuery(typeof init == 'string' ? charAt$1(init, 0) === '?' ? stringSlice$1(init, 1) : init : $toString$1(init));
    }
  };

  URLSearchParamsState.prototype = {
    type: URL_SEARCH_PARAMS,
    bindURL: function (url) {
      this.url = url;
      this.update();
    },
    parseObject: function (object) {
      var iteratorMethod = getIteratorMethod(object);
      var iterator, next, step, entryIterator, entryNext, first, second;

      if (iteratorMethod) {
        iterator = getIterator(object, iteratorMethod);
        next = iterator.next;
        while (!(step = call$2(next, iterator)).done) {
          entryIterator = getIterator(anObject$1(step.value));
          entryNext = entryIterator.next;
          if (
            (first = call$2(entryNext, entryIterator)).done ||
            (second = call$2(entryNext, entryIterator)).done ||
            !call$2(entryNext, entryIterator).done
          ) throw TypeError$2('Expected sequence with length 2');
          push$2(this.entries, { key: $toString$1(first.value), value: $toString$1(second.value) });
        }
      } else for (var key in object) if (hasOwn$3(object, key)) {
        push$2(this.entries, { key: key, value: $toString$1(object[key]) });
      }
    },
    parseQuery: function (query) {
      if (query) {
        var attributes = split$1(query, '&');
        var index = 0;
        var attribute, entry;
        while (index < attributes.length) {
          attribute = attributes[index++];
          if (attribute.length) {
            entry = split$1(attribute, '=');
            push$2(this.entries, {
              key: deserialize(shift$1(entry)),
              value: deserialize(join$1(entry, '='))
            });
          }
        }
      }
    },
    serialize: function () {
      var entries = this.entries;
      var result = [];
      var index = 0;
      var entry;
      while (index < entries.length) {
        entry = entries[index++];
        push$2(result, serialize(entry.key) + '=' + serialize(entry.value));
      } return join$1(result, '&');
    },
    update: function () {
      this.entries.length = 0;
      this.parseQuery(this.url.query);
    },
    updateURL: function () {
      if (this.url) this.url.update();
    }
  };

  // `URLSearchParams` constructor
  // https://url.spec.whatwg.org/#interface-urlsearchparams
  var URLSearchParamsConstructor = function URLSearchParams(/* init */) {
    anInstance$1(this, URLSearchParamsPrototype);
    var init = arguments.length > 0 ? arguments[0] : undefined;
    var state = setInternalState$1(this, new URLSearchParamsState(init));
    if (!DESCRIPTORS$2) this.length = state.entries.length;
  };

  var URLSearchParamsPrototype = URLSearchParamsConstructor.prototype;

  defineBuiltIns(URLSearchParamsPrototype, {
    // `URLSearchParams.prototype.append` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-append
    append: function append(name, value) {
      validateArgumentsLength$1(arguments.length, 2);
      var state = getInternalParamsState(this);
      push$2(state.entries, { key: $toString$1(name), value: $toString$1(value) });
      if (!DESCRIPTORS$2) this.length++;
      state.updateURL();
    },
    // `URLSearchParams.prototype.delete` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-delete
    'delete': function (name) {
      validateArgumentsLength$1(arguments.length, 1);
      var state = getInternalParamsState(this);
      var entries = state.entries;
      var key = $toString$1(name);
      var index = 0;
      while (index < entries.length) {
        if (entries[index].key === key) splice(entries, index, 1);
        else index++;
      }
      if (!DESCRIPTORS$2) this.length = entries.length;
      state.updateURL();
    },
    // `URLSearchParams.prototype.get` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-get
    get: function get(name) {
      validateArgumentsLength$1(arguments.length, 1);
      var entries = getInternalParamsState(this).entries;
      var key = $toString$1(name);
      var index = 0;
      for (; index < entries.length; index++) {
        if (entries[index].key === key) return entries[index].value;
      }
      return null;
    },
    // `URLSearchParams.prototype.getAll` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-getall
    getAll: function getAll(name) {
      validateArgumentsLength$1(arguments.length, 1);
      var entries = getInternalParamsState(this).entries;
      var key = $toString$1(name);
      var result = [];
      var index = 0;
      for (; index < entries.length; index++) {
        if (entries[index].key === key) push$2(result, entries[index].value);
      }
      return result;
    },
    // `URLSearchParams.prototype.has` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-has
    has: function has(name) {
      validateArgumentsLength$1(arguments.length, 1);
      var entries = getInternalParamsState(this).entries;
      var key = $toString$1(name);
      var index = 0;
      while (index < entries.length) {
        if (entries[index++].key === key) return true;
      }
      return false;
    },
    // `URLSearchParams.prototype.set` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-set
    set: function set(name, value) {
      validateArgumentsLength$1(arguments.length, 1);
      var state = getInternalParamsState(this);
      var entries = state.entries;
      var found = false;
      var key = $toString$1(name);
      var val = $toString$1(value);
      var index = 0;
      var entry;
      for (; index < entries.length; index++) {
        entry = entries[index];
        if (entry.key === key) {
          if (found) splice(entries, index--, 1);
          else {
            found = true;
            entry.value = val;
          }
        }
      }
      if (!found) push$2(entries, { key: key, value: val });
      if (!DESCRIPTORS$2) this.length = entries.length;
      state.updateURL();
    },
    // `URLSearchParams.prototype.sort` method
    // https://url.spec.whatwg.org/#dom-urlsearchparams-sort
    sort: function sort() {
      var state = getInternalParamsState(this);
      arraySort(state.entries, function (a, b) {
        return a.key > b.key ? 1 : -1;
      });
      state.updateURL();
    },
    // `URLSearchParams.prototype.forEach` method
    forEach: function forEach(callback /* , thisArg */) {
      var entries = getInternalParamsState(this).entries;
      var boundFunction = bind$2(callback, arguments.length > 1 ? arguments[1] : undefined);
      var index = 0;
      var entry;
      while (index < entries.length) {
        entry = entries[index++];
        boundFunction(entry.value, entry.key, this);
      }
    },
    // `URLSearchParams.prototype.keys` method
    keys: function keys() {
      return new URLSearchParamsIterator(this, 'keys');
    },
    // `URLSearchParams.prototype.values` method
    values: function values() {
      return new URLSearchParamsIterator(this, 'values');
    },
    // `URLSearchParams.prototype.entries` method
    entries: function entries() {
      return new URLSearchParamsIterator(this, 'entries');
    }
  }, { enumerable: true });

  // `URLSearchParams.prototype[@@iterator]` method
  defineBuiltIn$1(URLSearchParamsPrototype, ITERATOR, URLSearchParamsPrototype.entries, { name: 'entries' });

  // `URLSearchParams.prototype.toString` method
  // https://url.spec.whatwg.org/#urlsearchparams-stringification-behavior
  defineBuiltIn$1(URLSearchParamsPrototype, 'toString', function toString() {
    return getInternalParamsState(this).serialize();
  }, { enumerable: true });

  // `URLSearchParams.prototype.size` getter
  // https://github.com/whatwg/url/pull/734
  if (DESCRIPTORS$2) defineBuiltInAccessor$1(URLSearchParamsPrototype, 'size', {
    get: function size() {
      return getInternalParamsState(this).entries.length;
    },
    configurable: true,
    enumerable: true
  });

  setToStringTag$1(URLSearchParamsConstructor, URL_SEARCH_PARAMS);

  $$9({ global: true, constructor: true, forced: !USE_NATIVE_URL$1 }, {
    URLSearchParams: URLSearchParamsConstructor
  });

  // Wrap `fetch` and `Request` for correct work with polyfilled `URLSearchParams`
  if (!USE_NATIVE_URL$1 && isCallable(Headers)) {
    var headersHas = uncurryThis$3(HeadersPrototype.has);
    var headersSet = uncurryThis$3(HeadersPrototype.set);

    var wrapRequestOptions = function (init) {
      if (isObject$5(init)) {
        var body = init.body;
        var headers;
        if (classof(body) === URL_SEARCH_PARAMS) {
          headers = init.headers ? new Headers(init.headers) : new Headers();
          if (!headersHas(headers, 'content-type')) {
            headersSet(headers, 'content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
          }
          return create(init, {
            body: createPropertyDescriptor(0, $toString$1(body)),
            headers: createPropertyDescriptor(0, headers)
          });
        }
      } return init;
    };

    if (isCallable(nativeFetch)) {
      $$9({ global: true, enumerable: true, dontCallGetSet: true, forced: true }, {
        fetch: function fetch(input /* , init */) {
          return nativeFetch(input, arguments.length > 1 ? wrapRequestOptions(arguments[1]) : {});
        }
      });
    }

    if (isCallable(NativeRequest)) {
      var RequestConstructor = function Request(input /* , init */) {
        anInstance$1(this, RequestPrototype);
        return new NativeRequest(input, arguments.length > 1 ? wrapRequestOptions(arguments[1]) : {});
      };

      RequestPrototype.constructor = RequestConstructor;
      RequestConstructor.prototype = RequestPrototype;

      $$9({ global: true, constructor: true, dontCallGetSet: true, forced: true }, {
        Request: RequestConstructor
      });
    }
  }

  var web_urlSearchParams_constructor = {
    URLSearchParams: URLSearchParamsConstructor,
    getState: getInternalParamsState
  };

  // TODO: in core-js@4, move /modules/ dependencies to public entries for better optimization by tools like `preset-env`

  var $$8 = _export;
  var DESCRIPTORS$1 = descriptors;
  var USE_NATIVE_URL = urlConstructorDetection;
  var global$1 = global$u;
  var bind$1 = functionBindContext;
  var uncurryThis$2 = functionUncurryThis;
  var defineBuiltIn = defineBuiltIn$e;
  var defineBuiltInAccessor = defineBuiltInAccessor$7;
  var anInstance = anInstance$6;
  var hasOwn$2 = hasOwnProperty_1;
  var assign = objectAssign;
  var arrayFrom = arrayFrom$1;
  var arraySlice = arraySliceSimple;
  var codeAt = stringMultibyte.codeAt;
  var toASCII = stringPunycodeToAscii;
  var $toString = toString$i;
  var setToStringTag = setToStringTag$8;
  var validateArgumentsLength = validateArgumentsLength$3;
  var URLSearchParamsModule = web_urlSearchParams_constructor;
  var InternalStateModule = internalState;

  var setInternalState = InternalStateModule.set;
  var getInternalURLState = InternalStateModule.getterFor('URL');
  var URLSearchParams$1 = URLSearchParamsModule.URLSearchParams;
  var getInternalSearchParamsState = URLSearchParamsModule.getState;

  var NativeURL = global$1.URL;
  var TypeError$1 = global$1.TypeError;
  var parseInt$1 = global$1.parseInt;
  var floor = Math.floor;
  var pow = Math.pow;
  var charAt = uncurryThis$2(''.charAt);
  var exec = uncurryThis$2(/./.exec);
  var join = uncurryThis$2([].join);
  var numberToString = uncurryThis$2(1.0.toString);
  var pop = uncurryThis$2([].pop);
  var push$1 = uncurryThis$2([].push);
  var replace = uncurryThis$2(''.replace);
  var shift = uncurryThis$2([].shift);
  var split = uncurryThis$2(''.split);
  var stringSlice = uncurryThis$2(''.slice);
  var toLowerCase = uncurryThis$2(''.toLowerCase);
  var unshift = uncurryThis$2([].unshift);

  var INVALID_AUTHORITY = 'Invalid authority';
  var INVALID_SCHEME = 'Invalid scheme';
  var INVALID_HOST = 'Invalid host';
  var INVALID_PORT = 'Invalid port';

  var ALPHA = /[a-z]/i;
  // eslint-disable-next-line regexp/no-obscure-range -- safe
  var ALPHANUMERIC = /[\d+-.a-z]/i;
  var DIGIT = /\d/;
  var HEX_START = /^0x/i;
  var OCT = /^[0-7]+$/;
  var DEC = /^\d+$/;
  var HEX = /^[\da-f]+$/i;
  /* eslint-disable regexp/no-control-character -- safe */
  var FORBIDDEN_HOST_CODE_POINT = /[\0\t\n\r #%/:<>?@[\\\]^|]/;
  var FORBIDDEN_HOST_CODE_POINT_EXCLUDING_PERCENT = /[\0\t\n\r #/:<>?@[\\\]^|]/;
  var LEADING_C0_CONTROL_OR_SPACE = /^[\u0000-\u0020]+/;
  var TRAILING_C0_CONTROL_OR_SPACE = /(^|[^\u0000-\u0020])[\u0000-\u0020]+$/;
  var TAB_AND_NEW_LINE = /[\t\n\r]/g;
  /* eslint-enable regexp/no-control-character -- safe */
  var EOF;

  // https://url.spec.whatwg.org/#ipv4-number-parser
  var parseIPv4 = function (input) {
    var parts = split(input, '.');
    var partsLength, numbers, index, part, radix, number, ipv4;
    if (parts.length && parts[parts.length - 1] == '') {
      parts.length--;
    }
    partsLength = parts.length;
    if (partsLength > 4) return input;
    numbers = [];
    for (index = 0; index < partsLength; index++) {
      part = parts[index];
      if (part == '') return input;
      radix = 10;
      if (part.length > 1 && charAt(part, 0) == '0') {
        radix = exec(HEX_START, part) ? 16 : 8;
        part = stringSlice(part, radix == 8 ? 1 : 2);
      }
      if (part === '') {
        number = 0;
      } else {
        if (!exec(radix == 10 ? DEC : radix == 8 ? OCT : HEX, part)) return input;
        number = parseInt$1(part, radix);
      }
      push$1(numbers, number);
    }
    for (index = 0; index < partsLength; index++) {
      number = numbers[index];
      if (index == partsLength - 1) {
        if (number >= pow(256, 5 - partsLength)) return null;
      } else if (number > 255) return null;
    }
    ipv4 = pop(numbers);
    for (index = 0; index < numbers.length; index++) {
      ipv4 += numbers[index] * pow(256, 3 - index);
    }
    return ipv4;
  };

  // https://url.spec.whatwg.org/#concept-ipv6-parser
  // eslint-disable-next-line max-statements -- TODO
  var parseIPv6 = function (input) {
    var address = [0, 0, 0, 0, 0, 0, 0, 0];
    var pieceIndex = 0;
    var compress = null;
    var pointer = 0;
    var value, length, numbersSeen, ipv4Piece, number, swaps, swap;

    var chr = function () {
      return charAt(input, pointer);
    };

    if (chr() == ':') {
      if (charAt(input, 1) != ':') return;
      pointer += 2;
      pieceIndex++;
      compress = pieceIndex;
    }
    while (chr()) {
      if (pieceIndex == 8) return;
      if (chr() == ':') {
        if (compress !== null) return;
        pointer++;
        pieceIndex++;
        compress = pieceIndex;
        continue;
      }
      value = length = 0;
      while (length < 4 && exec(HEX, chr())) {
        value = value * 16 + parseInt$1(chr(), 16);
        pointer++;
        length++;
      }
      if (chr() == '.') {
        if (length == 0) return;
        pointer -= length;
        if (pieceIndex > 6) return;
        numbersSeen = 0;
        while (chr()) {
          ipv4Piece = null;
          if (numbersSeen > 0) {
            if (chr() == '.' && numbersSeen < 4) pointer++;
            else return;
          }
          if (!exec(DIGIT, chr())) return;
          while (exec(DIGIT, chr())) {
            number = parseInt$1(chr(), 10);
            if (ipv4Piece === null) ipv4Piece = number;
            else if (ipv4Piece == 0) return;
            else ipv4Piece = ipv4Piece * 10 + number;
            if (ipv4Piece > 255) return;
            pointer++;
          }
          address[pieceIndex] = address[pieceIndex] * 256 + ipv4Piece;
          numbersSeen++;
          if (numbersSeen == 2 || numbersSeen == 4) pieceIndex++;
        }
        if (numbersSeen != 4) return;
        break;
      } else if (chr() == ':') {
        pointer++;
        if (!chr()) return;
      } else if (chr()) return;
      address[pieceIndex++] = value;
    }
    if (compress !== null) {
      swaps = pieceIndex - compress;
      pieceIndex = 7;
      while (pieceIndex != 0 && swaps > 0) {
        swap = address[pieceIndex];
        address[pieceIndex--] = address[compress + swaps - 1];
        address[compress + --swaps] = swap;
      }
    } else if (pieceIndex != 8) return;
    return address;
  };

  var findLongestZeroSequence = function (ipv6) {
    var maxIndex = null;
    var maxLength = 1;
    var currStart = null;
    var currLength = 0;
    var index = 0;
    for (; index < 8; index++) {
      if (ipv6[index] !== 0) {
        if (currLength > maxLength) {
          maxIndex = currStart;
          maxLength = currLength;
        }
        currStart = null;
        currLength = 0;
      } else {
        if (currStart === null) currStart = index;
        ++currLength;
      }
    }
    if (currLength > maxLength) {
      maxIndex = currStart;
      maxLength = currLength;
    }
    return maxIndex;
  };

  // https://url.spec.whatwg.org/#host-serializing
  var serializeHost = function (host) {
    var result, index, compress, ignore0;
    // ipv4
    if (typeof host == 'number') {
      result = [];
      for (index = 0; index < 4; index++) {
        unshift(result, host % 256);
        host = floor(host / 256);
      } return join(result, '.');
    // ipv6
    } else if (typeof host == 'object') {
      result = '';
      compress = findLongestZeroSequence(host);
      for (index = 0; index < 8; index++) {
        if (ignore0 && host[index] === 0) continue;
        if (ignore0) ignore0 = false;
        if (compress === index) {
          result += index ? ':' : '::';
          ignore0 = true;
        } else {
          result += numberToString(host[index], 16);
          if (index < 7) result += ':';
        }
      }
      return '[' + result + ']';
    } return host;
  };

  var C0ControlPercentEncodeSet = {};
  var fragmentPercentEncodeSet = assign({}, C0ControlPercentEncodeSet, {
    ' ': 1, '"': 1, '<': 1, '>': 1, '`': 1
  });
  var pathPercentEncodeSet = assign({}, fragmentPercentEncodeSet, {
    '#': 1, '?': 1, '{': 1, '}': 1
  });
  var userinfoPercentEncodeSet = assign({}, pathPercentEncodeSet, {
    '/': 1, ':': 1, ';': 1, '=': 1, '@': 1, '[': 1, '\\': 1, ']': 1, '^': 1, '|': 1
  });

  var percentEncode = function (chr, set) {
    var code = codeAt(chr, 0);
    return code > 0x20 && code < 0x7F && !hasOwn$2(set, chr) ? chr : encodeURIComponent(chr);
  };

  // https://url.spec.whatwg.org/#special-scheme
  var specialSchemes = {
    ftp: 21,
    file: null,
    http: 80,
    https: 443,
    ws: 80,
    wss: 443
  };

  // https://url.spec.whatwg.org/#windows-drive-letter
  var isWindowsDriveLetter = function (string, normalized) {
    var second;
    return string.length == 2 && exec(ALPHA, charAt(string, 0))
      && ((second = charAt(string, 1)) == ':' || (!normalized && second == '|'));
  };

  // https://url.spec.whatwg.org/#start-with-a-windows-drive-letter
  var startsWithWindowsDriveLetter = function (string) {
    var third;
    return string.length > 1 && isWindowsDriveLetter(stringSlice(string, 0, 2)) && (
      string.length == 2 ||
      ((third = charAt(string, 2)) === '/' || third === '\\' || third === '?' || third === '#')
    );
  };

  // https://url.spec.whatwg.org/#single-dot-path-segment
  var isSingleDot = function (segment) {
    return segment === '.' || toLowerCase(segment) === '%2e';
  };

  // https://url.spec.whatwg.org/#double-dot-path-segment
  var isDoubleDot = function (segment) {
    segment = toLowerCase(segment);
    return segment === '..' || segment === '%2e.' || segment === '.%2e' || segment === '%2e%2e';
  };

  // States:
  var SCHEME_START = {};
  var SCHEME = {};
  var NO_SCHEME = {};
  var SPECIAL_RELATIVE_OR_AUTHORITY = {};
  var PATH_OR_AUTHORITY = {};
  var RELATIVE = {};
  var RELATIVE_SLASH = {};
  var SPECIAL_AUTHORITY_SLASHES = {};
  var SPECIAL_AUTHORITY_IGNORE_SLASHES = {};
  var AUTHORITY = {};
  var HOST = {};
  var HOSTNAME = {};
  var PORT = {};
  var FILE = {};
  var FILE_SLASH = {};
  var FILE_HOST = {};
  var PATH_START = {};
  var PATH = {};
  var CANNOT_BE_A_BASE_URL_PATH = {};
  var QUERY = {};
  var FRAGMENT = {};

  var URLState = function (url, isBase, base) {
    var urlString = $toString(url);
    var baseState, failure, searchParams;
    if (isBase) {
      failure = this.parse(urlString);
      if (failure) throw TypeError$1(failure);
      this.searchParams = null;
    } else {
      if (base !== undefined) baseState = new URLState(base, true);
      failure = this.parse(urlString, null, baseState);
      if (failure) throw TypeError$1(failure);
      searchParams = getInternalSearchParamsState(new URLSearchParams$1());
      searchParams.bindURL(this);
      this.searchParams = searchParams;
    }
  };

  URLState.prototype = {
    type: 'URL',
    // https://url.spec.whatwg.org/#url-parsing
    // eslint-disable-next-line max-statements -- TODO
    parse: function (input, stateOverride, base) {
      var url = this;
      var state = stateOverride || SCHEME_START;
      var pointer = 0;
      var buffer = '';
      var seenAt = false;
      var seenBracket = false;
      var seenPasswordToken = false;
      var codePoints, chr, bufferCodePoints, failure;

      input = $toString(input);

      if (!stateOverride) {
        url.scheme = '';
        url.username = '';
        url.password = '';
        url.host = null;
        url.port = null;
        url.path = [];
        url.query = null;
        url.fragment = null;
        url.cannotBeABaseURL = false;
        input = replace(input, LEADING_C0_CONTROL_OR_SPACE, '');
        input = replace(input, TRAILING_C0_CONTROL_OR_SPACE, '$1');
      }

      input = replace(input, TAB_AND_NEW_LINE, '');

      codePoints = arrayFrom(input);

      while (pointer <= codePoints.length) {
        chr = codePoints[pointer];
        switch (state) {
          case SCHEME_START:
            if (chr && exec(ALPHA, chr)) {
              buffer += toLowerCase(chr);
              state = SCHEME;
            } else if (!stateOverride) {
              state = NO_SCHEME;
              continue;
            } else return INVALID_SCHEME;
            break;

          case SCHEME:
            if (chr && (exec(ALPHANUMERIC, chr) || chr == '+' || chr == '-' || chr == '.')) {
              buffer += toLowerCase(chr);
            } else if (chr == ':') {
              if (stateOverride && (
                (url.isSpecial() != hasOwn$2(specialSchemes, buffer)) ||
                (buffer == 'file' && (url.includesCredentials() || url.port !== null)) ||
                (url.scheme == 'file' && !url.host)
              )) return;
              url.scheme = buffer;
              if (stateOverride) {
                if (url.isSpecial() && specialSchemes[url.scheme] == url.port) url.port = null;
                return;
              }
              buffer = '';
              if (url.scheme == 'file') {
                state = FILE;
              } else if (url.isSpecial() && base && base.scheme == url.scheme) {
                state = SPECIAL_RELATIVE_OR_AUTHORITY;
              } else if (url.isSpecial()) {
                state = SPECIAL_AUTHORITY_SLASHES;
              } else if (codePoints[pointer + 1] == '/') {
                state = PATH_OR_AUTHORITY;
                pointer++;
              } else {
                url.cannotBeABaseURL = true;
                push$1(url.path, '');
                state = CANNOT_BE_A_BASE_URL_PATH;
              }
            } else if (!stateOverride) {
              buffer = '';
              state = NO_SCHEME;
              pointer = 0;
              continue;
            } else return INVALID_SCHEME;
            break;

          case NO_SCHEME:
            if (!base || (base.cannotBeABaseURL && chr != '#')) return INVALID_SCHEME;
            if (base.cannotBeABaseURL && chr == '#') {
              url.scheme = base.scheme;
              url.path = arraySlice(base.path);
              url.query = base.query;
              url.fragment = '';
              url.cannotBeABaseURL = true;
              state = FRAGMENT;
              break;
            }
            state = base.scheme == 'file' ? FILE : RELATIVE;
            continue;

          case SPECIAL_RELATIVE_OR_AUTHORITY:
            if (chr == '/' && codePoints[pointer + 1] == '/') {
              state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
              pointer++;
            } else {
              state = RELATIVE;
              continue;
            } break;

          case PATH_OR_AUTHORITY:
            if (chr == '/') {
              state = AUTHORITY;
              break;
            } else {
              state = PATH;
              continue;
            }

          case RELATIVE:
            url.scheme = base.scheme;
            if (chr == EOF) {
              url.username = base.username;
              url.password = base.password;
              url.host = base.host;
              url.port = base.port;
              url.path = arraySlice(base.path);
              url.query = base.query;
            } else if (chr == '/' || (chr == '\\' && url.isSpecial())) {
              state = RELATIVE_SLASH;
            } else if (chr == '?') {
              url.username = base.username;
              url.password = base.password;
              url.host = base.host;
              url.port = base.port;
              url.path = arraySlice(base.path);
              url.query = '';
              state = QUERY;
            } else if (chr == '#') {
              url.username = base.username;
              url.password = base.password;
              url.host = base.host;
              url.port = base.port;
              url.path = arraySlice(base.path);
              url.query = base.query;
              url.fragment = '';
              state = FRAGMENT;
            } else {
              url.username = base.username;
              url.password = base.password;
              url.host = base.host;
              url.port = base.port;
              url.path = arraySlice(base.path);
              url.path.length--;
              state = PATH;
              continue;
            } break;

          case RELATIVE_SLASH:
            if (url.isSpecial() && (chr == '/' || chr == '\\')) {
              state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
            } else if (chr == '/') {
              state = AUTHORITY;
            } else {
              url.username = base.username;
              url.password = base.password;
              url.host = base.host;
              url.port = base.port;
              state = PATH;
              continue;
            } break;

          case SPECIAL_AUTHORITY_SLASHES:
            state = SPECIAL_AUTHORITY_IGNORE_SLASHES;
            if (chr != '/' || charAt(buffer, pointer + 1) != '/') continue;
            pointer++;
            break;

          case SPECIAL_AUTHORITY_IGNORE_SLASHES:
            if (chr != '/' && chr != '\\') {
              state = AUTHORITY;
              continue;
            } break;

          case AUTHORITY:
            if (chr == '@') {
              if (seenAt) buffer = '%40' + buffer;
              seenAt = true;
              bufferCodePoints = arrayFrom(buffer);
              for (var i = 0; i < bufferCodePoints.length; i++) {
                var codePoint = bufferCodePoints[i];
                if (codePoint == ':' && !seenPasswordToken) {
                  seenPasswordToken = true;
                  continue;
                }
                var encodedCodePoints = percentEncode(codePoint, userinfoPercentEncodeSet);
                if (seenPasswordToken) url.password += encodedCodePoints;
                else url.username += encodedCodePoints;
              }
              buffer = '';
            } else if (
              chr == EOF || chr == '/' || chr == '?' || chr == '#' ||
              (chr == '\\' && url.isSpecial())
            ) {
              if (seenAt && buffer == '') return INVALID_AUTHORITY;
              pointer -= arrayFrom(buffer).length + 1;
              buffer = '';
              state = HOST;
            } else buffer += chr;
            break;

          case HOST:
          case HOSTNAME:
            if (stateOverride && url.scheme == 'file') {
              state = FILE_HOST;
              continue;
            } else if (chr == ':' && !seenBracket) {
              if (buffer == '') return INVALID_HOST;
              failure = url.parseHost(buffer);
              if (failure) return failure;
              buffer = '';
              state = PORT;
              if (stateOverride == HOSTNAME) return;
            } else if (
              chr == EOF || chr == '/' || chr == '?' || chr == '#' ||
              (chr == '\\' && url.isSpecial())
            ) {
              if (url.isSpecial() && buffer == '') return INVALID_HOST;
              if (stateOverride && buffer == '' && (url.includesCredentials() || url.port !== null)) return;
              failure = url.parseHost(buffer);
              if (failure) return failure;
              buffer = '';
              state = PATH_START;
              if (stateOverride) return;
              continue;
            } else {
              if (chr == '[') seenBracket = true;
              else if (chr == ']') seenBracket = false;
              buffer += chr;
            } break;

          case PORT:
            if (exec(DIGIT, chr)) {
              buffer += chr;
            } else if (
              chr == EOF || chr == '/' || chr == '?' || chr == '#' ||
              (chr == '\\' && url.isSpecial()) ||
              stateOverride
            ) {
              if (buffer != '') {
                var port = parseInt$1(buffer, 10);
                if (port > 0xFFFF) return INVALID_PORT;
                url.port = (url.isSpecial() && port === specialSchemes[url.scheme]) ? null : port;
                buffer = '';
              }
              if (stateOverride) return;
              state = PATH_START;
              continue;
            } else return INVALID_PORT;
            break;

          case FILE:
            url.scheme = 'file';
            if (chr == '/' || chr == '\\') state = FILE_SLASH;
            else if (base && base.scheme == 'file') {
              if (chr == EOF) {
                url.host = base.host;
                url.path = arraySlice(base.path);
                url.query = base.query;
              } else if (chr == '?') {
                url.host = base.host;
                url.path = arraySlice(base.path);
                url.query = '';
                state = QUERY;
              } else if (chr == '#') {
                url.host = base.host;
                url.path = arraySlice(base.path);
                url.query = base.query;
                url.fragment = '';
                state = FRAGMENT;
              } else {
                if (!startsWithWindowsDriveLetter(join(arraySlice(codePoints, pointer), ''))) {
                  url.host = base.host;
                  url.path = arraySlice(base.path);
                  url.shortenPath();
                }
                state = PATH;
                continue;
              }
            } else {
              state = PATH;
              continue;
            } break;

          case FILE_SLASH:
            if (chr == '/' || chr == '\\') {
              state = FILE_HOST;
              break;
            }
            if (base && base.scheme == 'file' && !startsWithWindowsDriveLetter(join(arraySlice(codePoints, pointer), ''))) {
              if (isWindowsDriveLetter(base.path[0], true)) push$1(url.path, base.path[0]);
              else url.host = base.host;
            }
            state = PATH;
            continue;

          case FILE_HOST:
            if (chr == EOF || chr == '/' || chr == '\\' || chr == '?' || chr == '#') {
              if (!stateOverride && isWindowsDriveLetter(buffer)) {
                state = PATH;
              } else if (buffer == '') {
                url.host = '';
                if (stateOverride) return;
                state = PATH_START;
              } else {
                failure = url.parseHost(buffer);
                if (failure) return failure;
                if (url.host == 'localhost') url.host = '';
                if (stateOverride) return;
                buffer = '';
                state = PATH_START;
              } continue;
            } else buffer += chr;
            break;

          case PATH_START:
            if (url.isSpecial()) {
              state = PATH;
              if (chr != '/' && chr != '\\') continue;
            } else if (!stateOverride && chr == '?') {
              url.query = '';
              state = QUERY;
            } else if (!stateOverride && chr == '#') {
              url.fragment = '';
              state = FRAGMENT;
            } else if (chr != EOF) {
              state = PATH;
              if (chr != '/') continue;
            } break;

          case PATH:
            if (
              chr == EOF || chr == '/' ||
              (chr == '\\' && url.isSpecial()) ||
              (!stateOverride && (chr == '?' || chr == '#'))
            ) {
              if (isDoubleDot(buffer)) {
                url.shortenPath();
                if (chr != '/' && !(chr == '\\' && url.isSpecial())) {
                  push$1(url.path, '');
                }
              } else if (isSingleDot(buffer)) {
                if (chr != '/' && !(chr == '\\' && url.isSpecial())) {
                  push$1(url.path, '');
                }
              } else {
                if (url.scheme == 'file' && !url.path.length && isWindowsDriveLetter(buffer)) {
                  if (url.host) url.host = '';
                  buffer = charAt(buffer, 0) + ':'; // normalize windows drive letter
                }
                push$1(url.path, buffer);
              }
              buffer = '';
              if (url.scheme == 'file' && (chr == EOF || chr == '?' || chr == '#')) {
                while (url.path.length > 1 && url.path[0] === '') {
                  shift(url.path);
                }
              }
              if (chr == '?') {
                url.query = '';
                state = QUERY;
              } else if (chr == '#') {
                url.fragment = '';
                state = FRAGMENT;
              }
            } else {
              buffer += percentEncode(chr, pathPercentEncodeSet);
            } break;

          case CANNOT_BE_A_BASE_URL_PATH:
            if (chr == '?') {
              url.query = '';
              state = QUERY;
            } else if (chr == '#') {
              url.fragment = '';
              state = FRAGMENT;
            } else if (chr != EOF) {
              url.path[0] += percentEncode(chr, C0ControlPercentEncodeSet);
            } break;

          case QUERY:
            if (!stateOverride && chr == '#') {
              url.fragment = '';
              state = FRAGMENT;
            } else if (chr != EOF) {
              if (chr == "'" && url.isSpecial()) url.query += '%27';
              else if (chr == '#') url.query += '%23';
              else url.query += percentEncode(chr, C0ControlPercentEncodeSet);
            } break;

          case FRAGMENT:
            if (chr != EOF) url.fragment += percentEncode(chr, fragmentPercentEncodeSet);
            break;
        }

        pointer++;
      }
    },
    // https://url.spec.whatwg.org/#host-parsing
    parseHost: function (input) {
      var result, codePoints, index;
      if (charAt(input, 0) == '[') {
        if (charAt(input, input.length - 1) != ']') return INVALID_HOST;
        result = parseIPv6(stringSlice(input, 1, -1));
        if (!result) return INVALID_HOST;
        this.host = result;
      // opaque host
      } else if (!this.isSpecial()) {
        if (exec(FORBIDDEN_HOST_CODE_POINT_EXCLUDING_PERCENT, input)) return INVALID_HOST;
        result = '';
        codePoints = arrayFrom(input);
        for (index = 0; index < codePoints.length; index++) {
          result += percentEncode(codePoints[index], C0ControlPercentEncodeSet);
        }
        this.host = result;
      } else {
        input = toASCII(input);
        if (exec(FORBIDDEN_HOST_CODE_POINT, input)) return INVALID_HOST;
        result = parseIPv4(input);
        if (result === null) return INVALID_HOST;
        this.host = result;
      }
    },
    // https://url.spec.whatwg.org/#cannot-have-a-username-password-port
    cannotHaveUsernamePasswordPort: function () {
      return !this.host || this.cannotBeABaseURL || this.scheme == 'file';
    },
    // https://url.spec.whatwg.org/#include-credentials
    includesCredentials: function () {
      return this.username != '' || this.password != '';
    },
    // https://url.spec.whatwg.org/#is-special
    isSpecial: function () {
      return hasOwn$2(specialSchemes, this.scheme);
    },
    // https://url.spec.whatwg.org/#shorten-a-urls-path
    shortenPath: function () {
      var path = this.path;
      var pathSize = path.length;
      if (pathSize && (this.scheme != 'file' || pathSize != 1 || !isWindowsDriveLetter(path[0], true))) {
        path.length--;
      }
    },
    // https://url.spec.whatwg.org/#concept-url-serializer
    serialize: function () {
      var url = this;
      var scheme = url.scheme;
      var username = url.username;
      var password = url.password;
      var host = url.host;
      var port = url.port;
      var path = url.path;
      var query = url.query;
      var fragment = url.fragment;
      var output = scheme + ':';
      if (host !== null) {
        output += '//';
        if (url.includesCredentials()) {
          output += username + (password ? ':' + password : '') + '@';
        }
        output += serializeHost(host);
        if (port !== null) output += ':' + port;
      } else if (scheme == 'file') output += '//';
      output += url.cannotBeABaseURL ? path[0] : path.length ? '/' + join(path, '/') : '';
      if (query !== null) output += '?' + query;
      if (fragment !== null) output += '#' + fragment;
      return output;
    },
    // https://url.spec.whatwg.org/#dom-url-href
    setHref: function (href) {
      var failure = this.parse(href);
      if (failure) throw TypeError$1(failure);
      this.searchParams.update();
    },
    // https://url.spec.whatwg.org/#dom-url-origin
    getOrigin: function () {
      var scheme = this.scheme;
      var port = this.port;
      if (scheme == 'blob') try {
        return new URLConstructor(scheme.path[0]).origin;
      } catch (error) {
        return 'null';
      }
      if (scheme == 'file' || !this.isSpecial()) return 'null';
      return scheme + '://' + serializeHost(this.host) + (port !== null ? ':' + port : '');
    },
    // https://url.spec.whatwg.org/#dom-url-protocol
    getProtocol: function () {
      return this.scheme + ':';
    },
    setProtocol: function (protocol) {
      this.parse($toString(protocol) + ':', SCHEME_START);
    },
    // https://url.spec.whatwg.org/#dom-url-username
    getUsername: function () {
      return this.username;
    },
    setUsername: function (username) {
      var codePoints = arrayFrom($toString(username));
      if (this.cannotHaveUsernamePasswordPort()) return;
      this.username = '';
      for (var i = 0; i < codePoints.length; i++) {
        this.username += percentEncode(codePoints[i], userinfoPercentEncodeSet);
      }
    },
    // https://url.spec.whatwg.org/#dom-url-password
    getPassword: function () {
      return this.password;
    },
    setPassword: function (password) {
      var codePoints = arrayFrom($toString(password));
      if (this.cannotHaveUsernamePasswordPort()) return;
      this.password = '';
      for (var i = 0; i < codePoints.length; i++) {
        this.password += percentEncode(codePoints[i], userinfoPercentEncodeSet);
      }
    },
    // https://url.spec.whatwg.org/#dom-url-host
    getHost: function () {
      var host = this.host;
      var port = this.port;
      return host === null ? ''
        : port === null ? serializeHost(host)
        : serializeHost(host) + ':' + port;
    },
    setHost: function (host) {
      if (this.cannotBeABaseURL) return;
      this.parse(host, HOST);
    },
    // https://url.spec.whatwg.org/#dom-url-hostname
    getHostname: function () {
      var host = this.host;
      return host === null ? '' : serializeHost(host);
    },
    setHostname: function (hostname) {
      if (this.cannotBeABaseURL) return;
      this.parse(hostname, HOSTNAME);
    },
    // https://url.spec.whatwg.org/#dom-url-port
    getPort: function () {
      var port = this.port;
      return port === null ? '' : $toString(port);
    },
    setPort: function (port) {
      if (this.cannotHaveUsernamePasswordPort()) return;
      port = $toString(port);
      if (port == '') this.port = null;
      else this.parse(port, PORT);
    },
    // https://url.spec.whatwg.org/#dom-url-pathname
    getPathname: function () {
      var path = this.path;
      return this.cannotBeABaseURL ? path[0] : path.length ? '/' + join(path, '/') : '';
    },
    setPathname: function (pathname) {
      if (this.cannotBeABaseURL) return;
      this.path = [];
      this.parse(pathname, PATH_START);
    },
    // https://url.spec.whatwg.org/#dom-url-search
    getSearch: function () {
      var query = this.query;
      return query ? '?' + query : '';
    },
    setSearch: function (search) {
      search = $toString(search);
      if (search == '') {
        this.query = null;
      } else {
        if ('?' == charAt(search, 0)) search = stringSlice(search, 1);
        this.query = '';
        this.parse(search, QUERY);
      }
      this.searchParams.update();
    },
    // https://url.spec.whatwg.org/#dom-url-searchparams
    getSearchParams: function () {
      return this.searchParams.facade;
    },
    // https://url.spec.whatwg.org/#dom-url-hash
    getHash: function () {
      var fragment = this.fragment;
      return fragment ? '#' + fragment : '';
    },
    setHash: function (hash) {
      hash = $toString(hash);
      if (hash == '') {
        this.fragment = null;
        return;
      }
      if ('#' == charAt(hash, 0)) hash = stringSlice(hash, 1);
      this.fragment = '';
      this.parse(hash, FRAGMENT);
    },
    update: function () {
      this.query = this.searchParams.serialize() || null;
    }
  };

  // `URL` constructor
  // https://url.spec.whatwg.org/#url-class
  var URLConstructor = function URL(url /* , base */) {
    var that = anInstance(this, URLPrototype);
    var base = validateArgumentsLength(arguments.length, 1) > 1 ? arguments[1] : undefined;
    var state = setInternalState(that, new URLState(url, false, base));
    if (!DESCRIPTORS$1) {
      that.href = state.serialize();
      that.origin = state.getOrigin();
      that.protocol = state.getProtocol();
      that.username = state.getUsername();
      that.password = state.getPassword();
      that.host = state.getHost();
      that.hostname = state.getHostname();
      that.port = state.getPort();
      that.pathname = state.getPathname();
      that.search = state.getSearch();
      that.searchParams = state.getSearchParams();
      that.hash = state.getHash();
    }
  };

  var URLPrototype = URLConstructor.prototype;

  var accessorDescriptor = function (getter, setter) {
    return {
      get: function () {
        return getInternalURLState(this)[getter]();
      },
      set: setter && function (value) {
        return getInternalURLState(this)[setter](value);
      },
      configurable: true,
      enumerable: true
    };
  };

  if (DESCRIPTORS$1) {
    // `URL.prototype.href` accessors pair
    // https://url.spec.whatwg.org/#dom-url-href
    defineBuiltInAccessor(URLPrototype, 'href', accessorDescriptor('serialize', 'setHref'));
    // `URL.prototype.origin` getter
    // https://url.spec.whatwg.org/#dom-url-origin
    defineBuiltInAccessor(URLPrototype, 'origin', accessorDescriptor('getOrigin'));
    // `URL.prototype.protocol` accessors pair
    // https://url.spec.whatwg.org/#dom-url-protocol
    defineBuiltInAccessor(URLPrototype, 'protocol', accessorDescriptor('getProtocol', 'setProtocol'));
    // `URL.prototype.username` accessors pair
    // https://url.spec.whatwg.org/#dom-url-username
    defineBuiltInAccessor(URLPrototype, 'username', accessorDescriptor('getUsername', 'setUsername'));
    // `URL.prototype.password` accessors pair
    // https://url.spec.whatwg.org/#dom-url-password
    defineBuiltInAccessor(URLPrototype, 'password', accessorDescriptor('getPassword', 'setPassword'));
    // `URL.prototype.host` accessors pair
    // https://url.spec.whatwg.org/#dom-url-host
    defineBuiltInAccessor(URLPrototype, 'host', accessorDescriptor('getHost', 'setHost'));
    // `URL.prototype.hostname` accessors pair
    // https://url.spec.whatwg.org/#dom-url-hostname
    defineBuiltInAccessor(URLPrototype, 'hostname', accessorDescriptor('getHostname', 'setHostname'));
    // `URL.prototype.port` accessors pair
    // https://url.spec.whatwg.org/#dom-url-port
    defineBuiltInAccessor(URLPrototype, 'port', accessorDescriptor('getPort', 'setPort'));
    // `URL.prototype.pathname` accessors pair
    // https://url.spec.whatwg.org/#dom-url-pathname
    defineBuiltInAccessor(URLPrototype, 'pathname', accessorDescriptor('getPathname', 'setPathname'));
    // `URL.prototype.search` accessors pair
    // https://url.spec.whatwg.org/#dom-url-search
    defineBuiltInAccessor(URLPrototype, 'search', accessorDescriptor('getSearch', 'setSearch'));
    // `URL.prototype.searchParams` getter
    // https://url.spec.whatwg.org/#dom-url-searchparams
    defineBuiltInAccessor(URLPrototype, 'searchParams', accessorDescriptor('getSearchParams'));
    // `URL.prototype.hash` accessors pair
    // https://url.spec.whatwg.org/#dom-url-hash
    defineBuiltInAccessor(URLPrototype, 'hash', accessorDescriptor('getHash', 'setHash'));
  }

  // `URL.prototype.toJSON` method
  // https://url.spec.whatwg.org/#dom-url-tojson
  defineBuiltIn(URLPrototype, 'toJSON', function toJSON() {
    return getInternalURLState(this).serialize();
  }, { enumerable: true });

  // `URL.prototype.toString` method
  // https://url.spec.whatwg.org/#URL-stringification-behavior
  defineBuiltIn(URLPrototype, 'toString', function toString() {
    return getInternalURLState(this).serialize();
  }, { enumerable: true });

  if (NativeURL) {
    var nativeCreateObjectURL = NativeURL.createObjectURL;
    var nativeRevokeObjectURL = NativeURL.revokeObjectURL;
    // `URL.createObjectURL` method
    // https://developer.mozilla.org/en-US/docs/Web/API/URL/createObjectURL
    if (nativeCreateObjectURL) defineBuiltIn(URLConstructor, 'createObjectURL', bind$1(nativeCreateObjectURL, NativeURL));
    // `URL.revokeObjectURL` method
    // https://developer.mozilla.org/en-US/docs/Web/API/URL/revokeObjectURL
    if (nativeRevokeObjectURL) defineBuiltIn(URLConstructor, 'revokeObjectURL', bind$1(nativeRevokeObjectURL, NativeURL));
  }

  setToStringTag(URLConstructor, 'URL');

  $$8({ global: true, constructor: true, forced: !USE_NATIVE_URL, sham: !DESCRIPTORS$1 }, {
    URL: URLConstructor
  });

  var $$7 = _export;
  var $find = arrayIteration.find;
  var addToUnscopables$1 = addToUnscopables$6;

  var FIND = 'find';
  var SKIPS_HOLES = true;

  // Shouldn't skip holes
  if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

  // `Array.prototype.find` method
  // https://tc39.es/ecma262/#sec-array.prototype.find
  $$7({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
    find: function find(callbackfn /* , that = undefined */) {
      return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables$1(FIND);

  var call$1 = functionCall;
  var fixRegExpWellKnownSymbolLogic = fixRegexpWellKnownSymbolLogic;
  var anObject = anObject$r;
  var isNullOrUndefined = isNullOrUndefined$b;
  var requireObjectCoercible$1 = requireObjectCoercible$d;
  var sameValue = sameValue$1;
  var toString$1 = toString$i;
  var getMethod = getMethod$7;
  var regExpExec = regexpExecAbstract;

  // @@search logic
  fixRegExpWellKnownSymbolLogic('search', function (SEARCH, nativeSearch, maybeCallNative) {
    return [
      // `String.prototype.search` method
      // https://tc39.es/ecma262/#sec-string.prototype.search
      function search(regexp) {
        var O = requireObjectCoercible$1(this);
        var searcher = isNullOrUndefined(regexp) ? undefined : getMethod(regexp, SEARCH);
        return searcher ? call$1(searcher, regexp, O) : new RegExp(regexp)[SEARCH](toString$1(O));
      },
      // `RegExp.prototype[@@search]` method
      // https://tc39.es/ecma262/#sec-regexp.prototype-@@search
      function (string) {
        var rx = anObject(this);
        var S = toString$1(string);
        var res = maybeCallNative(nativeSearch, rx, S);

        if (res.done) return res.value;

        var previousLastIndex = rx.lastIndex;
        if (!sameValue(previousLastIndex, 0)) rx.lastIndex = 0;
        var result = regExpExec(rx, S);
        if (!sameValue(rx.lastIndex, previousLastIndex)) rx.lastIndex = previousLastIndex;
        return result === null ? -1 : result.index;
      }
    ];
  });

  var $$6 = _export;
  var fails$1 = fails$I;
  var toObject$1 = toObject$e;
  var nativeGetPrototypeOf = objectGetPrototypeOf$1;
  var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

  var FAILS_ON_PRIMITIVES$1 = fails$1(function () { nativeGetPrototypeOf(1); });

  // `Object.getPrototypeOf` method
  // https://tc39.es/ecma262/#sec-object.getprototypeof
  $$6({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$1, sham: !CORRECT_PROTOTYPE_GETTER }, {
    getPrototypeOf: function getPrototypeOf(it) {
      return nativeGetPrototypeOf(toObject$1(it));
    }
  });

  var $$5 = _export;

  // `Number.isNaN` method
  // https://tc39.es/ecma262/#sec-number.isnan
  $$5({ target: 'Number', stat: true }, {
    isNaN: function isNaN(number) {
      // eslint-disable-next-line no-self-compare -- NaN check
      return number != number;
    }
  });

  var $$4 = _export;
  var uncurryThis$1 = functionUncurryThisClause;
  var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
  var toLength = toLength$6;
  var toString = toString$i;
  var notARegExp = notARegexp;
  var requireObjectCoercible = requireObjectCoercible$d;
  var correctIsRegExpLogic = correctIsRegexpLogic;

  // eslint-disable-next-line es/no-string-prototype-endswith -- safe
  var nativeEndsWith = uncurryThis$1(''.endsWith);
  var slice = uncurryThis$1(''.slice);
  var min = Math.min;

  var CORRECT_IS_REGEXP_LOGIC = correctIsRegExpLogic('endsWith');
  // https://github.com/zloirock/core-js/pull/702
  var MDN_POLYFILL_BUG = !CORRECT_IS_REGEXP_LOGIC && !!function () {
    var descriptor = getOwnPropertyDescriptor(String.prototype, 'endsWith');
    return descriptor && !descriptor.writable;
  }();

  // `String.prototype.endsWith` method
  // https://tc39.es/ecma262/#sec-string.prototype.endswith
  $$4({ target: 'String', proto: true, forced: !MDN_POLYFILL_BUG && !CORRECT_IS_REGEXP_LOGIC }, {
    endsWith: function endsWith(searchString /* , endPosition = @length */) {
      var that = toString(requireObjectCoercible(this));
      notARegExp(searchString);
      var endPosition = arguments.length > 1 ? arguments[1] : undefined;
      var len = that.length;
      var end = endPosition === undefined ? len : min(toLength(endPosition), len);
      var search = toString(searchString);
      return nativeEndsWith
        ? nativeEndsWith(that, search, end)
        : slice(that, end - search.length, end) === search;
    }
  });

  var DESCRIPTORS = descriptors;
  var uncurryThis = functionUncurryThis;
  var objectKeys = objectKeys$4;
  var toIndexedObject = toIndexedObject$a;
  var $propertyIsEnumerable = objectPropertyIsEnumerable.f;

  var propertyIsEnumerable = uncurryThis($propertyIsEnumerable);
  var push = uncurryThis([].push);

  // `Object.{ entries, values }` methods implementation
  var createMethod = function (TO_ENTRIES) {
    return function (it) {
      var O = toIndexedObject(it);
      var keys = objectKeys(O);
      var length = keys.length;
      var i = 0;
      var result = [];
      var key;
      while (length > i) {
        key = keys[i++];
        if (!DESCRIPTORS || propertyIsEnumerable(O, key)) {
          push(result, TO_ENTRIES ? [key, O[key]] : O[key]);
        }
      }
      return result;
    };
  };

  var objectToArray = {
    // `Object.entries` method
    // https://tc39.es/ecma262/#sec-object.entries
    entries: createMethod(true),
    // `Object.values` method
    // https://tc39.es/ecma262/#sec-object.values
    values: createMethod(false)
  };

  var $$3 = _export;
  var $values = objectToArray.values;

  // `Object.values` method
  // https://tc39.es/ecma262/#sec-object.values
  $$3({ target: 'Object', stat: true }, {
    values: function values(O) {
      return $values(O);
    }
  });

  var isArray$3 = isArray$a;
  var lengthOfArrayLike$1 = lengthOfArrayLike$c;
  var doesNotExceedSafeInteger = doesNotExceedSafeInteger$3;
  var bind = functionBindContext;

  // `FlattenIntoArray` abstract operation
  // https://tc39.github.io/proposal-flatMap/#sec-FlattenIntoArray
  var flattenIntoArray$1 = function (target, original, source, sourceLen, start, depth, mapper, thisArg) {
    var targetIndex = start;
    var sourceIndex = 0;
    var mapFn = mapper ? bind(mapper, thisArg) : false;
    var element, elementLen;

    while (sourceIndex < sourceLen) {
      if (sourceIndex in source) {
        element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

        if (depth > 0 && isArray$3(element)) {
          elementLen = lengthOfArrayLike$1(element);
          targetIndex = flattenIntoArray$1(target, original, element, elementLen, targetIndex, depth - 1) - 1;
        } else {
          doesNotExceedSafeInteger(targetIndex + 1);
          target[targetIndex] = element;
        }

        targetIndex++;
      }
      sourceIndex++;
    }
    return targetIndex;
  };

  var flattenIntoArray_1 = flattenIntoArray$1;

  var $$2 = _export;
  var flattenIntoArray = flattenIntoArray_1;
  var toObject = toObject$e;
  var lengthOfArrayLike = lengthOfArrayLike$c;
  var toIntegerOrInfinity = toIntegerOrInfinity$8;
  var arraySpeciesCreate = arraySpeciesCreate$4;

  // `Array.prototype.flat` method
  // https://tc39.es/ecma262/#sec-array.prototype.flat
  $$2({ target: 'Array', proto: true }, {
    flat: function flat(/* depthArg = 1 */) {
      var depthArg = arguments.length ? arguments[0] : undefined;
      var O = toObject(this);
      var sourceLen = lengthOfArrayLike(O);
      var A = arraySpeciesCreate(O, 0);
      A.length = flattenIntoArray(A, O, O, sourceLen, 0, depthArg === undefined ? 1 : toIntegerOrInfinity(depthArg));
      return A;
    }
  });

  // this method was added to unscopables after implementation
  // in popular engines, so it's moved to a separate module
  var addToUnscopables = addToUnscopables$6;

  // https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
  addToUnscopables('flat');

  var $$1 = _export;
  var FREEZING = freezing;
  var fails = fails$I;
  var isObject$4 = isObject$q;
  var onFreeze = internalMetadata.exports.onFreeze;

  // eslint-disable-next-line es/no-object-freeze -- safe
  var $freeze = Object.freeze;
  var FAILS_ON_PRIMITIVES = fails(function () { $freeze(1); });

  // `Object.freeze` method
  // https://tc39.es/ecma262/#sec-object.freeze
  $$1({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES, sham: !FREEZING }, {
    freeze: function freeze(it) {
      return $freeze && isObject$4(it) ? $freeze(onFreeze(it)) : it;
    }
  });

  var mediaManager = {};

  var $ = _export;
  var call = functionCall;

  // `URL.prototype.toJSON` method
  // https://url.spec.whatwg.org/#dom-url-tojson
  $({ target: 'URL', proto: true, enumerable: true }, {
    toJSON: function toJSON() {
      return call(URL.prototype.toString, this);
    }
  });

  var cjs$1 = {};

  /*! (c) 2020 Andrea Giammarchi */

  var $parse = JSON.parse,
    $stringify = JSON.stringify;
  var keys = Object.keys;
  var Primitive = String; // it could be Number
  var primitive = 'string'; // it could be 'number'

  var ignore = {};
  var object = 'object';
  var noop = function noop(_, value) {
    return value;
  };
  var primitives = function primitives(value) {
    return value instanceof Primitive ? Primitive(value) : value;
  };
  var Primitives = function Primitives(_, value) {
    return typeof value === primitive ? new Primitive(value) : value;
  };
  var revive = function revive(input, parsed, output, $) {
    var lazy = [];
    for (var ke = keys(output), length = ke.length, y = 0; y < length; y++) {
      var k = ke[y];
      var value = output[k];
      if (value instanceof Primitive) {
        var tmp = input[value];
        if (typeof tmp === object && !parsed.has(tmp)) {
          parsed.add(tmp);
          output[k] = ignore;
          lazy.push({
            k: k,
            a: [input, parsed, tmp, $]
          });
        } else output[k] = $.call(output, k, tmp);
      } else if (output[k] !== ignore) output[k] = $.call(output, k, value);
    }
    for (var _length = lazy.length, i = 0; i < _length; i++) {
      var _lazy$i = lazy[i],
        _k = _lazy$i.k,
        a = _lazy$i.a;
      output[_k] = $.call(output, _k, revive.apply(null, a));
    }
    return output;
  };
  var set$2 = function set(known, input, value) {
    var index = Primitive(input.push(value) - 1);
    known.set(value, index);
    return index;
  };
  var parse = function parse(text, reviver) {
    var input = $parse(text, Primitives).map(primitives);
    var value = input[0];
    var $ = reviver || noop;
    var tmp = typeof value === object && value ? revive(input, new Set(), value, $) : value;
    return $.call({
      '': tmp
    }, '', tmp);
  };
  cjs$1.parse = parse;
  var stringify = function stringify(value, replacer, space) {
    var $ = replacer && typeof replacer === object ? function (k, v) {
      return k === '' || -1 < replacer.indexOf(k) ? v : void 0;
    } : replacer || noop;
    var known = new Map();
    var input = [];
    var output = [];
    var i = +set$2(known, input, $.call({
      '': value
    }, '', value));
    var firstRun = !i;
    while (i < input.length) {
      firstRun = true;
      output[i] = $stringify(input[i++], replace, space);
    }
    return '[' + output.join(',') + ']';
    function replace(key, value) {
      if (firstRun) {
        firstRun = !firstRun;
        return value;
      }
      var after = $.call(this, key, value);
      switch (typeof after) {
        case object:
          if (after === null) return after;
        case primitive:
          return known.get(after) || set$2(known, input, after);
      }
      return after;
    }
  };
  cjs$1.stringify = stringify;
  var toJSON = function toJSON(any) {
    return $parse(stringify(any));
  };
  cjs$1.toJSON = toJSON;
  var fromJSON = function fromJSON(any) {
    return parse($stringify(any));
  };
  cjs$1.fromJSON = fromJSON;

  var _mutations;
  function makeMap$2(str, expectsLowerCase) {
    var map = Object.create(null);
    var list = str.split(',');
    for (var i = 0; i < list.length; i++) {
      map[list[i]] = true;
    }
    return expectsLowerCase ? function (val) {
      return !!map[val.toLowerCase()];
    } : function (val) {
      return !!map[val];
    };
  }
  var NOOP$1 = function NOOP$1() {};
  var extend$2 = Object.assign;
  var hasOwnProperty$1 = Object.prototype.hasOwnProperty;
  var hasOwn$1 = function hasOwn$1(val, key) {
    return hasOwnProperty$1.call(val, key);
  };
  var isArray$2 = Array.isArray;
  var isMap$1 = function isMap$1(val) {
    return toTypeString$1(val) === '[object Map]';
  };
  var isFunction$2 = function isFunction$2(val) {
    return typeof val === 'function';
  };
  var isString$2 = function isString$2(val) {
    return typeof val === 'string';
  };
  var isSymbol = function isSymbol(val) {
    return typeof val === 'symbol';
  };
  var isObject$3 = function isObject$3(val) {
    return val !== null && typeof val === 'object';
  };
  var objectToString$1 = Object.prototype.toString;
  var toTypeString$1 = function toTypeString$1(value) {
    return objectToString$1.call(value);
  };
  var toRawType = function toRawType(value) {
    // extract "RawType" from strings like "[object RawType]"
    return toTypeString$1(value).slice(8, -1);
  };
  var isIntegerKey = function isIntegerKey(key) {
    return isString$2(key) && key !== 'NaN' && key[0] !== '-' && '' + parseInt(key, 10) === key;
  };
  // compare whether a value has changed, accounting for NaN.
  var hasChanged$1 = function hasChanged$1(value, oldValue) {
    return !Object.is(value, oldValue);
  };
  var def$1 = function def$1(obj, key, value) {
    Object.defineProperty(obj, key, {
      configurable: true,
      enumerable: false,
      value: value
    });
  };
  var toNumber$2 = function toNumber$2(val) {
    var n = parseFloat(val);
    return isNaN(n) ? val : n;
  };
  var activeEffectScope;
  var EffectScope = /*#__PURE__*/function () {
    function EffectScope(detached) {
      if (detached === void 0) {
        detached = false;
      }
      this.detached = detached;
      /**
       * @internal
       */
      this.active = true;
      /**
       * @internal
       */
      this.effects = [];
      /**
       * @internal
       */
      this.cleanups = [];
      this.parent = activeEffectScope;
      if (!detached && activeEffectScope) {
        this.index = (activeEffectScope.scopes || (activeEffectScope.scopes = [])).push(this) - 1;
      }
    }
    var _proto = EffectScope.prototype;
    _proto.run = function run(fn) {
      if (this.active) {
        var currentEffectScope = activeEffectScope;
        try {
          activeEffectScope = this;
          return fn();
        } finally {
          activeEffectScope = currentEffectScope;
        }
      }
    }
    /**
     * This should only be called on non-detached scopes
     * @internal
     */;
    _proto.on = function on() {
      activeEffectScope = this;
    }
    /**
     * This should only be called on non-detached scopes
     * @internal
     */;
    _proto.off = function off() {
      activeEffectScope = this.parent;
    };
    _proto.stop = function stop(fromParent) {
      if (this.active) {
        var i, l;
        for (i = 0, l = this.effects.length; i < l; i++) {
          this.effects[i].stop();
        }
        for (i = 0, l = this.cleanups.length; i < l; i++) {
          this.cleanups[i]();
        }
        if (this.scopes) {
          for (i = 0, l = this.scopes.length; i < l; i++) {
            this.scopes[i].stop(true);
          }
        }
        // nested scope, dereference from parent to avoid memory leaks
        if (!this.detached && this.parent && !fromParent) {
          // optimized O(1) removal
          var last = this.parent.scopes.pop();
          if (last && last !== this) {
            this.parent.scopes[this.index] = last;
            last.index = this.index;
          }
        }
        this.parent = undefined;
        this.active = false;
      }
    };
    return EffectScope;
  }();
  function effectScope(detached) {
    return new EffectScope(detached);
  }
  function recordEffectScope(effect, scope) {
    if (scope === void 0) {
      scope = activeEffectScope;
    }
    if (scope && scope.active) {
      scope.effects.push(effect);
    }
  }
  var createDep = function createDep(effects) {
    var dep = new Set(effects);
    dep.w = 0;
    dep.n = 0;
    return dep;
  };
  var wasTracked = function wasTracked(dep) {
    return (dep.w & trackOpBit) > 0;
  };
  var newTracked = function newTracked(dep) {
    return (dep.n & trackOpBit) > 0;
  };
  var initDepMarkers = function initDepMarkers(_ref) {
    var deps = _ref.deps;
    if (deps.length) {
      for (var i = 0; i < deps.length; i++) {
        deps[i].w |= trackOpBit; // set was tracked
      }
    }
  };

  var finalizeDepMarkers = function finalizeDepMarkers(effect) {
    var deps = effect.deps;
    if (deps.length) {
      var ptr = 0;
      for (var i = 0; i < deps.length; i++) {
        var dep = deps[i];
        if (wasTracked(dep) && !newTracked(dep)) {
          dep.delete(effect);
        } else {
          deps[ptr++] = dep;
        }
        // clear bits
        dep.w &= ~trackOpBit;
        dep.n &= ~trackOpBit;
      }
      deps.length = ptr;
    }
  };
  var targetMap = new WeakMap();
  // The number of effects currently being tracked recursively.
  var effectTrackDepth = 0;
  var trackOpBit = 1;
  /**
   * The bitwise track markers support at most 30 levels of recursion.
   * This value is chosen to enable modern JS engines to use a SMI on all platforms.
   * When recursion depth is greater, fall back to using a full cleanup.
   */
  var maxMarkerBits = 30;
  var activeEffect;
  var ITERATE_KEY = Symbol('');
  var MAP_KEY_ITERATE_KEY = Symbol('');
  var ReactiveEffect = /*#__PURE__*/function () {
    function ReactiveEffect(fn, scheduler, scope) {
      if (scheduler === void 0) {
        scheduler = null;
      }
      this.fn = fn;
      this.scheduler = scheduler;
      this.active = true;
      this.deps = [];
      this.parent = undefined;
      recordEffectScope(this, scope);
    }
    var _proto2 = ReactiveEffect.prototype;
    _proto2.run = function run() {
      if (!this.active) {
        return this.fn();
      }
      var parent = activeEffect;
      var lastShouldTrack = shouldTrack;
      while (parent) {
        if (parent === this) {
          return;
        }
        parent = parent.parent;
      }
      try {
        this.parent = activeEffect;
        activeEffect = this;
        shouldTrack = true;
        trackOpBit = 1 << ++effectTrackDepth;
        if (effectTrackDepth <= maxMarkerBits) {
          initDepMarkers(this);
        } else {
          cleanupEffect(this);
        }
        return this.fn();
      } finally {
        if (effectTrackDepth <= maxMarkerBits) {
          finalizeDepMarkers(this);
        }
        trackOpBit = 1 << --effectTrackDepth;
        activeEffect = this.parent;
        shouldTrack = lastShouldTrack;
        this.parent = undefined;
        if (this.deferStop) {
          this.stop();
        }
      }
    };
    _proto2.stop = function stop() {
      // stopped while running itself - defer the cleanup
      if (activeEffect === this) {
        this.deferStop = true;
      } else if (this.active) {
        cleanupEffect(this);
        if (this.onStop) {
          this.onStop();
        }
        this.active = false;
      }
    };
    return ReactiveEffect;
  }();
  function cleanupEffect(effect) {
    var deps = effect.deps;
    if (deps.length) {
      for (var i = 0; i < deps.length; i++) {
        deps[i].delete(effect);
      }
      deps.length = 0;
    }
  }
  var shouldTrack = true;
  var trackStack = [];
  function pauseTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = false;
  }
  function resetTracking() {
    var last = trackStack.pop();
    shouldTrack = last === undefined ? true : last;
  }
  function track(target, type, key) {
    if (shouldTrack && activeEffect) {
      var depsMap = targetMap.get(target);
      if (!depsMap) {
        targetMap.set(target, depsMap = new Map());
      }
      var dep = depsMap.get(key);
      if (!dep) {
        depsMap.set(key, dep = createDep());
      }
      trackEffects(dep);
    }
  }
  function trackEffects(dep, debuggerEventExtraInfo) {
    var shouldTrack = false;
    if (effectTrackDepth <= maxMarkerBits) {
      if (!newTracked(dep)) {
        dep.n |= trackOpBit; // set newly tracked
        shouldTrack = !wasTracked(dep);
      }
    } else {
      // Full cleanup mode.
      shouldTrack = !dep.has(activeEffect);
    }
    if (shouldTrack) {
      dep.add(activeEffect);
      activeEffect.deps.push(dep);
    }
  }
  function trigger(target, type, key, newValue, oldValue, oldTarget) {
    var depsMap = targetMap.get(target);
    if (!depsMap) {
      // never been tracked
      return;
    }
    var deps = [];
    if (type === "clear" /* TriggerOpTypes.CLEAR */) {
      // collection being cleared
      // trigger all effects for target
      deps = [].concat(depsMap.values());
    } else if (key === 'length' && isArray$2(target)) {
      var newLength = toNumber$2(newValue);
      depsMap.forEach(function (dep, key) {
        if (key === 'length' || key >= newLength) {
          deps.push(dep);
        }
      });
    } else {
      // schedule runs for SET | ADD | DELETE
      if (key !== void 0) {
        deps.push(depsMap.get(key));
      }
      // also run for iteration key on ADD | DELETE | Map.SET
      switch (type) {
        case "add" /* TriggerOpTypes.ADD */:
          if (!isArray$2(target)) {
            deps.push(depsMap.get(ITERATE_KEY));
            if (isMap$1(target)) {
              deps.push(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          } else if (isIntegerKey(key)) {
            // new index added to array -> length changes
            deps.push(depsMap.get('length'));
          }
          break;
        case "delete" /* TriggerOpTypes.DELETE */:
          if (!isArray$2(target)) {
            deps.push(depsMap.get(ITERATE_KEY));
            if (isMap$1(target)) {
              deps.push(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          }
          break;
        case "set" /* TriggerOpTypes.SET */:
          if (isMap$1(target)) {
            deps.push(depsMap.get(ITERATE_KEY));
          }
          break;
      }
    }
    if (deps.length === 1) {
      if (deps[0]) {
        {
          triggerEffects(deps[0]);
        }
      }
    } else {
      var effects = [];
      for (var _iterator = _createForOfIteratorHelperLoose(deps), _step; !(_step = _iterator()).done;) {
        var dep = _step.value;
        if (dep) {
          effects.push.apply(effects, dep);
        }
      }
      {
        triggerEffects(createDep(effects));
      }
    }
  }
  function triggerEffects(dep, debuggerEventExtraInfo) {
    // spread into array for stabilization
    var effects = isArray$2(dep) ? dep : [].concat(dep);
    for (var _iterator2 = _createForOfIteratorHelperLoose(effects), _step2; !(_step2 = _iterator2()).done;) {
      var effect = _step2.value;
      if (effect.computed) {
        triggerEffect(effect);
      }
    }
    for (var _iterator3 = _createForOfIteratorHelperLoose(effects), _step3; !(_step3 = _iterator3()).done;) {
      var _effect = _step3.value;
      if (!_effect.computed) {
        triggerEffect(_effect);
      }
    }
  }
  function triggerEffect(effect, debuggerEventExtraInfo) {
    if (effect !== activeEffect || effect.allowRecurse) {
      if (effect.scheduler) {
        effect.scheduler();
      } else {
        effect.run();
      }
    }
  }
  var isNonTrackableKeys = /*#__PURE__*/makeMap$2("__proto__,__v_isRef,__isVue");
  var builtInSymbols = new Set( /*#__PURE__*/
  Object.getOwnPropertyNames(Symbol)
  // ios10.x Object.getOwnPropertyNames(Symbol) can enumerate 'arguments' and 'caller'
  // but accessing them on Symbol leads to TypeError because Symbol is a strict mode
  // function
  .filter(function (key) {
    return key !== 'arguments' && key !== 'caller';
  }).map(function (key) {
    return Symbol[key];
  }).filter(isSymbol));
  var get = /*#__PURE__*/createGetter();
  var shallowGet = /*#__PURE__*/createGetter(false, true);
  var readonlyGet = /*#__PURE__*/createGetter(true);
  var arrayInstrumentations = /*#__PURE__*/createArrayInstrumentations();
  function createArrayInstrumentations() {
    var instrumentations = {};
    ['includes', 'indexOf', 'lastIndexOf'].forEach(function (key) {
      instrumentations[key] = function () {
        var arr = toRaw(this);
        for (var i = 0, l = this.length; i < l; i++) {
          track(arr, "get" /* TrackOpTypes.GET */, i + '');
        }
        // we run the method using the original args first (which may be reactive)
        for (var _len2 = arguments.length, args = new Array(_len2), _key3 = 0; _key3 < _len2; _key3++) {
          args[_key3] = arguments[_key3];
        }
        var res = arr[key].apply(arr, args);
        if (res === -1 || res === false) {
          // if that didn't work, run it again using raw values.
          return arr[key].apply(arr, args.map(toRaw));
        } else {
          return res;
        }
      };
    });
    ['push', 'pop', 'shift', 'unshift', 'splice'].forEach(function (key) {
      instrumentations[key] = function () {
        pauseTracking();
        for (var _len3 = arguments.length, args = new Array(_len3), _key4 = 0; _key4 < _len3; _key4++) {
          args[_key4] = arguments[_key4];
        }
        var res = toRaw(this)[key].apply(this, args);
        resetTracking();
        return res;
      };
    });
    return instrumentations;
  }
  function createGetter(isReadonly, shallow) {
    if (isReadonly === void 0) {
      isReadonly = false;
    }
    if (shallow === void 0) {
      shallow = false;
    }
    return function get(target, key, receiver) {
      if (key === "__v_isReactive" /* ReactiveFlags.IS_REACTIVE */) {
        return !isReadonly;
      } else if (key === "__v_isReadonly" /* ReactiveFlags.IS_READONLY */) {
        return isReadonly;
      } else if (key === "__v_isShallow" /* ReactiveFlags.IS_SHALLOW */) {
        return shallow;
      } else if (key === "__v_raw" /* ReactiveFlags.RAW */ && receiver === (isReadonly ? shallow ? shallowReadonlyMap : readonlyMap : shallow ? shallowReactiveMap : reactiveMap).get(target)) {
        return target;
      }
      var targetIsArray = isArray$2(target);
      if (!isReadonly && targetIsArray && hasOwn$1(arrayInstrumentations, key)) {
        return Reflect.get(arrayInstrumentations, key, receiver);
      }
      var res = Reflect.get(target, key, receiver);
      if (isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) {
        return res;
      }
      if (!isReadonly) {
        track(target, "get" /* TrackOpTypes.GET */, key);
      }
      if (shallow) {
        return res;
      }
      if (isRef(res)) {
        // ref unwrapping - skip unwrap for Array + integer key.
        return targetIsArray && isIntegerKey(key) ? res : res.value;
      }
      if (isObject$3(res)) {
        // Convert returned value into a proxy as well. we do the isObject check
        // here to avoid invalid value warning. Also need to lazy access readonly
        // and reactive here to avoid circular dependency.
        return isReadonly ? readonly(res) : reactive(res);
      }
      return res;
    };
  }
  var set = /*#__PURE__*/createSetter();
  var shallowSet = /*#__PURE__*/createSetter(true);
  function createSetter(shallow) {
    if (shallow === void 0) {
      shallow = false;
    }
    return function set(target, key, value, receiver) {
      var oldValue = target[key];
      if (isReadonly(oldValue) && isRef(oldValue) && !isRef(value)) {
        return false;
      }
      if (!shallow) {
        if (!isShallow(value) && !isReadonly(value)) {
          oldValue = toRaw(oldValue);
          value = toRaw(value);
        }
        if (!isArray$2(target) && isRef(oldValue) && !isRef(value)) {
          oldValue.value = value;
          return true;
        }
      }
      var hadKey = isArray$2(target) && isIntegerKey(key) ? Number(key) < target.length : hasOwn$1(target, key);
      var result = Reflect.set(target, key, value, receiver);
      // don't trigger if target is something up in the prototype chain of original
      if (target === toRaw(receiver)) {
        if (!hadKey) {
          trigger(target, "add" /* TriggerOpTypes.ADD */, key, value);
        } else if (hasChanged$1(value, oldValue)) {
          trigger(target, "set" /* TriggerOpTypes.SET */, key, value);
        }
      }
      return result;
    };
  }
  function deleteProperty(target, key) {
    var hadKey = hasOwn$1(target, key);
    target[key];
    var result = Reflect.deleteProperty(target, key);
    if (result && hadKey) {
      trigger(target, "delete" /* TriggerOpTypes.DELETE */, key, undefined);
    }
    return result;
  }
  function has(target, key) {
    var result = Reflect.has(target, key);
    if (!isSymbol(key) || !builtInSymbols.has(key)) {
      track(target, "has" /* TrackOpTypes.HAS */, key);
    }
    return result;
  }
  function ownKeys(target) {
    track(target, "iterate" /* TrackOpTypes.ITERATE */, isArray$2(target) ? 'length' : ITERATE_KEY);
    return Reflect.ownKeys(target);
  }
  var mutableHandlers = {
    get: get,
    set: set,
    deleteProperty: deleteProperty,
    has: has,
    ownKeys: ownKeys
  };
  var readonlyHandlers = {
    get: readonlyGet,
    set: function set(target, key) {
      return true;
    },
    deleteProperty: function deleteProperty(target, key) {
      return true;
    }
  };
  var shallowReactiveHandlers = /*#__PURE__*/extend$2({}, mutableHandlers, {
    get: shallowGet,
    set: shallowSet
  });
  var toShallow = function toShallow(value) {
    return value;
  };
  var getProto = function getProto(v) {
    return Reflect.getPrototypeOf(v);
  };
  function get$1(target, key, isReadonly, isShallow) {
    if (isReadonly === void 0) {
      isReadonly = false;
    }
    if (isShallow === void 0) {
      isShallow = false;
    }
    // #1772: readonly(reactive(Map)) should return readonly + reactive version
    // of the value
    target = target["__v_raw" /* ReactiveFlags.RAW */];
    var rawTarget = toRaw(target);
    var rawKey = toRaw(key);
    if (!isReadonly) {
      if (key !== rawKey) {
        track(rawTarget, "get" /* TrackOpTypes.GET */, key);
      }
      track(rawTarget, "get" /* TrackOpTypes.GET */, rawKey);
    }
    var _getProto = getProto(rawTarget),
      has = _getProto.has;
    var wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
    if (has.call(rawTarget, key)) {
      return wrap(target.get(key));
    } else if (has.call(rawTarget, rawKey)) {
      return wrap(target.get(rawKey));
    } else if (target !== rawTarget) {
      // #3602 readonly(reactive(Map))
      // ensure that the nested reactive `Map` can do tracking for itself
      target.get(key);
    }
  }
  function has$1(key, isReadonly) {
    if (isReadonly === void 0) {
      isReadonly = false;
    }
    var target = this["__v_raw" /* ReactiveFlags.RAW */];
    var rawTarget = toRaw(target);
    var rawKey = toRaw(key);
    if (!isReadonly) {
      if (key !== rawKey) {
        track(rawTarget, "has" /* TrackOpTypes.HAS */, key);
      }
      track(rawTarget, "has" /* TrackOpTypes.HAS */, rawKey);
    }
    return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
  }
  function size(target, isReadonly) {
    if (isReadonly === void 0) {
      isReadonly = false;
    }
    target = target["__v_raw" /* ReactiveFlags.RAW */];
    !isReadonly && track(toRaw(target), "iterate" /* TrackOpTypes.ITERATE */, ITERATE_KEY);
    return Reflect.get(target, 'size', target);
  }
  function add(value) {
    value = toRaw(value);
    var target = toRaw(this);
    var proto = getProto(target);
    var hadKey = proto.has.call(target, value);
    if (!hadKey) {
      target.add(value);
      trigger(target, "add" /* TriggerOpTypes.ADD */, value, value);
    }
    return this;
  }
  function set$1(key, value) {
    value = toRaw(value);
    var target = toRaw(this);
    var _getProto2 = getProto(target),
      has = _getProto2.has,
      get = _getProto2.get;
    var hadKey = has.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has.call(target, key);
    }
    var oldValue = get.call(target, key);
    target.set(key, value);
    if (!hadKey) {
      trigger(target, "add" /* TriggerOpTypes.ADD */, key, value);
    } else if (hasChanged$1(value, oldValue)) {
      trigger(target, "set" /* TriggerOpTypes.SET */, key, value);
    }
    return this;
  }
  function deleteEntry(key) {
    var target = toRaw(this);
    var _getProto3 = getProto(target),
      has = _getProto3.has,
      get = _getProto3.get;
    var hadKey = has.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has.call(target, key);
    }
    get ? get.call(target, key) : undefined;
    // forward the operation before queueing reactions
    var result = target.delete(key);
    if (hadKey) {
      trigger(target, "delete" /* TriggerOpTypes.DELETE */, key, undefined);
    }
    return result;
  }
  function clear() {
    var target = toRaw(this);
    var hadItems = target.size !== 0;
    // forward the operation before queueing reactions
    var result = target.clear();
    if (hadItems) {
      trigger(target, "clear" /* TriggerOpTypes.CLEAR */, undefined, undefined);
    }
    return result;
  }
  function createForEach(isReadonly, isShallow) {
    return function forEach(callback, thisArg) {
      var observed = this;
      var target = observed["__v_raw" /* ReactiveFlags.RAW */];
      var rawTarget = toRaw(target);
      var wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate" /* TrackOpTypes.ITERATE */, ITERATE_KEY);
      return target.forEach(function (value, key) {
        // important: make sure the callback is
        // 1. invoked with the reactive map as `this` and 3rd arg
        // 2. the value received should be a corresponding reactive/readonly.
        return callback.call(thisArg, wrap(value), wrap(key), observed);
      });
    };
  }
  function createIterableMethod(method, isReadonly, isShallow) {
    return function () {
      var _ref2;
      var target = this["__v_raw" /* ReactiveFlags.RAW */];
      var rawTarget = toRaw(target);
      var targetIsMap = isMap$1(rawTarget);
      var isPair = method === 'entries' || method === Symbol.iterator && targetIsMap;
      var isKeyOnly = method === 'keys' && targetIsMap;
      var innerIterator = target[method].apply(target, arguments);
      var wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate" /* TrackOpTypes.ITERATE */, isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY);
      // return a wrapped iterator which returns observed versions of the
      // values emitted from the real iterator
      return _ref2 = {
        // iterator protocol
        next: function next() {
          var _innerIterator$next = innerIterator.next(),
            value = _innerIterator$next.value,
            done = _innerIterator$next.done;
          return done ? {
            value: value,
            done: done
          } : {
            value: isPair ? [wrap(value[0]), wrap(value[1])] : wrap(value),
            done: done
          };
        }
      }, _ref2[Symbol.iterator] = function () {
        return this;
      }, _ref2;
    };
  }
  function createReadonlyMethod(type) {
    return function () {
      return type === "delete" /* TriggerOpTypes.DELETE */ ? false : this;
    };
  }
  function createInstrumentations() {
    var mutableInstrumentations = {
      get: function get(key) {
        return get$1(this, key);
      },
      get size() {
        return size(this);
      },
      has: has$1,
      add: add,
      set: set$1,
      delete: deleteEntry,
      clear: clear,
      forEach: createForEach(false, false)
    };
    var shallowInstrumentations = {
      get: function get(key) {
        return get$1(this, key, false, true);
      },
      get size() {
        return size(this);
      },
      has: has$1,
      add: add,
      set: set$1,
      delete: deleteEntry,
      clear: clear,
      forEach: createForEach(false, true)
    };
    var readonlyInstrumentations = {
      get: function get(key) {
        return get$1(this, key, true);
      },
      get size() {
        return size(this, true);
      },
      has: function has(key) {
        return has$1.call(this, key, true);
      },
      add: createReadonlyMethod("add" /* TriggerOpTypes.ADD */),
      set: createReadonlyMethod("set" /* TriggerOpTypes.SET */),
      delete: createReadonlyMethod("delete" /* TriggerOpTypes.DELETE */),
      clear: createReadonlyMethod("clear" /* TriggerOpTypes.CLEAR */),
      forEach: createForEach(true, false)
    };
    var shallowReadonlyInstrumentations = {
      get: function get(key) {
        return get$1(this, key, true, true);
      },
      get size() {
        return size(this, true);
      },
      has: function has(key) {
        return has$1.call(this, key, true);
      },
      add: createReadonlyMethod("add" /* TriggerOpTypes.ADD */),
      set: createReadonlyMethod("set" /* TriggerOpTypes.SET */),
      delete: createReadonlyMethod("delete" /* TriggerOpTypes.DELETE */),
      clear: createReadonlyMethod("clear" /* TriggerOpTypes.CLEAR */),
      forEach: createForEach(true, true)
    };
    var iteratorMethods = ['keys', 'values', 'entries', Symbol.iterator];
    iteratorMethods.forEach(function (method) {
      mutableInstrumentations[method] = createIterableMethod(method, false, false);
      readonlyInstrumentations[method] = createIterableMethod(method, true, false);
      shallowInstrumentations[method] = createIterableMethod(method, false, true);
      shallowReadonlyInstrumentations[method] = createIterableMethod(method, true, true);
    });
    return [mutableInstrumentations, readonlyInstrumentations, shallowInstrumentations, shallowReadonlyInstrumentations];
  }
  var _createInstrumentatio = /* #__PURE__*/createInstrumentations(),
    mutableInstrumentations = _createInstrumentatio[0],
    readonlyInstrumentations = _createInstrumentatio[1],
    shallowInstrumentations = _createInstrumentatio[2],
    shallowReadonlyInstrumentations = _createInstrumentatio[3];
  function createInstrumentationGetter(isReadonly, shallow) {
    var instrumentations = shallow ? isReadonly ? shallowReadonlyInstrumentations : shallowInstrumentations : isReadonly ? readonlyInstrumentations : mutableInstrumentations;
    return function (target, key, receiver) {
      if (key === "__v_isReactive" /* ReactiveFlags.IS_REACTIVE */) {
        return !isReadonly;
      } else if (key === "__v_isReadonly" /* ReactiveFlags.IS_READONLY */) {
        return isReadonly;
      } else if (key === "__v_raw" /* ReactiveFlags.RAW */) {
        return target;
      }
      return Reflect.get(hasOwn$1(instrumentations, key) && key in target ? instrumentations : target, key, receiver);
    };
  }
  var mutableCollectionHandlers = {
    get: /*#__PURE__*/createInstrumentationGetter(false, false)
  };
  var shallowCollectionHandlers = {
    get: /*#__PURE__*/createInstrumentationGetter(false, true)
  };
  var readonlyCollectionHandlers = {
    get: /*#__PURE__*/createInstrumentationGetter(true, false)
  };
  var reactiveMap = new WeakMap();
  var shallowReactiveMap = new WeakMap();
  var readonlyMap = new WeakMap();
  var shallowReadonlyMap = new WeakMap();
  function targetTypeMap(rawType) {
    switch (rawType) {
      case 'Object':
      case 'Array':
        return 1 /* TargetType.COMMON */;
      case 'Map':
      case 'Set':
      case 'WeakMap':
      case 'WeakSet':
        return 2 /* TargetType.COLLECTION */;
      default:
        return 0 /* TargetType.INVALID */;
    }
  }

  function getTargetType(value) {
    return value["__v_skip" /* ReactiveFlags.SKIP */] || !Object.isExtensible(value) ? 0 /* TargetType.INVALID */ : targetTypeMap(toRawType(value));
  }
  function reactive(target) {
    // if trying to observe a readonly proxy, return the readonly version.
    if (isReadonly(target)) {
      return target;
    }
    return createReactiveObject(target, false, mutableHandlers, mutableCollectionHandlers, reactiveMap);
  }
  /**
   * Return a shallowly-reactive copy of the original object, where only the root
   * level properties are reactive. It also does not auto-unwrap refs (even at the
   * root level).
   */
  function shallowReactive(target) {
    return createReactiveObject(target, false, shallowReactiveHandlers, shallowCollectionHandlers, shallowReactiveMap);
  }
  /**
   * Creates a readonly copy of the original object. Note the returned copy is not
   * made reactive, but `readonly` can be called on an already reactive object.
   */
  function readonly(target) {
    return createReactiveObject(target, true, readonlyHandlers, readonlyCollectionHandlers, readonlyMap);
  }
  function createReactiveObject(target, isReadonly, baseHandlers, collectionHandlers, proxyMap) {
    if (!isObject$3(target)) {
      return target;
    }
    // target is already a Proxy, return it.
    // exception: calling readonly() on a reactive object
    if (target["__v_raw" /* ReactiveFlags.RAW */] && !(isReadonly && target["__v_isReactive" /* ReactiveFlags.IS_REACTIVE */])) {
      return target;
    }
    // target already has corresponding Proxy
    var existingProxy = proxyMap.get(target);
    if (existingProxy) {
      return existingProxy;
    }
    // only specific value types can be observed.
    var targetType = getTargetType(target);
    if (targetType === 0 /* TargetType.INVALID */) {
      return target;
    }
    var proxy = new Proxy(target, targetType === 2 /* TargetType.COLLECTION */ ? collectionHandlers : baseHandlers);
    proxyMap.set(target, proxy);
    return proxy;
  }
  function isReactive(value) {
    if (isReadonly(value)) {
      return isReactive(value["__v_raw" /* ReactiveFlags.RAW */]);
    }

    return !!(value && value["__v_isReactive" /* ReactiveFlags.IS_REACTIVE */]);
  }

  function isReadonly(value) {
    return !!(value && value["__v_isReadonly" /* ReactiveFlags.IS_READONLY */]);
  }

  function isShallow(value) {
    return !!(value && value["__v_isShallow" /* ReactiveFlags.IS_SHALLOW */]);
  }

  function isProxy(value) {
    return isReactive(value) || isReadonly(value);
  }
  function toRaw(observed) {
    var raw = observed && observed["__v_raw" /* ReactiveFlags.RAW */];
    return raw ? toRaw(raw) : observed;
  }
  function markRaw(value) {
    def$1(value, "__v_skip" /* ReactiveFlags.SKIP */, true);
    return value;
  }
  var toReactive = function toReactive(value) {
    return isObject$3(value) ? reactive(value) : value;
  };
  var toReadonly = function toReadonly(value) {
    return isObject$3(value) ? readonly(value) : value;
  };
  function trackRefValue(ref) {
    if (shouldTrack && activeEffect) {
      ref = toRaw(ref);
      {
        trackEffects(ref.dep || (ref.dep = createDep()));
      }
    }
  }
  function triggerRefValue(ref, newVal) {
    ref = toRaw(ref);
    if (ref.dep) {
      {
        triggerEffects(ref.dep);
      }
    }
  }
  function isRef(r) {
    return !!(r && r.__v_isRef === true);
  }
  function ref(value) {
    return createRef(value, false);
  }
  function createRef(rawValue, shallow) {
    if (isRef(rawValue)) {
      return rawValue;
    }
    return new RefImpl(rawValue, shallow);
  }
  var RefImpl = /*#__PURE__*/function () {
    function RefImpl(value, __v_isShallow) {
      this.__v_isShallow = __v_isShallow;
      this.dep = undefined;
      this.__v_isRef = true;
      this._rawValue = __v_isShallow ? value : toRaw(value);
      this._value = __v_isShallow ? value : toReactive(value);
    }
    _createClass(RefImpl, [{
      key: "value",
      get: function get() {
        trackRefValue(this);
        return this._value;
      },
      set: function set(newVal) {
        var useDirectValue = this.__v_isShallow || isShallow(newVal) || isReadonly(newVal);
        newVal = useDirectValue ? newVal : toRaw(newVal);
        if (hasChanged$1(newVal, this._rawValue)) {
          this._rawValue = newVal;
          this._value = useDirectValue ? newVal : toReactive(newVal);
          triggerRefValue(this);
        }
      }
    }]);
    return RefImpl;
  }();
  function unref(ref) {
    return isRef(ref) ? ref.value : ref;
  }
  var shallowUnwrapHandlers = {
    get: function get(target, key, receiver) {
      return unref(Reflect.get(target, key, receiver));
    },
    set: function set(target, key, value, receiver) {
      var oldValue = target[key];
      if (isRef(oldValue) && !isRef(value)) {
        oldValue.value = value;
        return true;
      } else {
        return Reflect.set(target, key, value, receiver);
      }
    }
  };
  function proxyRefs(objectWithRefs) {
    return isReactive(objectWithRefs) ? objectWithRefs : new Proxy(objectWithRefs, shallowUnwrapHandlers);
  }
  function toRefs(object) {
    var ret = isArray$2(object) ? new Array(object.length) : {};
    for (var key in object) {
      ret[key] = toRef(object, key);
    }
    return ret;
  }
  var ObjectRefImpl = /*#__PURE__*/function () {
    function ObjectRefImpl(_object, _key, _defaultValue) {
      this._object = _object;
      this._key = _key;
      this._defaultValue = _defaultValue;
      this.__v_isRef = true;
    }
    _createClass(ObjectRefImpl, [{
      key: "value",
      get: function get() {
        var val = this._object[this._key];
        return val === undefined ? this._defaultValue : val;
      },
      set: function set(newVal) {
        this._object[this._key] = newVal;
      }
    }]);
    return ObjectRefImpl;
  }();
  function toRef(object, key, defaultValue) {
    var val = object[key];
    return isRef(val) ? val : new ObjectRefImpl(object, key, defaultValue);
  }
  var _a;
  var ComputedRefImpl = /*#__PURE__*/function () {
    function ComputedRefImpl(getter, _setter, isReadonly, isSSR) {
      var _this2 = this;
      this._setter = _setter;
      this.dep = undefined;
      this.__v_isRef = true;
      this[_a] = false;
      this._dirty = true;
      this.effect = new ReactiveEffect(getter, function () {
        if (!_this2._dirty) {
          _this2._dirty = true;
          triggerRefValue(_this2);
        }
      });
      this.effect.computed = this;
      this.effect.active = this._cacheable = !isSSR;
      this["__v_isReadonly" /* ReactiveFlags.IS_READONLY */] = isReadonly;
    }
    _createClass(ComputedRefImpl, [{
      key: "value",
      get: function get() {
        // the computed ref may get wrapped by other proxies e.g. readonly() #3376
        var self = toRaw(this);
        trackRefValue(self);
        if (self._dirty || !self._cacheable) {
          self._dirty = false;
          self._value = self.effect.run();
        }
        return self._value;
      },
      set: function set(newValue) {
        this._setter(newValue);
      }
    }]);
    return ComputedRefImpl;
  }();
  _a = "__v_isReadonly" /* ReactiveFlags.IS_READONLY */;
  function computed$1(getterOrOptions, debugOptions, isSSR) {
    if (isSSR === void 0) {
      isSSR = false;
    }
    var getter;
    var setter;
    var onlyGetter = isFunction$2(getterOrOptions);
    if (onlyGetter) {
      getter = getterOrOptions;
      setter = NOOP$1;
    } else {
      getter = getterOrOptions.get;
      setter = getterOrOptions.set;
    }
    var cRef = new ComputedRefImpl(getter, setter, onlyGetter || !setter, isSSR);
    return cRef;
  }

  /**
   * Make a map and return a function for checking if a key
   * is in that map.
   * IMPORTANT: all calls of this function must be prefixed with
   * \/\*#\_\_PURE\_\_\*\/
   * So that rollup can tree-shake them if necessary.
   */
  function makeMap$1(str, expectsLowerCase) {
    var map = Object.create(null);
    var list = str.split(',');
    for (var i = 0; i < list.length; i++) {
      map[list[i]] = true;
    }
    return expectsLowerCase ? function (val) {
      return !!map[val.toLowerCase()];
    } : function (val) {
      return !!map[val];
    };
  }
  function normalizeStyle(value) {
    if (isArray$1(value)) {
      var res = {};
      for (var i = 0; i < value.length; i++) {
        var item = value[i];
        var normalized = isString$1(item) ? parseStringStyle(item) : normalizeStyle(item);
        if (normalized) {
          for (var key in normalized) {
            res[key] = normalized[key];
          }
        }
      }
      return res;
    } else if (isString$1(value)) {
      return value;
    } else if (isObject$2(value)) {
      return value;
    }
  }
  var listDelimiterRE = /;(?![^(]*\))/g;
  var propertyDelimiterRE = /:([^]+)/;
  var styleCommentRE = /\/\*[\s\S]*?\*\//g;
  function parseStringStyle(cssText) {
    var ret = {};
    cssText.replace(styleCommentRE, '').split(listDelimiterRE).forEach(function (item) {
      if (item) {
        var tmp = item.split(propertyDelimiterRE);
        tmp.length > 1 && (ret[tmp[0].trim()] = tmp[1].trim());
      }
    });
    return ret;
  }
  function normalizeClass(value) {
    var res = '';
    if (isString$1(value)) {
      res = value;
    } else if (isArray$1(value)) {
      for (var i = 0; i < value.length; i++) {
        var normalized = normalizeClass(value[i]);
        if (normalized) {
          res += normalized + ' ';
        }
      }
    } else if (isObject$2(value)) {
      for (var name in value) {
        if (value[name]) {
          res += name + ' ';
        }
      }
    }
    return res.trim();
  }

  /**
   * For converting {{ interpolation }} values to displayed strings.
   * @private
   */
  var toDisplayString = function toDisplayString(val) {
    return isString$1(val) ? val : val == null ? '' : isArray$1(val) || isObject$2(val) && (val.toString === objectToString || !isFunction$1(val.toString)) ? JSON.stringify(val, replacer, 2) : String(val);
  };
  var replacer = function replacer(_key, val) {
    // can't use isRef here since @vue/shared has no deps
    if (val && val.__v_isRef) {
      return replacer(_key, val.value);
    } else if (isMap(val)) {
      var _ref7;
      return _ref7 = {}, _ref7["Map(" + val.size + ")"] = [].concat(val.entries()).reduce(function (entries, _ref) {
        var key = _ref[0],
          val = _ref[1];
        entries[key + " =>"] = val;
        return entries;
      }, {}), _ref7;
    } else if (isSet(val)) {
      var _ref8;
      return _ref8 = {}, _ref8["Set(" + val.size + ")"] = [].concat(val.values()), _ref8;
    } else if (isObject$2(val) && !isArray$1(val) && !isPlainObject(val)) {
      return String(val);
    }
    return val;
  };
  var EMPTY_OBJ = {};
  var EMPTY_ARR = [];
  var NOOP = function NOOP() {};
  /**
   * Always return false.
   */
  var NO = function NO() {
    return false;
  };
  var onRE$1 = /^on[^a-z]/;
  var isOn$1 = function isOn$1(key) {
    return onRE$1.test(key);
  };
  var isModelListener$1 = function isModelListener$1(key) {
    return key.startsWith('onUpdate:');
  };
  var extend$1 = Object.assign;
  var remove = function remove(arr, el) {
    var i = arr.indexOf(el);
    if (i > -1) {
      arr.splice(i, 1);
    }
  };
  var hasOwnProperty = Object.prototype.hasOwnProperty;
  var hasOwn = function hasOwn(val, key) {
    return hasOwnProperty.call(val, key);
  };
  var isArray$1 = Array.isArray;
  var isMap = function isMap(val) {
    return toTypeString(val) === '[object Map]';
  };
  var isSet = function isSet(val) {
    return toTypeString(val) === '[object Set]';
  };
  var isFunction$1 = function isFunction$1(val) {
    return typeof val === 'function';
  };
  var isString$1 = function isString$1(val) {
    return typeof val === 'string';
  };
  var isObject$2 = function isObject$2(val) {
    return val !== null && typeof val === 'object';
  };
  var isPromise$1 = function isPromise$1(val) {
    return isObject$2(val) && isFunction$1(val.then) && isFunction$1(val.catch);
  };
  var objectToString = Object.prototype.toString;
  var toTypeString = function toTypeString(value) {
    return objectToString.call(value);
  };
  var isPlainObject = function isPlainObject(val) {
    return toTypeString(val) === '[object Object]';
  };
  var isReservedProp = /*#__PURE__*/makeMap$1(
  // the leading comma is intentional so empty string "" is also included
  ',key,ref,ref_for,ref_key,' + 'onVnodeBeforeMount,onVnodeMounted,' + 'onVnodeBeforeUpdate,onVnodeUpdated,' + 'onVnodeBeforeUnmount,onVnodeUnmounted');
  var cacheStringFunction$1 = function cacheStringFunction$1(fn) {
    var cache = Object.create(null);
    return function (str) {
      var hit = cache[str];
      return hit || (cache[str] = fn(str));
    };
  };
  var camelizeRE = /-(\w)/g;
  /**
   * @private
   */
  var camelize = cacheStringFunction$1(function (str) {
    return str.replace(camelizeRE, function (_, c) {
      return c ? c.toUpperCase() : '';
    });
  });
  var hyphenateRE$1 = /\B([A-Z])/g;
  /**
   * @private
   */
  var hyphenate$1 = cacheStringFunction$1(function (str) {
    return str.replace(hyphenateRE$1, '-$1').toLowerCase();
  });
  /**
   * @private
   */
  var capitalize$1 = cacheStringFunction$1(function (str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  });
  /**
   * @private
   */
  var toHandlerKey = cacheStringFunction$1(function (str) {
    return str ? "on" + capitalize$1(str) : "";
  });
  // compare whether a value has changed, accounting for NaN.
  var hasChanged = function hasChanged(value, oldValue) {
    return !Object.is(value, oldValue);
  };
  var invokeArrayFns$1 = function invokeArrayFns$1(fns, arg) {
    for (var i = 0; i < fns.length; i++) {
      fns[i](arg);
    }
  };
  var def = function def(obj, key, value) {
    Object.defineProperty(obj, key, {
      configurable: true,
      enumerable: false,
      value: value
    });
  };
  var toNumber$1 = function toNumber$1(val) {
    var n = parseFloat(val);
    return isNaN(n) ? val : n;
  };
  var _globalThis;
  var getGlobalThis = function getGlobalThis() {
    return _globalThis || (_globalThis = typeof globalThis !== 'undefined' ? globalThis : typeof self !== 'undefined' ? self : typeof window !== 'undefined' ? window : typeof commonjsGlobal !== 'undefined' ? commonjsGlobal : {});
  };
  function callWithErrorHandling(fn, instance, type, args) {
    var res;
    try {
      res = args ? fn.apply(void 0, args) : fn();
    } catch (err) {
      handleError$1(err, instance, type);
    }
    return res;
  }
  function callWithAsyncErrorHandling(fn, instance, type, args) {
    if (isFunction$1(fn)) {
      var res = callWithErrorHandling(fn, instance, type, args);
      if (res && isPromise$1(res)) {
        res.catch(function (err) {
          handleError$1(err, instance, type);
        });
      }
      return res;
    }
    var values = [];
    for (var i = 0; i < fn.length; i++) {
      values.push(callWithAsyncErrorHandling(fn[i], instance, type, args));
    }
    return values;
  }
  function handleError$1(err, instance, type, throwInDev) {
    instance ? instance.vnode : null;
    if (instance) {
      var cur = instance.parent;
      // the exposed instance is the render proxy to keep it consistent with 2.x
      var exposedInstance = instance.proxy;
      // in production the hook receives only the error code
      var errorInfo = type;
      while (cur) {
        var errorCapturedHooks = cur.ec;
        if (errorCapturedHooks) {
          for (var i = 0; i < errorCapturedHooks.length; i++) {
            if (errorCapturedHooks[i](err, exposedInstance, errorInfo) === false) {
              return;
            }
          }
        }
        cur = cur.parent;
      }
      // app-level handling
      var appErrorHandler = instance.appContext.config.errorHandler;
      if (appErrorHandler) {
        callWithErrorHandling(appErrorHandler, null, 10 /* ErrorCodes.APP_ERROR_HANDLER */, [err, exposedInstance, errorInfo]);
        return;
      }
    }
    logError(err);
  }
  function logError(err, type, contextVNode, throwInDev) {
    {
      // recover in prod to reduce the impact on end-user
      console.error(err);
    }
  }
  var isFlushing = false;
  var isFlushPending = false;
  var queue = [];
  var flushIndex = 0;
  var pendingPostFlushCbs = [];
  var activePostFlushCbs = null;
  var postFlushIndex = 0;
  var resolvedPromise = /*#__PURE__*/Promise.resolve();
  var currentFlushPromise = null;
  function nextTick(fn) {
    var p = currentFlushPromise || resolvedPromise;
    return fn ? p.then(this ? fn.bind(this) : fn) : p;
  }
  // #2768
  // Use binary-search to find a suitable position in the queue,
  // so that the queue maintains the increasing order of job's id,
  // which can prevent the job from being skipped and also can avoid repeated patching.
  function findInsertionIndex(id) {
    // the start index should be `flushIndex + 1`
    var start = flushIndex + 1;
    var end = queue.length;
    while (start < end) {
      var middle = start + end >>> 1;
      var middleJobId = getId(queue[middle]);
      middleJobId < id ? start = middle + 1 : end = middle;
    }
    return start;
  }
  function queueJob(job) {
    // the dedupe search uses the startIndex argument of Array.includes()
    // by default the search index includes the current job that is being run
    // so it cannot recursively trigger itself again.
    // if the job is a watch() callback, the search will start with a +1 index to
    // allow it recursively trigger itself - it is the user's responsibility to
    // ensure it doesn't end up in an infinite loop.
    if (!queue.length || !queue.includes(job, isFlushing && job.allowRecurse ? flushIndex + 1 : flushIndex)) {
      if (job.id == null) {
        queue.push(job);
      } else {
        queue.splice(findInsertionIndex(job.id), 0, job);
      }
      queueFlush();
    }
  }
  function queueFlush() {
    if (!isFlushing && !isFlushPending) {
      isFlushPending = true;
      currentFlushPromise = resolvedPromise.then(flushJobs);
    }
  }
  function invalidateJob(job) {
    var i = queue.indexOf(job);
    if (i > flushIndex) {
      queue.splice(i, 1);
    }
  }
  function queuePostFlushCb(cb) {
    if (!isArray$1(cb)) {
      if (!activePostFlushCbs || !activePostFlushCbs.includes(cb, cb.allowRecurse ? postFlushIndex + 1 : postFlushIndex)) {
        pendingPostFlushCbs.push(cb);
      }
    } else {
      // if cb is an array, it is a component lifecycle hook which can only be
      // triggered by a job, which is already deduped in the main queue, so
      // we can skip duplicate check here to improve perf
      pendingPostFlushCbs.push.apply(pendingPostFlushCbs, cb);
    }
    queueFlush();
  }
  function flushPreFlushCbs(seen,
  // if currently flushing, skip the current job itself
  i) {
    if (i === void 0) {
      i = isFlushing ? flushIndex + 1 : 0;
    }
    for (; i < queue.length; i++) {
      var cb = queue[i];
      if (cb && cb.pre) {
        queue.splice(i, 1);
        i--;
        cb();
      }
    }
  }
  function flushPostFlushCbs(seen) {
    if (pendingPostFlushCbs.length) {
      var deduped = [].concat(new Set(pendingPostFlushCbs));
      pendingPostFlushCbs.length = 0;
      // #1947 already has active queue, nested flushPostFlushCbs call
      if (activePostFlushCbs) {
        var _activePostFlushCbs;
        (_activePostFlushCbs = activePostFlushCbs).push.apply(_activePostFlushCbs, deduped);
        return;
      }
      activePostFlushCbs = deduped;
      activePostFlushCbs.sort(function (a, b) {
        return getId(a) - getId(b);
      });
      for (postFlushIndex = 0; postFlushIndex < activePostFlushCbs.length; postFlushIndex++) {
        activePostFlushCbs[postFlushIndex]();
      }
      activePostFlushCbs = null;
      postFlushIndex = 0;
    }
  }
  var getId = function getId(job) {
    return job.id == null ? Infinity : job.id;
  };
  var comparator = function comparator(a, b) {
    var diff = getId(a) - getId(b);
    if (diff === 0) {
      if (a.pre && !b.pre) return -1;
      if (b.pre && !a.pre) return 1;
    }
    return diff;
  };
  function flushJobs(seen) {
    isFlushPending = false;
    isFlushing = true;
    // Sort queue before flush.
    // This ensures that:
    // 1. Components are updated from parent to child. (because parent is always
    //    created before the child so its render effect will have smaller
    //    priority number)
    // 2. If a component is unmounted during a parent component's update,
    //    its update can be skipped.
    queue.sort(comparator);
    // conditional usage of checkRecursiveUpdate must be determined out of
    // try ... catch block since Rollup by default de-optimizes treeshaking
    // inside try-catch. This can leave all warning code unshaked. Although
    // they would get eventually shaken by a minifier like terser, some minifiers
    // would fail to do that (e.g. https://github.com/evanw/esbuild/issues/1610)
    var check = NOOP;
    try {
      for (flushIndex = 0; flushIndex < queue.length; flushIndex++) {
        var job = queue[flushIndex];
        if (job && job.active !== false) {
          if ("production" !== 'production' && check(job)) ;
          // console.log(`running:`, job.id)
          callWithErrorHandling(job, null, 14 /* ErrorCodes.SCHEDULER */);
        }
      }
    } finally {
      flushIndex = 0;
      queue.length = 0;
      flushPostFlushCbs();
      isFlushing = false;
      currentFlushPromise = null;
      // some postFlushCb queued jobs!
      // keep flushing until it drains.
      if (queue.length || pendingPostFlushCbs.length) {
        flushJobs();
      }
    }
  }
  function emit$1(instance, event) {
    if (instance.isUnmounted) return;
    var props = instance.vnode.props || EMPTY_OBJ;
    for (var _len3 = arguments.length, rawArgs = new Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {
      rawArgs[_key3 - 2] = arguments[_key3];
    }
    var args = rawArgs;
    var isModelListener = event.startsWith('update:');
    // for v-model update:xxx events, apply modifiers on args
    var modelArg = isModelListener && event.slice(7);
    if (modelArg && modelArg in props) {
      var modifiersKey = (modelArg === 'modelValue' ? 'model' : modelArg) + "Modifiers";
      var _ref22 = props[modifiersKey] || EMPTY_OBJ,
        number = _ref22.number,
        trim = _ref22.trim;
      if (trim) {
        args = rawArgs.map(function (a) {
          return isString$1(a) ? a.trim() : a;
        });
      }
      if (number) {
        args = rawArgs.map(toNumber$1);
      }
    }
    var handlerName;
    var handler = props[handlerName = toHandlerKey(event)] ||
    // also try camelCase event handler (#2249)
    props[handlerName = toHandlerKey(camelize(event))];
    // for v-model update:xxx events, also trigger kebab-case equivalent
    // for props passed via kebab-case
    if (!handler && isModelListener) {
      handler = props[handlerName = toHandlerKey(hyphenate$1(event))];
    }
    if (handler) {
      callWithAsyncErrorHandling(handler, instance, 6 /* ErrorCodes.COMPONENT_EVENT_HANDLER */, args);
    }
    var onceHandler = props[handlerName + "Once"];
    if (onceHandler) {
      if (!instance.emitted) {
        instance.emitted = {};
      } else if (instance.emitted[handlerName]) {
        return;
      }
      instance.emitted[handlerName] = true;
      callWithAsyncErrorHandling(onceHandler, instance, 6 /* ErrorCodes.COMPONENT_EVENT_HANDLER */, args);
    }
  }
  function normalizeEmitsOptions(comp, appContext, asMixin) {
    if (asMixin === void 0) {
      asMixin = false;
    }
    var cache = appContext.emitsCache;
    var cached = cache.get(comp);
    if (cached !== undefined) {
      return cached;
    }
    var raw = comp.emits;
    var normalized = {};
    // apply mixin/extends props
    var hasExtends = false;
    if (!isFunction$1(comp)) {
      var extendEmits = function extendEmits(raw) {
        var normalizedFromExtend = normalizeEmitsOptions(raw, appContext, true);
        if (normalizedFromExtend) {
          hasExtends = true;
          extend$1(normalized, normalizedFromExtend);
        }
      };
      if (!asMixin && appContext.mixins.length) {
        appContext.mixins.forEach(extendEmits);
      }
      if (comp.extends) {
        extendEmits(comp.extends);
      }
      if (comp.mixins) {
        comp.mixins.forEach(extendEmits);
      }
    }
    if (!raw && !hasExtends) {
      if (isObject$2(comp)) {
        cache.set(comp, null);
      }
      return null;
    }
    if (isArray$1(raw)) {
      raw.forEach(function (key) {
        return normalized[key] = null;
      });
    } else {
      extend$1(normalized, raw);
    }
    if (isObject$2(comp)) {
      cache.set(comp, normalized);
    }
    return normalized;
  }
  // Check if an incoming prop key is a declared emit event listener.
  // e.g. With `emits: { click: null }`, props named `onClick` and `onclick` are
  // both considered matched listeners.
  function isEmitListener(options, key) {
    if (!options || !isOn$1(key)) {
      return false;
    }
    key = key.slice(2).replace(/Once$/, '');
    return hasOwn(options, key[0].toLowerCase() + key.slice(1)) || hasOwn(options, hyphenate$1(key)) || hasOwn(options, key);
  }

  /**
   * mark the current rendering instance for asset resolution (e.g.
   * resolveComponent, resolveDirective) during render
   */
  var currentRenderingInstance = null;
  var currentScopeId = null;
  /**
   * Note: rendering calls maybe nested. The function returns the parent rendering
   * instance if present, which should be restored after the render is done:
   *
   * ```js
   * const prev = setCurrentRenderingInstance(i)
   * // ...render
   * setCurrentRenderingInstance(prev)
   * ```
   */
  function setCurrentRenderingInstance(instance) {
    var prev = currentRenderingInstance;
    currentRenderingInstance = instance;
    currentScopeId = instance && instance.type.__scopeId || null;
    return prev;
  }
  /**
   * Wrap a slot function to memoize current rendering instance
   * @private compiler helper
   */
  function withCtx(fn, ctx, isNonScopedSlot // false only
  ) {
    if (ctx === void 0) {
      ctx = currentRenderingInstance;
    }
    if (!ctx) return fn;
    // already normalized
    if (fn._n) {
      return fn;
    }
    var renderFnWithContext = function renderFnWithContext() {
      // If a user calls a compiled slot inside a template expression (#1745), it
      // can mess up block tracking, so by default we disable block tracking and
      // force bail out when invoking a compiled slot (indicated by the ._d flag).
      // This isn't necessary if rendering a compiled `<slot>`, so we flip the
      // ._d flag off when invoking the wrapped fn inside `renderSlot`.
      if (renderFnWithContext._d) {
        setBlockTracking(-1);
      }
      var prevInstance = setCurrentRenderingInstance(ctx);
      var res;
      try {
        res = fn.apply(void 0, arguments);
      } finally {
        setCurrentRenderingInstance(prevInstance);
        if (renderFnWithContext._d) {
          setBlockTracking(1);
        }
      }
      return res;
    };
    // mark normalized to avoid duplicated wrapping
    renderFnWithContext._n = true;
    // mark this as compiled by default
    // this is used in vnode.ts -> normalizeChildren() to set the slot
    // rendering flag.
    renderFnWithContext._c = true;
    // disable block tracking by default
    renderFnWithContext._d = true;
    return renderFnWithContext;
  }
  function markAttrsAccessed() {}
  function renderComponentRoot(instance) {
    var Component = instance.type,
      vnode = instance.vnode,
      proxy = instance.proxy,
      withProxy = instance.withProxy,
      props = instance.props,
      _instance$propsOption = instance.propsOptions,
      propsOptions = _instance$propsOption[0],
      slots = instance.slots,
      attrs = instance.attrs,
      emit = instance.emit,
      render = instance.render,
      renderCache = instance.renderCache,
      data = instance.data,
      setupState = instance.setupState,
      ctx = instance.ctx,
      inheritAttrs = instance.inheritAttrs;
    var result;
    var fallthroughAttrs;
    var prev = setCurrentRenderingInstance(instance);
    try {
      if (vnode.shapeFlag & 4 /* ShapeFlags.STATEFUL_COMPONENT */) {
        // withProxy is a proxy with a different `has` trap only for
        // runtime-compiled render functions using `with` block.
        var proxyToUse = withProxy || proxy;
        result = normalizeVNode(render.call(proxyToUse, proxyToUse, renderCache, props, setupState, data, ctx));
        fallthroughAttrs = attrs;
      } else {
        // functional
        var _render = Component;
        // in dev, mark attrs accessed if optional props (attrs === props)
        if ("production" !== 'production' && attrs === props) ;
        result = normalizeVNode(_render.length > 1 ? _render(props, "production" !== 'production' ? {
          get attrs() {
            markAttrsAccessed();
            return attrs;
          },
          slots: slots,
          emit: emit
        } : {
          attrs: attrs,
          slots: slots,
          emit: emit
        }) : _render(props, null /* we know it doesn't need it */));
        fallthroughAttrs = Component.props ? attrs : getFunctionalFallthrough(attrs);
      }
    } catch (err) {
      blockStack.length = 0;
      handleError$1(err, instance, 1 /* ErrorCodes.RENDER_FUNCTION */);
      result = createVNode(Comment);
    }
    // attr merging
    // in dev mode, comments are preserved, and it's possible for a template
    // to have comments along side the root element which makes it a fragment
    var root = result;
    if (fallthroughAttrs && inheritAttrs !== false) {
      var keys = Object.keys(fallthroughAttrs);
      var _root = root,
        shapeFlag = _root.shapeFlag;
      if (keys.length) {
        if (shapeFlag & (1 /* ShapeFlags.ELEMENT */ | 6 /* ShapeFlags.COMPONENT */)) {
          if (propsOptions && keys.some(isModelListener$1)) {
            // If a v-model listener (onUpdate:xxx) has a corresponding declared
            // prop, it indicates this component expects to handle v-model and
            // it should not fallthrough.
            // related: #1543, #1643, #1989
            fallthroughAttrs = filterModelListeners(fallthroughAttrs, propsOptions);
          }
          root = cloneVNode(root, fallthroughAttrs);
        }
      }
    }
    // inherit directives
    if (vnode.dirs) {
      // clone before mutating since the root may be a hoisted vnode
      root = cloneVNode(root);
      root.dirs = root.dirs ? root.dirs.concat(vnode.dirs) : vnode.dirs;
    }
    // inherit transition data
    if (vnode.transition) {
      root.transition = vnode.transition;
    }
    {
      result = root;
    }
    setCurrentRenderingInstance(prev);
    return result;
  }
  var getFunctionalFallthrough = function getFunctionalFallthrough(attrs) {
    var res;
    for (var key in attrs) {
      if (key === 'class' || key === 'style' || isOn$1(key)) {
        (res || (res = {}))[key] = attrs[key];
      }
    }
    return res;
  };
  var filterModelListeners = function filterModelListeners(attrs, props) {
    var res = {};
    for (var key in attrs) {
      if (!isModelListener$1(key) || !(key.slice(9) in props)) {
        res[key] = attrs[key];
      }
    }
    return res;
  };
  function shouldUpdateComponent(prevVNode, nextVNode, optimized) {
    var prevProps = prevVNode.props,
      prevChildren = prevVNode.children,
      component = prevVNode.component;
    var nextProps = nextVNode.props,
      nextChildren = nextVNode.children,
      patchFlag = nextVNode.patchFlag;
    var emits = component.emitsOptions;
    // force child update for runtime directive or transition on component vnode.
    if (nextVNode.dirs || nextVNode.transition) {
      return true;
    }
    if (optimized && patchFlag >= 0) {
      if (patchFlag & 1024 /* PatchFlags.DYNAMIC_SLOTS */) {
        // slot content that references values that might have changed,
        // e.g. in a v-for
        return true;
      }
      if (patchFlag & 16 /* PatchFlags.FULL_PROPS */) {
        if (!prevProps) {
          return !!nextProps;
        }
        // presence of this flag indicates props are always non-null
        return hasPropsChanged(prevProps, nextProps, emits);
      } else if (patchFlag & 8 /* PatchFlags.PROPS */) {
        var dynamicProps = nextVNode.dynamicProps;
        for (var i = 0; i < dynamicProps.length; i++) {
          var key = dynamicProps[i];
          if (nextProps[key] !== prevProps[key] && !isEmitListener(emits, key)) {
            return true;
          }
        }
      }
    } else {
      // this path is only taken by manually written render functions
      // so presence of any children leads to a forced update
      if (prevChildren || nextChildren) {
        if (!nextChildren || !nextChildren.$stable) {
          return true;
        }
      }
      if (prevProps === nextProps) {
        return false;
      }
      if (!prevProps) {
        return !!nextProps;
      }
      if (!nextProps) {
        return true;
      }
      return hasPropsChanged(prevProps, nextProps, emits);
    }
    return false;
  }
  function hasPropsChanged(prevProps, nextProps, emitsOptions) {
    var nextKeys = Object.keys(nextProps);
    if (nextKeys.length !== Object.keys(prevProps).length) {
      return true;
    }
    for (var i = 0; i < nextKeys.length; i++) {
      var key = nextKeys[i];
      if (nextProps[key] !== prevProps[key] && !isEmitListener(emitsOptions, key)) {
        return true;
      }
    }
    return false;
  }
  function updateHOCHostEl(_ref4, el // HostNode
  ) {
    var vnode = _ref4.vnode,
      parent = _ref4.parent;
    while (parent && parent.subTree === vnode) {
      (vnode = parent.vnode).el = el;
      parent = parent.parent;
    }
  }
  var isSuspense = function isSuspense(type) {
    return type.__isSuspense;
  };
  function queueEffectWithSuspense(fn, suspense) {
    if (suspense && suspense.pendingBranch) {
      if (isArray$1(fn)) {
        var _suspense$effects;
        (_suspense$effects = suspense.effects).push.apply(_suspense$effects, fn);
      } else {
        suspense.effects.push(fn);
      }
    } else {
      queuePostFlushCb(fn);
    }
  }
  function provide(key, value) {
    if (!currentInstance) ;else {
      var provides = currentInstance.provides;
      // by default an instance inherits its parent's provides object
      // but when it needs to provide values of its own, it creates its
      // own provides object using parent provides object as prototype.
      // this way in `inject` we can simply look up injections from direct
      // parent and let the prototype chain do the work.
      var parentProvides = currentInstance.parent && currentInstance.parent.provides;
      if (parentProvides === provides) {
        provides = currentInstance.provides = Object.create(parentProvides);
      }
      // TS doesn't allow symbol as index type
      provides[key] = value;
    }
  }
  function inject(key, defaultValue, treatDefaultAsFactory) {
    if (treatDefaultAsFactory === void 0) {
      treatDefaultAsFactory = false;
    }
    // fallback to `currentRenderingInstance` so that this can be called in
    // a functional component
    var instance = currentInstance || currentRenderingInstance;
    if (instance) {
      // #2400
      // to support `app.use` plugins,
      // fallback to appContext's `provides` if the instance is at root
      var provides = instance.parent == null ? instance.vnode.appContext && instance.vnode.appContext.provides : instance.parent.provides;
      if (provides && key in provides) {
        // TS doesn't allow symbol as index type
        return provides[key];
      } else if (arguments.length > 1) {
        return treatDefaultAsFactory && isFunction$1(defaultValue) ? defaultValue.call(instance.proxy) : defaultValue;
      } else ;
    }
  }
  // initial value for watchers to trigger on undefined initial values
  var INITIAL_WATCHER_VALUE = {};
  // implementation
  function watch(source, cb, options) {
    return doWatch(source, cb, options);
  }
  function doWatch(source, cb, _temp) {
    var _ref23 = _temp === void 0 ? EMPTY_OBJ : _temp,
      immediate = _ref23.immediate,
      deep = _ref23.deep,
      flush = _ref23.flush;
      _ref23.onTrack;
      _ref23.onTrigger;
    var instance = currentInstance;
    var getter;
    var forceTrigger = false;
    var isMultiSource = false;
    if (isRef(source)) {
      getter = function getter() {
        return source.value;
      };
      forceTrigger = isShallow(source);
    } else if (isReactive(source)) {
      getter = function getter() {
        return source;
      };
      deep = true;
    } else if (isArray$1(source)) {
      isMultiSource = true;
      forceTrigger = source.some(function (s) {
        return isReactive(s) || isShallow(s);
      });
      getter = function getter() {
        return source.map(function (s) {
          if (isRef(s)) {
            return s.value;
          } else if (isReactive(s)) {
            return traverse(s);
          } else if (isFunction$1(s)) {
            return callWithErrorHandling(s, instance, 2 /* ErrorCodes.WATCH_GETTER */);
          } else ;
        });
      };
    } else if (isFunction$1(source)) {
      if (cb) {
        // getter with cb
        getter = function getter() {
          return callWithErrorHandling(source, instance, 2 /* ErrorCodes.WATCH_GETTER */);
        };
      } else {
        // no cb -> simple effect
        getter = function getter() {
          if (instance && instance.isUnmounted) {
            return;
          }
          if (cleanup) {
            cleanup();
          }
          return callWithAsyncErrorHandling(source, instance, 3 /* ErrorCodes.WATCH_CALLBACK */, [onCleanup]);
        };
      }
    } else {
      getter = NOOP;
    }
    if (cb && deep) {
      var baseGetter = getter;
      getter = function getter() {
        return traverse(baseGetter());
      };
    }
    var cleanup;
    var onCleanup = function onCleanup(fn) {
      cleanup = effect.onStop = function () {
        callWithErrorHandling(fn, instance, 4 /* ErrorCodes.WATCH_CLEANUP */);
      };
    };
    // in SSR there is no need to setup an actual effect, and it should be noop
    // unless it's eager or sync flush
    var ssrCleanup;
    if (isInSSRComponentSetup) {
      // we will also not call the invalidate callback (+ runner is not set up)
      onCleanup = NOOP;
      if (!cb) {
        getter();
      } else if (immediate) {
        callWithAsyncErrorHandling(cb, instance, 3 /* ErrorCodes.WATCH_CALLBACK */, [getter(), isMultiSource ? [] : undefined, onCleanup]);
      }
      if (flush === 'sync') {
        var ctx = useSSRContext();
        ssrCleanup = ctx.__watcherHandles || (ctx.__watcherHandles = []);
      } else {
        return NOOP;
      }
    }
    var oldValue = isMultiSource ? new Array(source.length).fill(INITIAL_WATCHER_VALUE) : INITIAL_WATCHER_VALUE;
    var job = function job() {
      if (!effect.active) {
        return;
      }
      if (cb) {
        // watch(source, cb)
        var newValue = effect.run();
        if (deep || forceTrigger || (isMultiSource ? newValue.some(function (v, i) {
          return hasChanged(v, oldValue[i]);
        }) : hasChanged(newValue, oldValue)) || false) {
          // cleanup before running cb again
          if (cleanup) {
            cleanup();
          }
          callWithAsyncErrorHandling(cb, instance, 3 /* ErrorCodes.WATCH_CALLBACK */, [newValue,
          // pass undefined as the old value when it's changed for the first time
          oldValue === INITIAL_WATCHER_VALUE ? undefined : isMultiSource && oldValue[0] === INITIAL_WATCHER_VALUE ? [] : oldValue, onCleanup]);
          oldValue = newValue;
        }
      } else {
        // watchEffect
        effect.run();
      }
    };
    // important: mark the job as a watcher callback so that scheduler knows
    // it is allowed to self-trigger (#1727)
    job.allowRecurse = !!cb;
    var scheduler;
    if (flush === 'sync') {
      scheduler = job; // the scheduler function gets called directly
    } else if (flush === 'post') {
      scheduler = function scheduler() {
        return queuePostRenderEffect(job, instance && instance.suspense);
      };
    } else {
      // default: 'pre'
      job.pre = true;
      if (instance) job.id = instance.uid;
      scheduler = function scheduler() {
        return queueJob(job);
      };
    }
    var effect = new ReactiveEffect(getter, scheduler);
    // initial run
    if (cb) {
      if (immediate) {
        job();
      } else {
        oldValue = effect.run();
      }
    } else if (flush === 'post') {
      queuePostRenderEffect(effect.run.bind(effect), instance && instance.suspense);
    } else {
      effect.run();
    }
    var unwatch = function unwatch() {
      effect.stop();
      if (instance && instance.scope) {
        remove(instance.scope.effects, effect);
      }
    };
    if (ssrCleanup) ssrCleanup.push(unwatch);
    return unwatch;
  }
  // this.$watch
  function instanceWatch(source, value, options) {
    var publicThis = this.proxy;
    var getter = isString$1(source) ? source.includes('.') ? createPathGetter(publicThis, source) : function () {
      return publicThis[source];
    } : source.bind(publicThis, publicThis);
    var cb;
    if (isFunction$1(value)) {
      cb = value;
    } else {
      cb = value.handler;
      options = value;
    }
    var cur = currentInstance;
    setCurrentInstance(this);
    var res = doWatch(getter, cb.bind(publicThis), options);
    if (cur) {
      setCurrentInstance(cur);
    } else {
      unsetCurrentInstance();
    }
    return res;
  }
  function createPathGetter(ctx, path) {
    var segments = path.split('.');
    return function () {
      var cur = ctx;
      for (var i = 0; i < segments.length && cur; i++) {
        cur = cur[segments[i]];
      }
      return cur;
    };
  }
  function traverse(value, seen) {
    if (!isObject$2(value) || value["__v_skip" /* ReactiveFlags.SKIP */]) {
      return value;
    }
    seen = seen || new Set();
    if (seen.has(value)) {
      return value;
    }
    seen.add(value);
    if (isRef(value)) {
      traverse(value.value, seen);
    } else if (isArray$1(value)) {
      for (var i = 0; i < value.length; i++) {
        traverse(value[i], seen);
      }
    } else if (isSet(value) || isMap(value)) {
      value.forEach(function (v) {
        traverse(v, seen);
      });
    } else if (isPlainObject(value)) {
      for (var key in value) {
        traverse(value[key], seen);
      }
    }
    return value;
  }
  function useTransitionState() {
    var state = {
      isMounted: false,
      isLeaving: false,
      isUnmounting: false,
      leavingVNodes: new Map()
    };
    onMounted(function () {
      state.isMounted = true;
    });
    onBeforeUnmount(function () {
      state.isUnmounting = true;
    });
    return state;
  }
  var TransitionHookValidator = [Function, Array];
  var BaseTransitionImpl = {
    name: "BaseTransition",
    props: {
      mode: String,
      appear: Boolean,
      persisted: Boolean,
      // enter
      onBeforeEnter: TransitionHookValidator,
      onEnter: TransitionHookValidator,
      onAfterEnter: TransitionHookValidator,
      onEnterCancelled: TransitionHookValidator,
      // leave
      onBeforeLeave: TransitionHookValidator,
      onLeave: TransitionHookValidator,
      onAfterLeave: TransitionHookValidator,
      onLeaveCancelled: TransitionHookValidator,
      // appear
      onBeforeAppear: TransitionHookValidator,
      onAppear: TransitionHookValidator,
      onAfterAppear: TransitionHookValidator,
      onAppearCancelled: TransitionHookValidator
    },
    setup: function setup(props, _ref6) {
      var slots = _ref6.slots;
      var instance = getCurrentInstance();
      var state = useTransitionState();
      var prevTransitionKey;
      return function () {
        var children = slots.default && getTransitionRawChildren(slots.default(), true);
        if (!children || !children.length) {
          return;
        }
        var child = children[0];
        if (children.length > 1) {
          // locate first non-comment child
          for (var _iterator4 = _createForOfIteratorHelperLoose(children), _step4; !(_step4 = _iterator4()).done;) {
            var c = _step4.value;
            if (c.type !== Comment) {
              child = c;
              break;
            }
          }
        }
        // there's no need to track reactivity for these props so use the raw
        // props for a bit better perf
        var rawProps = toRaw(props);
        var mode = rawProps.mode;
        if (state.isLeaving) {
          return emptyPlaceholder(child);
        }
        // in the case of <transition><keep-alive/></transition>, we need to
        // compare the type of the kept-alive children.
        var innerChild = getKeepAliveChild(child);
        if (!innerChild) {
          return emptyPlaceholder(child);
        }
        var enterHooks = resolveTransitionHooks(innerChild, rawProps, state, instance);
        setTransitionHooks(innerChild, enterHooks);
        var oldChild = instance.subTree;
        var oldInnerChild = oldChild && getKeepAliveChild(oldChild);
        var transitionKeyChanged = false;
        var getTransitionKey = innerChild.type.getTransitionKey;
        if (getTransitionKey) {
          var key = getTransitionKey();
          if (prevTransitionKey === undefined) {
            prevTransitionKey = key;
          } else if (key !== prevTransitionKey) {
            prevTransitionKey = key;
            transitionKeyChanged = true;
          }
        }
        // handle mode
        if (oldInnerChild && oldInnerChild.type !== Comment && (!isSameVNodeType(innerChild, oldInnerChild) || transitionKeyChanged)) {
          var leavingHooks = resolveTransitionHooks(oldInnerChild, rawProps, state, instance);
          // update old tree's hooks in case of dynamic transition
          setTransitionHooks(oldInnerChild, leavingHooks);
          // switching between different views
          if (mode === 'out-in') {
            state.isLeaving = true;
            // return placeholder node and queue update when leave finishes
            leavingHooks.afterLeave = function () {
              state.isLeaving = false;
              // #6835
              // it also needs to be updated when active is undefined
              if (instance.update.active !== false) {
                instance.update();
              }
            };
            return emptyPlaceholder(child);
          } else if (mode === 'in-out' && innerChild.type !== Comment) {
            leavingHooks.delayLeave = function (el, earlyRemove, delayedLeave) {
              var leavingVNodesCache = getLeavingNodesForType(state, oldInnerChild);
              leavingVNodesCache[String(oldInnerChild.key)] = oldInnerChild;
              // early removal callback
              el._leaveCb = function () {
                earlyRemove();
                el._leaveCb = undefined;
                delete enterHooks.delayedLeave;
              };
              enterHooks.delayedLeave = delayedLeave;
            };
          }
        }
        return child;
      };
    }
  };
  // export the public type for h/tsx inference
  // also to avoid inline import() in generated d.ts files
  var BaseTransition = BaseTransitionImpl;
  function getLeavingNodesForType(state, vnode) {
    var leavingVNodes = state.leavingVNodes;
    var leavingVNodesCache = leavingVNodes.get(vnode.type);
    if (!leavingVNodesCache) {
      leavingVNodesCache = Object.create(null);
      leavingVNodes.set(vnode.type, leavingVNodesCache);
    }
    return leavingVNodesCache;
  }
  // The transition hooks are attached to the vnode as vnode.transition
  // and will be called at appropriate timing in the renderer.
  function resolveTransitionHooks(vnode, props, state, instance) {
    var appear = props.appear,
      mode = props.mode,
      _props$persisted = props.persisted,
      persisted = _props$persisted === void 0 ? false : _props$persisted,
      onBeforeEnter = props.onBeforeEnter,
      onEnter = props.onEnter,
      onAfterEnter = props.onAfterEnter,
      onEnterCancelled = props.onEnterCancelled,
      onBeforeLeave = props.onBeforeLeave,
      onLeave = props.onLeave,
      onAfterLeave = props.onAfterLeave,
      onLeaveCancelled = props.onLeaveCancelled,
      onBeforeAppear = props.onBeforeAppear,
      onAppear = props.onAppear,
      onAfterAppear = props.onAfterAppear,
      onAppearCancelled = props.onAppearCancelled;
    var key = String(vnode.key);
    var leavingVNodesCache = getLeavingNodesForType(state, vnode);
    var callHook = function callHook(hook, args) {
      hook && callWithAsyncErrorHandling(hook, instance, 9 /* ErrorCodes.TRANSITION_HOOK */, args);
    };
    var callAsyncHook = function callAsyncHook(hook, args) {
      var done = args[1];
      callHook(hook, args);
      if (isArray$1(hook)) {
        if (hook.every(function (hook) {
          return hook.length <= 1;
        })) done();
      } else if (hook.length <= 1) {
        done();
      }
    };
    var hooks = {
      mode: mode,
      persisted: persisted,
      beforeEnter: function beforeEnter(el) {
        var hook = onBeforeEnter;
        if (!state.isMounted) {
          if (appear) {
            hook = onBeforeAppear || onBeforeEnter;
          } else {
            return;
          }
        }
        // for same element (v-show)
        if (el._leaveCb) {
          el._leaveCb(true /* cancelled */);
        }
        // for toggled element with same key (v-if)
        var leavingVNode = leavingVNodesCache[key];
        if (leavingVNode && isSameVNodeType(vnode, leavingVNode) && leavingVNode.el._leaveCb) {
          // force early removal (not cancelled)
          leavingVNode.el._leaveCb();
        }
        callHook(hook, [el]);
      },
      enter: function enter(el) {
        var hook = onEnter;
        var afterHook = onAfterEnter;
        var cancelHook = onEnterCancelled;
        if (!state.isMounted) {
          if (appear) {
            hook = onAppear || onEnter;
            afterHook = onAfterAppear || onAfterEnter;
            cancelHook = onAppearCancelled || onEnterCancelled;
          } else {
            return;
          }
        }
        var called = false;
        var done = el._enterCb = function (cancelled) {
          if (called) return;
          called = true;
          if (cancelled) {
            callHook(cancelHook, [el]);
          } else {
            callHook(afterHook, [el]);
          }
          if (hooks.delayedLeave) {
            hooks.delayedLeave();
          }
          el._enterCb = undefined;
        };
        if (hook) {
          callAsyncHook(hook, [el, done]);
        } else {
          done();
        }
      },
      leave: function leave(el, remove) {
        var key = String(vnode.key);
        if (el._enterCb) {
          el._enterCb(true /* cancelled */);
        }

        if (state.isUnmounting) {
          return remove();
        }
        callHook(onBeforeLeave, [el]);
        var called = false;
        var done = el._leaveCb = function (cancelled) {
          if (called) return;
          called = true;
          remove();
          if (cancelled) {
            callHook(onLeaveCancelled, [el]);
          } else {
            callHook(onAfterLeave, [el]);
          }
          el._leaveCb = undefined;
          if (leavingVNodesCache[key] === vnode) {
            delete leavingVNodesCache[key];
          }
        };
        leavingVNodesCache[key] = vnode;
        if (onLeave) {
          callAsyncHook(onLeave, [el, done]);
        } else {
          done();
        }
      },
      clone: function clone(vnode) {
        return resolveTransitionHooks(vnode, props, state, instance);
      }
    };
    return hooks;
  }
  // the placeholder really only handles one special case: KeepAlive
  // in the case of a KeepAlive in a leave phase we need to return a KeepAlive
  // placeholder with empty content to avoid the KeepAlive instance from being
  // unmounted.
  function emptyPlaceholder(vnode) {
    if (isKeepAlive(vnode)) {
      vnode = cloneVNode(vnode);
      vnode.children = null;
      return vnode;
    }
  }
  function getKeepAliveChild(vnode) {
    return isKeepAlive(vnode) ? vnode.children ? vnode.children[0] : undefined : vnode;
  }
  function setTransitionHooks(vnode, hooks) {
    if (vnode.shapeFlag & 6 /* ShapeFlags.COMPONENT */ && vnode.component) {
      setTransitionHooks(vnode.component.subTree, hooks);
    } else if (vnode.shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
      vnode.ssContent.transition = hooks.clone(vnode.ssContent);
      vnode.ssFallback.transition = hooks.clone(vnode.ssFallback);
    } else {
      vnode.transition = hooks;
    }
  }
  function getTransitionRawChildren(children, keepComment, parentKey) {
    if (keepComment === void 0) {
      keepComment = false;
    }
    var ret = [];
    var keyedFragmentCount = 0;
    for (var i = 0; i < children.length; i++) {
      var child = children[i];
      // #5360 inherit parent key in case of <template v-for>
      var key = parentKey == null ? child.key : String(parentKey) + String(child.key != null ? child.key : i);
      // handle fragment children case, e.g. v-for
      if (child.type === Fragment) {
        if (child.patchFlag & 128 /* PatchFlags.KEYED_FRAGMENT */) keyedFragmentCount++;
        ret = ret.concat(getTransitionRawChildren(child.children, keepComment, key));
      }
      // comment placeholders should be skipped, e.g. v-if
      else if (keepComment || child.type !== Comment) {
        ret.push(key != null ? cloneVNode(child, {
          key: key
        }) : child);
      }
    }
    // #1126 if a transition children list contains multiple sub fragments, these
    // fragments will be merged into a flat children array. Since each v-for
    // fragment may contain different static bindings inside, we need to de-op
    // these children to force full diffs to ensure correct behavior.
    if (keyedFragmentCount > 1) {
      for (var _i = 0; _i < ret.length; _i++) {
        ret[_i].patchFlag = -2 /* PatchFlags.BAIL */;
      }
    }

    return ret;
  }
  var isAsyncWrapper = function isAsyncWrapper(i) {
    return !!i.type.__asyncLoader;
  };
  var isKeepAlive = function isKeepAlive(vnode) {
    return vnode.type.__isKeepAlive;
  };
  function onActivated(hook, target) {
    registerKeepAliveHook(hook, "a" /* LifecycleHooks.ACTIVATED */, target);
  }
  function onDeactivated(hook, target) {
    registerKeepAliveHook(hook, "da" /* LifecycleHooks.DEACTIVATED */, target);
  }
  function registerKeepAliveHook(hook, type, target) {
    if (target === void 0) {
      target = currentInstance;
    }
    // cache the deactivate branch check wrapper for injected hooks so the same
    // hook can be properly deduped by the scheduler. "__wdc" stands for "with
    // deactivation check".
    var wrappedHook = hook.__wdc || (hook.__wdc = function () {
      // only fire the hook if the target instance is NOT in a deactivated branch.
      var current = target;
      while (current) {
        if (current.isDeactivated) {
          return;
        }
        current = current.parent;
      }
      return hook();
    });
    injectHook(type, wrappedHook, target);
    // In addition to registering it on the target instance, we walk up the parent
    // chain and register it on all ancestor instances that are keep-alive roots.
    // This avoids the need to walk the entire component tree when invoking these
    // hooks, and more importantly, avoids the need to track child components in
    // arrays.
    if (target) {
      var current = target.parent;
      while (current && current.parent) {
        if (isKeepAlive(current.parent.vnode)) {
          injectToKeepAliveRoot(wrappedHook, type, target, current);
        }
        current = current.parent;
      }
    }
  }
  function injectToKeepAliveRoot(hook, type, target, keepAliveRoot) {
    // injectHook wraps the original for error handling, so make sure to remove
    // the wrapped version.
    var injected = injectHook(type, hook, keepAliveRoot, true /* prepend */);
    onUnmounted(function () {
      remove(keepAliveRoot[type], injected);
    }, target);
  }
  function injectHook(type, hook, target, prepend) {
    if (target === void 0) {
      target = currentInstance;
    }
    if (prepend === void 0) {
      prepend = false;
    }
    if (target) {
      var hooks = target[type] || (target[type] = []);
      // cache the error handling wrapper for injected hooks so the same hook
      // can be properly deduped by the scheduler. "__weh" stands for "with error
      // handling".
      var wrappedHook = hook.__weh || (hook.__weh = function () {
        if (target.isUnmounted) {
          return;
        }
        // disable tracking inside all lifecycle hooks
        // since they can potentially be called inside effects.
        pauseTracking();
        // Set currentInstance during hook invocation.
        // This assumes the hook does not synchronously trigger other hooks, which
        // can only be false when the user does something really funky.
        setCurrentInstance(target);
        for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
          args[_key4] = arguments[_key4];
        }
        var res = callWithAsyncErrorHandling(hook, target, type, args);
        unsetCurrentInstance();
        resetTracking();
        return res;
      });
      if (prepend) {
        hooks.unshift(wrappedHook);
      } else {
        hooks.push(wrappedHook);
      }
      return wrappedHook;
    }
  }
  var createHook = function createHook(lifecycle) {
    return function (hook, target) {
      if (target === void 0) {
        target = currentInstance;
      }
      return (
        // post-create lifecycle registrations are noops during SSR (except for serverPrefetch)
        (!isInSSRComponentSetup || lifecycle === "sp" /* LifecycleHooks.SERVER_PREFETCH */) && injectHook(lifecycle, function () {
          return hook.apply(void 0, arguments);
        }, target)
      );
    };
  };
  var onBeforeMount = createHook("bm" /* LifecycleHooks.BEFORE_MOUNT */);
  var onMounted = createHook("m" /* LifecycleHooks.MOUNTED */);
  var onBeforeUpdate = createHook("bu" /* LifecycleHooks.BEFORE_UPDATE */);
  var onUpdated = createHook("u" /* LifecycleHooks.UPDATED */);
  var onBeforeUnmount = createHook("bum" /* LifecycleHooks.BEFORE_UNMOUNT */);
  var onUnmounted = createHook("um" /* LifecycleHooks.UNMOUNTED */);
  var onServerPrefetch = createHook("sp" /* LifecycleHooks.SERVER_PREFETCH */);
  var onRenderTriggered = createHook("rtg" /* LifecycleHooks.RENDER_TRIGGERED */);
  var onRenderTracked = createHook("rtc" /* LifecycleHooks.RENDER_TRACKED */);
  function onErrorCaptured(hook, target) {
    if (target === void 0) {
      target = currentInstance;
    }
    injectHook("ec" /* LifecycleHooks.ERROR_CAPTURED */, hook, target);
  }
  /**
   * Adds directives to a VNode.
   */
  function withDirectives(vnode, directives) {
    var internalInstance = currentRenderingInstance;
    if (internalInstance === null) {
      return vnode;
    }
    var instance = getExposeProxy(internalInstance) || internalInstance.proxy;
    var bindings = vnode.dirs || (vnode.dirs = []);
    for (var i = 0; i < directives.length; i++) {
      var _directives$i = directives[i],
        dir = _directives$i[0],
        value = _directives$i[1],
        arg = _directives$i[2],
        _directives$i$ = _directives$i[3],
        modifiers = _directives$i$ === void 0 ? EMPTY_OBJ : _directives$i$;
      if (dir) {
        if (isFunction$1(dir)) {
          dir = {
            mounted: dir,
            updated: dir
          };
        }
        if (dir.deep) {
          traverse(value);
        }
        bindings.push({
          dir: dir,
          instance: instance,
          value: value,
          oldValue: void 0,
          arg: arg,
          modifiers: modifiers
        });
      }
    }
    return vnode;
  }
  function invokeDirectiveHook(vnode, prevVNode, instance, name) {
    var bindings = vnode.dirs;
    var oldBindings = prevVNode && prevVNode.dirs;
    for (var i = 0; i < bindings.length; i++) {
      var binding = bindings[i];
      if (oldBindings) {
        binding.oldValue = oldBindings[i].value;
      }
      var hook = binding.dir[name];
      if (hook) {
        // disable tracking inside all lifecycle hooks
        // since they can potentially be called inside effects.
        pauseTracking();
        callWithAsyncErrorHandling(hook, instance, 8 /* ErrorCodes.DIRECTIVE_HOOK */, [vnode.el, binding, vnode, prevVNode]);
        resetTracking();
      }
    }
  }
  var COMPONENTS = 'components';
  /**
   * @private
   */
  function resolveComponent(name, maybeSelfReference) {
    return resolveAsset(COMPONENTS, name, true, maybeSelfReference) || name;
  }
  var NULL_DYNAMIC_COMPONENT = Symbol();
  // implementation
  function resolveAsset(type, name, warnMissing, maybeSelfReference) {
    if (maybeSelfReference === void 0) {
      maybeSelfReference = false;
    }
    var instance = currentRenderingInstance || currentInstance;
    if (instance) {
      var Component = instance.type;
      // explicit self name has highest priority
      if (type === COMPONENTS) {
        var selfName = getComponentName(Component, false /* do not include inferred name to avoid breaking existing code */);
        if (selfName && (selfName === name || selfName === camelize(name) || selfName === capitalize$1(camelize(name)))) {
          return Component;
        }
      }
      var res =
      // local registration
      // check instance[type] first which is resolved for options API
      resolve(instance[type] || Component[type], name) ||
      // global registration
      resolve(instance.appContext[type], name);
      if (!res && maybeSelfReference) {
        // fallback to implicit self-reference
        return Component;
      }
      return res;
    }
  }
  function resolve(registry, name) {
    return registry && (registry[name] || registry[camelize(name)] || registry[capitalize$1(camelize(name))]);
  }

  /**
   * Actual implementation
   */
  function renderList(source, renderItem, cache, index) {
    var ret;
    var cached = cache && cache[index];
    if (isArray$1(source) || isString$1(source)) {
      ret = new Array(source.length);
      for (var i = 0, l = source.length; i < l; i++) {
        ret[i] = renderItem(source[i], i, undefined, cached && cached[i]);
      }
    } else if (typeof source === 'number') {
      ret = new Array(source);
      for (var _i2 = 0; _i2 < source; _i2++) {
        ret[_i2] = renderItem(_i2 + 1, _i2, undefined, cached && cached[_i2]);
      }
    } else if (isObject$2(source)) {
      if (source[Symbol.iterator]) {
        ret = Array.from(source, function (item, i) {
          return renderItem(item, i, undefined, cached && cached[i]);
        });
      } else {
        var keys = Object.keys(source);
        ret = new Array(keys.length);
        for (var _i3 = 0, _l = keys.length; _i3 < _l; _i3++) {
          var key = keys[_i3];
          ret[_i3] = renderItem(source[key], key, _i3, cached && cached[_i3]);
        }
      }
    } else {
      ret = [];
    }
    if (cache) {
      cache[index] = ret;
    }
    return ret;
  }

  /**
   * Compiler runtime helper for rendering `<slot/>`
   * @private
   */
  function renderSlot(slots, name, props,
  // this is not a user-facing function, so the fallback is always generated by
  // the compiler and guaranteed to be a function returning an array
  fallback, noSlotted) {
    if (props === void 0) {
      props = {};
    }
    if (currentRenderingInstance.isCE || currentRenderingInstance.parent && isAsyncWrapper(currentRenderingInstance.parent) && currentRenderingInstance.parent.isCE) {
      if (name !== 'default') props.name = name;
      return createVNode('slot', props, fallback && fallback());
    }
    var slot = slots[name];
    // a compiled slot disables block tracking by default to avoid manual
    // invocation interfering with template-based block tracking, but in
    // `renderSlot` we can be sure that it's template-based so we can force
    // enable it.
    if (slot && slot._c) {
      slot._d = false;
    }
    openBlock();
    var validSlotContent = slot && ensureValidVNode(slot(props));
    var rendered = createBlock(Fragment, {
      key: props.key ||
      // slot content array of a dynamic conditional slot may have a branch
      // key attached in the `createSlots` helper, respect that
      validSlotContent && validSlotContent.key || "_" + name
    }, validSlotContent || (fallback ? fallback() : []), validSlotContent && slots._ === 1 /* SlotFlags.STABLE */ ? 64 /* PatchFlags.STABLE_FRAGMENT */ : -2 /* PatchFlags.BAIL */);
    if (!noSlotted && rendered.scopeId) {
      rendered.slotScopeIds = [rendered.scopeId + '-s'];
    }
    if (slot && slot._c) {
      slot._d = true;
    }
    return rendered;
  }
  function ensureValidVNode(vnodes) {
    return vnodes.some(function (child) {
      if (!isVNode(child)) return true;
      if (child.type === Comment) return false;
      if (child.type === Fragment && !ensureValidVNode(child.children)) return false;
      return true;
    }) ? vnodes : null;
  }

  /**
   * #2437 In Vue 3, functional components do not have a public instance proxy but
   * they exist in the internal parent chain. For code that relies on traversing
   * public $parent chains, skip functional ones and go to the parent instead.
   */
  var getPublicInstance = function getPublicInstance(i) {
    if (!i) return null;
    if (isStatefulComponent(i)) return getExposeProxy(i) || i.proxy;
    return getPublicInstance(i.parent);
  };
  var publicPropertiesMap =
  // Move PURE marker to new line to workaround compiler discarding it
  // due to type annotation
  /*#__PURE__*/
  extend$1(Object.create(null), {
    $: function $(i) {
      return i;
    },
    $el: function $el(i) {
      return i.vnode.el;
    },
    $data: function $data(i) {
      return i.data;
    },
    $props: function $props(i) {
      return i.props;
    },
    $attrs: function $attrs(i) {
      return i.attrs;
    },
    $slots: function $slots(i) {
      return i.slots;
    },
    $refs: function $refs(i) {
      return i.refs;
    },
    $parent: function $parent(i) {
      return getPublicInstance(i.parent);
    },
    $root: function $root(i) {
      return getPublicInstance(i.root);
    },
    $emit: function $emit(i) {
      return i.emit;
    },
    $options: function $options(i) {
      return resolveMergedOptions(i);
    },
    $forceUpdate: function $forceUpdate(i) {
      return i.f || (i.f = function () {
        return queueJob(i.update);
      });
    },
    $nextTick: function $nextTick(i) {
      return i.n || (i.n = nextTick.bind(i.proxy));
    },
    $watch: function $watch(i) {
      return instanceWatch.bind(i);
    }
  });
  var hasSetupBinding = function hasSetupBinding(state, key) {
    return state !== EMPTY_OBJ && !state.__isScriptSetup && hasOwn(state, key);
  };
  var PublicInstanceProxyHandlers = {
    get: function get(_ref9, key) {
      var instance = _ref9._;
      var ctx = instance.ctx,
        setupState = instance.setupState,
        data = instance.data,
        props = instance.props,
        accessCache = instance.accessCache,
        type = instance.type,
        appContext = instance.appContext;
      // data / props / ctx
      // This getter gets called for every property access on the render context
      // during render and is a major hotspot. The most expensive part of this
      // is the multiple hasOwn() calls. It's much faster to do a simple property
      // access on a plain object, so we use an accessCache object (with null
      // prototype) to memoize what access type a key corresponds to.
      var normalizedProps;
      if (key[0] !== '$') {
        var n = accessCache[key];
        if (n !== undefined) {
          switch (n) {
            case 1 /* AccessTypes.SETUP */:
              return setupState[key];
            case 2 /* AccessTypes.DATA */:
              return data[key];
            case 4 /* AccessTypes.CONTEXT */:
              return ctx[key];
            case 3 /* AccessTypes.PROPS */:
              return props[key];
            // default: just fallthrough
          }
        } else if (hasSetupBinding(setupState, key)) {
          accessCache[key] = 1 /* AccessTypes.SETUP */;
          return setupState[key];
        } else if (data !== EMPTY_OBJ && hasOwn(data, key)) {
          accessCache[key] = 2 /* AccessTypes.DATA */;
          return data[key];
        } else if (
        // only cache other properties when instance has declared (thus stable)
        // props
        (normalizedProps = instance.propsOptions[0]) && hasOwn(normalizedProps, key)) {
          accessCache[key] = 3 /* AccessTypes.PROPS */;
          return props[key];
        } else if (ctx !== EMPTY_OBJ && hasOwn(ctx, key)) {
          accessCache[key] = 4 /* AccessTypes.CONTEXT */;
          return ctx[key];
        } else if (shouldCacheAccess) {
          accessCache[key] = 0 /* AccessTypes.OTHER */;
        }
      }

      var publicGetter = publicPropertiesMap[key];
      var cssModule, globalProperties;
      // public $xxx properties
      if (publicGetter) {
        if (key === '$attrs') {
          track(instance, "get" /* TrackOpTypes.GET */, key);
        }
        return publicGetter(instance);
      } else if (
      // css module (injected by vue-loader)
      (cssModule = type.__cssModules) && (cssModule = cssModule[key])) {
        return cssModule;
      } else if (ctx !== EMPTY_OBJ && hasOwn(ctx, key)) {
        // user may set custom properties to `this` that start with `$`
        accessCache[key] = 4 /* AccessTypes.CONTEXT */;
        return ctx[key];
      } else if (
      // global properties
      globalProperties = appContext.config.globalProperties, hasOwn(globalProperties, key)) {
        {
          return globalProperties[key];
        }
      } else ;
    },
    set: function set(_ref10, key, value) {
      var instance = _ref10._;
      var data = instance.data,
        setupState = instance.setupState,
        ctx = instance.ctx;
      if (hasSetupBinding(setupState, key)) {
        setupState[key] = value;
        return true;
      } else if (data !== EMPTY_OBJ && hasOwn(data, key)) {
        data[key] = value;
        return true;
      } else if (hasOwn(instance.props, key)) {
        return false;
      }
      if (key[0] === '$' && key.slice(1) in instance) {
        return false;
      } else {
        {
          ctx[key] = value;
        }
      }
      return true;
    },
    has: function has(_ref11, key) {
      var _ref11$_ = _ref11._,
        data = _ref11$_.data,
        setupState = _ref11$_.setupState,
        accessCache = _ref11$_.accessCache,
        ctx = _ref11$_.ctx,
        appContext = _ref11$_.appContext,
        propsOptions = _ref11$_.propsOptions;
      var normalizedProps;
      return !!accessCache[key] || data !== EMPTY_OBJ && hasOwn(data, key) || hasSetupBinding(setupState, key) || (normalizedProps = propsOptions[0]) && hasOwn(normalizedProps, key) || hasOwn(ctx, key) || hasOwn(publicPropertiesMap, key) || hasOwn(appContext.config.globalProperties, key);
    },
    defineProperty: function defineProperty(target, key, descriptor) {
      if (descriptor.get != null) {
        // invalidate key cache of a getter based property #5417
        target._.accessCache[key] = 0;
      } else if (hasOwn(descriptor, 'value')) {
        this.set(target, key, descriptor.value, null);
      }
      return Reflect.defineProperty(target, key, descriptor);
    }
  };
  var shouldCacheAccess = true;
  function applyOptions(instance) {
    var options = resolveMergedOptions(instance);
    var publicThis = instance.proxy;
    var ctx = instance.ctx;
    // do not cache property access on public proxy during state initialization
    shouldCacheAccess = false;
    // call beforeCreate first before accessing other options since
    // the hook may mutate resolved options (#2791)
    if (options.beforeCreate) {
      callHook$1(options.beforeCreate, instance, "bc" /* LifecycleHooks.BEFORE_CREATE */);
    }

    var dataOptions = options.data,
      computedOptions = options.computed,
      methods = options.methods,
      watchOptions = options.watch,
      provideOptions = options.provide,
      injectOptions = options.inject,
      created = options.created,
      beforeMount = options.beforeMount,
      mounted = options.mounted,
      beforeUpdate = options.beforeUpdate,
      updated = options.updated,
      activated = options.activated,
      deactivated = options.deactivated;
      options.beforeDestroy;
      var beforeUnmount = options.beforeUnmount;
      options.destroyed;
      var unmounted = options.unmounted,
      render = options.render,
      renderTracked = options.renderTracked,
      renderTriggered = options.renderTriggered,
      errorCaptured = options.errorCaptured,
      serverPrefetch = options.serverPrefetch,
      expose = options.expose,
      inheritAttrs = options.inheritAttrs,
      components = options.components,
      directives = options.directives;
      options.filters;
    var checkDuplicateProperties = null;
    // options initialization order (to be consistent with Vue 2):
    // - props (already done outside of this function)
    // - inject
    // - methods
    // - data (deferred since it relies on `this` access)
    // - computed
    // - watch (deferred since it relies on `this` access)
    if (injectOptions) {
      resolveInjections(injectOptions, ctx, checkDuplicateProperties, instance.appContext.config.unwrapInjectedRef);
    }
    if (methods) {
      for (var key in methods) {
        var methodHandler = methods[key];
        if (isFunction$1(methodHandler)) {
          // In dev mode, we use the `createRenderContext` function to define
          // methods to the proxy target, and those are read-only but
          // reconfigurable, so it needs to be redefined here
          {
            ctx[key] = methodHandler.bind(publicThis);
          }
        }
      }
    }
    if (dataOptions) {
      var data = dataOptions.call(publicThis, publicThis);
      if (!isObject$2(data)) ;else {
        instance.data = reactive(data);
      }
    }
    // state initialization complete at this point - start caching access
    shouldCacheAccess = true;
    if (computedOptions) {
      var _loop = function _loop() {
        var opt = computedOptions[_key5];
        var get = isFunction$1(opt) ? opt.bind(publicThis, publicThis) : isFunction$1(opt.get) ? opt.get.bind(publicThis, publicThis) : NOOP;
        var set = !isFunction$1(opt) && isFunction$1(opt.set) ? opt.set.bind(publicThis) : NOOP;
        var c = computed({
          get: get,
          set: set
        });
        Object.defineProperty(ctx, _key5, {
          enumerable: true,
          configurable: true,
          get: function get() {
            return c.value;
          },
          set: function set(v) {
            return c.value = v;
          }
        });
      };
      for (var _key5 in computedOptions) {
        _loop();
      }
    }
    if (watchOptions) {
      for (var _key7 in watchOptions) {
        createWatcher(watchOptions[_key7], ctx, publicThis, _key7);
      }
    }
    if (provideOptions) {
      var provides = isFunction$1(provideOptions) ? provideOptions.call(publicThis) : provideOptions;
      Reflect.ownKeys(provides).forEach(function (key) {
        provide(key, provides[key]);
      });
    }
    if (created) {
      callHook$1(created, instance, "c" /* LifecycleHooks.CREATED */);
    }

    function registerLifecycleHook(register, hook) {
      if (isArray$1(hook)) {
        hook.forEach(function (_hook) {
          return register(_hook.bind(publicThis));
        });
      } else if (hook) {
        register(hook.bind(publicThis));
      }
    }
    registerLifecycleHook(onBeforeMount, beforeMount);
    registerLifecycleHook(onMounted, mounted);
    registerLifecycleHook(onBeforeUpdate, beforeUpdate);
    registerLifecycleHook(onUpdated, updated);
    registerLifecycleHook(onActivated, activated);
    registerLifecycleHook(onDeactivated, deactivated);
    registerLifecycleHook(onErrorCaptured, errorCaptured);
    registerLifecycleHook(onRenderTracked, renderTracked);
    registerLifecycleHook(onRenderTriggered, renderTriggered);
    registerLifecycleHook(onBeforeUnmount, beforeUnmount);
    registerLifecycleHook(onUnmounted, unmounted);
    registerLifecycleHook(onServerPrefetch, serverPrefetch);
    if (isArray$1(expose)) {
      if (expose.length) {
        var exposed = instance.exposed || (instance.exposed = {});
        expose.forEach(function (key) {
          Object.defineProperty(exposed, key, {
            get: function get() {
              return publicThis[key];
            },
            set: function set(val) {
              return publicThis[key] = val;
            }
          });
        });
      } else if (!instance.exposed) {
        instance.exposed = {};
      }
    }
    // options that are handled when creating the instance but also need to be
    // applied from mixins
    if (render && instance.render === NOOP) {
      instance.render = render;
    }
    if (inheritAttrs != null) {
      instance.inheritAttrs = inheritAttrs;
    }
    // asset options.
    if (components) instance.components = components;
    if (directives) instance.directives = directives;
  }
  function resolveInjections(injectOptions, ctx, checkDuplicateProperties, unwrapRef) {
    if (unwrapRef === void 0) {
      unwrapRef = false;
    }
    if (isArray$1(injectOptions)) {
      injectOptions = normalizeInject(injectOptions);
    }
    var _loop2 = function _loop2() {
      var opt = injectOptions[key];
      var injected;
      if (isObject$2(opt)) {
        if ('default' in opt) {
          injected = inject(opt.from || key, opt.default, true /* treat default function as factory */);
        } else {
          injected = inject(opt.from || key);
        }
      } else {
        injected = inject(opt);
      }
      if (isRef(injected)) {
        // TODO remove the check in 3.3
        if (unwrapRef) {
          Object.defineProperty(ctx, key, {
            enumerable: true,
            configurable: true,
            get: function get() {
              return injected.value;
            },
            set: function set(v) {
              return injected.value = v;
            }
          });
        } else {
          ctx[key] = injected;
        }
      } else {
        ctx[key] = injected;
      }
    };
    for (var key in injectOptions) {
      _loop2();
    }
  }
  function callHook$1(hook, instance, type) {
    callWithAsyncErrorHandling(isArray$1(hook) ? hook.map(function (h) {
      return h.bind(instance.proxy);
    }) : hook.bind(instance.proxy), instance, type);
  }
  function createWatcher(raw, ctx, publicThis, key) {
    var getter = key.includes('.') ? createPathGetter(publicThis, key) : function () {
      return publicThis[key];
    };
    if (isString$1(raw)) {
      var handler = ctx[raw];
      if (isFunction$1(handler)) {
        watch(getter, handler);
      }
    } else if (isFunction$1(raw)) {
      watch(getter, raw.bind(publicThis));
    } else if (isObject$2(raw)) {
      if (isArray$1(raw)) {
        raw.forEach(function (r) {
          return createWatcher(r, ctx, publicThis, key);
        });
      } else {
        var _handler = isFunction$1(raw.handler) ? raw.handler.bind(publicThis) : ctx[raw.handler];
        if (isFunction$1(_handler)) {
          watch(getter, _handler, raw);
        }
      }
    } else ;
  }
  /**
   * Resolve merged options and cache it on the component.
   * This is done only once per-component since the merging does not involve
   * instances.
   */
  function resolveMergedOptions(instance) {
    var base = instance.type;
    var mixins = base.mixins,
      extendsOptions = base.extends;
    var _instance$appContext = instance.appContext,
      globalMixins = _instance$appContext.mixins,
      cache = _instance$appContext.optionsCache,
      optionMergeStrategies = _instance$appContext.config.optionMergeStrategies;
    var cached = cache.get(base);
    var resolved;
    if (cached) {
      resolved = cached;
    } else if (!globalMixins.length && !mixins && !extendsOptions) {
      {
        resolved = base;
      }
    } else {
      resolved = {};
      if (globalMixins.length) {
        globalMixins.forEach(function (m) {
          return mergeOptions(resolved, m, optionMergeStrategies, true);
        });
      }
      mergeOptions(resolved, base, optionMergeStrategies);
    }
    if (isObject$2(base)) {
      cache.set(base, resolved);
    }
    return resolved;
  }
  function mergeOptions(to, from, strats, asMixin) {
    if (asMixin === void 0) {
      asMixin = false;
    }
    var mixins = from.mixins,
      extendsOptions = from.extends;
    if (extendsOptions) {
      mergeOptions(to, extendsOptions, strats, true);
    }
    if (mixins) {
      mixins.forEach(function (m) {
        return mergeOptions(to, m, strats, true);
      });
    }
    for (var key in from) {
      if (asMixin && key === 'expose') ;else {
        var strat = internalOptionMergeStrats[key] || strats && strats[key];
        to[key] = strat ? strat(to[key], from[key]) : from[key];
      }
    }
    return to;
  }
  var internalOptionMergeStrats = {
    data: mergeDataFn,
    props: mergeObjectOptions,
    emits: mergeObjectOptions,
    // objects
    methods: mergeObjectOptions,
    computed: mergeObjectOptions,
    // lifecycle
    beforeCreate: mergeAsArray,
    created: mergeAsArray,
    beforeMount: mergeAsArray,
    mounted: mergeAsArray,
    beforeUpdate: mergeAsArray,
    updated: mergeAsArray,
    beforeDestroy: mergeAsArray,
    beforeUnmount: mergeAsArray,
    destroyed: mergeAsArray,
    unmounted: mergeAsArray,
    activated: mergeAsArray,
    deactivated: mergeAsArray,
    errorCaptured: mergeAsArray,
    serverPrefetch: mergeAsArray,
    // assets
    components: mergeObjectOptions,
    directives: mergeObjectOptions,
    // watch
    watch: mergeWatchOptions,
    // provide / inject
    provide: mergeDataFn,
    inject: mergeInject
  };
  function mergeDataFn(to, from) {
    if (!from) {
      return to;
    }
    if (!to) {
      return from;
    }
    return function mergedDataFn() {
      return extend$1(isFunction$1(to) ? to.call(this, this) : to, isFunction$1(from) ? from.call(this, this) : from);
    };
  }
  function mergeInject(to, from) {
    return mergeObjectOptions(normalizeInject(to), normalizeInject(from));
  }
  function normalizeInject(raw) {
    if (isArray$1(raw)) {
      var res = {};
      for (var i = 0; i < raw.length; i++) {
        res[raw[i]] = raw[i];
      }
      return res;
    }
    return raw;
  }
  function mergeAsArray(to, from) {
    return to ? [].concat(new Set([].concat(to, from))) : from;
  }
  function mergeObjectOptions(to, from) {
    return to ? extend$1(extend$1(Object.create(null), to), from) : from;
  }
  function mergeWatchOptions(to, from) {
    if (!to) return from;
    if (!from) return to;
    var merged = extend$1(Object.create(null), to);
    for (var key in from) {
      merged[key] = mergeAsArray(to[key], from[key]);
    }
    return merged;
  }
  function initProps(instance, rawProps, isStateful,
  // result of bitwise flag comparison
  isSSR) {
    if (isSSR === void 0) {
      isSSR = false;
    }
    var props = {};
    var attrs = {};
    def(attrs, InternalObjectKey, 1);
    instance.propsDefaults = Object.create(null);
    setFullProps(instance, rawProps, props, attrs);
    // ensure all declared prop keys are present
    for (var key in instance.propsOptions[0]) {
      if (!(key in props)) {
        props[key] = undefined;
      }
    }
    if (isStateful) {
      // stateful
      instance.props = isSSR ? props : shallowReactive(props);
    } else {
      if (!instance.type.props) {
        // functional w/ optional props, props === attrs
        instance.props = attrs;
      } else {
        // functional w/ declared props
        instance.props = props;
      }
    }
    instance.attrs = attrs;
  }
  function updateProps(instance, rawProps, rawPrevProps, optimized) {
    var props = instance.props,
      attrs = instance.attrs,
      patchFlag = instance.vnode.patchFlag;
    var rawCurrentProps = toRaw(props);
    var _instance$propsOption2 = instance.propsOptions,
      options = _instance$propsOption2[0];
    var hasAttrsChanged = false;
    if (
    // always force full diff in dev
    // - #1942 if hmr is enabled with sfc component
    // - vite#872 non-sfc component used by sfc component
    (optimized || patchFlag > 0) && !(patchFlag & 16 /* PatchFlags.FULL_PROPS */)) {
      if (patchFlag & 8 /* PatchFlags.PROPS */) {
        // Compiler-generated props & no keys change, just set the updated
        // the props.
        var propsToUpdate = instance.vnode.dynamicProps;
        for (var i = 0; i < propsToUpdate.length; i++) {
          var key = propsToUpdate[i];
          // skip if the prop key is a declared emit event listener
          if (isEmitListener(instance.emitsOptions, key)) {
            continue;
          }
          // PROPS flag guarantees rawProps to be non-null
          var value = rawProps[key];
          if (options) {
            // attr / props separation was done on init and will be consistent
            // in this code path, so just check if attrs have it.
            if (hasOwn(attrs, key)) {
              if (value !== attrs[key]) {
                attrs[key] = value;
                hasAttrsChanged = true;
              }
            } else {
              var camelizedKey = camelize(key);
              props[camelizedKey] = resolvePropValue(options, rawCurrentProps, camelizedKey, value, instance, false /* isAbsent */);
            }
          } else {
            if (value !== attrs[key]) {
              attrs[key] = value;
              hasAttrsChanged = true;
            }
          }
        }
      }
    } else {
      // full props update.
      if (setFullProps(instance, rawProps, props, attrs)) {
        hasAttrsChanged = true;
      }
      // in case of dynamic props, check if we need to delete keys from
      // the props object
      var kebabKey;
      for (var _key8 in rawCurrentProps) {
        if (!rawProps ||
        // for camelCase
        !hasOwn(rawProps, _key8) && (
        // it's possible the original props was passed in as kebab-case
        // and converted to camelCase (#955)
        (kebabKey = hyphenate$1(_key8)) === _key8 || !hasOwn(rawProps, kebabKey))) {
          if (options) {
            if (rawPrevProps && (
            // for camelCase
            rawPrevProps[_key8] !== undefined ||
            // for kebab-case
            rawPrevProps[kebabKey] !== undefined)) {
              props[_key8] = resolvePropValue(options, rawCurrentProps, _key8, undefined, instance, true /* isAbsent */);
            }
          } else {
            delete props[_key8];
          }
        }
      }
      // in the case of functional component w/o props declaration, props and
      // attrs point to the same object so it should already have been updated.
      if (attrs !== rawCurrentProps) {
        for (var _key9 in attrs) {
          if (!rawProps || !hasOwn(rawProps, _key9) && !false) {
            delete attrs[_key9];
            hasAttrsChanged = true;
          }
        }
      }
    }
    // trigger updates for $attrs in case it's used in component slots
    if (hasAttrsChanged) {
      trigger(instance, "set" /* TriggerOpTypes.SET */, '$attrs');
    }
  }
  function setFullProps(instance, rawProps, props, attrs) {
    var _instance$propsOption3 = instance.propsOptions,
      options = _instance$propsOption3[0],
      needCastKeys = _instance$propsOption3[1];
    var hasAttrsChanged = false;
    var rawCastValues;
    if (rawProps) {
      for (var key in rawProps) {
        // key, ref are reserved and never passed down
        if (isReservedProp(key)) {
          continue;
        }
        var value = rawProps[key];
        // prop option names are camelized during normalization, so to support
        // kebab -> camel conversion here we need to camelize the key.
        var camelKey = void 0;
        if (options && hasOwn(options, camelKey = camelize(key))) {
          if (!needCastKeys || !needCastKeys.includes(camelKey)) {
            props[camelKey] = value;
          } else {
            (rawCastValues || (rawCastValues = {}))[camelKey] = value;
          }
        } else if (!isEmitListener(instance.emitsOptions, key)) {
          if (!(key in attrs) || value !== attrs[key]) {
            attrs[key] = value;
            hasAttrsChanged = true;
          }
        }
      }
    }
    if (needCastKeys) {
      var rawCurrentProps = toRaw(props);
      var castValues = rawCastValues || EMPTY_OBJ;
      for (var i = 0; i < needCastKeys.length; i++) {
        var _key10 = needCastKeys[i];
        props[_key10] = resolvePropValue(options, rawCurrentProps, _key10, castValues[_key10], instance, !hasOwn(castValues, _key10));
      }
    }
    return hasAttrsChanged;
  }
  function resolvePropValue(options, props, key, value, instance, isAbsent) {
    var opt = options[key];
    if (opt != null) {
      var hasDefault = hasOwn(opt, 'default');
      // default values
      if (hasDefault && value === undefined) {
        var defaultValue = opt.default;
        if (opt.type !== Function && isFunction$1(defaultValue)) {
          var propsDefaults = instance.propsDefaults;
          if (key in propsDefaults) {
            value = propsDefaults[key];
          } else {
            setCurrentInstance(instance);
            value = propsDefaults[key] = defaultValue.call(null, props);
            unsetCurrentInstance();
          }
        } else {
          value = defaultValue;
        }
      }
      // boolean casting
      if (opt[0 /* BooleanFlags.shouldCast */]) {
        if (isAbsent && !hasDefault) {
          value = false;
        } else if (opt[1 /* BooleanFlags.shouldCastTrue */] && (value === '' || value === hyphenate$1(key))) {
          value = true;
        }
      }
    }
    return value;
  }
  function normalizePropsOptions(comp, appContext, asMixin) {
    if (asMixin === void 0) {
      asMixin = false;
    }
    var cache = appContext.propsCache;
    var cached = cache.get(comp);
    if (cached) {
      return cached;
    }
    var raw = comp.props;
    var normalized = {};
    var needCastKeys = [];
    // apply mixin/extends props
    var hasExtends = false;
    if (!isFunction$1(comp)) {
      var extendProps = function extendProps(raw) {
        hasExtends = true;
        var _normalizePropsOption = normalizePropsOptions(raw, appContext, true),
          props = _normalizePropsOption[0],
          keys = _normalizePropsOption[1];
        extend$1(normalized, props);
        if (keys) needCastKeys.push.apply(needCastKeys, keys);
      };
      if (!asMixin && appContext.mixins.length) {
        appContext.mixins.forEach(extendProps);
      }
      if (comp.extends) {
        extendProps(comp.extends);
      }
      if (comp.mixins) {
        comp.mixins.forEach(extendProps);
      }
    }
    if (!raw && !hasExtends) {
      if (isObject$2(comp)) {
        cache.set(comp, EMPTY_ARR);
      }
      return EMPTY_ARR;
    }
    if (isArray$1(raw)) {
      for (var i = 0; i < raw.length; i++) {
        var normalizedKey = camelize(raw[i]);
        if (validatePropName(normalizedKey)) {
          normalized[normalizedKey] = EMPTY_OBJ;
        }
      }
    } else if (raw) {
      for (var key in raw) {
        var _normalizedKey = camelize(key);
        if (validatePropName(_normalizedKey)) {
          var opt = raw[key];
          var prop = normalized[_normalizedKey] = isArray$1(opt) || isFunction$1(opt) ? {
            type: opt
          } : Object.assign({}, opt);
          if (prop) {
            var booleanIndex = getTypeIndex(Boolean, prop.type);
            var stringIndex = getTypeIndex(String, prop.type);
            prop[0 /* BooleanFlags.shouldCast */] = booleanIndex > -1;
            prop[1 /* BooleanFlags.shouldCastTrue */] = stringIndex < 0 || booleanIndex < stringIndex;
            // if the prop needs boolean casting or default value
            if (booleanIndex > -1 || hasOwn(prop, 'default')) {
              needCastKeys.push(_normalizedKey);
            }
          }
        }
      }
    }
    var res = [normalized, needCastKeys];
    if (isObject$2(comp)) {
      cache.set(comp, res);
    }
    return res;
  }
  function validatePropName(key) {
    if (key[0] !== '$') {
      return true;
    }
    return false;
  }
  // use function string name to check type constructors
  // so that it works across vms / iframes.
  function getType(ctor) {
    var match = ctor && ctor.toString().match(/^\s*function (\w+)/);
    return match ? match[1] : ctor === null ? 'null' : '';
  }
  function isSameType(a, b) {
    return getType(a) === getType(b);
  }
  function getTypeIndex(type, expectedTypes) {
    if (isArray$1(expectedTypes)) {
      return expectedTypes.findIndex(function (t) {
        return isSameType(t, type);
      });
    } else if (isFunction$1(expectedTypes)) {
      return isSameType(expectedTypes, type) ? 0 : -1;
    }
    return -1;
  }
  var isInternalKey = function isInternalKey(key) {
    return key[0] === '_' || key === '$stable';
  };
  var normalizeSlotValue = function normalizeSlotValue(value) {
    return isArray$1(value) ? value.map(normalizeVNode) : [normalizeVNode(value)];
  };
  var normalizeSlot = function normalizeSlot(key, rawSlot, ctx) {
    if (rawSlot._n) {
      // already normalized - #5353
      return rawSlot;
    }
    var normalized = withCtx(function () {
      return normalizeSlotValue(rawSlot.apply(void 0, arguments));
    }, ctx);
    normalized._c = false;
    return normalized;
  };
  var normalizeObjectSlots = function normalizeObjectSlots(rawSlots, slots, instance) {
    var ctx = rawSlots._ctx;
    var _loop3 = function _loop3() {
      if (isInternalKey(key)) return "continue";
      var value = rawSlots[key];
      if (isFunction$1(value)) {
        slots[key] = normalizeSlot(key, value, ctx);
      } else if (value != null) {
        var normalized = normalizeSlotValue(value);
        slots[key] = function () {
          return normalized;
        };
      }
    };
    for (var key in rawSlots) {
      var _ret = _loop3();
      if (_ret === "continue") continue;
    }
  };
  var normalizeVNodeSlots = function normalizeVNodeSlots(instance, children) {
    var normalized = normalizeSlotValue(children);
    instance.slots.default = function () {
      return normalized;
    };
  };
  var initSlots = function initSlots(instance, children) {
    if (instance.vnode.shapeFlag & 32 /* ShapeFlags.SLOTS_CHILDREN */) {
      var type = children._;
      if (type) {
        // users can get the shallow readonly version of the slots object through `this.$slots`,
        // we should avoid the proxy object polluting the slots of the internal instance
        instance.slots = toRaw(children);
        // make compiler marker non-enumerable
        def(children, '_', type);
      } else {
        normalizeObjectSlots(children, instance.slots = {});
      }
    } else {
      instance.slots = {};
      if (children) {
        normalizeVNodeSlots(instance, children);
      }
    }
    def(instance.slots, InternalObjectKey, 1);
  };
  var updateSlots = function updateSlots(instance, children, optimized) {
    var vnode = instance.vnode,
      slots = instance.slots;
    var needDeletionCheck = true;
    var deletionComparisonTarget = EMPTY_OBJ;
    if (vnode.shapeFlag & 32 /* ShapeFlags.SLOTS_CHILDREN */) {
      var type = children._;
      if (type) {
        // compiled slots.
        if (optimized && type === 1 /* SlotFlags.STABLE */) {
          // compiled AND stable.
          // no need to update, and skip stale slots removal.
          needDeletionCheck = false;
        } else {
          // compiled but dynamic (v-if/v-for on slots) - update slots, but skip
          // normalization.
          extend$1(slots, children);
          // #2893
          // when rendering the optimized slots by manually written render function,
          // we need to delete the `slots._` flag if necessary to make subsequent updates reliable,
          // i.e. let the `renderSlot` create the bailed Fragment
          if (!optimized && type === 1 /* SlotFlags.STABLE */) {
            delete slots._;
          }
        }
      } else {
        needDeletionCheck = !children.$stable;
        normalizeObjectSlots(children, slots);
      }
      deletionComparisonTarget = children;
    } else if (children) {
      // non slot object children (direct value) passed to a component
      normalizeVNodeSlots(instance, children);
      deletionComparisonTarget = {
        default: 1
      };
    }
    // delete stale slots
    if (needDeletionCheck) {
      for (var key in slots) {
        if (!isInternalKey(key) && !(key in deletionComparisonTarget)) {
          delete slots[key];
        }
      }
    }
  };
  function createAppContext() {
    return {
      app: null,
      config: {
        isNativeTag: NO,
        performance: false,
        globalProperties: {},
        optionMergeStrategies: {},
        errorHandler: undefined,
        warnHandler: undefined,
        compilerOptions: {}
      },
      mixins: [],
      components: {},
      directives: {},
      provides: Object.create(null),
      optionsCache: new WeakMap(),
      propsCache: new WeakMap(),
      emitsCache: new WeakMap()
    };
  }
  var uid = 0;
  function createAppAPI(render, hydrate) {
    return function createApp(rootComponent, rootProps) {
      if (rootProps === void 0) {
        rootProps = null;
      }
      if (!isFunction$1(rootComponent)) {
        rootComponent = Object.assign({}, rootComponent);
      }
      if (rootProps != null && !isObject$2(rootProps)) {
        rootProps = null;
      }
      var context = createAppContext();
      var installedPlugins = new Set();
      var isMounted = false;
      var app = context.app = {
        _uid: uid++,
        _component: rootComponent,
        _props: rootProps,
        _container: null,
        _context: context,
        _instance: null,
        version: version,
        get config() {
          return context.config;
        },
        set config(v) {},
        use: function use(plugin) {
          for (var _len6 = arguments.length, options = new Array(_len6 > 1 ? _len6 - 1 : 0), _key6 = 1; _key6 < _len6; _key6++) {
            options[_key6 - 1] = arguments[_key6];
          }
          if (installedPlugins.has(plugin)) ;else if (plugin && isFunction$1(plugin.install)) {
            installedPlugins.add(plugin);
            plugin.install.apply(plugin, [app].concat(options));
          } else if (isFunction$1(plugin)) {
            installedPlugins.add(plugin);
            plugin.apply(void 0, [app].concat(options));
          } else ;
          return app;
        },
        mixin: function mixin(_mixin) {
          {
            if (!context.mixins.includes(_mixin)) {
              context.mixins.push(_mixin);
            }
          }
          return app;
        },
        component: function component(name, _component) {
          if (!_component) {
            return context.components[name];
          }
          context.components[name] = _component;
          return app;
        },
        directive: function directive(name, _directive) {
          if (!_directive) {
            return context.directives[name];
          }
          context.directives[name] = _directive;
          return app;
        },
        mount: function mount(rootContainer, isHydrate, isSVG) {
          if (!isMounted) {
            var vnode = createVNode(rootComponent, rootProps);
            // store app context on the root VNode.
            // this will be set on the root instance on initial mount.
            vnode.appContext = context;
            if (isHydrate && hydrate) {
              hydrate(vnode, rootContainer);
            } else {
              render(vnode, rootContainer, isSVG);
            }
            isMounted = true;
            app._container = rootContainer;
            rootContainer.__vue_app__ = app;
            return getExposeProxy(vnode.component) || vnode.component.proxy;
          }
        },
        unmount: function unmount() {
          if (isMounted) {
            render(null, app._container);
            delete app._container.__vue_app__;
          }
        },
        provide: function provide(key, value) {
          context.provides[key] = value;
          return app;
        }
      };
      return app;
    };
  }

  /**
   * Function for handling a template ref
   */
  function setRef(rawRef, oldRawRef, parentSuspense, vnode, isUnmount) {
    if (isUnmount === void 0) {
      isUnmount = false;
    }
    if (isArray$1(rawRef)) {
      rawRef.forEach(function (r, i) {
        return setRef(r, oldRawRef && (isArray$1(oldRawRef) ? oldRawRef[i] : oldRawRef), parentSuspense, vnode, isUnmount);
      });
      return;
    }
    if (isAsyncWrapper(vnode) && !isUnmount) {
      // when mounting async components, nothing needs to be done,
      // because the template ref is forwarded to inner component
      return;
    }
    var refValue = vnode.shapeFlag & 4 /* ShapeFlags.STATEFUL_COMPONENT */ ? getExposeProxy(vnode.component) || vnode.component.proxy : vnode.el;
    var value = isUnmount ? null : refValue;
    var owner = rawRef.i,
      ref = rawRef.r;
    var oldRef = oldRawRef && oldRawRef.r;
    var refs = owner.refs === EMPTY_OBJ ? owner.refs = {} : owner.refs;
    var setupState = owner.setupState;
    // dynamic ref changed. unset old ref
    if (oldRef != null && oldRef !== ref) {
      if (isString$1(oldRef)) {
        refs[oldRef] = null;
        if (hasOwn(setupState, oldRef)) {
          setupState[oldRef] = null;
        }
      } else if (isRef(oldRef)) {
        oldRef.value = null;
      }
    }
    if (isFunction$1(ref)) {
      callWithErrorHandling(ref, owner, 12 /* ErrorCodes.FUNCTION_REF */, [value, refs]);
    } else {
      var _isString = isString$1(ref);
      var _isRef = isRef(ref);
      if (_isString || _isRef) {
        var doSet = function doSet() {
          if (rawRef.f) {
            var existing = _isString ? hasOwn(setupState, ref) ? setupState[ref] : refs[ref] : ref.value;
            if (isUnmount) {
              isArray$1(existing) && remove(existing, refValue);
            } else {
              if (!isArray$1(existing)) {
                if (_isString) {
                  refs[ref] = [refValue];
                  if (hasOwn(setupState, ref)) {
                    setupState[ref] = refs[ref];
                  }
                } else {
                  ref.value = [refValue];
                  if (rawRef.k) refs[rawRef.k] = ref.value;
                }
              } else if (!existing.includes(refValue)) {
                existing.push(refValue);
              }
            }
          } else if (_isString) {
            refs[ref] = value;
            if (hasOwn(setupState, ref)) {
              setupState[ref] = value;
            }
          } else if (_isRef) {
            ref.value = value;
            if (rawRef.k) refs[rawRef.k] = value;
          } else ;
        };
        if (value) {
          doSet.id = -1;
          queuePostRenderEffect(doSet, parentSuspense);
        } else {
          doSet();
        }
      }
    }
  }
  var queuePostRenderEffect = queueEffectWithSuspense;
  /**
   * The createRenderer function accepts two generic arguments:
   * HostNode and HostElement, corresponding to Node and Element types in the
   * host environment. For example, for runtime-dom, HostNode would be the DOM
   * `Node` interface and HostElement would be the DOM `Element` interface.
   *
   * Custom renderers can pass in the platform specific types like this:
   *
   * ``` js
   * const { render, createApp } = createRenderer<Node, Element>({
   *   patchProp,
   *   ...nodeOps
   * })
   * ```
   */
  function createRenderer(options) {
    return baseCreateRenderer(options);
  }
  // implementation
  function baseCreateRenderer(options, createHydrationFns) {
    var target = getGlobalThis();
    target.__VUE__ = true;
    var hostInsert = options.insert,
      hostRemove = options.remove,
      hostPatchProp = options.patchProp,
      hostCreateElement = options.createElement,
      hostCreateText = options.createText,
      hostCreateComment = options.createComment,
      hostSetText = options.setText,
      hostSetElementText = options.setElementText,
      hostParentNode = options.parentNode,
      hostNextSibling = options.nextSibling,
      _options$setScopeId = options.setScopeId,
      hostSetScopeId = _options$setScopeId === void 0 ? NOOP : _options$setScopeId,
      hostInsertStaticContent = options.insertStaticContent;
    // Note: functions inside this closure should use `const xxx = () => {}`
    // style in order to prevent being inlined by minifiers.
    var patch = function patch(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      if (anchor === void 0) {
        anchor = null;
      }
      if (parentComponent === void 0) {
        parentComponent = null;
      }
      if (parentSuspense === void 0) {
        parentSuspense = null;
      }
      if (isSVG === void 0) {
        isSVG = false;
      }
      if (slotScopeIds === void 0) {
        slotScopeIds = null;
      }
      if (optimized === void 0) {
        optimized = !!n2.dynamicChildren;
      }
      if (n1 === n2) {
        return;
      }
      // patching & not same type, unmount old tree
      if (n1 && !isSameVNodeType(n1, n2)) {
        anchor = getNextHostNode(n1);
        unmount(n1, parentComponent, parentSuspense, true);
        n1 = null;
      }
      if (n2.patchFlag === -2 /* PatchFlags.BAIL */) {
        optimized = false;
        n2.dynamicChildren = null;
      }
      var type = n2.type,
        ref = n2.ref,
        shapeFlag = n2.shapeFlag;
      switch (type) {
        case Text:
          processText(n1, n2, container, anchor);
          break;
        case Comment:
          processCommentNode(n1, n2, container, anchor);
          break;
        case Static:
          if (n1 == null) {
            mountStaticNode(n2, container, anchor, isSVG);
          }
          break;
        case Fragment:
          processFragment(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          break;
        default:
          if (shapeFlag & 1 /* ShapeFlags.ELEMENT */) {
            processElement(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          } else if (shapeFlag & 6 /* ShapeFlags.COMPONENT */) {
            processComponent(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          } else if (shapeFlag & 64 /* ShapeFlags.TELEPORT */) {
            type.process(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized, internals);
          } else if (shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
            type.process(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized, internals);
          } else ;
      }
      // set ref
      if (ref != null && parentComponent) {
        setRef(ref, n1 && n1.ref, parentSuspense, n2 || n1, !n2);
      }
    };
    var processText = function processText(n1, n2, container, anchor) {
      if (n1 == null) {
        hostInsert(n2.el = hostCreateText(n2.children), container, anchor);
      } else {
        var el = n2.el = n1.el;
        if (n2.children !== n1.children) {
          hostSetText(el, n2.children);
        }
      }
    };
    var processCommentNode = function processCommentNode(n1, n2, container, anchor) {
      if (n1 == null) {
        hostInsert(n2.el = hostCreateComment(n2.children || ''), container, anchor);
      } else {
        // there's no support for dynamic comments
        n2.el = n1.el;
      }
    };
    var mountStaticNode = function mountStaticNode(n2, container, anchor, isSVG) {
      var _hostInsertStaticCont = hostInsertStaticContent(n2.children, container, anchor, isSVG, n2.el, n2.anchor);
      n2.el = _hostInsertStaticCont[0];
      n2.anchor = _hostInsertStaticCont[1];
    };
    var moveStaticNode = function moveStaticNode(_ref12, container, nextSibling) {
      var el = _ref12.el,
        anchor = _ref12.anchor;
      var next;
      while (el && el !== anchor) {
        next = hostNextSibling(el);
        hostInsert(el, container, nextSibling);
        el = next;
      }
      hostInsert(anchor, container, nextSibling);
    };
    var removeStaticNode = function removeStaticNode(_ref13) {
      var el = _ref13.el,
        anchor = _ref13.anchor;
      var next;
      while (el && el !== anchor) {
        next = hostNextSibling(el);
        hostRemove(el);
        el = next;
      }
      hostRemove(anchor);
    };
    var processElement = function processElement(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      isSVG = isSVG || n2.type === 'svg';
      if (n1 == null) {
        mountElement(n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
      } else {
        patchElement(n1, n2, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
      }
    };
    var mountElement = function mountElement(vnode, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      var el;
      var vnodeHook;
      var type = vnode.type,
        props = vnode.props,
        shapeFlag = vnode.shapeFlag,
        transition = vnode.transition,
        dirs = vnode.dirs;
      el = vnode.el = hostCreateElement(vnode.type, isSVG, props && props.is, props);
      // mount children first, since some props may rely on child content
      // being already rendered, e.g. `<select value>`
      if (shapeFlag & 8 /* ShapeFlags.TEXT_CHILDREN */) {
        hostSetElementText(el, vnode.children);
      } else if (shapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
        mountChildren(vnode.children, el, null, parentComponent, parentSuspense, isSVG && type !== 'foreignObject', slotScopeIds, optimized);
      }
      if (dirs) {
        invokeDirectiveHook(vnode, null, parentComponent, 'created');
      }
      // props
      if (props) {
        for (var key in props) {
          if (key !== 'value' && !isReservedProp(key)) {
            hostPatchProp(el, key, null, props[key], isSVG, vnode.children, parentComponent, parentSuspense, unmountChildren);
          }
        }
        /**
         * Special case for setting value on DOM elements:
         * - it can be order-sensitive (e.g. should be set *after* min/max, #2325, #4024)
         * - it needs to be forced (#1471)
         * #2353 proposes adding another renderer option to configure this, but
         * the properties affects are so finite it is worth special casing it
         * here to reduce the complexity. (Special casing it also should not
         * affect non-DOM renderers)
         */
        if ('value' in props) {
          hostPatchProp(el, 'value', null, props.value);
        }
        if (vnodeHook = props.onVnodeBeforeMount) {
          invokeVNodeHook(vnodeHook, parentComponent, vnode);
        }
      }
      // scopeId
      setScopeId(el, vnode, vnode.scopeId, slotScopeIds, parentComponent);
      if (dirs) {
        invokeDirectiveHook(vnode, null, parentComponent, 'beforeMount');
      }
      // #1583 For inside suspense + suspense not resolved case, enter hook should call when suspense resolved
      // #1689 For inside suspense + suspense resolved case, just call it
      var needCallTransitionHooks = (!parentSuspense || parentSuspense && !parentSuspense.pendingBranch) && transition && !transition.persisted;
      if (needCallTransitionHooks) {
        transition.beforeEnter(el);
      }
      hostInsert(el, container, anchor);
      if ((vnodeHook = props && props.onVnodeMounted) || needCallTransitionHooks || dirs) {
        queuePostRenderEffect(function () {
          vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, vnode);
          needCallTransitionHooks && transition.enter(el);
          dirs && invokeDirectiveHook(vnode, null, parentComponent, 'mounted');
        }, parentSuspense);
      }
    };
    var setScopeId = function setScopeId(el, vnode, scopeId, slotScopeIds, parentComponent) {
      if (scopeId) {
        hostSetScopeId(el, scopeId);
      }
      if (slotScopeIds) {
        for (var i = 0; i < slotScopeIds.length; i++) {
          hostSetScopeId(el, slotScopeIds[i]);
        }
      }
      if (parentComponent) {
        var subTree = parentComponent.subTree;
        if (vnode === subTree) {
          var parentVNode = parentComponent.vnode;
          setScopeId(el, parentVNode, parentVNode.scopeId, parentVNode.slotScopeIds, parentComponent.parent);
        }
      }
    };
    var mountChildren = function mountChildren(children, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized, start) {
      if (start === void 0) {
        start = 0;
      }
      for (var i = start; i < children.length; i++) {
        var child = children[i] = optimized ? cloneIfMounted(children[i]) : normalizeVNode(children[i]);
        patch(null, child, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
      }
    };
    var patchElement = function patchElement(n1, n2, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      var el = n2.el = n1.el;
      var patchFlag = n2.patchFlag,
        dynamicChildren = n2.dynamicChildren,
        dirs = n2.dirs;
      // #1426 take the old vnode's patch flag into account since user may clone a
      // compiler-generated vnode, which de-opts to FULL_PROPS
      patchFlag |= n1.patchFlag & 16 /* PatchFlags.FULL_PROPS */;
      var oldProps = n1.props || EMPTY_OBJ;
      var newProps = n2.props || EMPTY_OBJ;
      var vnodeHook;
      // disable recurse in beforeUpdate hooks
      parentComponent && toggleRecurse(parentComponent, false);
      if (vnodeHook = newProps.onVnodeBeforeUpdate) {
        invokeVNodeHook(vnodeHook, parentComponent, n2, n1);
      }
      if (dirs) {
        invokeDirectiveHook(n2, n1, parentComponent, 'beforeUpdate');
      }
      parentComponent && toggleRecurse(parentComponent, true);
      var areChildrenSVG = isSVG && n2.type !== 'foreignObject';
      if (dynamicChildren) {
        patchBlockChildren(n1.dynamicChildren, dynamicChildren, el, parentComponent, parentSuspense, areChildrenSVG, slotScopeIds);
      } else if (!optimized) {
        // full diff
        patchChildren(n1, n2, el, null, parentComponent, parentSuspense, areChildrenSVG, slotScopeIds, false);
      }
      if (patchFlag > 0) {
        // the presence of a patchFlag means this element's render code was
        // generated by the compiler and can take the fast path.
        // in this path old node and new node are guaranteed to have the same shape
        // (i.e. at the exact same position in the source template)
        if (patchFlag & 16 /* PatchFlags.FULL_PROPS */) {
          // element props contain dynamic keys, full diff needed
          patchProps(el, n2, oldProps, newProps, parentComponent, parentSuspense, isSVG);
        } else {
          // class
          // this flag is matched when the element has dynamic class bindings.
          if (patchFlag & 2 /* PatchFlags.CLASS */) {
            if (oldProps.class !== newProps.class) {
              hostPatchProp(el, 'class', null, newProps.class, isSVG);
            }
          }
          // style
          // this flag is matched when the element has dynamic style bindings
          if (patchFlag & 4 /* PatchFlags.STYLE */) {
            hostPatchProp(el, 'style', oldProps.style, newProps.style, isSVG);
          }
          // props
          // This flag is matched when the element has dynamic prop/attr bindings
          // other than class and style. The keys of dynamic prop/attrs are saved for
          // faster iteration.
          // Note dynamic keys like :[foo]="bar" will cause this optimization to
          // bail out and go through a full diff because we need to unset the old key
          if (patchFlag & 8 /* PatchFlags.PROPS */) {
            // if the flag is present then dynamicProps must be non-null
            var propsToUpdate = n2.dynamicProps;
            for (var i = 0; i < propsToUpdate.length; i++) {
              var key = propsToUpdate[i];
              var prev = oldProps[key];
              var next = newProps[key];
              // #1471 force patch value
              if (next !== prev || key === 'value') {
                hostPatchProp(el, key, prev, next, isSVG, n1.children, parentComponent, parentSuspense, unmountChildren);
              }
            }
          }
        }
        // text
        // This flag is matched when the element has only dynamic text children.
        if (patchFlag & 1 /* PatchFlags.TEXT */) {
          if (n1.children !== n2.children) {
            hostSetElementText(el, n2.children);
          }
        }
      } else if (!optimized && dynamicChildren == null) {
        // unoptimized, full diff
        patchProps(el, n2, oldProps, newProps, parentComponent, parentSuspense, isSVG);
      }
      if ((vnodeHook = newProps.onVnodeUpdated) || dirs) {
        queuePostRenderEffect(function () {
          vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, n2, n1);
          dirs && invokeDirectiveHook(n2, n1, parentComponent, 'updated');
        }, parentSuspense);
      }
    };
    // The fast path for blocks.
    var patchBlockChildren = function patchBlockChildren(oldChildren, newChildren, fallbackContainer, parentComponent, parentSuspense, isSVG, slotScopeIds) {
      for (var i = 0; i < newChildren.length; i++) {
        var oldVNode = oldChildren[i];
        var newVNode = newChildren[i];
        // Determine the container (parent element) for the patch.
        var container =
        // oldVNode may be an errored async setup() component inside Suspense
        // which will not have a mounted element
        oldVNode.el && (
        // - In the case of a Fragment, we need to provide the actual parent
        // of the Fragment itself so it can move its children.
        oldVNode.type === Fragment ||
        // - In the case of different nodes, there is going to be a replacement
        // which also requires the correct parent container
        !isSameVNodeType(oldVNode, newVNode) ||
        // - In the case of a component, it could contain anything.
        oldVNode.shapeFlag & (6 /* ShapeFlags.COMPONENT */ | 64 /* ShapeFlags.TELEPORT */)) ? hostParentNode(oldVNode.el) :
        // In other cases, the parent container is not actually used so we
        // just pass the block element here to avoid a DOM parentNode call.
        fallbackContainer;
        patch(oldVNode, newVNode, container, null, parentComponent, parentSuspense, isSVG, slotScopeIds, true);
      }
    };
    var patchProps = function patchProps(el, vnode, oldProps, newProps, parentComponent, parentSuspense, isSVG) {
      if (oldProps !== newProps) {
        if (oldProps !== EMPTY_OBJ) {
          for (var key in oldProps) {
            if (!isReservedProp(key) && !(key in newProps)) {
              hostPatchProp(el, key, oldProps[key], null, isSVG, vnode.children, parentComponent, parentSuspense, unmountChildren);
            }
          }
        }
        for (var _key11 in newProps) {
          // empty string is not valid prop
          if (isReservedProp(_key11)) continue;
          var next = newProps[_key11];
          var prev = oldProps[_key11];
          // defer patching value
          if (next !== prev && _key11 !== 'value') {
            hostPatchProp(el, _key11, prev, next, isSVG, vnode.children, parentComponent, parentSuspense, unmountChildren);
          }
        }
        if ('value' in newProps) {
          hostPatchProp(el, 'value', oldProps.value, newProps.value);
        }
      }
    };
    var processFragment = function processFragment(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      var fragmentStartAnchor = n2.el = n1 ? n1.el : hostCreateText('');
      var fragmentEndAnchor = n2.anchor = n1 ? n1.anchor : hostCreateText('');
      var patchFlag = n2.patchFlag,
        dynamicChildren = n2.dynamicChildren,
        fragmentSlotScopeIds = n2.slotScopeIds;
      // check if this is a slot fragment with :slotted scope ids
      if (fragmentSlotScopeIds) {
        slotScopeIds = slotScopeIds ? slotScopeIds.concat(fragmentSlotScopeIds) : fragmentSlotScopeIds;
      }
      if (n1 == null) {
        hostInsert(fragmentStartAnchor, container, anchor);
        hostInsert(fragmentEndAnchor, container, anchor);
        // a fragment can only have array children
        // since they are either generated by the compiler, or implicitly created
        // from arrays.
        mountChildren(n2.children, container, fragmentEndAnchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
      } else {
        if (patchFlag > 0 && patchFlag & 64 /* PatchFlags.STABLE_FRAGMENT */ && dynamicChildren &&
        // #2715 the previous fragment could've been a BAILed one as a result
        // of renderSlot() with no valid children
        n1.dynamicChildren) {
          // a stable fragment (template root or <template v-for>) doesn't need to
          // patch children order, but it may contain dynamicChildren.
          patchBlockChildren(n1.dynamicChildren, dynamicChildren, container, parentComponent, parentSuspense, isSVG, slotScopeIds);
          if (
          // #2080 if the stable fragment has a key, it's a <template v-for> that may
          //  get moved around. Make sure all root level vnodes inherit el.
          // #2134 or if it's a component root, it may also get moved around
          // as the component is being moved.
          n2.key != null || parentComponent && n2 === parentComponent.subTree) {
            traverseStaticChildren(n1, n2, true /* shallow */);
          }
        } else {
          // keyed / unkeyed, or manual fragments.
          // for keyed & unkeyed, since they are compiler generated from v-for,
          // each child is guaranteed to be a block so the fragment will never
          // have dynamicChildren.
          patchChildren(n1, n2, container, fragmentEndAnchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
        }
      }
    };
    var processComponent = function processComponent(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      n2.slotScopeIds = slotScopeIds;
      if (n1 == null) {
        if (n2.shapeFlag & 512 /* ShapeFlags.COMPONENT_KEPT_ALIVE */) {
          parentComponent.ctx.activate(n2, container, anchor, isSVG, optimized);
        } else {
          mountComponent(n2, container, anchor, parentComponent, parentSuspense, isSVG, optimized);
        }
      } else {
        updateComponent(n1, n2, optimized);
      }
    };
    var mountComponent = function mountComponent(initialVNode, container, anchor, parentComponent, parentSuspense, isSVG, optimized) {
      var instance = initialVNode.component = createComponentInstance(initialVNode, parentComponent, parentSuspense);
      // inject renderer internals for keepAlive
      if (isKeepAlive(initialVNode)) {
        instance.ctx.renderer = internals;
      }
      // resolve props and slots for setup context
      {
        setupComponent(instance);
      }
      // setup() is async. This component relies on async logic to be resolved
      // before proceeding
      if (instance.asyncDep) {
        parentSuspense && parentSuspense.registerDep(instance, setupRenderEffect);
        // Give it a placeholder if this is not hydration
        // TODO handle self-defined fallback
        if (!initialVNode.el) {
          var placeholder = instance.subTree = createVNode(Comment);
          processCommentNode(null, placeholder, container, anchor);
        }
        return;
      }
      setupRenderEffect(instance, initialVNode, container, anchor, parentSuspense, isSVG, optimized);
    };
    var updateComponent = function updateComponent(n1, n2, optimized) {
      var instance = n2.component = n1.component;
      if (shouldUpdateComponent(n1, n2, optimized)) {
        if (instance.asyncDep && !instance.asyncResolved) {
          updateComponentPreRender(instance, n2, optimized);
          return;
        } else {
          // normal update
          instance.next = n2;
          // in case the child component is also queued, remove it to avoid
          // double updating the same child component in the same flush.
          invalidateJob(instance.update);
          // instance.update is the reactive effect.
          instance.update();
        }
      } else {
        // no update needed. just copy over properties
        n2.el = n1.el;
        instance.vnode = n2;
      }
    };
    var setupRenderEffect = function setupRenderEffect(instance, initialVNode, container, anchor, parentSuspense, isSVG, optimized) {
      var componentUpdateFn = function componentUpdateFn() {
        if (!instance.isMounted) {
          var vnodeHook;
          var _initialVNode = initialVNode,
            el = _initialVNode.el,
            props = _initialVNode.props;
          var bm = instance.bm,
            m = instance.m,
            parent = instance.parent;
          var isAsyncWrapperVNode = isAsyncWrapper(initialVNode);
          toggleRecurse(instance, false);
          // beforeMount hook
          if (bm) {
            invokeArrayFns$1(bm);
          }
          // onVnodeBeforeMount
          if (!isAsyncWrapperVNode && (vnodeHook = props && props.onVnodeBeforeMount)) {
            invokeVNodeHook(vnodeHook, parent, initialVNode);
          }
          toggleRecurse(instance, true);
          if (el && hydrateNode) {
            // vnode has adopted host node - perform hydration instead of mount.
            var hydrateSubTree = function hydrateSubTree() {
              instance.subTree = renderComponentRoot(instance);
              hydrateNode(el, instance.subTree, instance, parentSuspense, null);
            };
            if (isAsyncWrapperVNode) {
              initialVNode.type.__asyncLoader().then(
              // note: we are moving the render call into an async callback,
              // which means it won't track dependencies - but it's ok because
              // a server-rendered async wrapper is already in resolved state
              // and it will never need to change.
              function () {
                return !instance.isUnmounted && hydrateSubTree();
              });
            } else {
              hydrateSubTree();
            }
          } else {
            var subTree = instance.subTree = renderComponentRoot(instance);
            patch(null, subTree, container, anchor, instance, parentSuspense, isSVG);
            initialVNode.el = subTree.el;
          }
          // mounted hook
          if (m) {
            queuePostRenderEffect(m, parentSuspense);
          }
          // onVnodeMounted
          if (!isAsyncWrapperVNode && (vnodeHook = props && props.onVnodeMounted)) {
            var scopedInitialVNode = initialVNode;
            queuePostRenderEffect(function () {
              return invokeVNodeHook(vnodeHook, parent, scopedInitialVNode);
            }, parentSuspense);
          }
          // activated hook for keep-alive roots.
          // #1742 activated hook must be accessed after first render
          // since the hook may be injected by a child keep-alive
          if (initialVNode.shapeFlag & 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */ || parent && isAsyncWrapper(parent.vnode) && parent.vnode.shapeFlag & 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */) {
            instance.a && queuePostRenderEffect(instance.a, parentSuspense);
          }
          instance.isMounted = true;
          // #2458: deference mount-only object parameters to prevent memleaks
          initialVNode = container = anchor = null;
        } else {
          // updateComponent
          // This is triggered by mutation of component's own state (next: null)
          // OR parent calling processComponent (next: VNode)
          var next = instance.next,
            bu = instance.bu,
            u = instance.u,
            _parent = instance.parent,
            vnode = instance.vnode;
          var originNext = next;
          var _vnodeHook;
          // Disallow component effect recursion during pre-lifecycle hooks.
          toggleRecurse(instance, false);
          if (next) {
            next.el = vnode.el;
            updateComponentPreRender(instance, next, optimized);
          } else {
            next = vnode;
          }
          // beforeUpdate hook
          if (bu) {
            invokeArrayFns$1(bu);
          }
          // onVnodeBeforeUpdate
          if (_vnodeHook = next.props && next.props.onVnodeBeforeUpdate) {
            invokeVNodeHook(_vnodeHook, _parent, next, vnode);
          }
          toggleRecurse(instance, true);
          var nextTree = renderComponentRoot(instance);
          var prevTree = instance.subTree;
          instance.subTree = nextTree;
          patch(prevTree, nextTree,
          // parent may have changed if it's in a teleport
          hostParentNode(prevTree.el),
          // anchor may have changed if it's in a fragment
          getNextHostNode(prevTree), instance, parentSuspense, isSVG);
          next.el = nextTree.el;
          if (originNext === null) {
            // self-triggered update. In case of HOC, update parent component
            // vnode el. HOC is indicated by parent instance's subTree pointing
            // to child component's vnode
            updateHOCHostEl(instance, nextTree.el);
          }
          // updated hook
          if (u) {
            queuePostRenderEffect(u, parentSuspense);
          }
          // onVnodeUpdated
          if (_vnodeHook = next.props && next.props.onVnodeUpdated) {
            queuePostRenderEffect(function () {
              return invokeVNodeHook(_vnodeHook, _parent, next, vnode);
            }, parentSuspense);
          }
        }
      };
      // create reactive effect for rendering
      var effect = instance.effect = new ReactiveEffect(componentUpdateFn, function () {
        return queueJob(update);
      }, instance.scope // track it in component's effect scope
      );

      var update = instance.update = function () {
        return effect.run();
      };
      update.id = instance.uid;
      // allowRecurse
      // #1801, #2043 component render effects should allow recursive updates
      toggleRecurse(instance, true);
      update();
    };
    var updateComponentPreRender = function updateComponentPreRender(instance, nextVNode, optimized) {
      nextVNode.component = instance;
      var prevProps = instance.vnode.props;
      instance.vnode = nextVNode;
      instance.next = null;
      updateProps(instance, nextVNode.props, prevProps, optimized);
      updateSlots(instance, nextVNode.children, optimized);
      pauseTracking();
      // props update may have triggered pre-flush watchers.
      // flush them before the render update.
      flushPreFlushCbs();
      resetTracking();
    };
    var patchChildren = function patchChildren(n1, n2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      if (optimized === void 0) {
        optimized = false;
      }
      var c1 = n1 && n1.children;
      var prevShapeFlag = n1 ? n1.shapeFlag : 0;
      var c2 = n2.children;
      var patchFlag = n2.patchFlag,
        shapeFlag = n2.shapeFlag;
      // fast path
      if (patchFlag > 0) {
        if (patchFlag & 128 /* PatchFlags.KEYED_FRAGMENT */) {
          // this could be either fully-keyed or mixed (some keyed some not)
          // presence of patchFlag means children are guaranteed to be arrays
          patchKeyedChildren(c1, c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          return;
        } else if (patchFlag & 256 /* PatchFlags.UNKEYED_FRAGMENT */) {
          // unkeyed
          patchUnkeyedChildren(c1, c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          return;
        }
      }
      // children has 3 possibilities: text, array or no children.
      if (shapeFlag & 8 /* ShapeFlags.TEXT_CHILDREN */) {
        // text children fast path
        if (prevShapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
          unmountChildren(c1, parentComponent, parentSuspense);
        }
        if (c2 !== c1) {
          hostSetElementText(container, c2);
        }
      } else {
        if (prevShapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
          // prev children was array
          if (shapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
            // two arrays, cannot assume anything, do full diff
            patchKeyedChildren(c1, c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          } else {
            // no new children, just unmount old
            unmountChildren(c1, parentComponent, parentSuspense, true);
          }
        } else {
          // prev children was text OR null
          // new children is array OR null
          if (prevShapeFlag & 8 /* ShapeFlags.TEXT_CHILDREN */) {
            hostSetElementText(container, '');
          }
          // mount new if array
          if (shapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
            mountChildren(c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          }
        }
      }
    };
    var patchUnkeyedChildren = function patchUnkeyedChildren(c1, c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      c1 = c1 || EMPTY_ARR;
      c2 = c2 || EMPTY_ARR;
      var oldLength = c1.length;
      var newLength = c2.length;
      var commonLength = Math.min(oldLength, newLength);
      var i;
      for (i = 0; i < commonLength; i++) {
        var nextChild = c2[i] = optimized ? cloneIfMounted(c2[i]) : normalizeVNode(c2[i]);
        patch(c1[i], nextChild, container, null, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
      }
      if (oldLength > newLength) {
        // remove old
        unmountChildren(c1, parentComponent, parentSuspense, true, false, commonLength);
      } else {
        // mount new
        mountChildren(c2, container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized, commonLength);
      }
    };
    // can be all-keyed or mixed
    var patchKeyedChildren = function patchKeyedChildren(c1, c2, container, parentAnchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized) {
      var i = 0;
      var l2 = c2.length;
      var e1 = c1.length - 1; // prev ending index
      var e2 = l2 - 1; // next ending index
      // 1. sync from start
      // (a b) c
      // (a b) d e
      while (i <= e1 && i <= e2) {
        var n1 = c1[i];
        var n2 = c2[i] = optimized ? cloneIfMounted(c2[i]) : normalizeVNode(c2[i]);
        if (isSameVNodeType(n1, n2)) {
          patch(n1, n2, container, null, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
        } else {
          break;
        }
        i++;
      }
      // 2. sync from end
      // a (b c)
      // d e (b c)
      while (i <= e1 && i <= e2) {
        var _n = c1[e1];
        var _n2 = c2[e2] = optimized ? cloneIfMounted(c2[e2]) : normalizeVNode(c2[e2]);
        if (isSameVNodeType(_n, _n2)) {
          patch(_n, _n2, container, null, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
        } else {
          break;
        }
        e1--;
        e2--;
      }
      // 3. common sequence + mount
      // (a b)
      // (a b) c
      // i = 2, e1 = 1, e2 = 2
      // (a b)
      // c (a b)
      // i = 0, e1 = -1, e2 = 0
      if (i > e1) {
        if (i <= e2) {
          var nextPos = e2 + 1;
          var anchor = nextPos < l2 ? c2[nextPos].el : parentAnchor;
          while (i <= e2) {
            patch(null, c2[i] = optimized ? cloneIfMounted(c2[i]) : normalizeVNode(c2[i]), container, anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
            i++;
          }
        }
      }
      // 4. common sequence + unmount
      // (a b) c
      // (a b)
      // i = 2, e1 = 2, e2 = 1
      // a (b c)
      // (b c)
      // i = 0, e1 = 0, e2 = -1
      else if (i > e2) {
        while (i <= e1) {
          unmount(c1[i], parentComponent, parentSuspense, true);
          i++;
        }
      }
      // 5. unknown sequence
      // [i ... e1 + 1]: a b [c d e] f g
      // [i ... e2 + 1]: a b [e d c h] f g
      // i = 2, e1 = 4, e2 = 5
      else {
        var s1 = i; // prev starting index
        var s2 = i; // next starting index
        // 5.1 build key:index map for newChildren
        var keyToNewIndexMap = new Map();
        for (i = s2; i <= e2; i++) {
          var nextChild = c2[i] = optimized ? cloneIfMounted(c2[i]) : normalizeVNode(c2[i]);
          if (nextChild.key != null) {
            keyToNewIndexMap.set(nextChild.key, i);
          }
        }
        // 5.2 loop through old children left to be patched and try to patch
        // matching nodes & remove nodes that are no longer present
        var j;
        var patched = 0;
        var toBePatched = e2 - s2 + 1;
        var moved = false;
        // used to track whether any node has moved
        var maxNewIndexSoFar = 0;
        // works as Map<newIndex, oldIndex>
        // Note that oldIndex is offset by +1
        // and oldIndex = 0 is a special value indicating the new node has
        // no corresponding old node.
        // used for determining longest stable subsequence
        var newIndexToOldIndexMap = new Array(toBePatched);
        for (i = 0; i < toBePatched; i++) newIndexToOldIndexMap[i] = 0;
        for (i = s1; i <= e1; i++) {
          var prevChild = c1[i];
          if (patched >= toBePatched) {
            // all new children have been patched so this can only be a removal
            unmount(prevChild, parentComponent, parentSuspense, true);
            continue;
          }
          var newIndex = void 0;
          if (prevChild.key != null) {
            newIndex = keyToNewIndexMap.get(prevChild.key);
          } else {
            // key-less node, try to locate a key-less node of the same type
            for (j = s2; j <= e2; j++) {
              if (newIndexToOldIndexMap[j - s2] === 0 && isSameVNodeType(prevChild, c2[j])) {
                newIndex = j;
                break;
              }
            }
          }
          if (newIndex === undefined) {
            unmount(prevChild, parentComponent, parentSuspense, true);
          } else {
            newIndexToOldIndexMap[newIndex - s2] = i + 1;
            if (newIndex >= maxNewIndexSoFar) {
              maxNewIndexSoFar = newIndex;
            } else {
              moved = true;
            }
            patch(prevChild, c2[newIndex], container, null, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
            patched++;
          }
        }
        // 5.3 move and mount
        // generate longest stable subsequence only when nodes have moved
        var increasingNewIndexSequence = moved ? getSequence(newIndexToOldIndexMap) : EMPTY_ARR;
        j = increasingNewIndexSequence.length - 1;
        // looping backwards so that we can use last patched node as anchor
        for (i = toBePatched - 1; i >= 0; i--) {
          var nextIndex = s2 + i;
          var _nextChild = c2[nextIndex];
          var _anchor = nextIndex + 1 < l2 ? c2[nextIndex + 1].el : parentAnchor;
          if (newIndexToOldIndexMap[i] === 0) {
            // mount new
            patch(null, _nextChild, container, _anchor, parentComponent, parentSuspense, isSVG, slotScopeIds, optimized);
          } else if (moved) {
            // move if:
            // There is no stable subsequence (e.g. a reverse)
            // OR current node is not among the stable sequence
            if (j < 0 || i !== increasingNewIndexSequence[j]) {
              move(_nextChild, container, _anchor, 2 /* MoveType.REORDER */);
            } else {
              j--;
            }
          }
        }
      }
    };
    var move = function move(vnode, container, anchor, moveType, parentSuspense) {
      if (parentSuspense === void 0) {
        parentSuspense = null;
      }
      var el = vnode.el,
        type = vnode.type,
        transition = vnode.transition,
        children = vnode.children,
        shapeFlag = vnode.shapeFlag;
      if (shapeFlag & 6 /* ShapeFlags.COMPONENT */) {
        move(vnode.component.subTree, container, anchor, moveType);
        return;
      }
      if (shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
        vnode.suspense.move(container, anchor, moveType);
        return;
      }
      if (shapeFlag & 64 /* ShapeFlags.TELEPORT */) {
        type.move(vnode, container, anchor, internals);
        return;
      }
      if (type === Fragment) {
        hostInsert(el, container, anchor);
        for (var i = 0; i < children.length; i++) {
          move(children[i], container, anchor, moveType);
        }
        hostInsert(vnode.anchor, container, anchor);
        return;
      }
      if (type === Static) {
        moveStaticNode(vnode, container, anchor);
        return;
      }
      // single nodes
      var needTransition = moveType !== 2 /* MoveType.REORDER */ && shapeFlag & 1 /* ShapeFlags.ELEMENT */ && transition;
      if (needTransition) {
        if (moveType === 0 /* MoveType.ENTER */) {
          transition.beforeEnter(el);
          hostInsert(el, container, anchor);
          queuePostRenderEffect(function () {
            return transition.enter(el);
          }, parentSuspense);
        } else {
          var leave = transition.leave,
            delayLeave = transition.delayLeave,
            afterLeave = transition.afterLeave;
          var _remove = function _remove() {
            return hostInsert(el, container, anchor);
          };
          var performLeave = function performLeave() {
            leave(el, function () {
              _remove();
              afterLeave && afterLeave();
            });
          };
          if (delayLeave) {
            delayLeave(el, _remove, performLeave);
          } else {
            performLeave();
          }
        }
      } else {
        hostInsert(el, container, anchor);
      }
    };
    var unmount = function unmount(vnode, parentComponent, parentSuspense, doRemove, optimized) {
      if (doRemove === void 0) {
        doRemove = false;
      }
      if (optimized === void 0) {
        optimized = false;
      }
      var type = vnode.type,
        props = vnode.props,
        ref = vnode.ref,
        children = vnode.children,
        dynamicChildren = vnode.dynamicChildren,
        shapeFlag = vnode.shapeFlag,
        patchFlag = vnode.patchFlag,
        dirs = vnode.dirs;
      // unset ref
      if (ref != null) {
        setRef(ref, null, parentSuspense, vnode, true);
      }
      if (shapeFlag & 256 /* ShapeFlags.COMPONENT_SHOULD_KEEP_ALIVE */) {
        parentComponent.ctx.deactivate(vnode);
        return;
      }
      var shouldInvokeDirs = shapeFlag & 1 /* ShapeFlags.ELEMENT */ && dirs;
      var shouldInvokeVnodeHook = !isAsyncWrapper(vnode);
      var vnodeHook;
      if (shouldInvokeVnodeHook && (vnodeHook = props && props.onVnodeBeforeUnmount)) {
        invokeVNodeHook(vnodeHook, parentComponent, vnode);
      }
      if (shapeFlag & 6 /* ShapeFlags.COMPONENT */) {
        unmountComponent(vnode.component, parentSuspense, doRemove);
      } else {
        if (shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
          vnode.suspense.unmount(parentSuspense, doRemove);
          return;
        }
        if (shouldInvokeDirs) {
          invokeDirectiveHook(vnode, null, parentComponent, 'beforeUnmount');
        }
        if (shapeFlag & 64 /* ShapeFlags.TELEPORT */) {
          vnode.type.remove(vnode, parentComponent, parentSuspense, optimized, internals, doRemove);
        } else if (dynamicChildren && (
        // #1153: fast path should not be taken for non-stable (v-for) fragments
        type !== Fragment || patchFlag > 0 && patchFlag & 64 /* PatchFlags.STABLE_FRAGMENT */)) {
          // fast path for block nodes: only need to unmount dynamic children.
          unmountChildren(dynamicChildren, parentComponent, parentSuspense, false, true);
        } else if (type === Fragment && patchFlag & (128 /* PatchFlags.KEYED_FRAGMENT */ | 256 /* PatchFlags.UNKEYED_FRAGMENT */) || !optimized && shapeFlag & 16 /* ShapeFlags.ARRAY_CHILDREN */) {
          unmountChildren(children, parentComponent, parentSuspense);
        }
        if (doRemove) {
          remove(vnode);
        }
      }
      if (shouldInvokeVnodeHook && (vnodeHook = props && props.onVnodeUnmounted) || shouldInvokeDirs) {
        queuePostRenderEffect(function () {
          vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, vnode);
          shouldInvokeDirs && invokeDirectiveHook(vnode, null, parentComponent, 'unmounted');
        }, parentSuspense);
      }
    };
    var remove = function remove(vnode) {
      var type = vnode.type,
        el = vnode.el,
        anchor = vnode.anchor,
        transition = vnode.transition;
      if (type === Fragment) {
        {
          removeFragment(el, anchor);
        }
        return;
      }
      if (type === Static) {
        removeStaticNode(vnode);
        return;
      }
      var performRemove = function performRemove() {
        hostRemove(el);
        if (transition && !transition.persisted && transition.afterLeave) {
          transition.afterLeave();
        }
      };
      if (vnode.shapeFlag & 1 /* ShapeFlags.ELEMENT */ && transition && !transition.persisted) {
        var leave = transition.leave,
          delayLeave = transition.delayLeave;
        var performLeave = function performLeave() {
          return leave(el, performRemove);
        };
        if (delayLeave) {
          delayLeave(vnode.el, performRemove, performLeave);
        } else {
          performLeave();
        }
      } else {
        performRemove();
      }
    };
    var removeFragment = function removeFragment(cur, end) {
      // For fragments, directly remove all contained DOM nodes.
      // (fragment child nodes cannot have transition)
      var next;
      while (cur !== end) {
        next = hostNextSibling(cur);
        hostRemove(cur);
        cur = next;
      }
      hostRemove(end);
    };
    var unmountComponent = function unmountComponent(instance, parentSuspense, doRemove) {
      var bum = instance.bum,
        scope = instance.scope,
        update = instance.update,
        subTree = instance.subTree,
        um = instance.um;
      // beforeUnmount hook
      if (bum) {
        invokeArrayFns$1(bum);
      }
      // stop effects in component scope
      scope.stop();
      // update may be null if a component is unmounted before its async
      // setup has resolved.
      if (update) {
        // so that scheduler will no longer invoke it
        update.active = false;
        unmount(subTree, instance, parentSuspense, doRemove);
      }
      // unmounted hook
      if (um) {
        queuePostRenderEffect(um, parentSuspense);
      }
      queuePostRenderEffect(function () {
        instance.isUnmounted = true;
      }, parentSuspense);
      // A component with async dep inside a pending suspense is unmounted before
      // its async dep resolves. This should remove the dep from the suspense, and
      // cause the suspense to resolve immediately if that was the last dep.
      if (parentSuspense && parentSuspense.pendingBranch && !parentSuspense.isUnmounted && instance.asyncDep && !instance.asyncResolved && instance.suspenseId === parentSuspense.pendingId) {
        parentSuspense.deps--;
        if (parentSuspense.deps === 0) {
          parentSuspense.resolve();
        }
      }
    };
    var unmountChildren = function unmountChildren(children, parentComponent, parentSuspense, doRemove, optimized, start) {
      if (doRemove === void 0) {
        doRemove = false;
      }
      if (optimized === void 0) {
        optimized = false;
      }
      if (start === void 0) {
        start = 0;
      }
      for (var i = start; i < children.length; i++) {
        unmount(children[i], parentComponent, parentSuspense, doRemove, optimized);
      }
    };
    var getNextHostNode = function getNextHostNode(vnode) {
      if (vnode.shapeFlag & 6 /* ShapeFlags.COMPONENT */) {
        return getNextHostNode(vnode.component.subTree);
      }
      if (vnode.shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
        return vnode.suspense.next();
      }
      return hostNextSibling(vnode.anchor || vnode.el);
    };
    var render = function render(vnode, container, isSVG) {
      if (vnode == null) {
        if (container._vnode) {
          unmount(container._vnode, null, null, true);
        }
      } else {
        patch(container._vnode || null, vnode, container, null, null, null, isSVG);
      }
      flushPreFlushCbs();
      flushPostFlushCbs();
      container._vnode = vnode;
    };
    var internals = {
      p: patch,
      um: unmount,
      m: move,
      r: remove,
      mt: mountComponent,
      mc: mountChildren,
      pc: patchChildren,
      pbc: patchBlockChildren,
      n: getNextHostNode,
      o: options
    };
    var hydrate;
    var hydrateNode;
    if (createHydrationFns) {
      var _createHydrationFns = createHydrationFns(internals);
      hydrate = _createHydrationFns[0];
      hydrateNode = _createHydrationFns[1];
    }
    return {
      render: render,
      hydrate: hydrate,
      createApp: createAppAPI(render, hydrate)
    };
  }
  function toggleRecurse(_ref14, allowed) {
    var effect = _ref14.effect,
      update = _ref14.update;
    effect.allowRecurse = update.allowRecurse = allowed;
  }
  /**
   * #1156
   * When a component is HMR-enabled, we need to make sure that all static nodes
   * inside a block also inherit the DOM element from the previous tree so that
   * HMR updates (which are full updates) can retrieve the element for patching.
   *
   * #2080
   * Inside keyed `template` fragment static children, if a fragment is moved,
   * the children will always be moved. Therefore, in order to ensure correct move
   * position, el should be inherited from previous nodes.
   */
  function traverseStaticChildren(n1, n2, shallow) {
    if (shallow === void 0) {
      shallow = false;
    }
    var ch1 = n1.children;
    var ch2 = n2.children;
    if (isArray$1(ch1) && isArray$1(ch2)) {
      for (var i = 0; i < ch1.length; i++) {
        // this is only called in the optimized path so array children are
        // guaranteed to be vnodes
        var c1 = ch1[i];
        var c2 = ch2[i];
        if (c2.shapeFlag & 1 /* ShapeFlags.ELEMENT */ && !c2.dynamicChildren) {
          if (c2.patchFlag <= 0 || c2.patchFlag === 32 /* PatchFlags.HYDRATE_EVENTS */) {
            c2 = ch2[i] = cloneIfMounted(ch2[i]);
            c2.el = c1.el;
          }
          if (!shallow) traverseStaticChildren(c1, c2);
        }
        // #6852 also inherit for text nodes
        if (c2.type === Text) {
          c2.el = c1.el;
        }
      }
    }
  }
  // https://en.wikipedia.org/wiki/Longest_increasing_subsequence
  function getSequence(arr) {
    var p = arr.slice();
    var result = [0];
    var i, j, u, v, c;
    var len = arr.length;
    for (i = 0; i < len; i++) {
      var arrI = arr[i];
      if (arrI !== 0) {
        j = result[result.length - 1];
        if (arr[j] < arrI) {
          p[i] = j;
          result.push(i);
          continue;
        }
        u = 0;
        v = result.length - 1;
        while (u < v) {
          c = u + v >> 1;
          if (arr[result[c]] < arrI) {
            u = c + 1;
          } else {
            v = c;
          }
        }
        if (arrI < arr[result[u]]) {
          if (u > 0) {
            p[i] = result[u - 1];
          }
          result[u] = i;
        }
      }
    }
    u = result.length;
    v = result[u - 1];
    while (u-- > 0) {
      result[u] = v;
      v = p[v];
    }
    return result;
  }
  var isTeleport = function isTeleport(type) {
    return type.__isTeleport;
  };
  var Fragment = Symbol(undefined);
  var Text = Symbol(undefined);
  var Comment = Symbol(undefined);
  var Static = Symbol(undefined);
  // Since v-if and v-for are the two possible ways node structure can dynamically
  // change, once we consider v-if branches and each v-for fragment a block, we
  // can divide a template into nested blocks, and within each block the node
  // structure would be stable. This allows us to skip most children diffing
  // and only worry about the dynamic nodes (indicated by patch flags).
  var blockStack = [];
  var currentBlock = null;
  /**
   * Open a block.
   * This must be called before `createBlock`. It cannot be part of `createBlock`
   * because the children of the block are evaluated before `createBlock` itself
   * is called. The generated code typically looks like this:
   *
   * ```js
   * function render() {
   *   return (openBlock(),createBlock('div', null, [...]))
   * }
   * ```
   * disableTracking is true when creating a v-for fragment block, since a v-for
   * fragment always diffs its children.
   *
   * @private
   */
  function openBlock(disableTracking) {
    if (disableTracking === void 0) {
      disableTracking = false;
    }
    blockStack.push(currentBlock = disableTracking ? null : []);
  }
  function closeBlock() {
    blockStack.pop();
    currentBlock = blockStack[blockStack.length - 1] || null;
  }
  // Whether we should be tracking dynamic child nodes inside a block.
  // Only tracks when this value is > 0
  // We are not using a simple boolean because this value may need to be
  // incremented/decremented by nested usage of v-once (see below)
  var isBlockTreeEnabled = 1;
  /**
   * Block tracking sometimes needs to be disabled, for example during the
   * creation of a tree that needs to be cached by v-once. The compiler generates
   * code like this:
   *
   * ``` js
   * _cache[1] || (
   *   setBlockTracking(-1),
   *   _cache[1] = createVNode(...),
   *   setBlockTracking(1),
   *   _cache[1]
   * )
   * ```
   *
   * @private
   */
  function setBlockTracking(value) {
    isBlockTreeEnabled += value;
  }
  function setupBlock(vnode) {
    // save current block children on the block vnode
    vnode.dynamicChildren = isBlockTreeEnabled > 0 ? currentBlock || EMPTY_ARR : null;
    // close block
    closeBlock();
    // a block is always going to be patched, so track it as a child of its
    // parent block
    if (isBlockTreeEnabled > 0 && currentBlock) {
      currentBlock.push(vnode);
    }
    return vnode;
  }
  /**
   * @private
   */
  function createElementBlock(type, props, children, patchFlag, dynamicProps, shapeFlag) {
    return setupBlock(createBaseVNode(type, props, children, patchFlag, dynamicProps, shapeFlag, true /* isBlock */));
  }
  /**
   * Create a block root vnode. Takes the same exact arguments as `createVNode`.
   * A block root keeps track of dynamic nodes within the block in the
   * `dynamicChildren` array.
   *
   * @private
   */
  function createBlock(type, props, children, patchFlag, dynamicProps) {
    return setupBlock(createVNode(type, props, children, patchFlag, dynamicProps, true /* isBlock: prevent a block from tracking itself */));
  }

  function isVNode(value) {
    return value ? value.__v_isVNode === true : false;
  }
  function isSameVNodeType(n1, n2) {
    return n1.type === n2.type && n1.key === n2.key;
  }
  var InternalObjectKey = "__vInternal";
  var normalizeKey = function normalizeKey(_ref18) {
    var key = _ref18.key;
    return key != null ? key : null;
  };
  var normalizeRef = function normalizeRef(_ref19) {
    var ref = _ref19.ref,
      ref_key = _ref19.ref_key,
      ref_for = _ref19.ref_for;
    return ref != null ? isString$1(ref) || isRef(ref) || isFunction$1(ref) ? {
      i: currentRenderingInstance,
      r: ref,
      k: ref_key,
      f: !!ref_for
    } : ref : null;
  };
  function createBaseVNode(type, props, children, patchFlag, dynamicProps, shapeFlag /* ShapeFlags.ELEMENT */, isBlockNode, needFullChildrenNormalization) {
    if (props === void 0) {
      props = null;
    }
    if (children === void 0) {
      children = null;
    }
    if (patchFlag === void 0) {
      patchFlag = 0;
    }
    if (dynamicProps === void 0) {
      dynamicProps = null;
    }
    if (shapeFlag === void 0) {
      shapeFlag = type === Fragment ? 0 : 1;
    }
    if (isBlockNode === void 0) {
      isBlockNode = false;
    }
    if (needFullChildrenNormalization === void 0) {
      needFullChildrenNormalization = false;
    }
    var vnode = {
      __v_isVNode: true,
      __v_skip: true,
      type: type,
      props: props,
      key: props && normalizeKey(props),
      ref: props && normalizeRef(props),
      scopeId: currentScopeId,
      slotScopeIds: null,
      children: children,
      component: null,
      suspense: null,
      ssContent: null,
      ssFallback: null,
      dirs: null,
      transition: null,
      el: null,
      anchor: null,
      target: null,
      targetAnchor: null,
      staticCount: 0,
      shapeFlag: shapeFlag,
      patchFlag: patchFlag,
      dynamicProps: dynamicProps,
      dynamicChildren: null,
      appContext: null,
      ctx: currentRenderingInstance
    };
    if (needFullChildrenNormalization) {
      normalizeChildren(vnode, children);
      // normalize suspense children
      if (shapeFlag & 128 /* ShapeFlags.SUSPENSE */) {
        type.normalize(vnode);
      }
    } else if (children) {
      // compiled element vnode - if children is passed, only possible types are
      // string or Array.
      vnode.shapeFlag |= isString$1(children) ? 8 /* ShapeFlags.TEXT_CHILDREN */ : 16 /* ShapeFlags.ARRAY_CHILDREN */;
    }
    // track vnode for block tree
    if (isBlockTreeEnabled > 0 &&
    // avoid a block node from tracking itself
    !isBlockNode &&
    // has current parent block
    currentBlock && (
    // presence of a patch flag indicates this node needs patching on updates.
    // component nodes also should always be patched, because even if the
    // component doesn't need to update, it needs to persist the instance on to
    // the next vnode so that it can be properly unmounted later.
    vnode.patchFlag > 0 || shapeFlag & 6 /* ShapeFlags.COMPONENT */) &&
    // the EVENTS flag is only for hydration and if it is the only flag, the
    // vnode should not be considered dynamic due to handler caching.
    vnode.patchFlag !== 32 /* PatchFlags.HYDRATE_EVENTS */) {
      currentBlock.push(vnode);
    }
    return vnode;
  }
  var createVNode = _createVNode;
  function _createVNode(type, props, children, patchFlag, dynamicProps, isBlockNode) {
    if (props === void 0) {
      props = null;
    }
    if (children === void 0) {
      children = null;
    }
    if (patchFlag === void 0) {
      patchFlag = 0;
    }
    if (dynamicProps === void 0) {
      dynamicProps = null;
    }
    if (isBlockNode === void 0) {
      isBlockNode = false;
    }
    if (!type || type === NULL_DYNAMIC_COMPONENT) {
      type = Comment;
    }
    if (isVNode(type)) {
      // createVNode receiving an existing vnode. This happens in cases like
      // <component :is="vnode"/>
      // #2078 make sure to merge refs during the clone instead of overwriting it
      var cloned = cloneVNode(type, props, true /* mergeRef: true */);
      if (children) {
        normalizeChildren(cloned, children);
      }
      if (isBlockTreeEnabled > 0 && !isBlockNode && currentBlock) {
        if (cloned.shapeFlag & 6 /* ShapeFlags.COMPONENT */) {
          currentBlock[currentBlock.indexOf(type)] = cloned;
        } else {
          currentBlock.push(cloned);
        }
      }
      cloned.patchFlag |= -2 /* PatchFlags.BAIL */;
      return cloned;
    }
    // class component normalization.
    if (isClassComponent(type)) {
      type = type.__vccOpts;
    }
    // class & style normalization.
    if (props) {
      // for reactive or proxy objects, we need to clone it to enable mutation.
      props = guardReactiveProps(props);
      var _props = props,
        klass = _props.class,
        style = _props.style;
      if (klass && !isString$1(klass)) {
        props.class = normalizeClass(klass);
      }
      if (isObject$2(style)) {
        // reactive state objects need to be cloned since they are likely to be
        // mutated
        if (isProxy(style) && !isArray$1(style)) {
          style = extend$1({}, style);
        }
        props.style = normalizeStyle(style);
      }
    }
    // encode the vnode type information into a bitmap
    var shapeFlag = isString$1(type) ? 1 /* ShapeFlags.ELEMENT */ : isSuspense(type) ? 128 /* ShapeFlags.SUSPENSE */ : isTeleport(type) ? 64 /* ShapeFlags.TELEPORT */ : isObject$2(type) ? 4 /* ShapeFlags.STATEFUL_COMPONENT */ : isFunction$1(type) ? 2 /* ShapeFlags.FUNCTIONAL_COMPONENT */ : 0;
    return createBaseVNode(type, props, children, patchFlag, dynamicProps, shapeFlag, isBlockNode, true);
  }
  function guardReactiveProps(props) {
    if (!props) return null;
    return isProxy(props) || InternalObjectKey in props ? extend$1({}, props) : props;
  }
  function cloneVNode(vnode, extraProps, mergeRef) {
    if (mergeRef === void 0) {
      mergeRef = false;
    }
    // This is intentionally NOT using spread or extend to avoid the runtime
    // key enumeration cost.
    var props = vnode.props,
      ref = vnode.ref,
      patchFlag = vnode.patchFlag,
      children = vnode.children;
    var mergedProps = extraProps ? mergeProps(props || {}, extraProps) : props;
    var cloned = {
      __v_isVNode: true,
      __v_skip: true,
      type: vnode.type,
      props: mergedProps,
      key: mergedProps && normalizeKey(mergedProps),
      ref: extraProps && extraProps.ref ?
      // #2078 in the case of <component :is="vnode" ref="extra"/>
      // if the vnode itself already has a ref, cloneVNode will need to merge
      // the refs so the single vnode can be set on multiple refs
      mergeRef && ref ? isArray$1(ref) ? ref.concat(normalizeRef(extraProps)) : [ref, normalizeRef(extraProps)] : normalizeRef(extraProps) : ref,
      scopeId: vnode.scopeId,
      slotScopeIds: vnode.slotScopeIds,
      children: children,
      target: vnode.target,
      targetAnchor: vnode.targetAnchor,
      staticCount: vnode.staticCount,
      shapeFlag: vnode.shapeFlag,
      // if the vnode is cloned with extra props, we can no longer assume its
      // existing patch flag to be reliable and need to add the FULL_PROPS flag.
      // note: preserve flag for fragments since they use the flag for children
      // fast paths only.
      patchFlag: extraProps && vnode.type !== Fragment ? patchFlag === -1 // hoisted node
      ? 16 /* PatchFlags.FULL_PROPS */ : patchFlag | 16 /* PatchFlags.FULL_PROPS */ : patchFlag,
      dynamicProps: vnode.dynamicProps,
      dynamicChildren: vnode.dynamicChildren,
      appContext: vnode.appContext,
      dirs: vnode.dirs,
      transition: vnode.transition,
      // These should technically only be non-null on mounted VNodes. However,
      // they *should* be copied for kept-alive vnodes. So we just always copy
      // them since them being non-null during a mount doesn't affect the logic as
      // they will simply be overwritten.
      component: vnode.component,
      suspense: vnode.suspense,
      ssContent: vnode.ssContent && cloneVNode(vnode.ssContent),
      ssFallback: vnode.ssFallback && cloneVNode(vnode.ssFallback),
      el: vnode.el,
      anchor: vnode.anchor,
      ctx: vnode.ctx
    };
    return cloned;
  }
  /**
   * @private
   */
  function createTextVNode(text, flag) {
    if (text === void 0) {
      text = ' ';
    }
    if (flag === void 0) {
      flag = 0;
    }
    return createVNode(Text, null, text, flag);
  }
  /**
   * @private
   */
  function createCommentVNode(text,
  // when used as the v-else branch, the comment node must be created as a
  // block to ensure correct updates.
  asBlock) {
    if (text === void 0) {
      text = '';
    }
    if (asBlock === void 0) {
      asBlock = false;
    }
    return asBlock ? (openBlock(), createBlock(Comment, null, text)) : createVNode(Comment, null, text);
  }
  function normalizeVNode(child) {
    if (child == null || typeof child === 'boolean') {
      // empty placeholder
      return createVNode(Comment);
    } else if (isArray$1(child)) {
      // fragment
      return createVNode(Fragment, null,
      // #3666, avoid reference pollution when reusing vnode
      child.slice());
    } else if (typeof child === 'object') {
      // already vnode, this should be the most common since compiled templates
      // always produce all-vnode children arrays
      return cloneIfMounted(child);
    } else {
      // strings and numbers
      return createVNode(Text, null, String(child));
    }
  }
  // optimized normalization for template-compiled render fns
  function cloneIfMounted(child) {
    return child.el === null && child.patchFlag !== -1 /* PatchFlags.HOISTED */ || child.memo ? child : cloneVNode(child);
  }
  function normalizeChildren(vnode, children) {
    var type = 0;
    var shapeFlag = vnode.shapeFlag;
    if (children == null) {
      children = null;
    } else if (isArray$1(children)) {
      type = 16 /* ShapeFlags.ARRAY_CHILDREN */;
    } else if (typeof children === 'object') {
      if (shapeFlag & (1 /* ShapeFlags.ELEMENT */ | 64 /* ShapeFlags.TELEPORT */)) {
        // Normalize slot to plain children for plain element and Teleport
        var slot = children.default;
        if (slot) {
          // _c marker is added by withCtx() indicating this is a compiled slot
          slot._c && (slot._d = false);
          normalizeChildren(vnode, slot());
          slot._c && (slot._d = true);
        }
        return;
      } else {
        type = 32 /* ShapeFlags.SLOTS_CHILDREN */;
        var slotFlag = children._;
        if (!slotFlag && !(InternalObjectKey in children)) {
          children._ctx = currentRenderingInstance;
        } else if (slotFlag === 3 /* SlotFlags.FORWARDED */ && currentRenderingInstance) {
          // a child component receives forwarded slots from the parent.
          // its slot type is determined by its parent's slot type.
          if (currentRenderingInstance.slots._ === 1 /* SlotFlags.STABLE */) {
            children._ = 1 /* SlotFlags.STABLE */;
          } else {
            children._ = 2 /* SlotFlags.DYNAMIC */;
            vnode.patchFlag |= 1024 /* PatchFlags.DYNAMIC_SLOTS */;
          }
        }
      }
    } else if (isFunction$1(children)) {
      children = {
        default: children,
        _ctx: currentRenderingInstance
      };
      type = 32 /* ShapeFlags.SLOTS_CHILDREN */;
    } else {
      children = String(children);
      // force teleport children to array so it can be moved around
      if (shapeFlag & 64 /* ShapeFlags.TELEPORT */) {
        type = 16 /* ShapeFlags.ARRAY_CHILDREN */;
        children = [createTextVNode(children)];
      } else {
        type = 8 /* ShapeFlags.TEXT_CHILDREN */;
      }
    }

    vnode.children = children;
    vnode.shapeFlag |= type;
  }
  function mergeProps() {
    var ret = {};
    for (var i = 0; i < arguments.length; i++) {
      var toMerge = i < 0 || arguments.length <= i ? undefined : arguments[i];
      for (var key in toMerge) {
        if (key === 'class') {
          if (ret.class !== toMerge.class) {
            ret.class = normalizeClass([ret.class, toMerge.class]);
          }
        } else if (key === 'style') {
          ret.style = normalizeStyle([ret.style, toMerge.style]);
        } else if (isOn$1(key)) {
          var existing = ret[key];
          var incoming = toMerge[key];
          if (incoming && existing !== incoming && !(isArray$1(existing) && existing.includes(incoming))) {
            ret[key] = existing ? [].concat(existing, incoming) : incoming;
          }
        } else if (key !== '') {
          ret[key] = toMerge[key];
        }
      }
    }
    return ret;
  }
  function invokeVNodeHook(hook, instance, vnode, prevVNode) {
    if (prevVNode === void 0) {
      prevVNode = null;
    }
    callWithAsyncErrorHandling(hook, instance, 7 /* ErrorCodes.VNODE_HOOK */, [vnode, prevVNode]);
  }
  var emptyAppContext = createAppContext();
  var uid$1 = 0;
  function createComponentInstance(vnode, parent, suspense) {
    var type = vnode.type;
    // inherit parent app context - or - if root, adopt from root vnode
    var appContext = (parent ? parent.appContext : vnode.appContext) || emptyAppContext;
    var instance = {
      uid: uid$1++,
      vnode: vnode,
      type: type,
      parent: parent,
      appContext: appContext,
      root: null,
      next: null,
      subTree: null,
      effect: null,
      update: null,
      scope: new EffectScope(true /* detached */),
      render: null,
      proxy: null,
      exposed: null,
      exposeProxy: null,
      withProxy: null,
      provides: parent ? parent.provides : Object.create(appContext.provides),
      accessCache: null,
      renderCache: [],
      // local resolved assets
      components: null,
      directives: null,
      // resolved props and emits options
      propsOptions: normalizePropsOptions(type, appContext),
      emitsOptions: normalizeEmitsOptions(type, appContext),
      // emit
      emit: null,
      emitted: null,
      // props default value
      propsDefaults: EMPTY_OBJ,
      // inheritAttrs
      inheritAttrs: type.inheritAttrs,
      // state
      ctx: EMPTY_OBJ,
      data: EMPTY_OBJ,
      props: EMPTY_OBJ,
      attrs: EMPTY_OBJ,
      slots: EMPTY_OBJ,
      refs: EMPTY_OBJ,
      setupState: EMPTY_OBJ,
      setupContext: null,
      // suspense related
      suspense: suspense,
      suspenseId: suspense ? suspense.pendingId : 0,
      asyncDep: null,
      asyncResolved: false,
      // lifecycle hooks
      // not using enums here because it results in computed properties
      isMounted: false,
      isUnmounted: false,
      isDeactivated: false,
      bc: null,
      c: null,
      bm: null,
      m: null,
      bu: null,
      u: null,
      um: null,
      bum: null,
      da: null,
      a: null,
      rtg: null,
      rtc: null,
      ec: null,
      sp: null
    };
    {
      instance.ctx = {
        _: instance
      };
    }
    instance.root = parent ? parent.root : instance;
    instance.emit = emit$1.bind(null, instance);
    // apply custom element special handling
    if (vnode.ce) {
      vnode.ce(instance);
    }
    return instance;
  }
  var currentInstance = null;
  var getCurrentInstance = function getCurrentInstance() {
    return currentInstance || currentRenderingInstance;
  };
  var setCurrentInstance = function setCurrentInstance(instance) {
    currentInstance = instance;
    instance.scope.on();
  };
  var unsetCurrentInstance = function unsetCurrentInstance() {
    currentInstance && currentInstance.scope.off();
    currentInstance = null;
  };
  function isStatefulComponent(instance) {
    return instance.vnode.shapeFlag & 4 /* ShapeFlags.STATEFUL_COMPONENT */;
  }

  var isInSSRComponentSetup = false;
  function setupComponent(instance, isSSR) {
    if (isSSR === void 0) {
      isSSR = false;
    }
    isInSSRComponentSetup = isSSR;
    var _instance$vnode = instance.vnode,
      props = _instance$vnode.props,
      children = _instance$vnode.children;
    var isStateful = isStatefulComponent(instance);
    initProps(instance, props, isStateful, isSSR);
    initSlots(instance, children);
    var setupResult = isStateful ? setupStatefulComponent(instance, isSSR) : undefined;
    isInSSRComponentSetup = false;
    return setupResult;
  }
  function setupStatefulComponent(instance, isSSR) {
    var Component = instance.type;
    // 0. create render proxy property access cache
    instance.accessCache = Object.create(null);
    // 1. create public instance / render proxy
    // also mark it raw so it's never observed
    instance.proxy = markRaw(new Proxy(instance.ctx, PublicInstanceProxyHandlers));
    // 2. call setup()
    var setup = Component.setup;
    if (setup) {
      var setupContext = instance.setupContext = setup.length > 1 ? createSetupContext(instance) : null;
      setCurrentInstance(instance);
      pauseTracking();
      var setupResult = callWithErrorHandling(setup, instance, 0 /* ErrorCodes.SETUP_FUNCTION */, [instance.props, setupContext]);
      resetTracking();
      unsetCurrentInstance();
      if (isPromise$1(setupResult)) {
        setupResult.then(unsetCurrentInstance, unsetCurrentInstance);
        if (isSSR) {
          // return the promise so server-renderer can wait on it
          return setupResult.then(function (resolvedResult) {
            handleSetupResult(instance, resolvedResult, isSSR);
          }).catch(function (e) {
            handleError$1(e, instance, 0 /* ErrorCodes.SETUP_FUNCTION */);
          });
        } else {
          // async setup returned Promise.
          // bail here and wait for re-entry.
          instance.asyncDep = setupResult;
        }
      } else {
        handleSetupResult(instance, setupResult, isSSR);
      }
    } else {
      finishComponentSetup(instance, isSSR);
    }
  }
  function handleSetupResult(instance, setupResult, isSSR) {
    if (isFunction$1(setupResult)) {
      // setup returned an inline render function
      if (instance.type.__ssrInlineRender) {
        // when the function's name is `ssrRender` (compiled by SFC inline mode),
        // set it as ssrRender instead.
        instance.ssrRender = setupResult;
      } else {
        instance.render = setupResult;
      }
    } else if (isObject$2(setupResult)) {
      instance.setupState = proxyRefs(setupResult);
    } else ;
    finishComponentSetup(instance, isSSR);
  }
  var compile;
  function finishComponentSetup(instance, isSSR, skipOptions) {
    var Component = instance.type;
    // template / render function normalization
    // could be already set when returned from setup()
    if (!instance.render) {
      // only do on-the-fly compile if not in SSR - SSR on-the-fly compilation
      // is done by server-renderer
      if (!isSSR && compile && !Component.render) {
        var template = Component.template || resolveMergedOptions(instance).template;
        if (template) {
          var _instance$appContext$ = instance.appContext.config,
            isCustomElement = _instance$appContext$.isCustomElement,
            compilerOptions = _instance$appContext$.compilerOptions;
          var delimiters = Component.delimiters,
            componentCompilerOptions = Component.compilerOptions;
          var finalCompilerOptions = extend$1(extend$1({
            isCustomElement: isCustomElement,
            delimiters: delimiters
          }, compilerOptions), componentCompilerOptions);
          Component.render = compile(template, finalCompilerOptions);
        }
      }
      instance.render = Component.render || NOOP;
    }
    // support for 2.x options
    {
      setCurrentInstance(instance);
      pauseTracking();
      applyOptions(instance);
      resetTracking();
      unsetCurrentInstance();
    }
  }
  function createAttrsProxy(instance) {
    return new Proxy(instance.attrs, {
      get: function get(target, key) {
        track(instance, "get" /* TrackOpTypes.GET */, '$attrs');
        return target[key];
      }
    });
  }
  function createSetupContext(instance) {
    var expose = function expose(exposed) {
      instance.exposed = exposed || {};
    };
    var attrs;
    {
      return {
        get attrs() {
          return attrs || (attrs = createAttrsProxy(instance));
        },
        slots: instance.slots,
        emit: instance.emit,
        expose: expose
      };
    }
  }
  function getExposeProxy(instance) {
    if (instance.exposed) {
      return instance.exposeProxy || (instance.exposeProxy = new Proxy(proxyRefs(markRaw(instance.exposed)), {
        get: function get(target, key) {
          if (key in target) {
            return target[key];
          } else if (key in publicPropertiesMap) {
            return publicPropertiesMap[key](instance);
          }
        },
        has: function has(target, key) {
          return key in target || key in publicPropertiesMap;
        }
      }));
    }
  }
  function getComponentName(Component, includeInferred) {
    if (includeInferred === void 0) {
      includeInferred = true;
    }
    return isFunction$1(Component) ? Component.displayName || Component.name : Component.name || includeInferred && Component.__name;
  }
  function isClassComponent(value) {
    return isFunction$1(value) && '__vccOpts' in value;
  }
  var computed = function computed(getterOrOptions, debugOptions) {
    // @ts-ignore
    return computed$1(getterOrOptions, debugOptions, isInSSRComponentSetup);
  };

  // Actual implementation
  function h(type, propsOrChildren, children) {
    var l = arguments.length;
    if (l === 2) {
      if (isObject$2(propsOrChildren) && !isArray$1(propsOrChildren)) {
        // single vnode without props
        if (isVNode(propsOrChildren)) {
          return createVNode(type, null, [propsOrChildren]);
        }
        // props without children
        return createVNode(type, propsOrChildren);
      } else {
        // omit props
        return createVNode(type, null, propsOrChildren);
      }
    } else {
      if (l > 3) {
        children = Array.prototype.slice.call(arguments, 2);
      } else if (l === 3 && isVNode(children)) {
        children = [children];
      }
      return createVNode(type, propsOrChildren, children);
    }
  }
  var ssrContextKey = Symbol("");
  var useSSRContext = function useSSRContext() {
    {
      var ctx = inject(ssrContextKey);
      return ctx;
    }
  };

  // Core API ------------------------------------------------------------------
  var version = "3.2.45";

  /**
   * Make a map and return a function for checking if a key
   * is in that map.
   * IMPORTANT: all calls of this function must be prefixed with
   * \/\*#\_\_PURE\_\_\*\/
   * So that rollup can tree-shake them if necessary.
   */
  function makeMap(str, expectsLowerCase) {
    var map = Object.create(null);
    var list = str.split(',');
    for (var i = 0; i < list.length; i++) {
      map[list[i]] = true;
    }
    return expectsLowerCase ? function (val) {
      return !!map[val.toLowerCase()];
    } : function (val) {
      return !!map[val];
    };
  }

  /**
   * On the client we only need to offer special cases for boolean attributes that
   * have different names from their corresponding dom properties:
   * - itemscope -> N/A
   * - allowfullscreen -> allowFullscreen
   * - formnovalidate -> formNoValidate
   * - ismap -> isMap
   * - nomodule -> noModule
   * - novalidate -> noValidate
   * - readonly -> readOnly
   */
  var specialBooleanAttrs = "itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly";
  var isSpecialBooleanAttr = /*#__PURE__*/makeMap(specialBooleanAttrs);
  /**
   * Boolean attributes should be included if the value is truthy or ''.
   * e.g. `<select multiple>` compiles to `{ multiple: '' }`
   */
  function includeBooleanAttr(value) {
    return !!value || value === '';
  }
  var onRE = /^on[^a-z]/;
  var isOn = function isOn(key) {
    return onRE.test(key);
  };
  var isModelListener = function isModelListener(key) {
    return key.startsWith('onUpdate:');
  };
  var extend = Object.assign;
  var isArray = Array.isArray;
  var isFunction = function isFunction(val) {
    return typeof val === 'function';
  };
  var isString = function isString(val) {
    return typeof val === 'string';
  };
  var isObject$1 = function isObject$1(val) {
    return val !== null && typeof val === 'object';
  };
  var cacheStringFunction = function cacheStringFunction(fn) {
    var cache = Object.create(null);
    return function (str) {
      var hit = cache[str];
      return hit || (cache[str] = fn(str));
    };
  };
  var hyphenateRE = /\B([A-Z])/g;
  /**
   * @private
   */
  var hyphenate = cacheStringFunction(function (str) {
    return str.replace(hyphenateRE, '-$1').toLowerCase();
  });
  /**
   * @private
   */
  var capitalize = cacheStringFunction(function (str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  });
  var invokeArrayFns = function invokeArrayFns(fns, arg) {
    for (var i = 0; i < fns.length; i++) {
      fns[i](arg);
    }
  };
  var toNumber = function toNumber(val) {
    var n = parseFloat(val);
    return isNaN(n) ? val : n;
  };
  var svgNS = 'http://www.w3.org/2000/svg';
  var doc = typeof document !== 'undefined' ? document : null;
  var templateContainer = doc && /*#__PURE__*/doc.createElement('template');
  var nodeOps = {
    insert: function insert(child, parent, anchor) {
      parent.insertBefore(child, anchor || null);
    },
    remove: function remove(child) {
      var parent = child.parentNode;
      if (parent) {
        parent.removeChild(child);
      }
    },
    createElement: function createElement(tag, isSVG, is, props) {
      var el = isSVG ? doc.createElementNS(svgNS, tag) : doc.createElement(tag, is ? {
        is: is
      } : undefined);
      if (tag === 'select' && props && props.multiple != null) {
        el.setAttribute('multiple', props.multiple);
      }
      return el;
    },
    createText: function createText(text) {
      return doc.createTextNode(text);
    },
    createComment: function createComment(text) {
      return doc.createComment(text);
    },
    setText: function setText(node, text) {
      node.nodeValue = text;
    },
    setElementText: function setElementText(el, text) {
      el.textContent = text;
    },
    parentNode: function parentNode(node) {
      return node.parentNode;
    },
    nextSibling: function nextSibling(node) {
      return node.nextSibling;
    },
    querySelector: function querySelector(selector) {
      return doc.querySelector(selector);
    },
    setScopeId: function setScopeId(el, id) {
      el.setAttribute(id, '');
    },
    // __UNSAFE__
    // Reason: innerHTML.
    // Static content here can only come from compiled templates.
    // As long as the user only uses trusted templates, this is safe.
    insertStaticContent: function insertStaticContent(content, parent, anchor, isSVG, start, end) {
      // <parent> before | first ... last | anchor </parent>
      var before = anchor ? anchor.previousSibling : parent.lastChild;
      // #5308 can only take cached path if:
      // - has a single root node
      // - nextSibling info is still available
      if (start && (start === end || start.nextSibling)) {
        // cached
        while (true) {
          parent.insertBefore(start.cloneNode(true), anchor);
          if (start === end || !(start = start.nextSibling)) break;
        }
      } else {
        // fresh insert
        templateContainer.innerHTML = isSVG ? "<svg>" + content + "</svg>" : content;
        var template = templateContainer.content;
        if (isSVG) {
          // remove outer svg wrapper
          var wrapper = template.firstChild;
          while (wrapper.firstChild) {
            template.appendChild(wrapper.firstChild);
          }
          template.removeChild(wrapper);
        }
        parent.insertBefore(template, anchor);
      }
      return [
      // first
      before ? before.nextSibling : parent.firstChild,
      // last
      anchor ? anchor.previousSibling : parent.lastChild];
    }
  };

  // compiler should normalize class + :class bindings on the same element
  // into a single binding ['staticClass', dynamic]
  function patchClass(el, value, isSVG) {
    // directly setting className should be faster than setAttribute in theory
    // if this is an element during a transition, take the temporary transition
    // classes into account.
    var transitionClasses = el._vtc;
    if (transitionClasses) {
      value = (value ? [value].concat(transitionClasses) : [].concat(transitionClasses)).join(' ');
    }
    if (value == null) {
      el.removeAttribute('class');
    } else if (isSVG) {
      el.setAttribute('class', value);
    } else {
      el.className = value;
    }
  }
  function patchStyle(el, prev, next) {
    var style = el.style;
    var isCssString = isString(next);
    if (next && !isCssString) {
      for (var key in next) {
        setStyle(style, key, next[key]);
      }
      if (prev && !isString(prev)) {
        for (var _key12 in prev) {
          if (next[_key12] == null) {
            setStyle(style, _key12, '');
          }
        }
      }
    } else {
      var currentDisplay = style.display;
      if (isCssString) {
        if (prev !== next) {
          style.cssText = next;
        }
      } else if (prev) {
        el.removeAttribute('style');
      }
      // indicates that the `display` of the element is controlled by `v-show`,
      // so we always keep the current `display` value regardless of the `style`
      // value, thus handing over control to `v-show`.
      if ('_vod' in el) {
        style.display = currentDisplay;
      }
    }
  }
  var importantRE = /\s*!important$/;
  function setStyle(style, name, val) {
    if (isArray(val)) {
      val.forEach(function (v) {
        return setStyle(style, name, v);
      });
    } else {
      if (val == null) val = '';
      if (name.startsWith('--')) {
        // custom property definition
        style.setProperty(name, val);
      } else {
        var prefixed = autoPrefix(style, name);
        if (importantRE.test(val)) {
          // !important
          style.setProperty(hyphenate(prefixed), val.replace(importantRE, ''), 'important');
        } else {
          style[prefixed] = val;
        }
      }
    }
  }
  var prefixes = ['Webkit', 'Moz', 'ms'];
  var prefixCache = {};
  function autoPrefix(style, rawName) {
    var cached = prefixCache[rawName];
    if (cached) {
      return cached;
    }
    var name = camelize(rawName);
    if (name !== 'filter' && name in style) {
      return prefixCache[rawName] = name;
    }
    name = capitalize(name);
    for (var i = 0; i < prefixes.length; i++) {
      var prefixed = prefixes[i] + name;
      if (prefixed in style) {
        return prefixCache[rawName] = prefixed;
      }
    }
    return rawName;
  }
  var xlinkNS = 'http://www.w3.org/1999/xlink';
  function patchAttr(el, key, value, isSVG, instance) {
    if (isSVG && key.startsWith('xlink:')) {
      if (value == null) {
        el.removeAttributeNS(xlinkNS, key.slice(6, key.length));
      } else {
        el.setAttributeNS(xlinkNS, key, value);
      }
    } else {
      // note we are only checking boolean attributes that don't have a
      // corresponding dom prop of the same name here.
      var isBoolean = isSpecialBooleanAttr(key);
      if (value == null || isBoolean && !includeBooleanAttr(value)) {
        el.removeAttribute(key);
      } else {
        el.setAttribute(key, isBoolean ? '' : value);
      }
    }
  }

  // __UNSAFE__
  // functions. The user is responsible for using them with only trusted content.
  function patchDOMProp(el, key, value,
  // the following args are passed only due to potential innerHTML/textContent
  // overriding existing VNodes, in which case the old tree must be properly
  // unmounted.
  prevChildren, parentComponent, parentSuspense, unmountChildren) {
    if (key === 'innerHTML' || key === 'textContent') {
      if (prevChildren) {
        unmountChildren(prevChildren, parentComponent, parentSuspense);
      }
      el[key] = value == null ? '' : value;
      return;
    }
    if (key === 'value' && el.tagName !== 'PROGRESS' &&
    // custom elements may use _value internally
    !el.tagName.includes('-')) {
      // store value as _value as well since
      // non-string values will be stringified.
      el._value = value;
      var newValue = value == null ? '' : value;
      if (el.value !== newValue ||
      // #4956: always set for OPTION elements because its value falls back to
      // textContent if no value attribute is present. And setting .value for
      // OPTION has no side effect
      el.tagName === 'OPTION') {
        el.value = newValue;
      }
      if (value == null) {
        el.removeAttribute(key);
      }
      return;
    }
    var needRemove = false;
    if (value === '' || value == null) {
      var type = typeof el[key];
      if (type === 'boolean') {
        // e.g. <select multiple> compiles to { multiple: '' }
        value = includeBooleanAttr(value);
      } else if (value == null && type === 'string') {
        // e.g. <div :id="null">
        value = '';
        needRemove = true;
      } else if (type === 'number') {
        // e.g. <img :width="null">
        value = 0;
        needRemove = true;
      }
    }
    // some properties perform value validation and throw,
    // some properties has getter, no setter, will error in 'use strict'
    // eg. <select :type="null"></select> <select :willValidate="null"></select>
    try {
      el[key] = value;
    } catch (e) {}
    needRemove && el.removeAttribute(key);
  }
  function addEventListener(el, event, handler, options) {
    el.addEventListener(event, handler, options);
  }
  function removeEventListener(el, event, handler, options) {
    el.removeEventListener(event, handler, options);
  }
  function patchEvent(el, rawName, prevValue, nextValue, instance) {
    if (instance === void 0) {
      instance = null;
    }
    // vei = vue event invokers
    var invokers = el._vei || (el._vei = {});
    var existingInvoker = invokers[rawName];
    if (nextValue && existingInvoker) {
      // patch
      existingInvoker.value = nextValue;
    } else {
      var _parseName = parseName(rawName),
        name = _parseName[0],
        _options2 = _parseName[1];
      if (nextValue) {
        // add
        var invoker = invokers[rawName] = createInvoker(nextValue, instance);
        addEventListener(el, name, invoker, _options2);
      } else if (existingInvoker) {
        // remove
        removeEventListener(el, name, existingInvoker, _options2);
        invokers[rawName] = undefined;
      }
    }
  }
  var optionsModifierRE = /(?:Once|Passive|Capture)$/;
  function parseName(name) {
    var options;
    if (optionsModifierRE.test(name)) {
      options = {};
      var m;
      while (m = name.match(optionsModifierRE)) {
        name = name.slice(0, name.length - m[0].length);
        options[m[0].toLowerCase()] = true;
      }
    }
    var event = name[2] === ':' ? name.slice(3) : hyphenate(name.slice(2));
    return [event, options];
  }
  // To avoid the overhead of repeatedly calling Date.now(), we cache
  // and use the same timestamp for all event listeners attached in the same tick.
  var cachedNow = 0;
  var p = /*#__PURE__*/Promise.resolve();
  var getNow = function getNow() {
    return cachedNow || (p.then(function () {
      return cachedNow = 0;
    }), cachedNow = Date.now());
  };
  function createInvoker(initialValue, instance) {
    var invoker = function invoker(e) {
      // async edge case vuejs/vue#6566
      // inner click event triggers patch, event handler
      // attached to outer element during patch, and triggered again. This
      // happens because browsers fire microtask ticks between event propagation.
      // this no longer happens for templates in Vue 3, but could still be
      // theoretically possible for hand-written render functions.
      // the solution: we save the timestamp when a handler is attached,
      // and also attach the timestamp to any event that was handled by vue
      // for the first time (to avoid inconsistent event timestamp implementations
      // or events fired from iframes, e.g. #2513)
      // The handler would only fire if the event passed to it was fired
      // AFTER it was attached.
      if (!e._vts) {
        e._vts = Date.now();
      } else if (e._vts <= invoker.attached) {
        return;
      }
      callWithAsyncErrorHandling(patchStopImmediatePropagation(e, invoker.value), instance, 5 /* ErrorCodes.NATIVE_EVENT_HANDLER */, [e]);
    };
    invoker.value = initialValue;
    invoker.attached = getNow();
    return invoker;
  }
  function patchStopImmediatePropagation(e, value) {
    if (isArray(value)) {
      var originalStop = e.stopImmediatePropagation;
      e.stopImmediatePropagation = function () {
        originalStop.call(e);
        e._stopped = true;
      };
      return value.map(function (fn) {
        return function (e) {
          return !e._stopped && fn && fn(e);
        };
      });
    } else {
      return value;
    }
  }
  var nativeOnRE = /^on[a-z]/;
  var patchProp = function patchProp(el, key, prevValue, nextValue, isSVG, prevChildren, parentComponent, parentSuspense, unmountChildren) {
    if (isSVG === void 0) {
      isSVG = false;
    }
    if (key === 'class') {
      patchClass(el, nextValue, isSVG);
    } else if (key === 'style') {
      patchStyle(el, prevValue, nextValue);
    } else if (isOn(key)) {
      // ignore v-model listeners
      if (!isModelListener(key)) {
        patchEvent(el, key, prevValue, nextValue, parentComponent);
      }
    } else if (key[0] === '.' ? (key = key.slice(1), true) : key[0] === '^' ? (key = key.slice(1), false) : shouldSetAsProp(el, key, nextValue, isSVG)) {
      patchDOMProp(el, key, nextValue, prevChildren, parentComponent, parentSuspense, unmountChildren);
    } else {
      // special case for <input v-model type="checkbox"> with
      // :true-value & :false-value
      // store value as dom properties since non-string values will be
      // stringified.
      if (key === 'true-value') {
        el._trueValue = nextValue;
      } else if (key === 'false-value') {
        el._falseValue = nextValue;
      }
      patchAttr(el, key, nextValue, isSVG);
    }
  };
  function shouldSetAsProp(el, key, value, isSVG) {
    if (isSVG) {
      // most keys must be set as attribute on svg elements to work
      // ...except innerHTML & textContent
      if (key === 'innerHTML' || key === 'textContent') {
        return true;
      }
      // or native onclick with function values
      if (key in el && nativeOnRE.test(key) && isFunction(value)) {
        return true;
      }
      return false;
    }
    // these are enumerated attrs, however their corresponding DOM properties
    // are actually booleans - this leads to setting it with a string "false"
    // value leading it to be coerced to `true`, so we need to always treat
    // them as attributes.
    // Note that `contentEditable` doesn't have this problem: its DOM
    // property is also enumerated string values.
    if (key === 'spellcheck' || key === 'draggable' || key === 'translate') {
      return false;
    }
    // #1787, #2840 form property on form elements is readonly and must be set as
    // attribute.
    if (key === 'form') {
      return false;
    }
    // #1526 <input list> must be set as attribute
    if (key === 'list' && el.tagName === 'INPUT') {
      return false;
    }
    // #2766 <textarea type> must be set as attribute
    if (key === 'type' && el.tagName === 'TEXTAREA') {
      return false;
    }
    // native onclick with string value, must be set as attribute
    if (nativeOnRE.test(key) && isString(value)) {
      return false;
    }
    return key in el;
  }
  var TRANSITION = 'transition';
  var ANIMATION = 'animation';
  // DOM Transition is a higher-order-component based on the platform-agnostic
  // base Transition component, with DOM-specific logic.
  var Transition = function Transition(props, _ref) {
    var slots = _ref.slots;
    return h(BaseTransition, resolveTransitionProps(props), slots);
  };
  Transition.displayName = 'Transition';
  var DOMTransitionPropsValidators = {
    name: String,
    type: String,
    css: {
      type: Boolean,
      default: true
    },
    duration: [String, Number, Object],
    enterFromClass: String,
    enterActiveClass: String,
    enterToClass: String,
    appearFromClass: String,
    appearActiveClass: String,
    appearToClass: String,
    leaveFromClass: String,
    leaveActiveClass: String,
    leaveToClass: String
  };
  Transition.props = /*#__PURE__*/extend({}, BaseTransition.props, DOMTransitionPropsValidators);
  /**
   * #3227 Incoming hooks may be merged into arrays when wrapping Transition
   * with custom HOCs.
   */
  var callHook = function callHook(hook, args) {
    if (args === void 0) {
      args = [];
    }
    if (isArray(hook)) {
      hook.forEach(function (h) {
        return h.apply(void 0, args);
      });
    } else if (hook) {
      hook.apply(void 0, args);
    }
  };
  /**
   * Check if a hook expects a callback (2nd arg), which means the user
   * intends to explicitly control the end of the transition.
   */
  var hasExplicitCallback = function hasExplicitCallback(hook) {
    return hook ? isArray(hook) ? hook.some(function (h) {
      return h.length > 1;
    }) : hook.length > 1 : false;
  };
  function resolveTransitionProps(rawProps) {
    var baseProps = {};
    for (var key in rawProps) {
      if (!(key in DOMTransitionPropsValidators)) {
        baseProps[key] = rawProps[key];
      }
    }
    if (rawProps.css === false) {
      return baseProps;
    }
    var _rawProps$name = rawProps.name,
      name = _rawProps$name === void 0 ? 'v' : _rawProps$name,
      type = rawProps.type,
      duration = rawProps.duration,
      _rawProps$enterFromCl = rawProps.enterFromClass,
      enterFromClass = _rawProps$enterFromCl === void 0 ? name + "-enter-from" : _rawProps$enterFromCl,
      _rawProps$enterActive = rawProps.enterActiveClass,
      enterActiveClass = _rawProps$enterActive === void 0 ? name + "-enter-active" : _rawProps$enterActive,
      _rawProps$enterToClas = rawProps.enterToClass,
      enterToClass = _rawProps$enterToClas === void 0 ? name + "-enter-to" : _rawProps$enterToClas,
      _rawProps$appearFromC = rawProps.appearFromClass,
      appearFromClass = _rawProps$appearFromC === void 0 ? enterFromClass : _rawProps$appearFromC,
      _rawProps$appearActiv = rawProps.appearActiveClass,
      appearActiveClass = _rawProps$appearActiv === void 0 ? enterActiveClass : _rawProps$appearActiv,
      _rawProps$appearToCla = rawProps.appearToClass,
      appearToClass = _rawProps$appearToCla === void 0 ? enterToClass : _rawProps$appearToCla,
      _rawProps$leaveFromCl = rawProps.leaveFromClass,
      leaveFromClass = _rawProps$leaveFromCl === void 0 ? name + "-leave-from" : _rawProps$leaveFromCl,
      _rawProps$leaveActive = rawProps.leaveActiveClass,
      leaveActiveClass = _rawProps$leaveActive === void 0 ? name + "-leave-active" : _rawProps$leaveActive,
      _rawProps$leaveToClas = rawProps.leaveToClass,
      leaveToClass = _rawProps$leaveToClas === void 0 ? name + "-leave-to" : _rawProps$leaveToClas;
    var durations = normalizeDuration(duration);
    var enterDuration = durations && durations[0];
    var leaveDuration = durations && durations[1];
    var _onBeforeEnter = baseProps.onBeforeEnter,
      onEnter = baseProps.onEnter,
      _onEnterCancelled = baseProps.onEnterCancelled,
      _onLeave = baseProps.onLeave,
      _onLeaveCancelled = baseProps.onLeaveCancelled,
      _baseProps$onBeforeAp = baseProps.onBeforeAppear,
      _onBeforeAppear = _baseProps$onBeforeAp === void 0 ? _onBeforeEnter : _baseProps$onBeforeAp,
      _baseProps$onAppear = baseProps.onAppear,
      onAppear = _baseProps$onAppear === void 0 ? onEnter : _baseProps$onAppear,
      _baseProps$onAppearCa = baseProps.onAppearCancelled,
      _onAppearCancelled = _baseProps$onAppearCa === void 0 ? _onEnterCancelled : _baseProps$onAppearCa;
    var finishEnter = function finishEnter(el, isAppear, done) {
      removeTransitionClass(el, isAppear ? appearToClass : enterToClass);
      removeTransitionClass(el, isAppear ? appearActiveClass : enterActiveClass);
      done && done();
    };
    var finishLeave = function finishLeave(el, done) {
      el._isLeaving = false;
      removeTransitionClass(el, leaveFromClass);
      removeTransitionClass(el, leaveToClass);
      removeTransitionClass(el, leaveActiveClass);
      done && done();
    };
    var makeEnterHook = function makeEnterHook(isAppear) {
      return function (el, done) {
        var hook = isAppear ? onAppear : onEnter;
        var resolve = function resolve() {
          return finishEnter(el, isAppear, done);
        };
        callHook(hook, [el, resolve]);
        nextFrame(function () {
          removeTransitionClass(el, isAppear ? appearFromClass : enterFromClass);
          addTransitionClass(el, isAppear ? appearToClass : enterToClass);
          if (!hasExplicitCallback(hook)) {
            whenTransitionEnds(el, type, enterDuration, resolve);
          }
        });
      };
    };
    return extend(baseProps, {
      onBeforeEnter: function onBeforeEnter(el) {
        callHook(_onBeforeEnter, [el]);
        addTransitionClass(el, enterFromClass);
        addTransitionClass(el, enterActiveClass);
      },
      onBeforeAppear: function onBeforeAppear(el) {
        callHook(_onBeforeAppear, [el]);
        addTransitionClass(el, appearFromClass);
        addTransitionClass(el, appearActiveClass);
      },
      onEnter: makeEnterHook(false),
      onAppear: makeEnterHook(true),
      onLeave: function onLeave(el, done) {
        el._isLeaving = true;
        var resolve = function resolve() {
          return finishLeave(el, done);
        };
        addTransitionClass(el, leaveFromClass);
        // force reflow so *-leave-from classes immediately take effect (#2593)
        forceReflow();
        addTransitionClass(el, leaveActiveClass);
        nextFrame(function () {
          if (!el._isLeaving) {
            // cancelled
            return;
          }
          removeTransitionClass(el, leaveFromClass);
          addTransitionClass(el, leaveToClass);
          if (!hasExplicitCallback(_onLeave)) {
            whenTransitionEnds(el, type, leaveDuration, resolve);
          }
        });
        callHook(_onLeave, [el, resolve]);
      },
      onEnterCancelled: function onEnterCancelled(el) {
        finishEnter(el, false);
        callHook(_onEnterCancelled, [el]);
      },
      onAppearCancelled: function onAppearCancelled(el) {
        finishEnter(el, true);
        callHook(_onAppearCancelled, [el]);
      },
      onLeaveCancelled: function onLeaveCancelled(el) {
        finishLeave(el);
        callHook(_onLeaveCancelled, [el]);
      }
    });
  }
  function normalizeDuration(duration) {
    if (duration == null) {
      return null;
    } else if (isObject$1(duration)) {
      return [NumberOf(duration.enter), NumberOf(duration.leave)];
    } else {
      var n = NumberOf(duration);
      return [n, n];
    }
  }
  function NumberOf(val) {
    var res = toNumber(val);
    return res;
  }
  function addTransitionClass(el, cls) {
    cls.split(/\s+/).forEach(function (c) {
      return c && el.classList.add(c);
    });
    (el._vtc || (el._vtc = new Set())).add(cls);
  }
  function removeTransitionClass(el, cls) {
    cls.split(/\s+/).forEach(function (c) {
      return c && el.classList.remove(c);
    });
    var _vtc = el._vtc;
    if (_vtc) {
      _vtc.delete(cls);
      if (!_vtc.size) {
        el._vtc = undefined;
      }
    }
  }
  function nextFrame(cb) {
    requestAnimationFrame(function () {
      requestAnimationFrame(cb);
    });
  }
  var endId = 0;
  function whenTransitionEnds(el, expectedType, explicitTimeout, resolve) {
    var id = el._endId = ++endId;
    var resolveIfNotStale = function resolveIfNotStale() {
      if (id === el._endId) {
        resolve();
      }
    };
    if (explicitTimeout) {
      return setTimeout(resolveIfNotStale, explicitTimeout);
    }
    var _getTransitionInfo = getTransitionInfo(el, expectedType),
      type = _getTransitionInfo.type,
      timeout = _getTransitionInfo.timeout,
      propCount = _getTransitionInfo.propCount;
    if (!type) {
      return resolve();
    }
    var endEvent = type + 'end';
    var ended = 0;
    var end = function end() {
      el.removeEventListener(endEvent, onEnd);
      resolveIfNotStale();
    };
    var onEnd = function onEnd(e) {
      if (e.target === el && ++ended >= propCount) {
        end();
      }
    };
    setTimeout(function () {
      if (ended < propCount) {
        end();
      }
    }, timeout + 1);
    el.addEventListener(endEvent, onEnd);
  }
  function getTransitionInfo(el, expectedType) {
    var styles = window.getComputedStyle(el);
    // JSDOM may return undefined for transition properties
    var getStyleProperties = function getStyleProperties(key) {
      return (styles[key] || '').split(', ');
    };
    var transitionDelays = getStyleProperties(TRANSITION + "Delay");
    var transitionDurations = getStyleProperties(TRANSITION + "Duration");
    var transitionTimeout = getTimeout(transitionDelays, transitionDurations);
    var animationDelays = getStyleProperties(ANIMATION + "Delay");
    var animationDurations = getStyleProperties(ANIMATION + "Duration");
    var animationTimeout = getTimeout(animationDelays, animationDurations);
    var type = null;
    var timeout = 0;
    var propCount = 0;
    /* istanbul ignore if */
    if (expectedType === TRANSITION) {
      if (transitionTimeout > 0) {
        type = TRANSITION;
        timeout = transitionTimeout;
        propCount = transitionDurations.length;
      }
    } else if (expectedType === ANIMATION) {
      if (animationTimeout > 0) {
        type = ANIMATION;
        timeout = animationTimeout;
        propCount = animationDurations.length;
      }
    } else {
      timeout = Math.max(transitionTimeout, animationTimeout);
      type = timeout > 0 ? transitionTimeout > animationTimeout ? TRANSITION : ANIMATION : null;
      propCount = type ? type === TRANSITION ? transitionDurations.length : animationDurations.length : 0;
    }
    var hasTransform = type === TRANSITION && /\b(transform|all)(,|$)/.test(getStyleProperties(TRANSITION + "Property").toString());
    return {
      type: type,
      timeout: timeout,
      propCount: propCount,
      hasTransform: hasTransform
    };
  }
  function getTimeout(delays, durations) {
    while (delays.length < durations.length) {
      delays = delays.concat(delays);
    }
    return Math.max.apply(Math, durations.map(function (d, i) {
      return toMs(d) + toMs(delays[i]);
    }));
  }
  // Old versions of Chromium (below 61.0.3163.100) formats floating pointer
  // numbers in a locale-dependent way, using a comma instead of a dot.
  // If comma is not replaced with a dot, the input will be rounded down
  // (i.e. acting as a floor function) causing unexpected behaviors
  function toMs(s) {
    return Number(s.slice(0, -1).replace(',', '.')) * 1000;
  }
  // synchronously force layout to put elements into a certain state
  function forceReflow() {
    return document.body.offsetHeight;
  }
  var getModelAssigner = function getModelAssigner(vnode) {
    var fn = vnode.props['onUpdate:modelValue'] || false;
    return isArray(fn) ? function (value) {
      return invokeArrayFns(fn, value);
    } : fn;
  };
  function onCompositionStart(e) {
    e.target.composing = true;
  }
  function onCompositionEnd(e) {
    var target = e.target;
    if (target.composing) {
      target.composing = false;
      target.dispatchEvent(new Event('input'));
    }
  }
  // We are exporting the v-model runtime directly as vnode hooks so that it can
  // be tree-shaken in case v-model is never used.
  var vModelText = {
    created: function created(el, _ref3, vnode) {
      var _ref3$modifiers = _ref3.modifiers,
        lazy = _ref3$modifiers.lazy,
        trim = _ref3$modifiers.trim,
        number = _ref3$modifiers.number;
      el._assign = getModelAssigner(vnode);
      var castToNumber = number || vnode.props && vnode.props.type === 'number';
      addEventListener(el, lazy ? 'change' : 'input', function (e) {
        if (e.target.composing) return;
        var domValue = el.value;
        if (trim) {
          domValue = domValue.trim();
        }
        if (castToNumber) {
          domValue = toNumber(domValue);
        }
        el._assign(domValue);
      });
      if (trim) {
        addEventListener(el, 'change', function () {
          el.value = el.value.trim();
        });
      }
      if (!lazy) {
        addEventListener(el, 'compositionstart', onCompositionStart);
        addEventListener(el, 'compositionend', onCompositionEnd);
        // Safari < 10.2 & UIWebView doesn't fire compositionend when
        // switching focus before confirming composition choice
        // this also fixes the issue where some browsers e.g. iOS Chrome
        // fires "change" instead of "input" on autocomplete.
        addEventListener(el, 'change', onCompositionEnd);
      }
    },
    // set value on mounted so it's after min/max for type="range"
    mounted: function mounted(el, _ref4) {
      var value = _ref4.value;
      el.value = value == null ? '' : value;
    },
    beforeUpdate: function beforeUpdate(el, _ref5, vnode) {
      var value = _ref5.value,
        _ref5$modifiers = _ref5.modifiers,
        lazy = _ref5$modifiers.lazy,
        trim = _ref5$modifiers.trim,
        number = _ref5$modifiers.number;
      el._assign = getModelAssigner(vnode);
      // avoid clearing unresolved text. #2302
      if (el.composing) return;
      if (document.activeElement === el && el.type !== 'range') {
        if (lazy) {
          return;
        }
        if (trim && el.value.trim() === value) {
          return;
        }
        if ((number || el.type === 'number') && toNumber(el.value) === value) {
          return;
        }
      }
      var newValue = value == null ? '' : value;
      if (el.value !== newValue) {
        el.value = newValue;
      }
    }
  };
  var systemModifiers = ['ctrl', 'shift', 'alt', 'meta'];
  var modifierGuards = {
    stop: function stop(e) {
      return e.stopPropagation();
    },
    prevent: function prevent(e) {
      return e.preventDefault();
    },
    self: function self(e) {
      return e.target !== e.currentTarget;
    },
    ctrl: function ctrl(e) {
      return !e.ctrlKey;
    },
    shift: function shift(e) {
      return !e.shiftKey;
    },
    alt: function alt(e) {
      return !e.altKey;
    },
    meta: function meta(e) {
      return !e.metaKey;
    },
    left: function left(e) {
      return 'button' in e && e.button !== 0;
    },
    middle: function middle(e) {
      return 'button' in e && e.button !== 1;
    },
    right: function right(e) {
      return 'button' in e && e.button !== 2;
    },
    exact: function exact(e, modifiers) {
      return systemModifiers.some(function (m) {
        return e[m + "Key"] && !modifiers.includes(m);
      });
    }
  };
  /**
   * @private
   */
  var withModifiers = function withModifiers(fn, modifiers) {
    return function (event) {
      for (var i = 0; i < modifiers.length; i++) {
        var guard = modifierGuards[modifiers[i]];
        if (guard && guard(event, modifiers)) return;
      }
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }
      return fn.apply(void 0, [event].concat(args));
    };
  };
  // Kept for 2.x compat.
  // Note: IE11 compat for `spacebar` and `del` is removed for now.
  var keyNames = {
    esc: 'escape',
    space: ' ',
    up: 'arrow-up',
    left: 'arrow-left',
    right: 'arrow-right',
    down: 'arrow-down',
    delete: 'backspace'
  };
  /**
   * @private
   */
  var withKeys = function withKeys(fn, modifiers) {
    return function (event) {
      if (!('key' in event)) {
        return;
      }
      var eventKey = hyphenate(event.key);
      if (modifiers.some(function (k) {
        return k === eventKey || keyNames[k] === eventKey;
      })) {
        return fn(event);
      }
    };
  };
  var vShow = {
    beforeMount: function beforeMount(el, _ref15, _ref16) {
      var value = _ref15.value;
      var transition = _ref16.transition;
      el._vod = el.style.display === 'none' ? '' : el.style.display;
      if (transition && value) {
        transition.beforeEnter(el);
      } else {
        setDisplay(el, value);
      }
    },
    mounted: function mounted(el, _ref17, _ref18) {
      var value = _ref17.value;
      var transition = _ref18.transition;
      if (transition && value) {
        transition.enter(el);
      }
    },
    updated: function updated(el, _ref19, _ref20) {
      var value = _ref19.value,
        oldValue = _ref19.oldValue;
      var transition = _ref20.transition;
      if (!value === !oldValue) return;
      if (transition) {
        if (value) {
          transition.beforeEnter(el);
          setDisplay(el, true);
          transition.enter(el);
        } else {
          transition.leave(el, function () {
            setDisplay(el, false);
          });
        }
      } else {
        setDisplay(el, value);
      }
    },
    beforeUnmount: function beforeUnmount(el, _ref21) {
      var value = _ref21.value;
      setDisplay(el, value);
    }
  };
  function setDisplay(el, value) {
    el.style.display = value ? el._vod : 'none';
  }
  var rendererOptions = /*#__PURE__*/extend({
    patchProp: patchProp
  }, nodeOps);
  // lazy create the renderer - this makes core renderer logic tree-shakable
  // in case the user only imports reactivity utilities from Vue.
  var renderer;
  function ensureRenderer() {
    return renderer || (renderer = createRenderer(rendererOptions));
  }
  var createApp = function createApp() {
    var _ensureRenderer;
    var app = (_ensureRenderer = ensureRenderer()).createApp.apply(_ensureRenderer, arguments);
    var mount = app.mount;
    app.mount = function (containerOrSelector) {
      var container = normalizeContainer(containerOrSelector);
      if (!container) return;
      var component = app._component;
      if (!isFunction(component) && !component.render && !component.template) {
        // __UNSAFE__
        // Reason: potential execution of JS expressions in in-DOM template.
        // The user must make sure the in-DOM template is trusted. If it's
        // rendered by the server, the template should not contain any user data.
        component.template = container.innerHTML;
      }
      // clear content before mounting
      container.innerHTML = '';
      var proxy = mount(container, false, container instanceof SVGElement);
      if (container instanceof Element) {
        container.removeAttribute('v-cloak');
        container.setAttribute('data-v-app', '');
      }
      return proxy;
    };
    return app;
  };
  function normalizeContainer(container) {
    if (isString(container)) {
      var res = document.querySelector(container);
      return res;
    }
    return container;
  }

  // Loading state
  var SET_IS_LOADING = 'SET_IS_LOADING';

  // Selecting media items
  var SELECT_DIRECTORY = 'SELECT_DIRECTORY';
  var SELECT_BROWSER_ITEM = 'SELECT_BROWSER_ITEM';
  var SELECT_BROWSER_ITEMS = 'SELECT_BROWSER_ITEMS';
  var UNSELECT_BROWSER_ITEM = 'UNSELECT_BROWSER_ITEM';
  var UNSELECT_ALL_BROWSER_ITEMS = 'UNSELECT_ALL_BROWSER_ITEMS';

  // In/Decrease grid item size
  var INCREASE_GRID_SIZE = 'INCREASE_GRID_SIZE';
  var DECREASE_GRID_SIZE = 'DECREASE_GRID_SIZE';

  // Api handlers
  var LOAD_CONTENTS_SUCCESS = 'LOAD_CONTENTS_SUCCESS';
  var LOAD_FULL_CONTENTS_SUCCESS = 'LOAD_FULL_CONTENTS_SUCCESS';
  var CREATE_DIRECTORY_SUCCESS = 'CREATE_DIRECTORY_SUCCESS';
  var UPLOAD_SUCCESS = 'UPLOAD_SUCCESS';

  // Create folder modal
  var SHOW_CREATE_FOLDER_MODAL = 'SHOW_CREATE_FOLDER_MODAL';
  var HIDE_CREATE_FOLDER_MODAL = 'HIDE_CREATE_FOLDER_MODAL';

  // Confirm Delete Modal
  var SHOW_CONFIRM_DELETE_MODAL = 'SHOW_CONFIRM_DELETE_MODAL';
  var HIDE_CONFIRM_DELETE_MODAL = 'HIDE_CONFIRM_DELETE_MODAL';

  // Infobar
  var SHOW_INFOBAR = 'SHOW_INFOBAR';
  var HIDE_INFOBAR = 'HIDE_INFOBAR';

  // Delete items
  var DELETE_SUCCESS = 'DELETE_SUCCESS';

  // List view
  var CHANGE_LIST_VIEW = 'CHANGE_LIST_VIEW';

  // Preview modal
  var SHOW_PREVIEW_MODAL = 'SHOW_PREVIEW_MODAL';
  var HIDE_PREVIEW_MODAL = 'HIDE_PREVIEW_MODAL';

  // Rename modal
  var SHOW_RENAME_MODAL = 'SHOW_RENAME_MODAL';
  var HIDE_RENAME_MODAL = 'HIDE_RENAME_MODAL';
  var RENAME_SUCCESS = 'RENAME_SUCCESS';

  // Share modal
  var SHOW_SHARE_MODAL = 'SHOW_SHARE_MODAL';
  var HIDE_SHARE_MODAL = 'HIDE_SHARE_MODAL';

  // Search Query
  var SET_SEARCH_QUERY = 'SET_SEARCH_QUERY';

  // Update item properties
  var UPDATE_ITEM_PROPERTIES = 'UPDATE_ITEM_PROPERTIES';

  // Update sorting by
  var UPDATE_SORT_BY = 'UPDATE_SORT_BY';

  // Update sorting direction
  var UPDATE_SORT_DIRECTION = 'UPDATE_SORT_DIRECTION';

  /**
   * Send a notification
   * @param {String} message
   * @param {{}} options
   *
   */
  function notify(message, options) {
    var _Joomla$renderMessage;
    var timer;
    if (options.type === 'message') {
      timer = 3000;
    }
    Joomla.renderMessages((_Joomla$renderMessage = {}, _Joomla$renderMessage[options.type] = [Joomla.Text._(message)], _Joomla$renderMessage), undefined, true, timer);
  }
  var notifications = {
    /* Send and success notification */
    success: function success(message, options) {
      notify(message, Object.assign({
        type: 'message',
        // @todo rename it to success
        dismiss: true
      }, options));
    },
    /* Send an error notification */
    error: function error(message, options) {
      notify(message, Object.assign({
        type: 'error',
        // @todo rename it to danger
        dismiss: true
      }, options));
    },
    /* Ask the user a question */
    ask: function ask(message) {
      return window.confirm(message);
    }
  };
  var navigable = {
    methods: {
      navigateTo: function navigateTo(path) {
        this.$store.dispatch('getContents', path, false, false);
      }
    }
  };
  var script$v = {
    name: 'MediaBrowserItemRow',
    mixins: [navigable],
    props: {
      item: {
        type: Object,
        default: function _default() {}
      }
    },
    computed: {
      /* The dimension of a file */dimension: function dimension() {
        if (!this.item.width) {
          return '';
        }
        return this.item.width + "px * " + this.item.height + "px";
      },
      isDir: function isDir() {
        return this.item.type === 'dir';
      },
      /* The size of a file in KB */size: function size() {
        if (!this.item.size) {
          return '';
        }
        return "" + (this.item.size / 1024).toFixed(2);
      },
      selected: function selected() {
        return !!this.isSelected();
      }
    },
    methods: {
      /* Handle the on row double click event */onDblClick: function onDblClick() {
        if (this.isDir) {
          this.navigateTo(this.item.path);
          return;
        }

        // @todo remove the hardcoded extensions here
        var extensionWithPreview = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mp3', 'pdf'];

        // Show preview
        if (this.item.extension && extensionWithPreview.includes(this.item.extension.toLowerCase())) {
          this.$store.commit(SHOW_PREVIEW_MODAL);
          this.$store.dispatch('getFullContents', this.item);
        }
      },
      /**
       * Whether or not the item is currently selected
       * @returns {boolean}
       */
      isSelected: function isSelected() {
        var _this3 = this;
        return this.$store.state.selectedItems.some(function (selected) {
          return selected.path === _this3.item.path;
        });
      },
      /**
       * Handle the click event
       * @param event
       */
      onClick: function onClick(event) {
        var path = false;
        var data = {
          path: path,
          thumb: false,
          fileType: this.item.mime_type ? this.item.mime_type : false,
          extension: this.item.extension ? this.item.extension : false
        };
        if (this.item.type === 'file') {
          data.path = this.item.path;
          data.thumb = this.item.thumb ? this.item.thumb : false;
          data.width = this.item.width ? this.item.width : 0;
          data.height = this.item.height ? this.item.height : 0;
          window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
            bubbles: true,
            cancelable: false,
            detail: data
          }));
        }

        // Handle clicks when the item was not selected
        if (!this.isSelected()) {
          // Unselect all other selected items,
          // if the shift key was not pressed during the click event
          if (!(event.shiftKey || event.keyCode === 13)) {
            this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
          }
          this.$store.commit(SELECT_BROWSER_ITEM, this.item);
          return;
        }

        // If more than one item was selected and the user clicks again on the selected item,
        // he most probably wants to unselect all other items.
        if (this.$store.state.selectedItems.length > 1) {
          this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
          this.$store.commit(SELECT_BROWSER_ITEM, this.item);
        }
      }
    }
  };
  var _hoisted_1$v = ["data-type"];
  var _hoisted_2$t = {
    scope: "row",
    class: "name"
  };
  var _hoisted_3$j = {
    class: "size"
  };
  var _hoisted_4$b = {
    key: 0
  };
  var _hoisted_5$a = {
    class: "dimension"
  };
  var _hoisted_6$8 = {
    class: "created"
  };
  var _hoisted_7$6 = {
    class: "modified"
  };
  function render$v(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("tr", {
      class: normalizeClass(["media-browser-item", {
        selected: $options.selected
      }]),
      onDblclick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.onDblClick();
      }, ["stop", "prevent"])),
      onClick: _cache[1] || (_cache[1] = function () {
        return $options.onClick && $options.onClick.apply($options, arguments);
      })
    }, [createBaseVNode("td", {
      class: "type",
      "data-type": $props.item.extension
    }, null, 8 /* PROPS */, _hoisted_1$v), createBaseVNode("th", _hoisted_2$t, toDisplayString($props.item.name), 1 /* TEXT */), createBaseVNode("td", _hoisted_3$j, [createTextVNode(toDisplayString($options.size), 1 /* TEXT */), $options.size !== '' ? (openBlock(), createElementBlock("span", _hoisted_4$b, "KB")) : createCommentVNode("v-if", true)]), createBaseVNode("td", _hoisted_5$a, toDisplayString($options.dimension), 1 /* TEXT */), createBaseVNode("td", _hoisted_6$8, toDisplayString($props.item.create_date_formatted), 1 /* TEXT */), createBaseVNode("td", _hoisted_7$6, toDisplayString($props.item.modified_date_formatted), 1 /* TEXT */)], 34 /* CLASS, HYDRATE_EVENTS */);
  }

  script$v.render = render$v;
  script$v.__file = "administrator/components/com_media/resources/scripts/components/browser/table/row.vue";
  var script$u = {
    name: 'MediaBrowserTable',
    components: {
      MediaBrowserItemRow: script$v
    },
    props: {
      localItems: {
        type: Object,
        default: function _default() {}
      },
      currentDirectory: {
        type: String,
        default: ''
      }
    },
    methods: {
      changeOrder: function changeOrder(name) {
        this.$store.commit(UPDATE_SORT_BY, name);
        this.$store.commit(UPDATE_SORT_DIRECTION, this.$store.state.sortDirection === 'asc' ? 'desc' : 'asc');
      }
    }
  };
  var _hoisted_1$u = {
    class: "table media-browser-table"
  };
  var _hoisted_2$s = {
    class: "visually-hidden"
  };
  var _hoisted_3$i = {
    class: "media-browser-table-head"
  };
  var _hoisted_4$a = /*#__PURE__*/createBaseVNode("th", {
    class: "type",
    scope: "col"
  }, null, -1 /* HOISTED */);
  var _hoisted_5$9 = {
    class: "name",
    scope: "col"
  };
  var _hoisted_6$7 = {
    class: "size",
    scope: "col"
  };
  var _hoisted_7$5 = {
    class: "dimension",
    scope: "col"
  };
  var _hoisted_8$5 = {
    class: "created",
    scope: "col"
  };
  var _hoisted_9$5 = {
    class: "modified",
    scope: "col"
  };
  function render$u(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserItemRow = resolveComponent("MediaBrowserItemRow");
    return openBlock(), createElementBlock("table", _hoisted_1$u, [createBaseVNode("caption", _hoisted_2$s, toDisplayString(_ctx.sprintf('COM_MEDIA_BROWSER_TABLE_CAPTION', $props.currentDirectory)), 1 /* TEXT */), createBaseVNode("thead", _hoisted_3$i, [createBaseVNode("tr", null, [_hoisted_4$a, createBaseVNode("th", _hoisted_5$9, [createBaseVNode("button", {
      class: "btn btn-link",
      onClick: _cache[0] || (_cache[0] = function ($event) {
        return $options.changeOrder('name');
      })
    }, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_NAME')) + " ", 1 /* TEXT */), createBaseVNode("span", {
      class: normalizeClass(["ms-1", {
        'icon-sort': _ctx.$store.state.sortBy !== 'name',
        'icon-caret-up': _ctx.$store.state.sortBy === 'name' && _ctx.$store.state.sortDirection === 'asc',
        'icon-caret-down': _ctx.$store.state.sortBy === 'name' && _ctx.$store.state.sortDirection === 'desc'
      }]),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)])]), createBaseVNode("th", _hoisted_6$7, [createBaseVNode("button", {
      class: "btn btn-link",
      onClick: _cache[1] || (_cache[1] = function ($event) {
        return $options.changeOrder('size');
      })
    }, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_SIZE')) + " ", 1 /* TEXT */), createBaseVNode("span", {
      class: normalizeClass(["ms-1", {
        'icon-sort': _ctx.$store.state.sortBy !== 'size',
        'icon-caret-up': _ctx.$store.state.sortBy === 'size' && _ctx.$store.state.sortDirection === 'asc',
        'icon-caret-down': _ctx.$store.state.sortBy === 'size' && _ctx.$store.state.sortDirection === 'desc'
      }]),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)])]), createBaseVNode("th", _hoisted_7$5, [createBaseVNode("button", {
      class: "btn btn-link",
      onClick: _cache[2] || (_cache[2] = function ($event) {
        return $options.changeOrder('dimension');
      })
    }, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DIMENSION')) + " ", 1 /* TEXT */), createBaseVNode("span", {
      class: normalizeClass(["ms-1", {
        'icon-sort': _ctx.$store.state.sortBy !== 'dimension',
        'icon-caret-up': _ctx.$store.state.sortBy === 'dimension' && _ctx.$store.state.sortDirection === 'asc',
        'icon-caret-down': _ctx.$store.state.sortBy === 'dimension' && _ctx.$store.state.sortDirection === 'desc'
      }]),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)])]), createBaseVNode("th", _hoisted_8$5, [createBaseVNode("button", {
      class: "btn btn-link",
      onClick: _cache[3] || (_cache[3] = function ($event) {
        return $options.changeOrder('date_created');
      })
    }, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_CREATED')) + " ", 1 /* TEXT */), createBaseVNode("span", {
      class: normalizeClass(["ms-1", {
        'icon-sort': _ctx.$store.state.sortBy !== 'date_created',
        'icon-caret-up': _ctx.$store.state.sortBy === 'date_created' && _ctx.$store.state.sortDirection === 'asc',
        'icon-caret-down': _ctx.$store.state.sortBy === 'date_created' && _ctx.$store.state.sortDirection === 'desc'
      }]),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)])]), createBaseVNode("th", _hoisted_9$5, [createBaseVNode("button", {
      class: "btn btn-link",
      onClick: _cache[4] || (_cache[4] = function ($event) {
        return $options.changeOrder('date_modified');
      })
    }, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_MODIFIED')) + " ", 1 /* TEXT */), createBaseVNode("span", {
      class: normalizeClass(["ms-1", {
        'icon-sort': _ctx.$store.state.sortBy !== 'date_modified',
        'icon-caret-up': _ctx.$store.state.sortBy === 'date_modified' && _ctx.$store.state.sortDirection === 'asc',
        'icon-caret-down': _ctx.$store.state.sortBy === 'date_modified' && _ctx.$store.state.sortDirection === 'desc'
      }]),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)])])])]), createBaseVNode("tbody", null, [(openBlock(true), createElementBlock(Fragment, null, renderList($props.localItems, function (item) {
      return openBlock(), createBlock(_component_MediaBrowserItemRow, {
        key: item.path,
        item: item
      }, null, 8 /* PROPS */, ["item"]);
    }), 128 /* KEYED_FRAGMENT */))])]);
  }

  script$u.render = render$u;
  script$u.__file = "administrator/components/com_media/resources/scripts/components/browser/table/table.vue";
  var dirname = function dirname(path) {
    if (typeof path !== 'string') {
      throw new TypeError('Path must be a string. Received ' + JSON.stringify(path));
    }
    if (path.length === 0) return '.';
    var code = path.charCodeAt(0);
    var hasRoot = code === 47;
    var end = -1;
    var matchedSlash = true;
    for (var i = path.length - 1; i >= 1; --i) {
      code = path.charCodeAt(i);
      if (code === 47) {
        if (!matchedSlash) {
          end = i;
          break;
        }
      } else {
        // We saw the first non-path separator
        matchedSlash = false;
      }
    }
    if (end === -1) return hasRoot ? '/' : '.';
    if (hasRoot && end === 1) return '//';
    return path.slice(0, end);
  };

  /**
   * Normalize a single item
   * @param item
   * @returns {*}
   * @private
   */
  function normalizeItem(item) {
    if (item.type === 'dir') {
      item.directories = [];
      item.files = [];
    }
    item.directory = dirname(item.path);
    if (item.directory.indexOf(':', item.directory.length - 1) !== -1) {
      item.directory += '/';
    }
    return item;
  }

  /**
   * Normalize array data
   * @param data
   * @returns {{directories, files}}
   * @private
   */
  function normalizeArray(data) {
    var directories = data.filter(function (item) {
      return item.type === 'dir';
    }).map(function (directory) {
      return normalizeItem(directory);
    });
    var files = data.filter(function (item) {
      return item.type === 'file';
    }).map(function (file) {
      return normalizeItem(file);
    });
    return {
      directories: directories,
      files: files
    };
  }

  /**
   * Handle errors
   * @param error
   * @private
   *
   * @TODO DN improve error handling
   */
  function handleError(error) {
    var response = JSON.parse(error.response);
    if (response.message) {
      notifications.error(response.message);
    } else {
      switch (error.status) {
        case 409:
          // Handled in consumer
          break;
        case 404:
          notifications.error('COM_MEDIA_ERROR_NOT_FOUND');
          break;
        case 401:
          notifications.error('COM_MEDIA_ERROR_NOT_AUTHENTICATED');
          break;
        case 403:
          notifications.error('COM_MEDIA_ERROR_NOT_AUTHORIZED');
          break;
        case 500:
          notifications.error('COM_MEDIA_SERVER_ERROR');
          break;
        default:
          notifications.error('COM_MEDIA_ERROR');
      }
    }
    throw error;
  }

  /**
   * Api class for communication with the server
   */
  var Api = /*#__PURE__*/function () {
    /**
       * Store constructor
       */
    function Api() {
      var options = Joomla.getOptions('com_media', {});
      if (options.apiBaseUrl === undefined) {
        throw new TypeError('Media api baseUrl is not defined');
      }
      if (options.csrfToken === undefined) {
        throw new TypeError('Media api csrf token is not defined');
      }
      this.baseUrl = options.apiBaseUrl;
      this.csrfToken = Joomla.getOptions('csrf.token');
      this.imagesExtensions = options.imagesExtensions;
      this.audioExtensions = options.audioExtensions;
      this.videoExtensions = options.videoExtensions;
      this.documentExtensions = options.documentExtensions;
      this.mediaVersion = new Date().getTime().toString();
      this.canCreate = options.canCreate || false;
      this.canEdit = options.canEdit || false;
      this.canDelete = options.canDelete || false;
    }

    /**
       * Get the contents of a directory from the server
       * @param {string}   dir  The directory path
       * @param {boolean}  full whether or not the persistent url should be returned
       * @param {boolean}  content whether or not the content should be returned
       * @returns {Promise}
       */
    var _proto3 = Api.prototype;
    _proto3.getContents = function getContents(dir, full, content) {
      var _this4 = this;
      if (full === void 0) {
        full = false;
      }
      if (content === void 0) {
        content = false;
      }
      // Wrap the ajax call into a real promise
      return new Promise(function (resolve, reject) {
        var url = new URL(_this4.baseUrl + "&task=api.files&path=" + encodeURIComponent(dir));
        if (full) {
          url.searchParams.append('url', full);
        }
        if (content) {
          url.searchParams.append('content', content);
        }
        Joomla.request({
          url: url.toString(),
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess(response) {
            resolve(normalizeArray(JSON.parse(response).data));
          },
          onError: function onError(xhr) {
            reject(xhr);
          }
        });
      }).catch(handleError);
    }

    /**
       * Create a directory
       * @param name
       * @param parent
       * @returns {Promise.<T>}
       */;
    _proto3.createDirectory = function createDirectory(name, parent) {
      var _this5 = this;
      // Wrap the ajax call into a real promise
      return new Promise(function (resolve, reject) {
        var _data;
        var url = new URL(_this5.baseUrl + "&task=api.files&path=" + encodeURIComponent(parent));
        var data = (_data = {}, _data[_this5.csrfToken] = '1', _data.name = name, _data);
        Joomla.request({
          url: url.toString(),
          method: 'POST',
          data: JSON.stringify(data),
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess(response) {
            notifications.success('COM_MEDIA_CREATE_NEW_FOLDER_SUCCESS');
            resolve(normalizeItem(JSON.parse(response).data));
          },
          onError: function onError(xhr) {
            notifications.error('COM_MEDIA_CREATE_NEW_FOLDER_ERROR');
            reject(xhr);
          }
        });
      }).catch(handleError);
    }

    /**
       * Upload a file
       * @param name
       * @param parent
       * @param content base64 encoded string
       * @param override boolean whether or not we should override existing files
       * @return {Promise.<T>}
       */;
    _proto3.upload = function upload(name, parent, content, override) {
      var _this6 = this;
      // Wrap the ajax call into a real promise
      return new Promise(function (resolve, reject) {
        var _data2;
        var url = new URL(_this6.baseUrl + "&task=api.files&path=" + encodeURIComponent(parent));
        var data = (_data2 = {}, _data2[_this6.csrfToken] = '1', _data2.name = name, _data2.content = content, _data2);

        // Append override
        if (override === true) {
          data.override = true;
        }
        Joomla.request({
          url: url.toString(),
          method: 'POST',
          data: JSON.stringify(data),
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess(response) {
            notifications.success('COM_MEDIA_UPLOAD_SUCCESS');
            resolve(normalizeItem(JSON.parse(response).data));
          },
          onError: function onError(xhr) {
            reject(xhr);
          }
        });
      }).catch(handleError);
    }

    /**
       * Rename an item
       * @param path
       * @param newPath
       * @return {Promise.<T>}
       */;
    _proto3.rename = function rename(path, newPath) {
      var _this7 = this;
      // Wrap the ajax call into a real promise
      return new Promise(function (resolve, reject) {
        var _data3;
        var url = new URL(_this7.baseUrl + "&task=api.files&path=" + encodeURIComponent(path));
        var data = (_data3 = {}, _data3[_this7.csrfToken] = '1', _data3.newPath = newPath, _data3);
        Joomla.request({
          url: url.toString(),
          method: 'PUT',
          data: JSON.stringify(data),
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess(response) {
            notifications.success('COM_MEDIA_RENAME_SUCCESS');
            resolve(normalizeItem(JSON.parse(response).data));
          },
          onError: function onError(xhr) {
            notifications.error('COM_MEDIA_RENAME_ERROR');
            reject(xhr);
          }
        });
      }).catch(handleError);
    }

    /**
       * Delete a file
       * @param path
       * @return {Promise.<T>}
       */;
    _proto3.delete = function _delete(path) {
      var _this8 = this;
      // Wrap the ajax call into a real promise
      return new Promise(function (resolve, reject) {
        var _data4;
        var url = new URL(_this8.baseUrl + "&task=api.files&path=" + encodeURIComponent(path));
        var data = (_data4 = {}, _data4[_this8.csrfToken] = '1', _data4);
        Joomla.request({
          url: url.toString(),
          method: 'DELETE',
          data: JSON.stringify(data),
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess() {
            notifications.success('COM_MEDIA_DELETE_SUCCESS');
            resolve();
          },
          onError: function onError(xhr) {
            notifications.error('COM_MEDIA_DELETE_ERROR');
            reject(xhr);
          }
        });
      }).catch(handleError);
    };
    return Api;
  }();
  var api = new Api();
  var script$t = {
    name: 'MediaBrowserActionItemEdit',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      openRenameModal: function openRenameModal() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.closingAction();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      },
      editItem: function editItem() {
        this.mainAction();
      }
    }
  };
  var _hoisted_1$t = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action icon-pencil-alt",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$r = {
    class: "action-text"
  };
  function render$t(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-edit",
      onKeyup: [_cache[0] || (_cache[0] = withKeys(function ($event) {
        return $options.editItem();
      }, ["enter"])), _cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.editItem();
      }, ["space"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onClick: _cache[2] || (_cache[2] = withModifiers(function ($event) {
        return $options.editItem();
      }, ["stop"])),
      onFocus: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[4] || (_cache[4] = function ($event) {
        return $options.focused(false);
      })
    }, [_hoisted_1$t, createBaseVNode("span", _hoisted_2$r, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_EDIT')), 1 /* TEXT */)], 32 /* HYDRATE_EVENTS */);
  }

  script$t.render = render$t;
  script$t.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/edit.vue";
  var script$s = {
    name: 'MediaBrowserActionItemDelete',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      openConfirmDeleteModal: function openConfirmDeleteModal() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.hideActions();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      }
    }
  };
  var _hoisted_1$s = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action icon-trash",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$q = {
    class: "action-text"
  };
  function render$s(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-delete",
      onKeyup: [_cache[0] || (_cache[0] = withKeys(function ($event) {
        return $options.openConfirmDeleteModal();
      }, ["enter"])), _cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.openConfirmDeleteModal();
      }, ["space"])), _cache[4] || (_cache[4] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onFocus: _cache[2] || (_cache[2] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(false);
      }),
      onClick: _cache[5] || (_cache[5] = withModifiers(function ($event) {
        return $options.openConfirmDeleteModal();
      }, ["stop"]))
    }, [_hoisted_1$s, createBaseVNode("span", _hoisted_2$q, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_DELETE')), 1 /* TEXT */)], 32 /* HYDRATE_EVENTS */);
  }

  script$s.render = render$s;
  script$s.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/delete.vue";
  var script$r = {
    name: 'MediaBrowserActionItemDownload',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      download: function download() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.closingAction();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      }
    }
  };
  var _hoisted_1$r = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action icon-download",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$p = {
    class: "action-text"
  };
  function render$r(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-download",
      onKeyup: [_cache[0] || (_cache[0] = withKeys(function ($event) {
        return $options.download();
      }, ["enter"])), _cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.download();
      }, ["space"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onClick: _cache[2] || (_cache[2] = withModifiers(function ($event) {
        return $options.download();
      }, ["stop"])),
      onFocus: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[4] || (_cache[4] = function ($event) {
        return $options.focused(false);
      })
    }, [_hoisted_1$r, createBaseVNode("span", _hoisted_2$p, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_DOWNLOAD')), 1 /* TEXT */)], 32 /* HYDRATE_EVENTS */);
  }

  script$r.render = render$r;
  script$r.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/download.vue";
  var script$q = {
    name: 'MediaBrowserActionItemPreview',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      openPreview: function openPreview() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.closingAction();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      }
    }
  };
  var _hoisted_1$q = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action icon-search-plus",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$o = {
    class: "action-text"
  };
  function render$q(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-preview",
      onClick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.openPreview();
      }, ["stop"])),
      onKeyup: [_cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.openPreview();
      }, ["enter"])), _cache[2] || (_cache[2] = withKeys(function ($event) {
        return $options.openPreview();
      }, ["space"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onFocus: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[4] || (_cache[4] = function ($event) {
        return $options.focused(false);
      })
    }, [_hoisted_1$q, createBaseVNode("span", _hoisted_2$o, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_PREVIEW')), 1 /* TEXT */)], 32 /* HYDRATE_EVENTS */);
  }

  script$q.render = render$q;
  script$q.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/preview.vue";
  var script$p = {
    name: 'MediaBrowserActionItemRename',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      openRenameModal: function openRenameModal() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.closingAction();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      }
    }
  };
  var _hoisted_1$p = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action fa fa-i-cursor",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$n = {
    class: "action-text"
  };
  function render$p(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      ref: "actionRenameButton",
      type: "button",
      class: "action-rename",
      onClick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.openRenameModal();
      }, ["stop"])),
      onKeyup: [_cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.openRenameModal();
      }, ["enter"])), _cache[2] || (_cache[2] = withKeys(function ($event) {
        return $options.openRenameModal();
      }, ["space"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onFocus: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[4] || (_cache[4] = function ($event) {
        return $options.focused(false);
      })
    }, [_hoisted_1$p, createBaseVNode("span", _hoisted_2$n, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_RENAME')), 1 /* TEXT */)], 544 /* HYDRATE_EVENTS, NEED_PATCH */);
  }

  script$p.render = render$p;
  script$p.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/rename.vue";
  var script$o = {
    name: 'MediaBrowserActionItemShare',
    props: {
      onFocused: {
        type: Function,
        default: function _default() {}
      },
      mainAction: {
        type: Function,
        default: function _default() {}
      },
      closingAction: {
        type: Function,
        default: function _default() {}
      }
    },
    methods: {
      openShareUrlModal: function openShareUrlModal() {
        this.mainAction();
      },
      hideActions: function hideActions() {
        this.closingAction();
      },
      focused: function focused(bool) {
        this.onFocused(bool);
      }
    }
  };
  var _hoisted_1$o = /*#__PURE__*/createBaseVNode("span", {
    class: "image-browser-action icon-link",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_2$m = {
    class: "action-text"
  };
  function render$o(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-url",
      onClick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.openShareUrlModal();
      }, ["stop"])),
      onKeyup: [_cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.openShareUrlModal();
      }, ["enter"])), _cache[2] || (_cache[2] = withKeys(function ($event) {
        return $options.openShareUrlModal();
      }, ["space"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.hideActions();
      }, ["esc"]))],
      onFocus: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[4] || (_cache[4] = function ($event) {
        return $options.focused(false);
      })
    }, [_hoisted_1$o, createBaseVNode("span", _hoisted_2$m, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_SHARE')), 1 /* TEXT */)], 32 /* HYDRATE_EVENTS */);
  }

  script$o.render = render$o;
  script$o.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/share.vue";
  var script$n = {
    name: 'MediaBrowserActionItemToggle',
    props: {
      mainAction: {
        type: Function,
        default: function _default() {}
      }
    },
    emits: ['on-focused'],
    methods: {
      openActions: function openActions() {
        this.mainAction();
      },
      focused: function focused(bool) {
        this.$emit('on-focused', bool);
      }
    }
  };
  var _hoisted_1$n = ["aria-label", "title"];
  function render$n(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("button", {
      type: "button",
      class: "action-toggle",
      "aria-label": _ctx.sprintf('COM_MEDIA_MANAGE_ITEM', _ctx.$parent.$props.item.name),
      title: _ctx.sprintf('COM_MEDIA_MANAGE_ITEM', _ctx.$parent.$props.item.name),
      onKeyup: [_cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.openActions();
      }, ["enter"])), _cache[4] || (_cache[4] = withKeys(function ($event) {
        return $options.openActions();
      }, ["space"]))],
      onFocus: _cache[2] || (_cache[2] = function ($event) {
        return $options.focused(true);
      }),
      onBlur: _cache[3] || (_cache[3] = function ($event) {
        return $options.focused(false);
      })
    }, [createBaseVNode("span", {
      class: "image-browser-action icon-ellipsis-h",
      "aria-hidden": "true",
      onClick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.openActions();
      }, ["stop"]))
    })], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1$n);
  }
  script$n.render = render$n;
  script$n.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/toggle.vue";
  var script$m = {
    name: 'MediaBrowserActionItemsContainer',
    components: {
      MediaBrowserActionItemEdit: script$t,
      MediaBrowserActionItemDelete: script$s,
      MediaBrowserActionItemDownload: script$r,
      MediaBrowserActionItemPreview: script$q,
      MediaBrowserActionItemRename: script$p,
      MediaBrowserActionItemShare: script$o,
      MediaBrowserActionItemToggle: script$n
    },
    props: {
      item: {
        type: Object,
        default: function _default() {}
      },
      edit: {
        type: Function,
        default: function _default() {}
      },
      previewable: {
        type: Boolean,
        default: false
      },
      downloadable: {
        type: Boolean,
        default: false
      },
      shareable: {
        type: Boolean,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    computed: {
      canEdit: function canEdit() {
        return api.canEdit && (typeof this.item.canEdit !== 'undefined' ? this.item.canEdit : true);
      },
      canDelete: function canDelete() {
        return api.canDelete && (typeof this.item.canDelete !== 'undefined' ? this.item.canDelete : true);
      },
      canOpenEditView: function canOpenEditView() {
        return ['jpg', 'jpeg', 'png'].includes(this.item.extension.toLowerCase());
      }
    },
    watch: {
      '$store.state.showRenameModal': function $storeStateShowRenameModal(show) {
        var _this9 = this;
        if (!show && this.$refs.actionToggle && this.$store.state.selectedItems.find(function (item) {
          return item.name === _this9.item.name;
        }) !== undefined) {
          this.$refs.actionToggle.$el.focus();
        }
      }
    },
    methods: {
      /* Hide actions dropdown */hideActions: function hideActions() {
        this.showActions = false;
        this.$parent.$parent.$data.actionsActive = false;
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$store.commit(SHOW_PREVIEW_MODAL);
        this.$store.dispatch('getFullContents', this.item);
      },
      /* Download an item */download: function download() {
        this.$store.dispatch('download', this.item);
      },
      /* Opening confirm delete modal */openConfirmDeleteModal: function openConfirmDeleteModal() {
        this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
        this.$store.commit(SELECT_BROWSER_ITEM, this.item);
        this.$store.commit(SHOW_CONFIRM_DELETE_MODAL);
      },
      /* Rename an item */openRenameModal: function openRenameModal() {
        this.hideActions();
        this.$store.commit(SELECT_BROWSER_ITEM, this.item);
        this.$store.commit(SHOW_RENAME_MODAL);
      },
      /* Open modal for share url */openShareUrlModal: function openShareUrlModal() {
        this.$store.commit(SELECT_BROWSER_ITEM, this.item);
        this.$store.commit(SHOW_SHARE_MODAL);
      },
      /* Open actions dropdown */openActions: function openActions() {
        this.showActions = true;
        this.$parent.$parent.$data.actionsActive = true;
        var buttons = [].concat(this.$el.parentElement.querySelectorAll('.media-browser-actions-list button'));
        if (buttons.length) {
          buttons.forEach(function (button, i) {
            if (i === 0) {
              button.tabIndex = 0;
            } else {
              button.tabIndex = -1;
            }
          });
          buttons[0].focus();
        }
      },
      /* Open actions dropdown and focus on last element */openLastActions: function openLastActions() {
        this.showActions = true;
        this.$parent.$parent.$data.actionsActive = true;
        var buttons = [].concat(this.$el.parentElement.querySelectorAll('.media-browser-actions-list button'));
        if (buttons.length) {
          buttons.forEach(function (button, i) {
            if (i === buttons.length) {
              button.tabIndex = 0;
            } else {
              button.tabIndex = -1;
            }
          });
          this.$nextTick(function () {
            return buttons[buttons.length - 1].focus();
          });
        }
      },
      /* Focus on the next item or go to the beginning again */focusNext: function focusNext(event) {
        var active = event.target;
        var buttons = [].concat(active.parentElement.querySelectorAll('button'));
        var lastchild = buttons[buttons.length - 1];
        active.tabIndex = -1;
        if (active === lastchild) {
          buttons[0].focus();
          buttons[0].tabIndex = 0;
        } else {
          active.nextElementSibling.focus();
          active.nextElementSibling.tabIndex = 0;
        }
      },
      /* Focus on the previous item or go to the end again */focusPrev: function focusPrev(event) {
        var active = event.target;
        var buttons = [].concat(active.parentElement.querySelectorAll('button'));
        var firstchild = buttons[0];
        active.tabIndex = -1;
        if (active === firstchild) {
          buttons[buttons.length - 1].focus();
          buttons[buttons.length - 1].tabIndex = 0;
        } else {
          active.previousElementSibling.focus();
          active.previousElementSibling.tabIndex = 0;
        }
      },
      /* Focus on the first item */focusFirst: function focusFirst(event) {
        var active = event.target;
        var buttons = [].concat(active.parentElement.querySelectorAll('button'));
        buttons[0].focus();
        buttons.forEach(function (button, i) {
          if (i === 0) {
            button.tabIndex = 0;
          } else {
            button.tabIndex = -1;
          }
        });
      },
      /* Focus on the last item */focusLast: function focusLast(event) {
        var active = event.target;
        var buttons = [].concat(active.parentElement.querySelectorAll('button'));
        buttons[buttons.length - 1].focus();
        buttons.forEach(function (button, i) {
          if (i === buttons.length) {
            button.tabIndex = 0;
          } else {
            button.tabIndex = -1;
          }
        });
      },
      editItem: function editItem() {
        this.edit();
      },
      focused: function focused(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$m = ["aria-label", "title"];
  var _hoisted_2$l = ["aria-label"];
  var _hoisted_3$h = {
    "aria-hidden": "true",
    class: "media-browser-actions-item-name"
  };
  function render$m(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemToggle = resolveComponent("MediaBrowserActionItemToggle");
    var _component_MediaBrowserActionItemPreview = resolveComponent("MediaBrowserActionItemPreview");
    var _component_MediaBrowserActionItemDownload = resolveComponent("MediaBrowserActionItemDownload");
    var _component_MediaBrowserActionItemRename = resolveComponent("MediaBrowserActionItemRename");
    var _component_MediaBrowserActionItemEdit = resolveComponent("MediaBrowserActionItemEdit");
    var _component_MediaBrowserActionItemShare = resolveComponent("MediaBrowserActionItemShare");
    var _component_MediaBrowserActionItemDelete = resolveComponent("MediaBrowserActionItemDelete");
    return openBlock(), createElementBlock(Fragment, null, [createBaseVNode("span", {
      class: "media-browser-select",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM'),
      title: _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM'),
      tabindex: "0",
      onFocusin: _cache[0] || (_cache[0] = function ($event) {
        return $options.focused(true);
      }),
      onFocusout: _cache[1] || (_cache[1] = function ($event) {
        return $options.focused(false);
      })
    }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1$m), createBaseVNode("div", {
      class: normalizeClass(["media-browser-actions", {
        active: $data.showActions
      }])
    }, [createVNode(_component_MediaBrowserActionItemToggle, {
      ref: "actionToggle",
      "main-action": $options.openActions,
      onOnFocused: $options.focused,
      onKeyup: [_cache[2] || (_cache[2] = withKeys(function ($event) {
        return $options.openLastActions();
      }, ["up"])), _cache[3] || (_cache[3] = withKeys(function ($event) {
        return $options.openActions();
      }, ["down"])), _cache[4] || (_cache[4] = withKeys(function ($event) {
        return $options.openLastActions();
      }, ["end"])), _cache[5] || (_cache[5] = withKeys(function ($event) {
        return $options.openActions();
      }, ["home"]))],
      onKeydown: [_cache[6] || (_cache[6] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[7] || (_cache[7] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), _cache[8] || (_cache[8] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[9] || (_cache[9] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))]
    }, null, 8 /* PROPS */, ["main-action", "onOnFocused"]), $data.showActions ? (openBlock(), createElementBlock("div", {
      key: 0,
      ref: "actionList",
      class: "media-browser-actions-list",
      role: "toolbar",
      "aria-orientation": "vertical",
      "aria-label": _ctx.sprintf('COM_MEDIA_ACTIONS_TOOLBAR_LABEL', _ctx.$parent.$props.item.name)
    }, [createBaseVNode("span", _hoisted_3$h, [createBaseVNode("strong", null, toDisplayString(_ctx.$parent.$props.item.name), 1 /* TEXT */)]), $props.previewable ? (openBlock(), createBlock(_component_MediaBrowserActionItemPreview, {
      key: 0,
      ref: "actionPreview",
      "on-focused": $options.focused,
      "main-action": $options.openPreview,
      "closing-action": $options.hideActions,
      onKeydown: [_cache[10] || (_cache[10] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[11] || (_cache[11] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), _cache[12] || (_cache[12] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[13] || (_cache[13] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"])), withKeys($options.hideActions, ["tab"])],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"]), withKeys($options.hideActions, ["esc"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "closing-action", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true), $props.downloadable ? (openBlock(), createBlock(_component_MediaBrowserActionItemDownload, {
      key: 1,
      ref: "actionDownload",
      "on-focused": $options.focused,
      "main-action": $options.download,
      "closing-action": $options.hideActions,
      onKeydown: [_cache[14] || (_cache[14] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[15] || (_cache[15] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), withKeys($options.hideActions, ["tab"]), _cache[16] || (_cache[16] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[17] || (_cache[17] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.hideActions, ["esc"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "closing-action", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true), $options.canEdit ? (openBlock(), createBlock(_component_MediaBrowserActionItemRename, {
      key: 2,
      ref: "actionRename",
      "on-focused": $options.focused,
      "main-action": $options.openRenameModal,
      "closing-action": $options.hideActions,
      onKeydown: [_cache[18] || (_cache[18] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[19] || (_cache[19] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), withKeys($options.hideActions, ["tab"]), _cache[20] || (_cache[20] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[21] || (_cache[21] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.hideActions, ["esc"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "closing-action", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true), $options.canEdit && $options.canOpenEditView ? (openBlock(), createBlock(_component_MediaBrowserActionItemEdit, {
      key: 3,
      ref: "actionEdit",
      "on-focused": $options.focused,
      "main-action": $options.editItem,
      "closing-action": $options.hideActions,
      onKeydown: [_cache[22] || (_cache[22] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[23] || (_cache[23] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), withKeys($options.hideActions, ["tab"]), _cache[24] || (_cache[24] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[25] || (_cache[25] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.hideActions, ["esc"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "closing-action", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true), $props.shareable ? (openBlock(), createBlock(_component_MediaBrowserActionItemShare, {
      key: 4,
      ref: "actionShare",
      "on-focused": $options.focused,
      "main-action": $options.openShareUrlModal,
      "closing-action": $options.hideActions,
      onKeydown: [_cache[26] || (_cache[26] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[27] || (_cache[27] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), withKeys($options.hideActions, ["tab"]), _cache[28] || (_cache[28] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[29] || (_cache[29] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.hideActions, ["esc"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "closing-action", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true), $options.canDelete ? (openBlock(), createBlock(_component_MediaBrowserActionItemDelete, {
      key: 5,
      ref: "actionDelete",
      "on-focused": $options.focused,
      "main-action": $options.openConfirmDeleteModal,
      "hide-actions": $options.hideActions,
      onKeydown: [_cache[30] || (_cache[30] = withKeys(withModifiers(function () {}, ["prevent"]), ["up"])), _cache[31] || (_cache[31] = withKeys(withModifiers(function () {}, ["prevent"]), ["down"])), withKeys($options.hideActions, ["tab"]), _cache[32] || (_cache[32] = withKeys(withModifiers(function () {}, ["prevent"]), ["home"])), _cache[33] || (_cache[33] = withKeys(withModifiers(function () {}, ["prevent"]), ["end"]))],
      onKeyup: [withKeys($options.focusPrev, ["up"]), withKeys($options.focusNext, ["down"]), withKeys($options.hideActions, ["esc"]), withKeys($options.focusLast, ["end"]), withKeys($options.focusFirst, ["home"])]
    }, null, 8 /* PROPS */, ["on-focused", "main-action", "hide-actions", "onKeyup", "onKeydown"])) : createCommentVNode("v-if", true)], 8 /* PROPS */, _hoisted_2$l)) : createCommentVNode("v-if", true)], 2 /* CLASS */)], 64 /* STABLE_FRAGMENT */);
  }

  script$m.render = render$m;
  script$m.__file = "administrator/components/com_media/resources/scripts/components/browser/actionItems/actionItemsContainer.vue";
  var script$l = {
    name: 'MediaBrowserItemDirectory',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    mixins: [navigable],
    props: {
      item: {
        type: Object,
        default: function _default() {}
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    methods: {
      /* Handle the on preview double click event */onPreviewDblClick: function onPreviewDblClick() {
        this.navigateTo(this.item.path);
      },
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$l = /*#__PURE__*/createBaseVNode("div", {
    class: "file-background"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "folder-icon"
  }, [/*#__PURE__*/createBaseVNode("span", {
    class: "icon-folder"
  })])], -1 /* HOISTED */);
  var _hoisted_2$k = [_hoisted_1$l];
  var _hoisted_3$g = {
    class: "media-browser-item-info"
  };
  function render$l(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-item-directory",
      onMouseleave: _cache[2] || (_cache[2] = function ($event) {
        return $options.hideActions();
      })
    }, [createBaseVNode("div", {
      class: "media-browser-item-preview",
      tabindex: "0",
      onDblclick: _cache[0] || (_cache[0] = withModifiers(function ($event) {
        return $options.onPreviewDblClick();
      }, ["stop", "prevent"])),
      onKeyup: _cache[1] || (_cache[1] = withKeys(function ($event) {
        return $options.onPreviewDblClick();
      }, ["enter"]))
    }, _hoisted_2$k, 32 /* HYDRATE_EVENTS */), createBaseVNode("div", _hoisted_3$g, toDisplayString($props.item.name), 1 /* TEXT */), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$l.render = render$l;
  script$l.__file = "administrator/components/com_media/resources/scripts/components/browser/items/directory.vue";
  var script$k = {
    name: 'MediaBrowserItemFile',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    props: {
      item: {
        type: Object,
        default: function _default() {}
      },
      focused: {
        type: Boolean,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    methods: {
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$refs.container.openPreview();
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$k = /*#__PURE__*/createBaseVNode("div", {
    class: "media-browser-item-preview"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-background"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-icon"
  }, [/*#__PURE__*/createBaseVNode("span", {
    class: "icon-file-alt"
  })])])], -1 /* HOISTED */);
  var _hoisted_2$j = {
    class: "media-browser-item-info"
  };
  var _hoisted_3$f = ["aria-label", "title"];
  function render$k(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-item-file",
      onMouseleave: _cache[0] || (_cache[0] = function ($event) {
        return $options.hideActions();
      })
    }, [_hoisted_1$k, createBaseVNode("div", _hoisted_2$j, toDisplayString($props.item.name) + " " + toDisplayString($props.item.filetype), 1 /* TEXT */), createBaseVNode("span", {
      class: "media-browser-select",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM'),
      title: _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM')
    }, null, 8 /* PROPS */, _hoisted_3$f), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      previewable: true,
      downloadable: true,
      shareable: true,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$k.render = render$k;
  script$k.__file = "administrator/components/com_media/resources/scripts/components/browser/items/file.vue";
  var script$j = {
    name: 'MediaBrowserItemImage',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    props: {
      item: {
        type: Object,
        required: true
      },
      focused: {
        type: Boolean,
        required: true,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: {
          type: Boolean,
          default: false
        }
      };
    },
    computed: {
      getURL: function getURL() {
        if (!this.item.thumb_path) {
          return '';
        }
        return this.item.thumb_path.split(Joomla.getOptions('system.paths').rootFull).length > 1 ? this.item.thumb_path + "?" + (this.item.modified_date ? new Date(this.item.modified_date).valueOf() : api.mediaVersion) : "" + this.item.thumb_path;
      },
      width: function width() {
        return this.item.width > 0 ? this.item.width : null;
      },
      height: function height() {
        return this.item.height > 0 ? this.item.height : null;
      },
      loading: function loading() {
        return this.item.width > 0 ? 'lazy' : null;
      },
      altTag: function altTag() {
        return this.item.name;
      }
    },
    methods: {
      /* Check if the item is an image to edit */canEdit: function canEdit() {
        return ['jpg', 'jpeg', 'png'].includes(this.item.extension.toLowerCase());
      },
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$refs.container.openPreview();
      },
      /* Edit an item */editItem: function editItem() {
        // @todo should we use relative urls here?
        var fileBaseUrl = Joomla.getOptions('com_media').editViewUrl + "&path=";
        window.location.href = fileBaseUrl + this.item.path;
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      },
      setSize: function setSize(event) {
        if (this.item.mime_type === 'image/svg+xml') {
          var image = event.target;
          // Update the item properties
          this.$store.dispatch('updateItemProperties', {
            item: this.item,
            width: image.naturalWidth ? image.naturalWidth : 300,
            height: image.naturalHeight ? image.naturalHeight : 150
          });
          // @TODO Remove the fallback size (300x150) when https://bugzilla.mozilla.org/show_bug.cgi?id=1328124 is fixed
          // Also https://github.com/whatwg/html/issues/3510
        }
      }
    }
  };

  var _hoisted_1$j = ["title"];
  var _hoisted_2$i = {
    class: "image-background"
  };
  var _hoisted_3$e = ["src", "alt", "loading", "width", "height"];
  var _hoisted_4$9 = {
    key: 1,
    class: "icon-eye-slash image-placeholder",
    "aria-hidden": "true"
  };
  var _hoisted_5$8 = ["title"];
  var _hoisted_6$6 = ["aria-label", "title"];
  function render$j(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-image",
      tabindex: "0",
      onDblclick: _cache[1] || (_cache[1] = function ($event) {
        return $options.openPreview();
      }),
      onMouseleave: _cache[2] || (_cache[2] = function ($event) {
        return $options.hideActions();
      }),
      onKeyup: _cache[3] || (_cache[3] = withKeys(function ($event) {
        return $options.openPreview();
      }, ["enter"]))
    }, [createBaseVNode("div", {
      class: "media-browser-item-preview",
      title: $props.item.name
    }, [createBaseVNode("div", _hoisted_2$i, [$options.getURL ? (openBlock(), createElementBlock("img", {
      key: 0,
      class: "image-cropped",
      src: $options.getURL,
      alt: $options.altTag,
      loading: $options.loading,
      width: $options.width,
      height: $options.height,
      onLoad: _cache[0] || (_cache[0] = function () {
        return $options.setSize && $options.setSize.apply($options, arguments);
      })
    }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_3$e)) : createCommentVNode("v-if", true), !$options.getURL ? (openBlock(), createElementBlock("span", _hoisted_4$9)) : createCommentVNode("v-if", true)])], 8 /* PROPS */, _hoisted_1$j), createBaseVNode("div", {
      class: "media-browser-item-info",
      title: $props.item.name
    }, toDisplayString($props.item.name) + " " + toDisplayString($props.item.filetype), 9 /* TEXT, PROPS */, _hoisted_5$8), createBaseVNode("span", {
      class: "media-browser-select",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM'),
      title: _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM')
    }, null, 8 /* PROPS */, _hoisted_6$6), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      edit: $options.editItem,
      previewable: true,
      downloadable: true,
      shareable: true,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "edit", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$j.render = render$j;
  script$j.__file = "administrator/components/com_media/resources/scripts/components/browser/items/image.vue";
  var script$i = {
    name: 'MediaBrowserItemVideo',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    props: {
      item: {
        type: Object,
        default: function _default() {}
      },
      focused: {
        type: Boolean,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    methods: {
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$refs.container.openPreview();
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$i = /*#__PURE__*/createBaseVNode("div", {
    class: "media-browser-item-preview"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-background"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-icon"
  }, [/*#__PURE__*/createBaseVNode("span", {
    class: "fas fa-file-video"
  })])])], -1 /* HOISTED */);
  var _hoisted_2$h = {
    class: "media-browser-item-info"
  };
  function render$i(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-image",
      onDblclick: _cache[0] || (_cache[0] = function ($event) {
        return $options.openPreview();
      }),
      onMouseleave: _cache[1] || (_cache[1] = function ($event) {
        return $options.hideActions();
      })
    }, [_hoisted_1$i, createBaseVNode("div", _hoisted_2$h, toDisplayString($props.item.name) + " " + toDisplayString($props.item.filetype), 1 /* TEXT */), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      previewable: true,
      downloadable: true,
      shareable: true,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$i.render = render$i;
  script$i.__file = "administrator/components/com_media/resources/scripts/components/browser/items/video.vue";
  var script$h = {
    name: 'MediaBrowserItemAudio',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    props: {
      item: {
        type: Object,
        default: function _default() {}
      },
      focused: {
        type: Boolean,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    methods: {
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$refs.container.openPreview();
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$h = /*#__PURE__*/createBaseVNode("div", {
    class: "media-browser-item-preview"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-background"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-icon"
  }, [/*#__PURE__*/createBaseVNode("span", {
    class: "fas fa-file-audio"
  })])])], -1 /* HOISTED */);
  var _hoisted_2$g = {
    class: "media-browser-item-info"
  };
  function render$h(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-audio",
      tabindex: "0",
      onDblclick: _cache[0] || (_cache[0] = function ($event) {
        return $options.openPreview();
      }),
      onMouseleave: _cache[1] || (_cache[1] = function ($event) {
        return $options.hideActions();
      }),
      onKeyup: _cache[2] || (_cache[2] = withKeys(function ($event) {
        return $options.openPreview();
      }, ["enter"]))
    }, [_hoisted_1$h, createBaseVNode("div", _hoisted_2$g, toDisplayString($props.item.name) + " " + toDisplayString($props.item.filetype), 1 /* TEXT */), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      previewable: true,
      downloadable: true,
      shareable: true,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$h.render = render$h;
  script$h.__file = "administrator/components/com_media/resources/scripts/components/browser/items/audio.vue";
  var script$g = {
    name: 'MediaBrowserItemDocument',
    components: {
      MediaBrowserActionItemsContainer: script$m
    },
    props: {
      item: {
        type: Object,
        default: function _default() {}
      },
      focused: {
        type: Boolean,
        default: false
      }
    },
    emits: ['toggle-settings'],
    data: function data() {
      return {
        showActions: false
      };
    },
    methods: {
      /* Hide actions dropdown */hideActions: function hideActions() {
        if (this.$refs.container) {
          this.$refs.container.hideActions();
        }
      },
      /* Preview an item */openPreview: function openPreview() {
        this.$refs.container.openPreview();
      },
      toggleSettings: function toggleSettings(bool) {
        this.$emit('toggle-settings', bool);
      }
    }
  };
  var _hoisted_1$g = /*#__PURE__*/createBaseVNode("div", {
    class: "media-browser-item-preview"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-background"
  }, [/*#__PURE__*/createBaseVNode("div", {
    class: "file-icon"
  }, [/*#__PURE__*/createBaseVNode("span", {
    class: "fas fa-file-pdf"
  })])])], -1 /* HOISTED */);
  var _hoisted_2$f = {
    class: "media-browser-item-info"
  };
  var _hoisted_3$d = ["aria-label", "title"];
  function render$g(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserActionItemsContainer = resolveComponent("MediaBrowserActionItemsContainer");
    return openBlock(), createElementBlock("div", {
      class: "media-browser-doc",
      onDblclick: _cache[0] || (_cache[0] = function ($event) {
        return $options.openPreview();
      }),
      onMouseleave: _cache[1] || (_cache[1] = function ($event) {
        return $options.hideActions();
      })
    }, [_hoisted_1$g, createBaseVNode("div", _hoisted_2$f, toDisplayString($props.item.name) + " " + toDisplayString($props.item.filetype), 1 /* TEXT */), createBaseVNode("span", {
      class: "media-browser-select",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM'),
      title: _ctx.translate('COM_MEDIA_TOGGLE_SELECT_ITEM')
    }, null, 8 /* PROPS */, _hoisted_3$d), createVNode(_component_MediaBrowserActionItemsContainer, {
      ref: "container",
      item: $props.item,
      previewable: true,
      downloadable: true,
      shareable: true,
      onToggleSettings: $options.toggleSettings
    }, null, 8 /* PROPS */, ["item", "onToggleSettings"])], 32 /* HYDRATE_EVENTS */);
  }

  script$g.render = render$g;
  script$g.__file = "administrator/components/com_media/resources/scripts/components/browser/items/document.vue";
  var MediaBrowserItem = {
    props: {
      item: {
        type: Object,
        default: function _default() {}
      }
    },
    data: function data() {
      return {
        hoverActive: false,
        actionsActive: false
      };
    },
    methods: {
      /**
       * Return the correct item type component
       */
      itemType: function itemType() {
        // Render directory items
        if (this.item.type === 'dir') return script$l;

        // Render image items
        if (this.item.extension && api.imagesExtensions.includes(this.item.extension.toLowerCase())) {
          return script$j;
        }

        // Render video items
        if (this.item.extension && api.videoExtensions.includes(this.item.extension.toLowerCase())) {
          return script$i;
        }

        // Render audio items
        if (this.item.extension && api.audioExtensions.includes(this.item.extension.toLowerCase())) {
          return script$h;
        }

        // Render document items
        if (this.item.extension && api.documentExtensions.includes(this.item.extension.toLowerCase())) {
          return script$g;
        }

        // Default to file type
        return script$k;
      },
      /**
       * Get the styles for the media browser item
       * @returns {{}}
       */
      styles: function styles() {
        return {
          width: "calc(" + this.$store.state.gridSize + "% - 20px)"
        };
      },
      /**
       * Whether or not the item is currently selected
       * @returns {boolean}
       */
      isSelected: function isSelected() {
        var _this10 = this;
        return this.$store.state.selectedItems.some(function (selected) {
          return selected.path === _this10.item.path;
        });
      },
      /**
       * Whether or not the item is currently active (on hover or via tab)
       * @returns {boolean}
       */
      isHoverActive: function isHoverActive() {
        return this.hoverActive;
      },
      /**
       * Whether or not the item is currently active (on hover or via tab)
       * @returns {boolean}
       */
      hasActions: function hasActions() {
        return this.actionsActive;
      },
      /**
       * Turns on the hover class
       */
      mouseover: function mouseover() {
        this.hoverActive = true;
      },
      /**
       * Turns off the hover class
       */
      mouseleave: function mouseleave() {
        this.hoverActive = false;
      },
      /**
       * Handle the click event
       * @param event
       */
      handleClick: function handleClick(event) {
        if (this.item.path && this.item.type === 'file') {
          window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
            bubbles: true,
            cancelable: false,
            detail: {
              path: this.item.path,
              thumb: this.item.thumb,
              fileType: this.item.mime_type ? this.item.mime_type : false,
              extension: this.item.extension ? this.item.extension : false,
              width: this.item.width ? this.item.width : 0,
              height: this.item.height ? this.item.height : 0
            }
          }));
        }
        if (this.item.type === 'dir') {
          window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
            bubbles: true,
            cancelable: false,
            detail: {}
          }));
        }

        // Handle clicks when the item was not selected
        if (!this.isSelected()) {
          // Unselect all other selected items,
          // if the shift key was not pressed during the click event
          if (!(event.shiftKey || event.keyCode === 13)) {
            this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
          }
          this.$store.commit(SELECT_BROWSER_ITEM, this.item);
          return;
        }
        this.$store.dispatch('toggleBrowserItemSelect', this.item);
        window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
          bubbles: true,
          cancelable: false,
          detail: {}
        }));

        // If more than one item was selected and the user clicks again on the selected item,
        // he most probably wants to unselect all other items.
        if (this.$store.state.selectedItems.length > 1) {
          this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
          this.$store.commit(SELECT_BROWSER_ITEM, this.item);
        }
      },
      /**
       * Handle the when an element is focused in the child to display the layover for a11y
       * @param active
       */
      toggleSettings: function toggleSettings(active) {
        this["mouse" + (active ? 'over' : 'leave')]();
      }
    },
    render: function render() {
      return h('div', {
        class: {
          'media-browser-item': true,
          selected: this.isSelected(),
          active: this.isHoverActive(),
          actions: this.hasActions()
        },
        onClick: this.handleClick,
        onMouseover: this.mouseover,
        onMouseleave: this.mouseleave
      }, [h(this.itemType(), {
        item: this.item,
        onToggleSettings: this.toggleSettings,
        focused: false
      })]);
    }
  };
  var script$f = {
    name: 'MediaInfobar',
    computed: {
      /* Get the item to show in the infobar */item: function item() {
        // Check if there are selected items
        var selectedItems = this.$store.state.selectedItems;

        // If there is only one selected item, show that one.
        if (selectedItems.length === 1) {
          return selectedItems[0];
        }

        // If there are more selected items, use the last one
        if (selectedItems.length > 1) {
          return selectedItems.slice(-1)[0];
        }

        // Use the currently selected directory as a fallback
        return this.$store.getters.getSelectedDirectory;
      },
      /* Show/Hide the InfoBar */showInfoBar: function showInfoBar() {
        return this.$store.state.showInfoBar;
      }
    },
    methods: {
      hideInfoBar: function hideInfoBar() {
        this.$store.commit(HIDE_INFOBAR);
      }
    }
  };
  var _hoisted_1$f = {
    key: 0,
    class: "media-infobar"
  };
  var _hoisted_2$e = {
    key: 0,
    class: "text-center"
  };
  var _hoisted_3$c = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-file placeholder-icon"
  }, null, -1 /* HOISTED */);
  var _hoisted_4$8 = {
    key: 1
  };
  var _hoisted_5$7 = {
    key: 0
  };
  var _hoisted_6$5 = {
    key: 1
  };
  var _hoisted_7$4 = {
    key: 2
  };
  var _hoisted_8$4 = {
    key: 3
  };
  var _hoisted_9$4 = {
    key: 4
  };
  var _hoisted_10$1 = {
    key: 5
  };
  var _hoisted_11$1 = {
    key: 6
  };
  function render$f(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createBlock(Transition, {
      name: "infobar"
    }, {
      default: withCtx(function () {
        return [$options.showInfoBar && $options.item ? (openBlock(), createElementBlock("div", _hoisted_1$f, [createBaseVNode("span", {
          class: "infobar-close",
          onClick: _cache[0] || (_cache[0] = function ($event) {
            return $options.hideInfoBar();
          })
        }, "×"), createBaseVNode("h2", null, toDisplayString($options.item.name), 1 /* TEXT */), $options.item.path === '/' ? (openBlock(), createElementBlock("div", _hoisted_2$e, [_hoisted_3$c, createTextVNode(" Select file or folder to view its details. ")])) : (openBlock(), createElementBlock("dl", _hoisted_4$8, [createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_FOLDER')), 1 /* TEXT */), createBaseVNode("dd", null, toDisplayString($options.item.directory), 1 /* TEXT */), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_TYPE')), 1 /* TEXT */), $options.item.type === 'file' ? (openBlock(), createElementBlock("dd", _hoisted_5$7, toDisplayString(_ctx.translate('COM_MEDIA_FILE')), 1 /* TEXT */)) : $options.item.type === 'dir' ? (openBlock(), createElementBlock("dd", _hoisted_6$5, toDisplayString(_ctx.translate('COM_MEDIA_FOLDER')), 1 /* TEXT */)) : (openBlock(), createElementBlock("dd", _hoisted_7$4, " - ")), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_CREATED')), 1 /* TEXT */), createBaseVNode("dd", null, toDisplayString($options.item.create_date_formatted), 1 /* TEXT */), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_MODIFIED')), 1 /* TEXT */), createBaseVNode("dd", null, toDisplayString($options.item.modified_date_formatted), 1 /* TEXT */), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DIMENSION')), 1 /* TEXT */), $options.item.width || $options.item.height ? (openBlock(), createElementBlock("dd", _hoisted_8$4, toDisplayString($options.item.width) + "px * " + toDisplayString($options.item.height) + "px ", 1 /* TEXT */)) : (openBlock(), createElementBlock("dd", _hoisted_9$4, " - ")), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_SIZE')), 1 /* TEXT */), $options.item.size ? (openBlock(), createElementBlock("dd", _hoisted_10$1, toDisplayString(($options.item.size / 1024).toFixed(2)) + " KB ", 1 /* TEXT */)) : (openBlock(), createElementBlock("dd", _hoisted_11$1, " - ")), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_MIME_TYPE')), 1 /* TEXT */), createBaseVNode("dd", null, toDisplayString($options.item.mime_type), 1 /* TEXT */), createBaseVNode("dt", null, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_EXTENSION')), 1 /* TEXT */), createBaseVNode("dd", null, toDisplayString($options.item.extension || '-'), 1 /* TEXT */)]))])) : createCommentVNode("v-if", true)];
      }),
      _: 1 /* STABLE */
    });
  }

  script$f.render = render$f;
  script$f.__file = "administrator/components/com_media/resources/scripts/components/infobar/infobar.vue";
  function sortArray(array, by, direction) {
    return array.sort(function (a, b) {
      // By name
      if (by === 'name') {
        if (direction === 'asc') {
          return a.name.toUpperCase().localeCompare(b.name.toUpperCase(), 'en', {
            sensitivity: 'base'
          });
        }
        return b.name.toUpperCase().localeCompare(a.name.toUpperCase(), 'en', {
          sensitivity: 'base'
        });
      }
      // By size
      if (by === 'size') {
        if (direction === 'asc') {
          return parseInt(a.size, 10) - parseInt(b.size, 10);
        }
        return parseInt(b.size, 10) - parseInt(a.size, 10);
      }
      // By dimension
      if (by === 'dimension') {
        if (direction === 'asc') {
          return parseInt(a.width, 10) * parseInt(a.height, 10) - parseInt(b.width, 10) * parseInt(b.height, 10);
        }
        return parseInt(b.width, 10) * parseInt(b.height, 10) - parseInt(a.width, 10) * parseInt(a.height, 10);
      }
      // By date created
      if (by === 'date_created') {
        if (direction === 'asc') {
          return new Date(a.create_date) - new Date(b.create_date);
        }
        return new Date(b.create_date) - new Date(a.create_date);
      }
      // By date modified
      if (by === 'date_modified') {
        if (direction === 'asc') {
          return new Date(a.modified_date) - new Date(b.modified_date);
        }
        return new Date(b.modified_date) - new Date(a.modified_date);
      }
      return array;
    });
  }
  var script$e = {
    name: 'MediaBrowser',
    components: {
      MediaBrowserTable: script$u,
      MediaInfobar: script$f,
      MediaBrowserItem: MediaBrowserItem
    },
    computed: {
      /* Get the contents of the currently selected directory */localItems: function localItems() {
        var _this11 = this;
        var dirs = sortArray(this.$store.getters.getSelectedDirectoryDirectories.slice(0), this.$store.state.sortBy, this.$store.state.sortDirection);
        var files = sortArray(this.$store.getters.getSelectedDirectoryFiles.slice(0), this.$store.state.sortBy, this.$store.state.sortDirection);
        return [].concat(dirs.filter(function (dir) {
          return dir.name.toLowerCase().includes(_this11.$store.state.search.toLowerCase());
        }), files.filter(function (file) {
          return file.name.toLowerCase().includes(_this11.$store.state.search.toLowerCase());
        }));
      },
      /* The styles for the media-browser element */getHeight: function getHeight() {
        return {
          height: this.$store.state.listView === 'table' && !this.isEmpty ? 'unset' : '100%'
        };
      },
      mediaBrowserStyles: function mediaBrowserStyles() {
        return {
          width: this.$store.state.showInfoBar ? '75%' : '100%',
          height: this.$store.state.listView === 'table' && !this.isEmpty ? 'unset' : '100%'
        };
      },
      isEmptySearch: function isEmptySearch() {
        return this.$store.state.search !== '' && this.localItems.length === 0;
      },
      isEmpty: function isEmpty() {
        return ![].concat(this.$store.getters.getSelectedDirectoryDirectories, this.$store.getters.getSelectedDirectoryFiles).length && !this.$store.state.isLoading;
      },
      /* The styles for the media-browser element */listView: function listView() {
        return this.$store.state.listView;
      },
      mediaBrowserGridItemsClass: function mediaBrowserGridItemsClass() {
        var _ref24;
        return _ref24 = {}, _ref24["media-browser-items-" + this.$store.state.gridSize] = true, _ref24;
      },
      isModal: function isModal() {
        return Joomla.getOptions('com_media', {}).isModal;
      },
      currentDirectory: function currentDirectory() {
        var parts = this.$store.state.selectedDirectory.split('/').filter(function (crumb) {
          return crumb.length !== 0;
        });

        // The first part is the name of the drive, so if we have a folder name display it. Else
        // find the filename
        if (parts.length !== 1) {
          return parts[parts.length - 1];
        }
        var diskName = '';
        this.$store.state.disks.forEach(function (disk) {
          disk.drives.forEach(function (drive) {
            if (drive.root === parts[0] + "/") {
              diskName = drive.displayName;
            }
          });
        });
        return diskName;
      }
    },
    created: function created() {
      document.body.addEventListener('click', this.unselectAllBrowserItems, false);
    },
    beforeUnmount: function beforeUnmount() {
      document.body.removeEventListener('click', this.unselectAllBrowserItems, false);
    },
    methods: {
      /* Unselect all browser items */unselectAllBrowserItems: function unselectAllBrowserItems(event) {
        var clickedDelete = !!(event.target.id !== undefined && event.target.id === 'mediaDelete');
        var notClickedBrowserItems = this.$refs.browserItems && !this.$refs.browserItems.contains(event.target) || event.target === this.$refs.browserItems;
        var notClickedInfobar = this.$refs.infobar !== undefined && !this.$refs.infobar.$el.contains(event.target);
        var clickedOutside = notClickedBrowserItems && notClickedInfobar && !clickedDelete;
        if (clickedOutside) {
          this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
          window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
            bubbles: true,
            cancelable: false,
            detail: {
              path: '',
              thumb: false,
              fileType: false,
              extension: false
            }
          }));
        }
      },
      // Listeners for drag and drop
      // Fix for Chrome
      onDragEnter: function onDragEnter(e) {
        e.stopPropagation();
        return false;
      },
      // Notify user when file is over the drop area
      onDragOver: function onDragOver(e) {
        e.preventDefault();
        document.querySelector('.media-dragoutline').classList.add('active');
        return false;
      },
      /* Upload files */upload: function upload(file) {
        var _this12 = this;
        // Create a new file reader instance
        var reader = new FileReader();

        // Add the on load callback
        reader.onload = function (progressEvent) {
          var result = progressEvent.target.result;
          var splitIndex = result.indexOf('base64') + 7;
          var content = result.slice(splitIndex, result.length);

          // Upload the file
          _this12.$store.dispatch('uploadFile', {
            name: file.name,
            parent: _this12.$store.state.selectedDirectory,
            content: content
          });
        };
        reader.readAsDataURL(file);
      },
      // Logic for the dropped file
      onDrop: function onDrop(e) {
        var _this13 = this;
        e.preventDefault();

        // Loop through array of files and upload each file
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
          Array.from(e.dataTransfer.files).forEach(function (file) {
            document.querySelector('.media-dragoutline').classList.remove('active');
            _this13.upload(file);
          });
        }
        document.querySelector('.media-dragoutline').classList.remove('active');
      },
      // Reset the drop area border
      onDragLeave: function onDragLeave(e) {
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.media-dragoutline').classList.remove('active');
        return false;
      }
    }
  };
  var _hoisted_1$e = {
    key: 0,
    class: "pt-1"
  };
  var _hoisted_2$d = {
    class: "alert alert-info m-3"
  };
  var _hoisted_3$b = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-info-circle",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_4$7 = {
    class: "visually-hidden"
  };
  var _hoisted_5$6 = {
    key: 1,
    class: "text-center",
    style: {
      "display": "grid",
      "justify-content": "center",
      "align-content": "center",
      "margin-top": "-1rem",
      "color": "var(--gray-200)",
      "height": "100%"
    }
  };
  var _hoisted_6$4 = /*#__PURE__*/createBaseVNode("span", {
    class: "fa-8x icon-cloud-upload upload-icon",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_7$3 = {
    class: "media-dragoutline"
  };
  var _hoisted_8$3 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-cloud-upload upload-icon",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_9$3 = {
    key: 3,
    class: "media-browser-grid"
  };
  function render$e(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBrowserTable = resolveComponent("MediaBrowserTable");
    var _component_MediaBrowserItem = resolveComponent("MediaBrowserItem");
    var _component_MediaInfobar = resolveComponent("MediaInfobar");
    return openBlock(), createElementBlock("div", {
      ref: "browserItems",
      class: "media-browser",
      style: normalizeStyle($options.getHeight),
      onDragenter: _cache[0] || (_cache[0] = function () {
        return $options.onDragEnter && $options.onDragEnter.apply($options, arguments);
      }),
      onDrop: _cache[1] || (_cache[1] = function () {
        return $options.onDrop && $options.onDrop.apply($options, arguments);
      }),
      onDragover: _cache[2] || (_cache[2] = function () {
        return $options.onDragOver && $options.onDragOver.apply($options, arguments);
      }),
      onDragleave: _cache[3] || (_cache[3] = function () {
        return $options.onDragLeave && $options.onDragLeave.apply($options, arguments);
      })
    }, [$options.isEmptySearch ? (openBlock(), createElementBlock("div", _hoisted_1$e, [createBaseVNode("div", _hoisted_2$d, [_hoisted_3$b, createBaseVNode("span", _hoisted_4$7, toDisplayString(_ctx.translate('NOTICE')), 1 /* TEXT */), createTextVNode(" " + toDisplayString(_ctx.translate('JGLOBAL_NO_MATCHING_RESULTS')), 1 /* TEXT */)])])) : createCommentVNode("v-if", true), $options.isEmpty ? (openBlock(), createElementBlock("div", _hoisted_5$6, [_hoisted_6$4, createBaseVNode("p", null, toDisplayString(_ctx.translate("COM_MEDIA_DROP_FILE")), 1 /* TEXT */)])) : createCommentVNode("v-if", true), createBaseVNode("div", _hoisted_7$3, [_hoisted_8$3, createBaseVNode("p", null, toDisplayString(_ctx.translate('COM_MEDIA_DROP_FILE')), 1 /* TEXT */)]), $options.listView === 'table' && !$options.isEmpty && !$options.isEmptySearch ? (openBlock(), createBlock(_component_MediaBrowserTable, {
      key: 2,
      "local-items": $options.localItems,
      "current-directory": $options.currentDirectory,
      style: normalizeStyle($options.mediaBrowserStyles)
    }, null, 8 /* PROPS */, ["local-items", "current-directory", "style"])) : createCommentVNode("v-if", true), $options.listView === 'grid' && !$options.isEmpty ? (openBlock(), createElementBlock("div", _hoisted_9$3, [createBaseVNode("div", {
      class: normalizeClass(["media-browser-items", $options.mediaBrowserGridItemsClass]),
      style: normalizeStyle($options.mediaBrowserStyles)
    }, [(openBlock(true), createElementBlock(Fragment, null, renderList($options.localItems, function (item) {
      return openBlock(), createBlock(_component_MediaBrowserItem, {
        key: item.path,
        item: item
      }, null, 8 /* PROPS */, ["item"]);
    }), 128 /* KEYED_FRAGMENT */))], 6 /* CLASS, STYLE */)])) : createCommentVNode("v-if", true), createVNode(_component_MediaInfobar, {
      ref: "infobar"
    }, null, 512 /* NEED_PATCH */)], 36 /* STYLE, HYDRATE_EVENTS */);
  }

  script$e.render = render$e;
  script$e.__file = "administrator/components/com_media/resources/scripts/components/browser/browser.vue";
  var script$d = {
    name: 'MediaTree',
    mixins: [navigable],
    props: {
      root: {
        type: String,
        required: true
      },
      level: {
        type: Number,
        required: true
      },
      parentIndex: {
        type: Number,
        required: true
      }
    },
    emits: ['move-focus-to-parent'],
    computed: {
      /* Get the directories */directories: function directories() {
        var _this14 = this;
        return this.$store.state.directories.filter(function (directory) {
          return directory.directory === _this14.root;
        })
        // Sort alphabetically
        .sort(function (a, b) {
          return a.name.toUpperCase() < b.name.toUpperCase() ? -1 : 1;
        });
      }
    },
    methods: {
      isActive: function isActive(item) {
        return item.path === this.$store.state.selectedDirectory;
      },
      getTabindex: function getTabindex(item) {
        return this.isActive(item) ? 0 : -1;
      },
      onItemClick: function onItemClick(item) {
        this.navigateTo(item.path);
        window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
          bubbles: true,
          cancelable: false,
          detail: {}
        }));
      },
      hasChildren: function hasChildren(item) {
        return item.directories.length > 0;
      },
      isOpen: function isOpen(item) {
        return this.$store.state.selectedDirectory.includes(item.path);
      },
      iconClass: function iconClass(item) {
        return {
          fas: false,
          'icon-folder': !this.isOpen(item),
          'icon-folder-open': this.isOpen(item)
        };
      },
      setFocusToFirstChild: function setFocusToFirstChild() {
        this.$refs[this.root + "0"][0].focus();
      },
      moveFocusToNextElement: function moveFocusToNextElement(currentIndex) {
        if (currentIndex + 1 === this.directories.length) {
          return;
        }
        this.$refs[this.root + (currentIndex + 1)][0].focus();
      },
      moveFocusToPreviousElement: function moveFocusToPreviousElement(currentIndex) {
        if (currentIndex === 0) {
          return;
        }
        this.$refs[this.root + (currentIndex - 1)][0].focus();
      },
      moveFocusToChildElement: function moveFocusToChildElement(item) {
        if (!this.hasChildren(item)) {
          return;
        }
        this.$refs[item.path][0].setFocusToFirstChild();
      },
      moveFocusToParentElement: function moveFocusToParentElement() {
        this.$emit('move-focus-to-parent', this.parentIndex);
      },
      restoreFocus: function restoreFocus(parentIndex) {
        this.$refs[this.root + parentIndex][0].focus();
      }
    }
  };
  var _hoisted_1$d = {
    class: "media-tree",
    role: "group"
  };
  var _hoisted_2$c = ["aria-level", "aria-setsize", "aria-posinset", "tabindex", "onClick", "onKeyup"];
  var _hoisted_3$a = {
    class: "item-icon"
  };
  var _hoisted_4$6 = {
    class: "item-name"
  };
  function render$d(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaTree = resolveComponent("MediaTree");
    return openBlock(), createElementBlock("ul", _hoisted_1$d, [(openBlock(true), createElementBlock(Fragment, null, renderList($options.directories, function (item, index) {
      return openBlock(), createElementBlock("li", {
        key: item.path,
        class: normalizeClass(["media-tree-item", {
          active: $options.isActive(item)
        }]),
        role: "none"
      }, [createBaseVNode("a", {
        ref_for: true,
        ref: $props.root + index,
        role: "treeitem",
        "aria-level": $props.level,
        "aria-setsize": $options.directories.length,
        "aria-posinset": index,
        tabindex: $options.getTabindex(item),
        onClick: withModifiers(function ($event) {
          return $options.onItemClick(item);
        }, ["stop", "prevent"]),
        onKeyup: [withKeys(function ($event) {
          return $options.moveFocusToPreviousElement(index);
        }, ["up"]), withKeys(function ($event) {
          return $options.moveFocusToNextElement(index);
        }, ["down"]), withKeys(function ($event) {
          return $options.onItemClick(item);
        }, ["enter"]), withKeys(function ($event) {
          return $options.moveFocusToChildElement(item);
        }, ["right"]), _cache[0] || (_cache[0] = withKeys(function ($event) {
          return $options.moveFocusToParentElement();
        }, ["left"]))]
      }, [createBaseVNode("span", _hoisted_3$a, [createBaseVNode("span", {
        class: normalizeClass($options.iconClass(item))
      }, null, 2 /* CLASS */)]), createBaseVNode("span", _hoisted_4$6, toDisplayString(item.name), 1 /* TEXT */)], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_2$c), createVNode(Transition, {
        name: "slide-fade"
      }, {
        default: withCtx(function () {
          return [$options.hasChildren(item) ? withDirectives((openBlock(), createBlock(_component_MediaTree, {
            key: 0,
            ref_for: true,
            ref: item.path,
            "aria-expanded": $options.isOpen(item) ? 'true' : 'false',
            root: item.path,
            level: $props.level + 1,
            "parent-index": index,
            onMoveFocusToParent: $options.restoreFocus
          }, null, 8 /* PROPS */, ["aria-expanded", "root", "level", "parent-index", "onMoveFocusToParent"])), [[vShow, $options.isOpen(item)]]) : createCommentVNode("v-if", true)];
        }),
        _: 2 /* DYNAMIC */
      }, 1024 /* DYNAMIC_SLOTS */)], 2 /* CLASS */);
    }), 128 /* KEYED_FRAGMENT */))]);
  }

  script$d.render = render$d;
  script$d.__file = "administrator/components/com_media/resources/scripts/components/tree/tree.vue";
  var script$c = {
    name: 'MediaDrive',
    components: {
      MediaTree: script$d
    },
    mixins: [navigable],
    props: {
      drive: {
        type: Object,
        default: function _default() {}
      },
      total: {
        type: Number,
        default: 0
      },
      diskId: {
        type: String,
        default: ''
      },
      counter: {
        type: Number,
        default: 0
      }
    },
    computed: {
      /* Whether or not the item is active */isActive: function isActive() {
        return this.$store.state.selectedDirectory === this.drive.root;
      },
      getTabindex: function getTabindex() {
        return this.isActive ? 0 : -1;
      }
    },
    methods: {
      /* Handle the on drive click event */onDriveClick: function onDriveClick() {
        this.navigateTo(this.drive.root);
      },
      moveFocusToChildElement: function moveFocusToChildElement(nextRoot) {
        this.$refs[nextRoot].setFocusToFirstChild();
      },
      restoreFocus: function restoreFocus() {
        this.$refs['drive-root'].focus();
      }
    }
  };
  var _hoisted_1$c = ["aria-labelledby"];
  var _hoisted_2$b = ["aria-setsize", "tabindex"];
  var _hoisted_3$9 = {
    class: "item-name"
  };
  function render$c(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaTree = resolveComponent("MediaTree");
    return openBlock(), createElementBlock("div", {
      class: "media-drive",
      onClick: _cache[2] || (_cache[2] = withModifiers(function ($event) {
        return $options.onDriveClick();
      }, ["stop", "prevent"]))
    }, [createBaseVNode("ul", {
      class: "media-tree",
      role: "tree",
      "aria-labelledby": $props.diskId
    }, [createBaseVNode("li", {
      class: normalizeClass({
        active: $options.isActive,
        'media-tree-item': true,
        'media-drive-name': true
      }),
      role: "none"
    }, [createBaseVNode("a", {
      ref: "drive-root",
      role: "treeitem",
      "aria-level": "1",
      "aria-setsize": $props.counter,
      "aria-posinset": 1,
      tabindex: $options.getTabindex,
      onKeyup: [_cache[0] || (_cache[0] = withKeys(function ($event) {
        return $options.moveFocusToChildElement($props.drive.root);
      }, ["right"])), _cache[1] || (_cache[1] = withKeys(function () {
        return $options.onDriveClick && $options.onDriveClick.apply($options, arguments);
      }, ["enter"]))]
    }, [createBaseVNode("span", _hoisted_3$9, toDisplayString($props.drive.displayName), 1 /* TEXT */)], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_2$b), createVNode(_component_MediaTree, {
      ref: $props.drive.root,
      root: $props.drive.root,
      level: 2,
      "parent-index": 0,
      onMoveFocusToParent: $options.restoreFocus
    }, null, 8 /* PROPS */, ["root", "onMoveFocusToParent"])], 2 /* CLASS */)], 8 /* PROPS */, _hoisted_1$c)]);
  }
  script$c.render = render$c;
  script$c.__file = "administrator/components/com_media/resources/scripts/components/tree/drive.vue";
  var script$b = {
    name: 'MediaDisk',
    components: {
      MediaDrive: script$c
    },
    props: {
      disk: {
        type: Object,
        default: function _default() {}
      },
      uid: {
        type: String,
        default: ''
      }
    },
    computed: {
      diskId: function diskId() {
        return "disk-" + (this.uid + 1);
      }
    }
  };
  var _hoisted_1$b = {
    class: "media-disk"
  };
  var _hoisted_2$a = ["id"];
  function render$b(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaDrive = resolveComponent("MediaDrive");
    return openBlock(), createElementBlock("div", _hoisted_1$b, [createBaseVNode("h2", {
      id: $options.diskId,
      class: "media-disk-name"
    }, toDisplayString($props.disk.displayName), 9 /* TEXT, PROPS */, _hoisted_2$a), (openBlock(true), createElementBlock(Fragment, null, renderList($props.disk.drives, function (drive, index) {
      return openBlock(), createBlock(_component_MediaDrive, {
        key: index,
        "disk-id": $options.diskId,
        counter: index,
        drive: drive,
        total: $props.disk.drives.length
      }, null, 8 /* PROPS */, ["disk-id", "counter", "drive", "total"]);
    }), 128 /* KEYED_FRAGMENT */))]);
  }

  script$b.render = render$b;
  script$b.__file = "administrator/components/com_media/resources/scripts/components/tree/disk.vue";
  var script$a = {
    name: 'MediaBreadcrumb',
    mixins: [navigable],
    computed: {
      /* Get the crumbs from the current directory path */crumbs: function crumbs() {
        var items = [];
        var adapter = this.$store.state.selectedDirectory.split(':/');

        // Add the drive as first element
        if (adapter.length) {
          var drive = this.findDrive(adapter[0]);
          if (!drive) {
            return [];
          }
          items.push(drive);
          var path = adapter[0] + ":";
          adapter[1].split('/').filter(function (crumb) {
            return crumb.length !== 0;
          }).forEach(function (crumb, index) {
            path = path + "/" + crumb;
            items.push({
              name: crumb,
              index: index + 1,
              path: path
            });
          });
        }
        return items;
      },
      /* Whether or not the crumb is the last element in the list */isLast: function isLast(item) {
        return this.crumbs.indexOf(item) === this.crumbs.length - 1;
      }
    },
    methods: {
      /* Handle the on crumb click event */onCrumbClick: function onCrumbClick(index) {
        var destination = this.crumbs.find(function (crumb) {
          return crumb.index === index;
        });
        if (!destination) {
          return;
        }
        this.navigateTo(destination.path);
        window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
          bubbles: true,
          cancelable: false,
          detail: {}
        }));
      },
      findDrive: function findDrive(adapter) {
        var driveObject = null;
        this.$store.state.disks.forEach(function (disk) {
          disk.drives.forEach(function (drive) {
            if (drive.root.startsWith(adapter)) {
              driveObject = {
                name: drive.displayName,
                path: drive.root,
                index: 0
              };
            }
          });
        });
        return driveObject;
      }
    }
  };
  var _hoisted_1$a = ["aria-label"];
  var _hoisted_2$9 = ["aria-current", "onClick"];
  function render$a(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("nav", {
      class: "media-breadcrumb",
      "aria-label": _ctx.translate('COM_MEDIA_BREADCRUMB_LABEL')
    }, [createBaseVNode("ol", null, [(openBlock(true), createElementBlock(Fragment, null, renderList($options.crumbs, function (val, index) {
      return openBlock(), createElementBlock("li", {
        key: index,
        class: "media-breadcrumb-item"
      }, [createBaseVNode("a", {
        href: "#",
        "aria-current": index === Object.keys($options.crumbs).length - 1 ? 'page' : undefined,
        onClick: withModifiers(function ($event) {
          return $options.onCrumbClick(index);
        }, ["stop", "prevent"])
      }, toDisplayString(val.name), 9 /* TEXT, PROPS */, _hoisted_2$9)]);
    }), 128 /* KEYED_FRAGMENT */))])], 8 /* PROPS */, _hoisted_1$a);
  }
  script$a.render = render$a;
  script$a.__file = "administrator/components/com_media/resources/scripts/components/breadcrumb/breadcrumb.vue";
  var script$9 = {
    name: 'MediaToolbar',
    components: {
      MediaBreadcrumb: script$a
    },
    data: function data() {
      return {
        sortingOptions: false
      };
    },
    computed: {
      toggleListViewBtnIcon: function toggleListViewBtnIcon() {
        return this.isGridView ? 'icon-list' : 'icon-th';
      },
      isLoading: function isLoading() {
        return this.$store.state.isLoading;
      },
      atLeastOneItemSelected: function atLeastOneItemSelected() {
        return this.$store.state.selectedItems.length > 0;
      },
      isGridView: function isGridView() {
        return this.$store.state.listView === 'grid';
      },
      allItemsSelected: function allItemsSelected() {
        return this.$store.getters.getSelectedDirectoryContents.length === this.$store.state.selectedItems.length;
      },
      search: function search() {
        return this.$store.state.search;
      }
    },
    watch: {
      '$store.state.selectedItems': function $storeStateSelectedItems() {
        if (!this.allItemsSelected) {
          this.$refs.mediaToolbarSelectAll.checked = false;
        }
      }
    },
    methods: {
      toggleInfoBar: function toggleInfoBar() {
        if (this.$store.state.showInfoBar) {
          this.$store.commit(HIDE_INFOBAR);
        } else {
          this.$store.commit(SHOW_INFOBAR);
        }
      },
      decreaseGridSize: function decreaseGridSize() {
        if (!this.isGridSize('sm')) {
          this.$store.commit(DECREASE_GRID_SIZE);
        }
      },
      increaseGridSize: function increaseGridSize() {
        if (!this.isGridSize('xl')) {
          this.$store.commit(INCREASE_GRID_SIZE);
        }
      },
      changeListView: function changeListView() {
        if (this.$store.state.listView === 'grid') {
          this.$store.commit(CHANGE_LIST_VIEW, 'table');
        } else {
          this.$store.commit(CHANGE_LIST_VIEW, 'grid');
        }
      },
      toggleSelectAll: function toggleSelectAll() {
        if (this.allItemsSelected) {
          this.$store.commit(UNSELECT_ALL_BROWSER_ITEMS);
        } else {
          this.$store.commit(SELECT_BROWSER_ITEMS, this.$store.getters.getSelectedDirectoryContents);
          window.parent.document.dispatchEvent(new CustomEvent('onMediaFileSelected', {
            bubbles: true,
            cancelable: false,
            detail: {}
          }));
        }
      },
      isGridSize: function isGridSize(size) {
        return this.$store.state.gridSize === size;
      },
      changeSearch: function changeSearch(query) {
        this.$store.commit(SET_SEARCH_QUERY, query.target.value);
      },
      showSortOptions: function showSortOptions() {
        this.sortingOptions = !this.sortingOptions;
      },
      changeOrderDirection: function changeOrderDirection() {
        this.$store.commit(UPDATE_SORT_DIRECTION, this.$refs.orderdirection.value);
      },
      changeOrderBy: function changeOrderBy() {
        this.$store.commit(UPDATE_SORT_BY, this.$refs.orderby.value);
      }
    }
  };
  var _hoisted_1$9 = ["aria-label"];
  var _hoisted_2$8 = {
    key: 0,
    class: "media-loader"
  };
  var _hoisted_3$8 = {
    class: "media-view-icons"
  };
  var _hoisted_4$5 = ["aria-label"];
  var _hoisted_5$5 = {
    class: "media-view-search-input",
    role: "search"
  };
  var _hoisted_6$3 = {
    for: "media_search",
    class: "visually-hidden"
  };
  var _hoisted_7$2 = ["placeholder", "value"];
  var _hoisted_8$2 = {
    class: "media-view-icons"
  };
  var _hoisted_9$2 = ["aria-label"];
  var _hoisted_10 = /*#__PURE__*/createBaseVNode("span", {
    class: "fas fa-sort-amount-down-alt",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_11 = [_hoisted_10];
  var _hoisted_12 = ["aria-label"];
  var _hoisted_13 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-search-minus",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_14 = [_hoisted_13];
  var _hoisted_15 = ["aria-label"];
  var _hoisted_16 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-search-plus",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_17 = [_hoisted_16];
  var _hoisted_18 = ["aria-label"];
  var _hoisted_19 = ["aria-label"];
  var _hoisted_20 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-info",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_21 = [_hoisted_20];
  var _hoisted_22 = {
    key: 0,
    class: "row g-3 pt-2 pb-2 pe-3 justify-content-end",
    style: {
      "border-inline-start": "1px solid var(--template-bg-dark-7)",
      "margin-left": "0"
    }
  };
  var _hoisted_23 = {
    class: "col-3"
  };
  var _hoisted_24 = ["aria-label", "value"];
  var _hoisted_25 = {
    value: "name"
  };
  var _hoisted_26 = {
    value: "size"
  };
  var _hoisted_27 = {
    value: "dimension"
  };
  var _hoisted_28 = {
    value: "date_created"
  };
  var _hoisted_29 = {
    value: "date_modified"
  };
  var _hoisted_30 = {
    class: "col-3"
  };
  var _hoisted_31 = ["aria-label", "value"];
  var _hoisted_32 = {
    value: "asc"
  };
  var _hoisted_33 = {
    value: "desc"
  };
  function render$9(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaBreadcrumb = resolveComponent("MediaBreadcrumb");
    return openBlock(), createElementBlock(Fragment, null, [createBaseVNode("div", {
      class: "media-toolbar",
      role: "toolbar",
      "aria-label": _ctx.translate('COM_MEDIA_TOOLBAR_LABEL')
    }, [$options.isLoading ? (openBlock(), createElementBlock("div", _hoisted_2$8)) : createCommentVNode("v-if", true), createBaseVNode("div", _hoisted_3$8, [createBaseVNode("input", {
      ref: "mediaToolbarSelectAll",
      type: "checkbox",
      class: "media-toolbar-icon media-toolbar-select-all",
      "aria-label": _ctx.translate('COM_MEDIA_SELECT_ALL'),
      onClick: _cache[0] || (_cache[0] = withModifiers(function () {
        return $options.toggleSelectAll && $options.toggleSelectAll.apply($options, arguments);
      }, ["stop"]))
    }, null, 8 /* PROPS */, _hoisted_4$5)]), createVNode(_component_MediaBreadcrumb), createBaseVNode("div", _hoisted_5$5, [createBaseVNode("label", _hoisted_6$3, toDisplayString(_ctx.translate('COM_MEDIA_SEARCH')), 1 /* TEXT */), createBaseVNode("input", {
      id: "media_search",
      class: "form-control",
      type: "text",
      placeholder: _ctx.translate('COM_MEDIA_SEARCH'),
      value: $options.search,
      onInput: _cache[1] || (_cache[1] = function () {
        return $options.changeSearch && $options.changeSearch.apply($options, arguments);
      })
    }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_7$2)]), createBaseVNode("div", _hoisted_8$2, [$options.isGridView ? (openBlock(), createElementBlock("button", {
      key: 0,
      type: "button",
      class: normalizeClass(["media-toolbar-icon", {
        active: $data.sortingOptions
      }]),
      "aria-label": _ctx.translate('COM_MEDIA_CHANGE_ORDERING'),
      onClick: _cache[2] || (_cache[2] = function ($event) {
        return $options.showSortOptions();
      })
    }, _hoisted_11, 10 /* CLASS, PROPS */, _hoisted_9$2)) : createCommentVNode("v-if", true), $options.isGridView ? (openBlock(), createElementBlock("button", {
      key: 1,
      type: "button",
      class: normalizeClass(["media-toolbar-icon media-toolbar-decrease-grid-size", {
        disabled: $options.isGridSize('sm')
      }]),
      "aria-label": _ctx.translate('COM_MEDIA_DECREASE_GRID'),
      onClick: _cache[3] || (_cache[3] = withModifiers(function ($event) {
        return $options.decreaseGridSize();
      }, ["stop", "prevent"]))
    }, _hoisted_14, 10 /* CLASS, PROPS */, _hoisted_12)) : createCommentVNode("v-if", true), $options.isGridView ? (openBlock(), createElementBlock("button", {
      key: 2,
      type: "button",
      class: normalizeClass(["media-toolbar-icon media-toolbar-increase-grid-size", {
        disabled: $options.isGridSize('xl')
      }]),
      "aria-label": _ctx.translate('COM_MEDIA_INCREASE_GRID'),
      onClick: _cache[4] || (_cache[4] = withModifiers(function ($event) {
        return $options.increaseGridSize();
      }, ["stop", "prevent"]))
    }, _hoisted_17, 10 /* CLASS, PROPS */, _hoisted_15)) : createCommentVNode("v-if", true), createBaseVNode("button", {
      type: "button",
      class: "media-toolbar-icon media-toolbar-list-view",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_LIST_VIEW'),
      onClick: _cache[5] || (_cache[5] = withModifiers(function ($event) {
        return $options.changeListView();
      }, ["stop", "prevent"]))
    }, [createBaseVNode("span", {
      class: normalizeClass($options.toggleListViewBtnIcon),
      "aria-hidden": "true"
    }, null, 2 /* CLASS */)], 8 /* PROPS */, _hoisted_18), createBaseVNode("button", {
      type: "button",
      class: "media-toolbar-icon media-toolbar-info",
      "aria-label": _ctx.translate('COM_MEDIA_TOGGLE_INFO'),
      onClick: _cache[6] || (_cache[6] = withModifiers(function () {
        return $options.toggleInfoBar && $options.toggleInfoBar.apply($options, arguments);
      }, ["stop", "prevent"]))
    }, _hoisted_21, 8 /* PROPS */, _hoisted_19)])], 8 /* PROPS */, _hoisted_1$9), $options.isGridView && $data.sortingOptions ? (openBlock(), createElementBlock("div", _hoisted_22, [createBaseVNode("div", _hoisted_23, [createBaseVNode("select", {
      ref: "orderby",
      class: "form-select",
      "aria-label": _ctx.translate('COM_MEDIA_ORDER_BY'),
      value: _ctx.$store.state.sortBy,
      onChange: _cache[7] || (_cache[7] = function ($event) {
        return $options.changeOrderBy();
      })
    }, [createBaseVNode("option", _hoisted_25, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_NAME')), 1 /* TEXT */), createBaseVNode("option", _hoisted_26, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_SIZE')), 1 /* TEXT */), createBaseVNode("option", _hoisted_27, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DIMENSION')), 1 /* TEXT */), createBaseVNode("option", _hoisted_28, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_CREATED')), 1 /* TEXT */), createBaseVNode("option", _hoisted_29, toDisplayString(_ctx.translate('COM_MEDIA_MEDIA_DATE_MODIFIED')), 1 /* TEXT */)], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_24)]), createBaseVNode("div", _hoisted_30, [createBaseVNode("select", {
      ref: "orderdirection",
      class: "form-select",
      "aria-label": _ctx.translate('COM_MEDIA_ORDER_DIRECTION'),
      value: _ctx.$store.state.sortDirection,
      onChange: _cache[8] || (_cache[8] = function ($event) {
        return $options.changeOrderDirection();
      })
    }, [createBaseVNode("option", _hoisted_32, toDisplayString(_ctx.translate('COM_MEDIA_ORDER_ASC')), 1 /* TEXT */), createBaseVNode("option", _hoisted_33, toDisplayString(_ctx.translate('COM_MEDIA_ORDER_DESC')), 1 /* TEXT */)], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_31)])])) : createCommentVNode("v-if", true)], 64 /* STABLE_FRAGMENT */);
  }

  script$9.render = render$9;
  script$9.__file = "administrator/components/com_media/resources/scripts/components/toolbar/toolbar.vue";
  var script$8 = {
    name: 'MediaUpload',
    props: {
      accept: {
        type: String,
        default: ''
      },
      extensions: {
        type: Function,
        default: function _default() {
          return [];
        }
      },
      name: {
        type: String,
        default: 'file'
      },
      multiple: {
        type: Boolean,
        default: true
      }
    },
    created: function created() {
      var _this15 = this;
      // Listen to the toolbar upload click event
      MediaManager.Event.listen('onClickUpload', function () {
        return _this15.chooseFiles();
      });
    },
    methods: {
      /* Open the choose-file dialog */chooseFiles: function chooseFiles() {
        this.$refs.fileInput.click();
      },
      /* Upload files */upload: function upload(e) {
        var _this16 = this;
        e.preventDefault();
        var files = e.target.files;

        // Loop through array of files and upload each file
        Array.from(files).forEach(function (file) {
          // Create a new file reader instance
          var reader = new FileReader();

          // Add the on load callback
          reader.onload = function (progressEvent) {
            var result = progressEvent.target.result;
            var splitIndex = result.indexOf('base64') + 7;
            var content = result.slice(splitIndex, result.length);

            // Upload the file
            _this16.$store.dispatch('uploadFile', {
              name: file.name,
              parent: _this16.$store.state.selectedDirectory,
              content: content
            });
          };
          reader.readAsDataURL(file);
        });
      }
    }
  };
  var _hoisted_1$8 = ["name", "multiple", "accept"];
  function render$8(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("input", {
      ref: "fileInput",
      type: "file",
      class: "hidden",
      name: $props.name,
      multiple: $props.multiple,
      accept: $props.accept,
      onChange: _cache[0] || (_cache[0] = function () {
        return $options.upload && $options.upload.apply($options, arguments);
      })
    }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1$8);
  }
  script$8.render = render$8;
  script$8.__file = "administrator/components/com_media/resources/scripts/components/upload/upload.vue";

  /**
   * defines a focus group
   */
  var FOCUS_GROUP = 'data-focus-lock';
  /**
   * disables element discovery inside a group marked by key
   */
  var FOCUS_DISABLED = 'data-focus-lock-disabled';
  /**
   * allows uncontrolled focus within the marked area, effectively disabling focus lock for it's content
   */
  var FOCUS_ALLOW = 'data-no-focus-lock';
  /**
   * instructs autofocus engine to pick default autofocus inside a given node
   * can be set on the element or container
   */
  var FOCUS_AUTO = 'data-autofocus-inside';
  /**
   * instructs autofocus to ignore elements within a given node
   * can be set on the element or container
   */
  var FOCUS_NO_AUTOFOCUS = 'data-no-autofocus';

  /*
  IE11 support
   */
  var toArray = function toArray(a) {
    var ret = Array(a.length);
    for (var i = 0; i < a.length; ++i) {
      ret[i] = a[i];
    }
    return ret;
  };
  var asArray = function asArray(a) {
    return Array.isArray(a) ? a : [a];
  };
  var getFirst = function getFirst(a) {
    return Array.isArray(a) ? a[0] : a;
  };
  var isElementHidden = function isElementHidden(node) {
    // we can measure only "elements"
    // consider others as "visible"
    if (node.nodeType !== Node.ELEMENT_NODE) {
      return false;
    }
    var computedStyle = window.getComputedStyle(node, null);
    if (!computedStyle || !computedStyle.getPropertyValue) {
      return false;
    }
    return computedStyle.getPropertyValue('display') === 'none' || computedStyle.getPropertyValue('visibility') === 'hidden';
  };
  var getParentNode = function getParentNode(node) {
    // DOCUMENT_FRAGMENT_NODE can also point on ShadowRoot. In this case .host will point on the next node
    return node.parentNode && node.parentNode.nodeType === Node.DOCUMENT_FRAGMENT_NODE ?
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    node.parentNode.host : node.parentNode;
  };
  var isTopNode = function isTopNode(node) {
    // @ts-ignore
    return node === document || node && node.nodeType === Node.DOCUMENT_NODE;
  };
  var isVisibleUncached = function isVisibleUncached(node, checkParent) {
    return !node || isTopNode(node) || !isElementHidden(node) && checkParent(getParentNode(node));
  };
  var isVisibleCached = function isVisibleCached(visibilityCache, node) {
    var cached = visibilityCache.get(node);
    if (cached !== undefined) {
      return cached;
    }
    var result = isVisibleUncached(node, isVisibleCached.bind(undefined, visibilityCache));
    visibilityCache.set(node, result);
    return result;
  };
  var isAutoFocusAllowedUncached = function isAutoFocusAllowedUncached(node, checkParent) {
    return node && !isTopNode(node) ? isAutoFocusAllowed(node) ? checkParent(getParentNode(node)) : false : true;
  };
  var isAutoFocusAllowedCached = function isAutoFocusAllowedCached(cache, node) {
    var cached = cache.get(node);
    if (cached !== undefined) {
      return cached;
    }
    var result = isAutoFocusAllowedUncached(node, isAutoFocusAllowedCached.bind(undefined, cache));
    cache.set(node, result);
    return result;
  };
  var getDataset = function getDataset(node) {
    // @ts-ignore
    return node.dataset;
  };
  var isHTMLButtonElement = function isHTMLButtonElement(node) {
    return node.tagName === 'BUTTON';
  };
  var isHTMLInputElement = function isHTMLInputElement(node) {
    return node.tagName === 'INPUT';
  };
  var isRadioElement = function isRadioElement(node) {
    return isHTMLInputElement(node) && node.type === 'radio';
  };
  var notHiddenInput = function notHiddenInput(node) {
    return !((isHTMLInputElement(node) || isHTMLButtonElement(node)) && (node.type === 'hidden' || node.disabled));
  };
  var isAutoFocusAllowed = function isAutoFocusAllowed(node) {
    var attribute = node.getAttribute(FOCUS_NO_AUTOFOCUS);
    return ![true, 'true', ''].includes(attribute);
  };
  var isGuard = function isGuard(node) {
    var _a;
    return Boolean(node && ((_a = getDataset(node)) === null || _a === void 0 ? void 0 : _a.focusGuard));
  };
  var isNotAGuard = function isNotAGuard(node) {
    return !isGuard(node);
  };
  var isDefined = function isDefined(x) {
    return Boolean(x);
  };
  var tabSort = function tabSort(a, b) {
    var tabDiff = a.tabIndex - b.tabIndex;
    var indexDiff = a.index - b.index;
    if (tabDiff) {
      if (!a.tabIndex) {
        return 1;
      }
      if (!b.tabIndex) {
        return -1;
      }
    }
    return tabDiff || indexDiff;
  };
  var orderByTabIndex = function orderByTabIndex(nodes, filterNegative, keepGuards) {
    return toArray(nodes).map(function (node, index) {
      return {
        node: node,
        index: index,
        tabIndex: keepGuards && node.tabIndex === -1 ? (node.dataset || {}).focusGuard ? 0 : -1 : node.tabIndex
      };
    }).filter(function (data) {
      return !filterNegative || data.tabIndex >= 0;
    }).sort(tabSort);
  };

  /**
   * list of the object to be considered as focusable
   */
  var tabbables = ['button:enabled', 'select:enabled', 'textarea:enabled', 'input:enabled',
  // elements with explicit roles will also use explicit tabindex
  // '[role="button"]',
  'a[href]', 'area[href]', 'summary', 'iframe', 'object', 'embed', 'audio[controls]', 'video[controls]', '[tabindex]', '[contenteditable]', '[autofocus]'];
  var queryTabbables = tabbables.join(',');
  var queryGuardTabbables = "".concat(queryTabbables, ", [data-focus-guard]");
  var getFocusablesWithShadowDom = function getFocusablesWithShadowDom(parent, withGuards) {
    return toArray((parent.shadowRoot || parent).children).reduce(function (acc, child) {
      return acc.concat(child.matches(withGuards ? queryGuardTabbables : queryTabbables) ? [child] : [], getFocusablesWithShadowDom(child));
    }, []);
  };
  var getFocusablesWithIFrame = function getFocusablesWithIFrame(parent, withGuards) {
    var _a;
    // contentDocument of iframe will be null if current origin cannot access it
    if (parent instanceof HTMLIFrameElement && ((_a = parent.contentDocument) === null || _a === void 0 ? void 0 : _a.body)) {
      return getFocusables([parent.contentDocument.body], withGuards);
    }
    return [parent];
  };
  var getFocusables = function getFocusables(parents, withGuards) {
    return parents.reduce(function (acc, parent) {
      var _a;
      var focusableWithShadowDom = getFocusablesWithShadowDom(parent, withGuards);
      var focusableWithIframes = (_a = []).concat.apply(_a, focusableWithShadowDom.map(function (node) {
        return getFocusablesWithIFrame(node, withGuards);
      }));
      return acc.concat(
      // add all tabbables inside and within shadow DOMs in DOM order
      focusableWithIframes,
      // add if node is tabbable itself
      parent.parentNode ? toArray(parent.parentNode.querySelectorAll(queryTabbables)).filter(function (node) {
        return node === parent;
      }) : []);
    }, []);
  };
  /**
   * return a list of focusable nodes within an area marked as "auto-focusable"
   * @param parent
   */
  var getParentAutofocusables = function getParentAutofocusables(parent) {
    var parentFocus = parent.querySelectorAll("[".concat(FOCUS_AUTO, "]"));
    return toArray(parentFocus).map(function (node) {
      return getFocusables([node]);
    }).reduce(function (acc, nodes) {
      return acc.concat(nodes);
    }, []);
  };

  /**
   * given list of focusable elements keeps the ones user can interact with
   * @param nodes
   * @param visibilityCache
   */
  var filterFocusable = function filterFocusable(nodes, visibilityCache) {
    return toArray(nodes).filter(function (node) {
      return isVisibleCached(visibilityCache, node);
    }).filter(function (node) {
      return notHiddenInput(node);
    });
  };
  var filterAutoFocusable = function filterAutoFocusable(nodes, cache) {
    if (cache === void 0) {
      cache = new Map();
    }
    return toArray(nodes).filter(function (node) {
      return isAutoFocusAllowedCached(cache, node);
    });
  };
  /**
   * only tabbable ones
   * (but with guards which would be ignored)
   */
  var getTabbableNodes = function getTabbableNodes(topNodes, visibilityCache, withGuards) {
    return orderByTabIndex(filterFocusable(getFocusables(topNodes, withGuards), visibilityCache), true, withGuards);
  };
  /**
   * actually anything "focusable", not only tabbable
   * (without guards, as long as they are not expected to be focused)
   */
  var getAllTabbableNodes = function getAllTabbableNodes(topNodes, visibilityCache) {
    return orderByTabIndex(filterFocusable(getFocusables(topNodes), visibilityCache), false);
  };
  /**
   * return list of nodes which are expected to be auto-focused
   * @param topNode
   * @param visibilityCache
   */
  var parentAutofocusables = function parentAutofocusables(topNode, visibilityCache) {
    return filterFocusable(getParentAutofocusables(topNode), visibilityCache);
  };
  /*
   * Determines if element is contained in scope, including nested shadow DOMs
   */
  var contains = function contains(scope, element) {
    if (scope.shadowRoot) {
      return contains(scope.shadowRoot, element);
    } else {
      if (Object.getPrototypeOf(scope).contains !== undefined && Object.getPrototypeOf(scope).contains.call(scope, element)) {
        return true;
      }
      return toArray(scope.children).some(function (child) {
        var _a;
        if (child instanceof HTMLIFrameElement) {
          var iframeBody = (_a = child.contentDocument) === null || _a === void 0 ? void 0 : _a.body;
          if (iframeBody) {
            return contains(iframeBody, element);
          }
          return false;
        }
        return contains(child, element);
      });
    }
  };

  /**
   * in case of multiple nodes nested inside each other
   * keeps only top ones
   * this is O(nlogn)
   * @param nodes
   * @returns {*}
   */
  var filterNested = function filterNested(nodes) {
    var contained = new Set();
    var l = nodes.length;
    for (var i = 0; i < l; i += 1) {
      for (var j = i + 1; j < l; j += 1) {
        var position = nodes[i].compareDocumentPosition(nodes[j]);
        /* eslint-disable no-bitwise */
        if ((position & Node.DOCUMENT_POSITION_CONTAINED_BY) > 0) {
          contained.add(j);
        }
        if ((position & Node.DOCUMENT_POSITION_CONTAINS) > 0) {
          contained.add(i);
        }
        /* eslint-enable */
      }
    }

    return nodes.filter(function (_, index) {
      return !contained.has(index);
    });
  };
  /**
   * finds top most parent for a node
   * @param node
   * @returns {*}
   */
  var getTopParent = function getTopParent(node) {
    return node.parentNode ? getTopParent(node.parentNode) : node;
  };
  /**
   * returns all "focus containers" inside a given node
   * @param node
   * @returns {T}
   */
  var getAllAffectedNodes = function getAllAffectedNodes(node) {
    var nodes = asArray(node);
    return nodes.filter(Boolean).reduce(function (acc, currentNode) {
      var group = currentNode.getAttribute(FOCUS_GROUP);
      acc.push.apply(acc, group ? filterNested(toArray(getTopParent(currentNode).querySelectorAll("[".concat(FOCUS_GROUP, "=\"").concat(group, "\"]:not([").concat(FOCUS_DISABLED, "=\"disabled\"])")))) : [currentNode]);
      return acc;
    }, []);
  };
  var safeProbe = function safeProbe(cb) {
    try {
      return cb();
    } catch (e) {
      return undefined;
    }
  };

  /**
   * returns active element from document or from nested shadowdoms
   */
  var getActiveElement = function getActiveElement(inDocument) {
    if (inDocument === void 0) {
      inDocument = document;
    }
    if (!inDocument || !inDocument.activeElement) {
      return undefined;
    }
    var activeElement = inDocument.activeElement;
    return activeElement.shadowRoot ? getActiveElement(activeElement.shadowRoot) : activeElement instanceof HTMLIFrameElement && safeProbe(function () {
      return activeElement.contentWindow.document;
    }) ? getActiveElement(activeElement.contentWindow.document) : activeElement;
  };
  var focusInFrame = function focusInFrame(frame, activeElement) {
    return frame === activeElement;
  };
  var focusInsideIframe = function focusInsideIframe(topNode, activeElement) {
    return Boolean(toArray(topNode.querySelectorAll('iframe')).some(function (node) {
      return focusInFrame(node, activeElement);
    }));
  };
  /**
   * @returns {Boolean} true, if the current focus is inside given node or nodes
   */
  var focusInside = function focusInside(topNode, activeElement) {
    // const activeElement = document && getActiveElement();
    if (activeElement === void 0) {
      activeElement = getActiveElement(getFirst(topNode).ownerDocument);
    }
    if (!activeElement || activeElement.dataset && activeElement.dataset.focusGuard) {
      return false;
    }
    return getAllAffectedNodes(topNode).some(function (node) {
      return contains(node, activeElement) || focusInsideIframe(node, activeElement);
    });
  };

  /**
   * focus is hidden FROM the focus-lock
   * ie contained inside a node focus-lock shall ignore
   * @returns {boolean} focus is currently is in "allow" area
   */
  var focusIsHidden = function focusIsHidden(inDocument) {
    if (inDocument === void 0) {
      inDocument = document;
    }
    var activeElement = getActiveElement(inDocument);
    if (!activeElement) {
      return false;
    }
    // this does not support setting FOCUS_ALLOW within shadow dom
    return toArray(inDocument.querySelectorAll("[".concat(FOCUS_ALLOW, "]"))).some(function (node) {
      return contains(node, activeElement);
    });
  };
  var findSelectedRadio = function findSelectedRadio(node, nodes) {
    return nodes.filter(isRadioElement).filter(function (el) {
      return el.name === node.name;
    }).filter(function (el) {
      return el.checked;
    })[0] || node;
  };
  var correctNode = function correctNode(node, nodes) {
    if (isRadioElement(node) && node.name) {
      return findSelectedRadio(node, nodes);
    }
    return node;
  };
  /**
   * giving a set of radio inputs keeps only selected (tabbable) ones
   * @param nodes
   */
  var correctNodes = function correctNodes(nodes) {
    // IE11 has no Set(array) constructor
    var resultSet = new Set();
    nodes.forEach(function (node) {
      return resultSet.add(correctNode(node, nodes));
    });
    // using filter to support IE11
    return nodes.filter(function (node) {
      return resultSet.has(node);
    });
  };
  var pickFirstFocus = function pickFirstFocus(nodes) {
    if (nodes[0] && nodes.length > 1) {
      return correctNode(nodes[0], nodes);
    }
    return nodes[0];
  };
  var pickFocusable = function pickFocusable(nodes, index) {
    if (nodes.length > 1) {
      return nodes.indexOf(correctNode(nodes[index], nodes));
    }
    return index;
  };
  var NEW_FOCUS = 'NEW_FOCUS';
  /**
   * Main solver for the "find next focus" question
   * @param innerNodes
   * @param outerNodes
   * @param activeElement
   * @param lastNode
   * @returns {number|string|undefined|*}
   */
  var newFocus = function newFocus(innerNodes, outerNodes, activeElement, lastNode) {
    var cnt = innerNodes.length;
    var firstFocus = innerNodes[0];
    var lastFocus = innerNodes[cnt - 1];
    var isOnGuard = isGuard(activeElement);
    // focus is inside
    if (activeElement && innerNodes.indexOf(activeElement) >= 0) {
      return undefined;
    }
    var activeIndex = activeElement !== undefined ? outerNodes.indexOf(activeElement) : -1;
    var lastIndex = lastNode ? outerNodes.indexOf(lastNode) : activeIndex;
    var lastNodeInside = lastNode ? innerNodes.indexOf(lastNode) : -1;
    var indexDiff = activeIndex - lastIndex;
    var firstNodeIndex = outerNodes.indexOf(firstFocus);
    var lastNodeIndex = outerNodes.indexOf(lastFocus);
    var correctedNodes = correctNodes(outerNodes);
    var correctedIndex = activeElement !== undefined ? correctedNodes.indexOf(activeElement) : -1;
    var correctedIndexDiff = correctedIndex - (lastNode ? correctedNodes.indexOf(lastNode) : activeIndex);
    var returnFirstNode = pickFocusable(innerNodes, 0);
    var returnLastNode = pickFocusable(innerNodes, cnt - 1);
    // new focus
    if (activeIndex === -1 || lastNodeInside === -1) {
      return NEW_FOCUS;
    }
    // old focus
    if (!indexDiff && lastNodeInside >= 0) {
      return lastNodeInside;
    }
    // first element
    if (activeIndex <= firstNodeIndex && isOnGuard && Math.abs(indexDiff) > 1) {
      return returnLastNode;
    }
    // last element
    if (activeIndex >= lastNodeIndex && isOnGuard && Math.abs(indexDiff) > 1) {
      return returnFirstNode;
    }
    // jump out, but not on the guard
    if (indexDiff && Math.abs(correctedIndexDiff) > 1) {
      return lastNodeInside;
    }
    // focus above lock
    if (activeIndex <= firstNodeIndex) {
      return returnLastNode;
    }
    // focus below lock
    if (activeIndex > lastNodeIndex) {
      return returnFirstNode;
    }
    // index is inside tab order, but outside Lock
    if (indexDiff) {
      if (Math.abs(indexDiff) > 1) {
        return lastNodeInside;
      }
      return (cnt + lastNodeInside + indexDiff) % cnt;
    }
    // do nothing
    return undefined;
  };
  var findAutoFocused = function findAutoFocused(autoFocusables) {
    return function (node) {
      var _a;
      var autofocus = (_a = getDataset(node)) === null || _a === void 0 ? void 0 : _a.autofocus;
      return (
        // @ts-expect-error
        node.autofocus ||
        //
        autofocus !== undefined && autofocus !== 'false' ||
        //
        autoFocusables.indexOf(node) >= 0
      );
    };
  };
  var pickAutofocus = function pickAutofocus(nodesIndexes, orderedNodes, groups) {
    var nodes = nodesIndexes.map(function (_a) {
      var node = _a.node;
      return node;
    });
    var autoFocusable = filterAutoFocusable(nodes.filter(findAutoFocused(groups)));
    if (autoFocusable && autoFocusable.length) {
      return pickFirstFocus(autoFocusable);
    }
    return pickFirstFocus(filterAutoFocusable(orderedNodes));
  };
  var getParents = function getParents(node, parents) {
    if (parents === void 0) {
      parents = [];
    }
    parents.push(node);
    if (node.parentNode) {
      getParents(node.parentNode.host || node.parentNode, parents);
    }
    return parents;
  };
  /**
   * finds a parent for both nodeA and nodeB
   * @param nodeA
   * @param nodeB
   * @returns {boolean|*}
   */
  var getCommonParent = function getCommonParent(nodeA, nodeB) {
    var parentsA = getParents(nodeA);
    var parentsB = getParents(nodeB);
    // tslint:disable-next-line:prefer-for-of
    for (var i = 0; i < parentsA.length; i += 1) {
      var currentParent = parentsA[i];
      if (parentsB.indexOf(currentParent) >= 0) {
        return currentParent;
      }
    }
    return false;
  };
  var getTopCommonParent = function getTopCommonParent(baseActiveElement, leftEntry, rightEntries) {
    var activeElements = asArray(baseActiveElement);
    var leftEntries = asArray(leftEntry);
    var activeElement = activeElements[0];
    var topCommon = false;
    leftEntries.filter(Boolean).forEach(function (entry) {
      topCommon = getCommonParent(topCommon || entry, entry) || topCommon;
      rightEntries.filter(Boolean).forEach(function (subEntry) {
        var common = getCommonParent(activeElement, subEntry);
        if (common) {
          if (!topCommon || contains(common, topCommon)) {
            topCommon = common;
          } else {
            topCommon = getCommonParent(common, topCommon);
          }
        }
      });
    });
    // TODO: add assert here?
    return topCommon;
  };
  /**
   * return list of nodes which are expected to be autofocused inside a given top nodes
   * @param entries
   * @param visibilityCache
   */
  var allParentAutofocusables = function allParentAutofocusables(entries, visibilityCache) {
    return entries.reduce(function (acc, node) {
      return acc.concat(parentAutofocusables(node, visibilityCache));
    }, []);
  };
  var reorderNodes = function reorderNodes(srcNodes, dstNodes) {
    var remap = new Map();
    // no Set(dstNodes) for IE11 :(
    dstNodes.forEach(function (entity) {
      return remap.set(entity.node, entity);
    });
    // remap to dstNodes
    return srcNodes.map(function (node) {
      return remap.get(node);
    }).filter(isDefined);
  };
  /**
   * given top node(s) and the last active element return the element to be focused next
   * @param topNode
   * @param lastNode
   */
  var getFocusMerge = function getFocusMerge(topNode, lastNode) {
    var activeElement = getActiveElement(asArray(topNode).length > 0 ? document : getFirst(topNode).ownerDocument);
    var entries = getAllAffectedNodes(topNode).filter(isNotAGuard);
    var commonParent = getTopCommonParent(activeElement || topNode, topNode, entries);
    var visibilityCache = new Map();
    var anyFocusable = getAllTabbableNodes(entries, visibilityCache);
    var innerElements = getTabbableNodes(entries, visibilityCache).filter(function (_a) {
      var node = _a.node;
      return isNotAGuard(node);
    });
    if (!innerElements[0]) {
      innerElements = anyFocusable;
      if (!innerElements[0]) {
        return undefined;
      }
    }
    var outerNodes = getAllTabbableNodes([commonParent], visibilityCache).map(function (_a) {
      var node = _a.node;
      return node;
    });
    var orderedInnerElements = reorderNodes(outerNodes, innerElements);
    var innerNodes = orderedInnerElements.map(function (_a) {
      var node = _a.node;
      return node;
    });
    var newId = newFocus(innerNodes, outerNodes, activeElement, lastNode);
    if (newId === NEW_FOCUS) {
      var focusNode = pickAutofocus(anyFocusable, innerNodes, allParentAutofocusables(entries, visibilityCache));
      if (focusNode) {
        return {
          node: focusNode
        };
      } else {
        console.warn('focus-lock: cannot find any node to move focus into');
        return undefined;
      }
    }
    if (newId === undefined) {
      return newId;
    }
    return orderedInnerElements[newId];
  };
  var focusOn = function focusOn(target, focusOptions) {
    if ('focus' in target) {
      target.focus(focusOptions);
    }
    if ('contentWindow' in target && target.contentWindow) {
      target.contentWindow.focus();
    }
  };
  var guardCount = 0;
  var lockDisabled = false;
  /**
   * Sets focus at a given node. The last focused element will help to determine which element(first or last) should be focused.
   * HTML markers (see {@link import('./constants').FOCUS_AUTO} constants) can control autofocus
   * @param topNode
   * @param lastNode
   * @param options
   */
  var setFocus = function setFocus(topNode, lastNode, options) {
    if (options === void 0) {
      options = {};
    }
    var focusable = getFocusMerge(topNode, lastNode);
    if (lockDisabled) {
      return;
    }
    if (focusable) {
      if (guardCount > 2) {
        // tslint:disable-next-line:no-console
        console.error('FocusLock: focus-fighting detected. Only one focus management system could be active. ' + 'See https://github.com/theKashey/focus-lock/#focus-fighting');
        lockDisabled = true;
        setTimeout(function () {
          lockDisabled = false;
        }, 1);
        return;
      }
      guardCount++;
      focusOn(focusable.node, options.focusOptions);
      guardCount--;
    }
  };
  var moveFocusInside = setFocus;
  //

  function deferAction(action) {
    var setImmediate = window.setImmediate;
    if (typeof setImmediate !== 'undefined') {
      setImmediate(action);
    } else {
      setTimeout(action, 1);
    }
  }
  var lastActiveTrap = 0;
  var lastActiveFocus = null;
  var focusWasOutsideWindow = false;
  var focusOnBody = function focusOnBody() {
    return document && document.activeElement === document.body;
  };
  var isFreeFocus = function isFreeFocus() {
    return focusOnBody() || focusIsHidden();
  };
  var activateTrap = function activateTrap() {
    var result = false;
    if (lastActiveTrap) {
      var _lastActiveTrap = lastActiveTrap,
        observed = _lastActiveTrap.observed,
        onActivation = _lastActiveTrap.onActivation;
      if (focusWasOutsideWindow || !isFreeFocus() || !lastActiveFocus) {
        if (observed && !focusInside(observed)) {
          onActivation();
          result = moveFocusInside(observed, lastActiveFocus);
        }
        focusWasOutsideWindow = false;
        lastActiveFocus = document && document.activeElement;
      }
    }
    return result;
  };
  var reducePropsToState = function reducePropsToState(propsList) {
    return propsList.filter(function (_ref25) {
      var disabled = _ref25.disabled;
      return !disabled;
    }).slice(-1)[0];
  };
  var handleStateChangeOnClient = function handleStateChangeOnClient(trap) {
    if (lastActiveTrap !== trap) {
      lastActiveTrap = null;
    }
    lastActiveTrap = trap;
    if (trap) {
      activateTrap();
      deferAction(activateTrap);
    }
  };
  var instances = [];
  var emitChange = function emitChange() {
    handleStateChangeOnClient(reducePropsToState(instances));
  };
  var onTrap = function onTrap(event) {
    if (activateTrap() && event) {
      // prevent scroll jump
      event.stopPropagation();
      event.preventDefault();
    }
  };
  var onBlur = function onBlur() {
    deferAction(activateTrap);
  };
  var onWindowBlur = function onWindowBlur() {
    focusWasOutsideWindow = true;
  };
  var attachHandler = function attachHandler() {
    document.addEventListener('focusin', onTrap, true);
    document.addEventListener('focusout', onBlur);
    window.addEventListener('blur', onWindowBlur);
  };
  var detachHandler = function detachHandler() {
    document.removeEventListener('focusin', onTrap, true);
    document.removeEventListener('focusout', onBlur);
    window.removeEventListener('blur', onWindowBlur);
  };
  var script$7 = {
    name: 'Lock',
    props: {
      returnFocus: {
        type: Boolean
      },
      disabled: {
        type: Boolean
      },
      noFocusGuards: {
        type: [Boolean, String],
        default: false
      },
      group: {
        type: String
      }
    },
    setup: function setup(props) {
      var _toRefs = toRefs(props),
        returnFocus = _toRefs.returnFocus,
        disabled = _toRefs.disabled,
        noFocusGuards = _toRefs.noFocusGuards,
        group = _toRefs.group;
      var rootEl = ref(null);
      var data = ref({});
      var hidden = ref(""); //    "width: 1px;height: 0px;padding: 0;overflow: hidden;position: fixed;top: 0;left: 0;"

      var groupAttr = computed(function () {
        var _ref26;
        return _ref26 = {}, _ref26[FOCUS_GROUP] = group.value, _ref26;
      });
      var hasLeadingGuards = computed(function () {
        return noFocusGuards.value !== true;
      });
      var hasTailingGuards = computed(function () {
        return hasLeadingGuards.value && noFocusGuards.value !== 'tail';
      });
      watch(disabled, function () {
        data.value.disabled = disabled.value;
        emitChange();
      });
      var originalFocusedElement;
      onMounted(function () {
        var currentInstance = getCurrentInstance();
        if (!currentInstance) {
          return;
        }
        data.value.instance = currentInstance.proxy;
        data.value.observed = rootEl.value.querySelector("[data-lock]");
        data.value.disabled = disabled.value;
        data.value.onActivation = function () {
          originalFocusedElement = originalFocusedElement || document && document.activeElement;
        };
        if (!instances.length) {
          attachHandler();
        }
        instances.push(data.value);
        emitChange();
      });
      onUnmounted(function () {
        var currentInstance = getCurrentInstance();
        if (!currentInstance) {
          return;
        }
        instances = instances.filter(function (_ref27) {
          var instance = _ref27.instance;
          return instance !== currentInstance.proxy;
        });
        if (!instances.length) {
          detachHandler();
        }
        if (returnFocus.value && originalFocusedElement && originalFocusedElement.focus) {
          originalFocusedElement.focus();
        }
        emitChange();
      });
      return {
        groupAttr: groupAttr,
        hasLeadingGuards: hasLeadingGuards,
        hasTailingGuards: hasTailingGuards,
        hidden: hidden,
        onBlur: function onBlur() {
          return deferAction(emitChange);
        },
        rootEl: rootEl
      };
    }
  };
  var _hoisted_1$7 = {
    ref: "rootEl"
  };
  var _hoisted_2$7 = ["tabIndex"];
  var _hoisted_3$7 = ["tabIndex"];
  function render$7(_ctx, _cache, $props, $setup, $data, $options) {
    return openBlock(), createElementBlock("div", _hoisted_1$7, [$setup.hasLeadingGuards ? (openBlock(), createElementBlock("div", {
      key: 0,
      tabIndex: $props.disabled ? -1 : 0,
      style: normalizeStyle($setup.hidden),
      "aria-hidden": "true"
    }, null, 12 /* STYLE, PROPS */, _hoisted_2$7)) : createCommentVNode("v-if", true), createBaseVNode("div", mergeProps({
      onFocusout: _cache[0] || (_cache[0] = function () {
        return $setup.onBlur && $setup.onBlur.apply($setup, arguments);
      })
    }, $setup.groupAttr, {
      "data-lock": ""
    }), [renderSlot(_ctx.$slots, "default")], 16 /* FULL_PROPS */), $setup.hasTailingGuards ? (openBlock(), createElementBlock("div", {
      key: 1,
      tabIndex: $props.disabled ? -1 : 0,
      style: normalizeStyle($setup.hidden),
      "aria-hidden": "true"
    }, null, 12 /* STYLE, PROPS */, _hoisted_3$7)) : createCommentVNode("v-if", true)], 512 /* NEED_PATCH */);
  }

  script$7.render = render$7;
  script$7.__file = "node_modules/vue-focus-lock/src/Lock.vue";
  var script$6 = {
    name: 'MediaModal',
    components: {
      Lock: script$7
    },
    props: {
      /* Whether or not the close button in the header should be shown */
      showClose: {
        type: Boolean,
        default: true
      },
      /* The size of the modal */
      size: {
        type: String,
        default: ''
      },
      labelElement: {
        type: String,
        required: true
      }
    },
    emits: ['close'],
    computed: {
      /* Get the modal css class */modalClass: function modalClass() {
        return {
          'modal-sm': this.size === 'sm'
        };
      }
    },
    mounted: function mounted() {
      // Listen to keydown events on the document
      document.addEventListener('keydown', this.onKeyDown);
    },
    beforeUnmount: function beforeUnmount() {
      // Remove the keydown event listener
      document.removeEventListener('keydown', this.onKeyDown);
    },
    methods: {
      /* Close the modal instance */close: function close() {
        this.$emit('close');
      },
      /* Handle keydown events */onKeyDown: function onKeyDown(event) {
        if (event.keyCode === 27) {
          this.close();
        }
      }
    }
  };
  var _hoisted_1$6 = ["aria-labelledby"];
  var _hoisted_2$6 = {
    class: "modal-content"
  };
  var _hoisted_3$6 = {
    class: "modal-header"
  };
  var _hoisted_4$4 = {
    class: "modal-body"
  };
  var _hoisted_5$4 = {
    class: "modal-footer"
  };
  function render$6(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_Lock = resolveComponent("Lock");
    return openBlock(), createElementBlock("div", {
      class: "media-modal-backdrop",
      onClick: _cache[2] || (_cache[2] = function ($event) {
        return $options.close();
      })
    }, [createBaseVNode("div", {
      class: "modal",
      style: {
        "display": "flex"
      },
      onClick: _cache[1] || (_cache[1] = withModifiers(function () {}, ["stop"]))
    }, [createVNode(_component_Lock, null, {
      default: withCtx(function () {
        return [createBaseVNode("div", {
          class: normalizeClass(["modal-dialog", $options.modalClass]),
          role: "dialog",
          "aria-labelledby": $props.labelElement
        }, [createBaseVNode("div", _hoisted_2$6, [createBaseVNode("div", _hoisted_3$6, [renderSlot(_ctx.$slots, "header"), renderSlot(_ctx.$slots, "backdrop-close"), $props.showClose ? (openBlock(), createElementBlock("button", {
          key: 0,
          type: "button",
          class: "btn-close",
          "aria-label": "Close",
          onClick: _cache[0] || (_cache[0] = function ($event) {
            return $options.close();
          })
        })) : createCommentVNode("v-if", true)]), createBaseVNode("div", _hoisted_4$4, [renderSlot(_ctx.$slots, "body")]), createBaseVNode("div", _hoisted_5$4, [renderSlot(_ctx.$slots, "footer")])])], 10 /* CLASS, PROPS */, _hoisted_1$6)];
      }),
      _: 3 /* FORWARDED */
    })])]);
  }

  script$6.render = render$6;
  script$6.__file = "administrator/components/com_media/resources/scripts/components/modals/modal.vue";
  var script$5 = {
    name: 'MediaCreateFolderModal',
    components: {
      MediaModal: script$6
    },
    data: function data() {
      return {
        folder: ''
      };
    },
    watch: {
      '$store.state.showCreateFolderModal': function $storeStateShowCreateFolderModal(show) {
        var _this17 = this;
        this.$nextTick(function () {
          if (show && _this17.$refs.input) {
            _this17.$refs.input.focus();
          }
        });
      }
    },
    methods: {
      /* Check if the the form is valid */isValid: function isValid() {
        return this.folder;
      },
      /* Close the modal instance */close: function close() {
        this.reset();
        this.$store.commit(HIDE_CREATE_FOLDER_MODAL);
      },
      /* Save the form and create the folder */save: function save() {
        // Check if the form is valid
        if (!this.isValid()) {
          // @todo show an error message to user for insert a folder name
          // @todo mark the field as invalid
          return;
        }

        // Create the directory
        this.$store.dispatch('createDirectory', {
          name: this.folder,
          parent: this.$store.state.selectedDirectory
        });
        this.reset();
      },
      /* Reset the form */reset: function reset() {
        this.folder = '';
      }
    }
  };
  var _hoisted_1$5 = {
    id: "createFolderTitle",
    class: "modal-title"
  };
  var _hoisted_2$5 = {
    class: "p-3"
  };
  var _hoisted_3$5 = {
    class: "form-group"
  };
  var _hoisted_4$3 = {
    for: "folder"
  };
  var _hoisted_5$3 = ["disabled"];
  function render$5(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaModal = resolveComponent("MediaModal");
    return _ctx.$store.state.showCreateFolderModal ? (openBlock(), createBlock(_component_MediaModal, {
      key: 0,
      size: 'md',
      "label-element": "createFolderTitle",
      onClose: _cache[5] || (_cache[5] = function ($event) {
        return $options.close();
      })
    }, {
      header: withCtx(function () {
        return [createBaseVNode("h3", _hoisted_1$5, toDisplayString(_ctx.translate('COM_MEDIA_CREATE_NEW_FOLDER')), 1 /* TEXT */)];
      }),

      body: withCtx(function () {
        return [createBaseVNode("div", _hoisted_2$5, [createBaseVNode("form", {
          class: "form",
          novalidate: "",
          onSubmit: _cache[2] || (_cache[2] = withModifiers(function () {
            return $options.save && $options.save.apply($options, arguments);
          }, ["prevent"]))
        }, [createBaseVNode("div", _hoisted_3$5, [createBaseVNode("label", _hoisted_4$3, toDisplayString(_ctx.translate('COM_MEDIA_FOLDER_NAME')), 1 /* TEXT */), withDirectives(createBaseVNode("input", {
          id: "folder",
          ref: "input",
          "onUpdate:modelValue": _cache[0] || (_cache[0] = function ($event) {
            return $data.folder = $event;
          }),
          class: "form-control",
          type: "text",
          required: "",
          autocomplete: "off",
          onInput: _cache[1] || (_cache[1] = function ($event) {
            return $data.folder = $event.target.value;
          })
        }, null, 544 /* HYDRATE_EVENTS, NEED_PATCH */), [[vModelText, $data.folder, void 0, {
          trim: true
        }]])])], 32 /* HYDRATE_EVENTS */)])];
      }),

      footer: withCtx(function () {
        return [createBaseVNode("div", null, [createBaseVNode("button", {
          class: "btn btn-secondary",
          onClick: _cache[3] || (_cache[3] = function ($event) {
            return $options.close();
          })
        }, toDisplayString(_ctx.translate('JCANCEL')), 1 /* TEXT */), createBaseVNode("button", {
          class: "btn btn-success",
          disabled: !$options.isValid(),
          onClick: _cache[4] || (_cache[4] = function ($event) {
            return $options.save();
          })
        }, toDisplayString(_ctx.translate('JACTION_CREATE')), 9 /* TEXT, PROPS */, _hoisted_5$3)])];
      }),
      _: 1 /* STABLE */
    })) : createCommentVNode("v-if", true);
  }
  script$5.render = render$5;
  script$5.__file = "administrator/components/com_media/resources/scripts/components/modals/create-folder-modal.vue";
  var script$4 = {
    name: 'MediaPreviewModal',
    components: {
      MediaModal: script$6
    },
    computed: {
      /* Get the item to show in the modal */item: function item() {
        // Use the currently selected directory as a fallback
        return this.$store.state.selectedItem ? this.$store.state.selectedItem : this.$store.state.previewItem;
      },
      /* Get the hashed URL */getHashedURL: function getHashedURL() {
        if (this.item.adapter.startsWith('local-')) {
          return this.item.url + "?" + api.mediaVersion;
        }
        return this.item.url;
      },
      style: function style() {
        return this.item.mime_type !== 'image/svg+xml' ? null : 'width: clamp(300px, 1000px, 75vw)';
      }
    },
    methods: {
      /* Close the modal */close: function close() {
        this.$store.commit(HIDE_PREVIEW_MODAL);
      },
      isImage: function isImage() {
        return this.item.mime_type.indexOf('image/') === 0;
      },
      isVideo: function isVideo() {
        return this.item.mime_type.indexOf('video/') === 0;
      },
      isAudio: function isAudio() {
        return this.item.mime_type.indexOf('audio/') === 0;
      },
      isDoc: function isDoc() {
        return this.item.mime_type.indexOf('application/') === 0;
      }
    }
  };
  var _hoisted_1$4 = {
    id: "previewTitle",
    class: "modal-title text-light"
  };
  var _hoisted_2$4 = {
    class: "image-background"
  };
  var _hoisted_3$4 = ["src"];
  var _hoisted_4$2 = {
    key: 1,
    controls: ""
  };
  var _hoisted_5$2 = ["src", "type"];
  var _hoisted_6$2 = ["type", "data"];
  var _hoisted_7$1 = ["src", "type"];
  var _hoisted_8$1 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-times"
  }, null, -1 /* HOISTED */);
  var _hoisted_9$1 = [_hoisted_8$1];
  function render$4(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaModal = resolveComponent("MediaModal");
    return _ctx.$store.state.showPreviewModal && $options.item ? (openBlock(), createBlock(_component_MediaModal, {
      key: 0,
      size: 'md',
      class: "media-preview-modal",
      "label-element": "previewTitle",
      "show-close": false,
      onClose: _cache[1] || (_cache[1] = function ($event) {
        return $options.close();
      })
    }, {
      header: withCtx(function () {
        return [createBaseVNode("h3", _hoisted_1$4, toDisplayString($options.item.name), 1 /* TEXT */)];
      }),

      body: withCtx(function () {
        return [createBaseVNode("div", _hoisted_2$4, [$options.isAudio() ? (openBlock(), createElementBlock("audio", {
          key: 0,
          controls: "",
          src: $options.item.url
        }, null, 8 /* PROPS */, _hoisted_3$4)) : createCommentVNode("v-if", true), $options.isVideo() ? (openBlock(), createElementBlock("video", _hoisted_4$2, [createBaseVNode("source", {
          src: $options.item.url,
          type: $options.item.mime_type
        }, null, 8 /* PROPS */, _hoisted_5$2)])) : createCommentVNode("v-if", true), $options.isDoc() ? (openBlock(), createElementBlock("object", {
          key: 2,
          type: $options.item.mime_type,
          data: $options.item.url,
          width: "800",
          height: "600"
        }, null, 8 /* PROPS */, _hoisted_6$2)) : createCommentVNode("v-if", true), $options.isImage() ? (openBlock(), createElementBlock("img", {
          key: 3,
          src: $options.getHashedURL,
          type: $options.item.mime_type,
          style: normalizeStyle($options.style)
        }, null, 12 /* STYLE, PROPS */, _hoisted_7$1)) : createCommentVNode("v-if", true)])];
      }),
      "backdrop-close": withCtx(function () {
        return [createBaseVNode("button", {
          type: "button",
          class: "media-preview-close",
          onClick: _cache[0] || (_cache[0] = function ($event) {
            return $options.close();
          })
        }, _hoisted_9$1)];
      }),
      _: 1 /* STABLE */
    })) : createCommentVNode("v-if", true);
  }
  script$4.render = render$4;
  script$4.__file = "administrator/components/com_media/resources/scripts/components/modals/preview-modal.vue";
  var script$3 = {
    name: 'MediaRenameModal',
    components: {
      MediaModal: script$6
    },
    computed: {
      item: function item() {
        return this.$store.state.selectedItems[this.$store.state.selectedItems.length - 1];
      },
      name: function name() {
        return this.item.name.replace("." + this.item.extension, '');
      },
      extension: function extension() {
        return this.item.extension;
      }
    },
    updated: function updated() {
      var _this18 = this;
      this.$nextTick(function () {
        return _this18.$refs.nameField ? _this18.$refs.nameField.focus() : null;
      });
    },
    methods: {
      /* Check if the form is valid */isValid: function isValid() {
        return this.item.name.length > 0;
      },
      /* Close the modal instance */close: function close() {
        this.$store.commit(HIDE_RENAME_MODAL);
      },
      /* Save the form and create the folder */save: function save() {
        // Check if the form is valid
        if (!this.isValid()) {
          // @todo mark the field as invalid
          return;
        }
        var newName = this.$refs.nameField.value;
        if (this.extension.length) {
          newName += "." + this.item.extension;
        }
        var newPath = this.item.directory;
        if (newPath.substr(-1) !== '/') {
          newPath += '/';
        }

        // Rename the item
        this.$store.dispatch('renameItem', {
          item: this.item,
          newPath: newPath + newName,
          newName: newName
        });
      }
    }
  };
  var _hoisted_1$3 = {
    id: "renameTitle",
    class: "modal-title"
  };
  var _hoisted_2$3 = {
    class: "form-group p-3"
  };
  var _hoisted_3$3 = {
    for: "name"
  };
  var _hoisted_4$1 = ["placeholder", "value"];
  var _hoisted_5$1 = {
    key: 0,
    class: "input-group-text"
  };
  var _hoisted_6$1 = ["disabled"];
  function render$3(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaModal = resolveComponent("MediaModal");
    return _ctx.$store.state.showRenameModal ? (openBlock(), createBlock(_component_MediaModal, {
      key: 0,
      size: 'sm',
      "show-close": false,
      "label-element": "renameTitle",
      onClose: _cache[5] || (_cache[5] = function ($event) {
        return $options.close();
      })
    }, {
      header: withCtx(function () {
        return [createBaseVNode("h3", _hoisted_1$3, toDisplayString(_ctx.translate('COM_MEDIA_RENAME')), 1 /* TEXT */)];
      }),

      body: withCtx(function () {
        return [createBaseVNode("div", null, [createBaseVNode("form", {
          class: "form",
          novalidate: "",
          onSubmit: _cache[0] || (_cache[0] = withModifiers(function () {
            return $options.save && $options.save.apply($options, arguments);
          }, ["prevent"]))
        }, [createBaseVNode("div", _hoisted_2$3, [createBaseVNode("label", _hoisted_3$3, toDisplayString(_ctx.translate('COM_MEDIA_NAME')), 1 /* TEXT */), createBaseVNode("div", {
          class: normalizeClass({
            'input-group': $options.extension.length
          })
        }, [createBaseVNode("input", {
          id: "name",
          ref: "nameField",
          class: "form-control",
          type: "text",
          placeholder: _ctx.translate('COM_MEDIA_NAME'),
          value: $options.name,
          required: "",
          autocomplete: "off"
        }, null, 8 /* PROPS */, _hoisted_4$1), $options.extension.length ? (openBlock(), createElementBlock("span", _hoisted_5$1, toDisplayString($options.extension), 1 /* TEXT */)) : createCommentVNode("v-if", true)], 2 /* CLASS */)])], 32 /* HYDRATE_EVENTS */)])];
      }),

      footer: withCtx(function () {
        return [createBaseVNode("div", null, [createBaseVNode("button", {
          type: "button",
          class: "btn btn-secondary",
          onClick: _cache[1] || (_cache[1] = function ($event) {
            return $options.close();
          }),
          onKeyup: _cache[2] || (_cache[2] = withKeys(function ($event) {
            return $options.close();
          }, ["enter"]))
        }, toDisplayString(_ctx.translate('JCANCEL')), 33 /* TEXT, HYDRATE_EVENTS */), createBaseVNode("button", {
          type: "button",
          class: "btn btn-success",
          disabled: !$options.isValid(),
          onClick: _cache[3] || (_cache[3] = function ($event) {
            return $options.save();
          }),
          onKeyup: _cache[4] || (_cache[4] = withKeys(function ($event) {
            return $options.save();
          }, ["enter"]))
        }, toDisplayString(_ctx.translate('JAPPLY')), 41 /* TEXT, PROPS, HYDRATE_EVENTS */, _hoisted_6$1)])];
      }),
      _: 1 /* STABLE */
    })) : createCommentVNode("v-if", true);
  }
  script$3.render = render$3;
  script$3.__file = "administrator/components/com_media/resources/scripts/components/modals/rename-modal.vue";

  /**
   * Translate plugin
   */

  var Translate = {
    // Translate from Joomla text
    translate: function translate(key) {
      return Joomla.Text._(key, key);
    },
    sprintf: function sprintf(string) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      var newString = Translate.translate(string);
      var i = 0;
      return newString.replace(/%((%)|s|d)/g, function (m) {
        var val = args[i];
        if (m === '%d') {
          val = parseFloat(val);
          if (Number.isNaN(val)) {
            val = 0;
          }
        }
        i += 1;
        return val;
      });
    },
    install: function install(Vue) {
      return Vue.mixin({
        methods: {
          translate: function translate(key) {
            return Translate.translate(key);
          },
          sprintf: function sprintf(key) {
            for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
              args[_key2 - 1] = arguments[_key2];
            }
            return Translate.sprintf(key, args);
          }
        }
      });
    }
  };
  var script$2 = {
    name: 'MediaShareModal',
    components: {
      MediaModal: script$6
    },
    computed: {
      item: function item() {
        return this.$store.state.selectedItems[this.$store.state.selectedItems.length - 1];
      },
      url: function url() {
        return this.$store.state.previewItem && Object.prototype.hasOwnProperty.call(this.$store.state.previewItem, 'url') ? this.$store.state.previewItem.url : null;
      }
    },
    methods: {
      /* Close the modal instance and reset the form */close: function close() {
        this.$store.commit(HIDE_SHARE_MODAL);
        this.$store.commit(LOAD_FULL_CONTENTS_SUCCESS, null);
      },
      // Generate the url from backend
      generateUrl: function generateUrl() {
        this.$store.dispatch('getFullContents', this.item);
      },
      // Copy to clipboard
      copyToClipboard: function copyToClipboard() {
        this.$refs.urlText.focus();
        this.$refs.urlText.select();
        try {
          document.execCommand('copy');
        } catch (err) {
          // @todo Error handling in joomla way
          window.alert(Translate('COM_MEDIA_SHARE_COPY_FAILED_ERROR'));
        }
      }
    }
  };
  var _hoisted_1$2 = {
    id: "shareTitle",
    class: "modal-title"
  };
  var _hoisted_2$2 = {
    class: "p-3"
  };
  var _hoisted_3$2 = {
    class: "desc"
  };
  var _hoisted_4 = {
    key: 0,
    class: "control"
  };
  var _hoisted_5 = {
    key: 1,
    class: "control"
  };
  var _hoisted_6 = {
    class: "input-group"
  };
  var _hoisted_7 = ["title"];
  var _hoisted_8 = /*#__PURE__*/createBaseVNode("span", {
    class: "icon-clipboard",
    "aria-hidden": "true"
  }, null, -1 /* HOISTED */);
  var _hoisted_9 = [_hoisted_8];
  function render$2(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaModal = resolveComponent("MediaModal");
    return _ctx.$store.state.showShareModal ? (openBlock(), createBlock(_component_MediaModal, {
      key: 0,
      size: 'md',
      "show-close": false,
      "label-element": "shareTitle",
      onClose: _cache[4] || (_cache[4] = function ($event) {
        return $options.close();
      })
    }, {
      header: withCtx(function () {
        return [createBaseVNode("h3", _hoisted_1$2, toDisplayString(_ctx.translate('COM_MEDIA_SHARE')), 1 /* TEXT */)];
      }),

      body: withCtx(function () {
        return [createBaseVNode("div", _hoisted_2$2, [createBaseVNode("div", _hoisted_3$2, [createTextVNode(toDisplayString(_ctx.translate('COM_MEDIA_SHARE_DESC')) + " ", 1 /* TEXT */), !$options.url ? (openBlock(), createElementBlock("div", _hoisted_4, [createBaseVNode("button", {
          class: "btn btn-success w-100",
          type: "button",
          onClick: _cache[0] || (_cache[0] = function () {
            return $options.generateUrl && $options.generateUrl.apply($options, arguments);
          })
        }, toDisplayString(_ctx.translate('COM_MEDIA_ACTION_SHARE')), 1 /* TEXT */)])) : (openBlock(), createElementBlock("div", _hoisted_5, [createBaseVNode("span", _hoisted_6, [withDirectives(createBaseVNode("input", {
          id: "url",
          ref: "urlText",
          "onUpdate:modelValue": _cache[1] || (_cache[1] = function ($event) {
            return $options.url = $event;
          }),
          readonly: "",
          type: "url",
          class: "form-control input-xxlarge",
          placeholder: "URL",
          autocomplete: "off"
        }, null, 512 /* NEED_PATCH */), [[vModelText, $options.url]]), createBaseVNode("button", {
          class: "btn btn-secondary",
          type: "button",
          title: _ctx.translate('COM_MEDIA_SHARE_COPY'),
          onClick: _cache[2] || (_cache[2] = function () {
            return $options.copyToClipboard && $options.copyToClipboard.apply($options, arguments);
          })
        }, _hoisted_9, 8 /* PROPS */, _hoisted_7)])]))])])];
      }),
      footer: withCtx(function () {
        return [createBaseVNode("div", null, [createBaseVNode("button", {
          class: "btn btn-secondary",
          onClick: _cache[3] || (_cache[3] = function ($event) {
            return $options.close();
          })
        }, toDisplayString(_ctx.translate('JCANCEL')), 1 /* TEXT */)])];
      }),

      _: 1 /* STABLE */
    })) : createCommentVNode("v-if", true);
  }
  script$2.render = render$2;
  script$2.__file = "administrator/components/com_media/resources/scripts/components/modals/share-modal.vue";
  var script$1 = {
    name: 'MediaShareModal',
    components: {
      MediaModal: script$6
    },
    computed: {
      item: function item() {
        return this.$store.state.selectedItems[this.$store.state.selectedItems.length - 1];
      }
    },
    methods: {
      /* Delete Item */deleteItem: function deleteItem() {
        this.$store.dispatch('deleteSelectedItems');
        this.$store.commit(HIDE_CONFIRM_DELETE_MODAL);
      },
      /* Close the modal instance */close: function close() {
        this.$store.commit(HIDE_CONFIRM_DELETE_MODAL);
      }
    }
  };
  var _hoisted_1$1 = {
    id: "confirmDeleteTitle",
    class: "modal-title"
  };
  var _hoisted_2$1 = {
    class: "p-3"
  };
  var _hoisted_3$1 = {
    class: "desc"
  };
  function render$1(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaModal = resolveComponent("MediaModal");
    return _ctx.$store.state.showConfirmDeleteModal ? (openBlock(), createBlock(_component_MediaModal, {
      key: 0,
      size: 'md',
      "show-close": false,
      "label-element": "confirmDeleteTitle",
      onClose: _cache[2] || (_cache[2] = function ($event) {
        return $options.close();
      })
    }, {
      header: withCtx(function () {
        return [createBaseVNode("h3", _hoisted_1$1, toDisplayString(_ctx.translate('COM_MEDIA_CONFIRM_DELETE_MODAL_HEADING')), 1 /* TEXT */)];
      }),

      body: withCtx(function () {
        return [createBaseVNode("div", _hoisted_2$1, [createBaseVNode("div", _hoisted_3$1, toDisplayString(_ctx.translate('JGLOBAL_CONFIRM_DELETE')), 1 /* TEXT */)])];
      }),

      footer: withCtx(function () {
        return [createBaseVNode("div", null, [createBaseVNode("button", {
          class: "btn btn-success",
          onClick: _cache[0] || (_cache[0] = function ($event) {
            return $options.close();
          })
        }, toDisplayString(_ctx.translate('JCANCEL')), 1 /* TEXT */), createBaseVNode("button", {
          id: "media-delete-item",
          class: "btn btn-danger",
          onClick: _cache[1] || (_cache[1] = function ($event) {
            return $options.deleteItem();
          })
        }, toDisplayString(_ctx.translate('COM_MEDIA_CONFIRM_DELETE_MODAL')), 1 /* TEXT */)])];
      }),

      _: 1 /* STABLE */
    })) : createCommentVNode("v-if", true);
  }
  script$1.render = render$1;
  script$1.__file = "administrator/components/com_media/resources/scripts/components/modals/confirm-delete-modal.vue";
  var script = {
    name: 'MediaApp',
    components: {
      MediaBrowser: script$e,
      MediaDisk: script$b,
      MediaToolbar: script$9,
      MediaUpload: script$8,
      MediaCreateFolderModal: script$5,
      MediaPreviewModal: script$4,
      MediaRenameModal: script$3,
      MediaShareModal: script$2,
      MediaConfirmDeleteModal: script$1
    },
    data: function data() {
      return {
        // The full height of the app in px
        fullHeight: ''
      };
    },
    computed: {
      disks: function disks() {
        return this.$store.state.disks;
      }
    },
    created: function created() {
      var _this19 = this;
      // Listen to the toolbar events
      MediaManager.Event.listen('onClickCreateFolder', function () {
        return _this19.$store.commit(SHOW_CREATE_FOLDER_MODAL);
      });
      MediaManager.Event.listen('onClickDelete', function () {
        if (_this19.$store.state.selectedItems.length > 0) {
          _this19.$store.commit(SHOW_CONFIRM_DELETE_MODAL);
        } else {
          notifications.error('COM_MEDIA_PLEASE_SELECT_ITEM');
        }
      });
    },
    mounted: function mounted() {
      var _this20 = this;
      // Set the full height and add event listener when dom is updated
      this.$nextTick(function () {
        _this20.setFullHeight();
        // Add the global resize event listener
        window.addEventListener('resize', _this20.setFullHeight);
      });

      // Initial load the data
      this.$store.dispatch('getContents', this.$store.state.selectedDirectory, false, false);
    },
    beforeUnmount: function beforeUnmount() {
      // Remove the global resize event listener
      window.removeEventListener('resize', this.setFullHeight);
    },
    methods: {
      /* Set the full height on the app container */setFullHeight: function setFullHeight() {
        this.fullHeight = window.innerHeight - this.$el.getBoundingClientRect().top + "px";
      }
    }
  };
  var _hoisted_1 = {
    class: "media-container"
  };
  var _hoisted_2 = {
    class: "media-sidebar"
  };
  var _hoisted_3 = {
    class: "media-main"
  };
  function render(_ctx, _cache, $props, $setup, $data, $options) {
    var _component_MediaDisk = resolveComponent("MediaDisk");
    var _component_MediaToolbar = resolveComponent("MediaToolbar");
    var _component_MediaBrowser = resolveComponent("MediaBrowser");
    var _component_MediaUpload = resolveComponent("MediaUpload");
    var _component_MediaCreateFolderModal = resolveComponent("MediaCreateFolderModal");
    var _component_MediaPreviewModal = resolveComponent("MediaPreviewModal");
    var _component_MediaRenameModal = resolveComponent("MediaRenameModal");
    var _component_MediaShareModal = resolveComponent("MediaShareModal");
    var _component_MediaConfirmDeleteModal = resolveComponent("MediaConfirmDeleteModal");
    return openBlock(), createElementBlock("div", _hoisted_1, [createBaseVNode("div", _hoisted_2, [(openBlock(true), createElementBlock(Fragment, null, renderList($options.disks, function (disk, index) {
      return openBlock(), createBlock(_component_MediaDisk, {
        key: index.toString(),
        uid: index.toString(),
        disk: disk
      }, null, 8 /* PROPS */, ["uid", "disk"]);
    }), 128 /* KEYED_FRAGMENT */))]), createBaseVNode("div", _hoisted_3, [createVNode(_component_MediaToolbar), createVNode(_component_MediaBrowser)]), createVNode(_component_MediaUpload), createVNode(_component_MediaCreateFolderModal), createVNode(_component_MediaPreviewModal), createVNode(_component_MediaRenameModal), createVNode(_component_MediaShareModal), createVNode(_component_MediaConfirmDeleteModal)]);
  }
  script.render = render;
  script.__file = "administrator/components/com_media/resources/scripts/components/app.vue";

  /**
   * Media Event bus - used for communication between joomla and vue
   */
  var Event$1 = /*#__PURE__*/function () {
    /**
       * Media Event constructor
       */
    function Event$1() {
      this.events = {};
    }

    /**
       * Fire an event
       * @param event
       * @param data
       */
    var _proto4 = Event$1.prototype;
    _proto4.fire = function fire(event, data) {
      if (data === void 0) {
        data = null;
      }
      if (this.events[event]) {
        this.events[event].forEach(function (fn) {
          return fn(data);
        });
      }
    }

    /**
       * Listen to events
       * @param event
       * @param callback
       */;
    _proto4.listen = function listen(event, callback) {
      this.events[event] = this.events[event] || [];
      this.events[event].push(callback);
    };
    return Event$1;
  }();
  function getDevtoolsGlobalHook() {
    return getTarget().__VUE_DEVTOOLS_GLOBAL_HOOK__;
  }
  function getTarget() {
    // @ts-ignore
    return typeof navigator !== 'undefined' && typeof window !== 'undefined' ? window : typeof commonjsGlobal !== 'undefined' ? commonjsGlobal : {};
  }
  var isProxyAvailable = typeof Proxy === 'function';
  var HOOK_SETUP = 'devtools-plugin:setup';
  var HOOK_PLUGIN_SETTINGS_SET = 'plugin:settings:set';
  var supported;
  var perf;
  function isPerformanceSupported() {
    var _a;
    if (supported !== undefined) {
      return supported;
    }
    if (typeof window !== 'undefined' && window.performance) {
      supported = true;
      perf = window.performance;
    } else if (typeof commonjsGlobal !== 'undefined' && ((_a = commonjsGlobal.perf_hooks) === null || _a === void 0 ? void 0 : _a.performance)) {
      supported = true;
      perf = commonjsGlobal.perf_hooks.performance;
    } else {
      supported = false;
    }
    return supported;
  }
  function _now() {
    return isPerformanceSupported() ? perf.now() : Date.now();
  }
  var ApiProxy = /*#__PURE__*/function () {
    function ApiProxy(plugin, hook) {
      var _this21 = this;
      var _this = this;
      this.target = null;
      this.targetQueue = [];
      this.onQueue = [];
      this.plugin = plugin;
      this.hook = hook;
      var defaultSettings = {};
      if (plugin.settings) {
        for (var id in plugin.settings) {
          var item = plugin.settings[id];
          defaultSettings[id] = item.defaultValue;
        }
      }
      var localSettingsSaveId = "__vue-devtools-plugin-settings__" + plugin.id;
      var currentSettings = Object.assign({}, defaultSettings);
      try {
        var raw = localStorage.getItem(localSettingsSaveId);
        var data = JSON.parse(raw);
        Object.assign(currentSettings, data);
      } catch (e) {
        // noop
      }
      this.fallbacks = {
        getSettings: function getSettings() {
          return currentSettings;
        },
        setSettings: function setSettings(value) {
          try {
            localStorage.setItem(localSettingsSaveId, JSON.stringify(value));
          } catch (e) {
            // noop
          }
          currentSettings = value;
        },
        now: function now() {
          return _now();
        }
      };
      if (hook) {
        hook.on(HOOK_PLUGIN_SETTINGS_SET, function (pluginId, value) {
          if (pluginId === _this21.plugin.id) {
            _this21.fallbacks.setSettings(value);
          }
        });
      }
      this.proxiedOn = new Proxy({}, {
        get: function get(_target, prop) {
          if (_this21.target) {
            return _this21.target.on[prop];
          } else {
            return function () {
              for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
                args[_key] = arguments[_key];
              }
              _this.onQueue.push({
                method: prop,
                args: args
              });
            };
          }
        }
      });
      this.proxiedTarget = new Proxy({}, {
        get: function get(_target, prop) {
          if (_this21.target) {
            return _this21.target[prop];
          } else if (prop === 'on') {
            return _this21.proxiedOn;
          } else if (Object.keys(_this21.fallbacks).includes(prop)) {
            return function () {
              var _this$fallbacks;
              for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
                args[_key2] = arguments[_key2];
              }
              _this.targetQueue.push({
                method: prop,
                args: args,
                resolve: function resolve() {}
              });
              return (_this$fallbacks = _this.fallbacks)[prop].apply(_this$fallbacks, args);
            };
          } else {
            return function () {
              for (var _len3 = arguments.length, args = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
                args[_key3] = arguments[_key3];
              }
              return new Promise(function (resolve) {
                _this.targetQueue.push({
                  method: prop,
                  args: args,
                  resolve: resolve
                });
              });
            };
          }
        }
      });
    }
    var _proto5 = ApiProxy.prototype;
    _proto5.setRealTarget = /*#__PURE__*/function () {
      var _setRealTarget = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(target) {
        var _iterator5, _step5, _this$target$on, item, _iterator6, _step6, _this$target, _item;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              this.target = target;
              for (_iterator5 = _createForOfIteratorHelperLoose(this.onQueue); !(_step5 = _iterator5()).done;) {
                item = _step5.value;
                (_this$target$on = this.target.on)[item.method].apply(_this$target$on, item.args);
              }
              _iterator6 = _createForOfIteratorHelperLoose(this.targetQueue);
            case 3:
              if ((_step6 = _iterator6()).done) {
                _context.next = 12;
                break;
              }
              _item = _step6.value;
              _context.t0 = _item;
              _context.next = 8;
              return (_this$target = this.target)[_item.method].apply(_this$target, _item.args);
            case 8:
              _context.t1 = _context.sent;
              _context.t0.resolve.call(_context.t0, _context.t1);
            case 10:
              _context.next = 3;
              break;
            case 12:
            case "end":
              return _context.stop();
          }
        }, _callee, this);
      }));
      function setRealTarget(_x) {
        return _setRealTarget.apply(this, arguments);
      }
      return setRealTarget;
    }();
    return ApiProxy;
  }();
  function setupDevtoolsPlugin(pluginDescriptor, setupFn) {
    var descriptor = pluginDescriptor;
    var target = getTarget();
    var hook = getDevtoolsGlobalHook();
    var enableProxy = isProxyAvailable && descriptor.enableEarlyProxy;
    if (hook && (target.__VUE_DEVTOOLS_PLUGIN_API_AVAILABLE__ || !enableProxy)) {
      hook.emit(HOOK_SETUP, pluginDescriptor, setupFn);
    } else {
      var proxy = enableProxy ? new ApiProxy(descriptor, hook) : null;
      var list = target.__VUE_DEVTOOLS_PLUGINS__ = target.__VUE_DEVTOOLS_PLUGINS__ || [];
      list.push({
        pluginDescriptor: descriptor,
        setupFn: setupFn,
        proxy: proxy
      });
      if (proxy) setupFn(proxy.proxiedTarget);
    }
  }

  /*!
   * vuex v4.1.0
   * (c) 2022 Evan You
   * @license MIT
   */
  var storeKey = 'store';

  /**
   * forEach for object
   */
  function forEachValue(obj, fn) {
    Object.keys(obj).forEach(function (key) {
      return fn(obj[key], key);
    });
  }
  function isObject(obj) {
    return obj !== null && typeof obj === 'object';
  }
  function isPromise(val) {
    return val && typeof val.then === 'function';
  }
  function partial(fn, arg) {
    return function () {
      return fn(arg);
    };
  }
  function genericSubscribe(fn, subs, options) {
    if (subs.indexOf(fn) < 0) {
      options && options.prepend ? subs.unshift(fn) : subs.push(fn);
    }
    return function () {
      var i = subs.indexOf(fn);
      if (i > -1) {
        subs.splice(i, 1);
      }
    };
  }
  function resetStore(store, hot) {
    store._actions = Object.create(null);
    store._mutations = Object.create(null);
    store._wrappedGetters = Object.create(null);
    store._modulesNamespaceMap = Object.create(null);
    var state = store.state;
    // init all modules
    installModule(store, state, [], store._modules.root, true);
    // reset state
    resetStoreState(store, state, hot);
  }
  function resetStoreState(store, state, hot) {
    var oldState = store._state;
    var oldScope = store._scope;

    // bind store public getters
    store.getters = {};
    // reset local getters cache
    store._makeLocalGettersCache = Object.create(null);
    var wrappedGetters = store._wrappedGetters;
    var computedObj = {};
    var computedCache = {};

    // create a new effect scope and create computed object inside it to avoid
    // getters (computed) getting destroyed on component unmount.
    var scope = effectScope(true);
    scope.run(function () {
      forEachValue(wrappedGetters, function (fn, key) {
        // use computed to leverage its lazy-caching mechanism
        // direct inline function use will lead to closure preserving oldState.
        // using partial to return function with only arguments preserved in closure environment.
        computedObj[key] = partial(fn, store);
        computedCache[key] = computed(function () {
          return computedObj[key]();
        });
        Object.defineProperty(store.getters, key, {
          get: function get() {
            return computedCache[key].value;
          },
          enumerable: true // for local getters
        });
      });
    });

    store._state = reactive({
      data: state
    });

    // register the newly created effect scope to the store so that we can
    // dispose the effects when this method runs again in the future.
    store._scope = scope;

    // enable strict mode for new state
    if (store.strict) {
      enableStrictMode(store);
    }
    if (oldState) {
      if (hot) {
        // dispatch changes in all subscribed watchers
        // to force getter re-evaluation for hot reloading.
        store._withCommit(function () {
          oldState.data = null;
        });
      }
    }

    // dispose previously registered effect scope if there is one.
    if (oldScope) {
      oldScope.stop();
    }
  }
  function installModule(store, rootState, path, module, hot) {
    var isRoot = !path.length;
    var namespace = store._modules.getNamespace(path);

    // register in namespace map
    if (module.namespaced) {
      if (store._modulesNamespaceMap[namespace] && "production" !== 'production') {
        console.error("[vuex] duplicate namespace " + namespace + " for the namespaced module " + path.join('/'));
      }
      store._modulesNamespaceMap[namespace] = module;
    }

    // set state
    if (!isRoot && !hot) {
      var parentState = getNestedState(rootState, path.slice(0, -1));
      var moduleName = path[path.length - 1];
      store._withCommit(function () {
        parentState[moduleName] = module.state;
      });
    }
    var local = module.context = makeLocalContext(store, namespace, path);
    module.forEachMutation(function (mutation, key) {
      var namespacedType = namespace + key;
      registerMutation(store, namespacedType, mutation, local);
    });
    module.forEachAction(function (action, key) {
      var type = action.root ? key : namespace + key;
      var handler = action.handler || action;
      registerAction(store, type, handler, local);
    });
    module.forEachGetter(function (getter, key) {
      var namespacedType = namespace + key;
      registerGetter(store, namespacedType, getter, local);
    });
    module.forEachChild(function (child, key) {
      installModule(store, rootState, path.concat(key), child, hot);
    });
  }

  /**
   * make localized dispatch, commit, getters and state
   * if there is no namespace, just use root ones
   */
  function makeLocalContext(store, namespace, path) {
    var noNamespace = namespace === '';
    var local = {
      dispatch: noNamespace ? store.dispatch : function (_type, _payload, _options) {
        var args = unifyObjectStyle(_type, _payload, _options);
        var payload = args.payload;
        var options = args.options;
        var type = args.type;
        if (!options || !options.root) {
          type = namespace + type;
        }
        return store.dispatch(type, payload);
      },
      commit: noNamespace ? store.commit : function (_type, _payload, _options) {
        var args = unifyObjectStyle(_type, _payload, _options);
        var payload = args.payload;
        var options = args.options;
        var type = args.type;
        if (!options || !options.root) {
          type = namespace + type;
        }
        store.commit(type, payload, options);
      }
    };

    // getters and state object must be gotten lazily
    // because they will be changed by state update
    Object.defineProperties(local, {
      getters: {
        get: noNamespace ? function () {
          return store.getters;
        } : function () {
          return makeLocalGetters(store, namespace);
        }
      },
      state: {
        get: function get() {
          return getNestedState(store.state, path);
        }
      }
    });
    return local;
  }
  function makeLocalGetters(store, namespace) {
    if (!store._makeLocalGettersCache[namespace]) {
      var gettersProxy = {};
      var splitPos = namespace.length;
      Object.keys(store.getters).forEach(function (type) {
        // skip if the target getter is not match this namespace
        if (type.slice(0, splitPos) !== namespace) {
          return;
        }

        // extract local getter type
        var localType = type.slice(splitPos);

        // Add a port to the getters proxy.
        // Define as getter property because
        // we do not want to evaluate the getters in this time.
        Object.defineProperty(gettersProxy, localType, {
          get: function get() {
            return store.getters[type];
          },
          enumerable: true
        });
      });
      store._makeLocalGettersCache[namespace] = gettersProxy;
    }
    return store._makeLocalGettersCache[namespace];
  }
  function registerMutation(store, type, handler, local) {
    var entry = store._mutations[type] || (store._mutations[type] = []);
    entry.push(function wrappedMutationHandler(payload) {
      handler.call(store, local.state, payload);
    });
  }
  function registerAction(store, type, handler, local) {
    var entry = store._actions[type] || (store._actions[type] = []);
    entry.push(function wrappedActionHandler(payload) {
      var res = handler.call(store, {
        dispatch: local.dispatch,
        commit: local.commit,
        getters: local.getters,
        state: local.state,
        rootGetters: store.getters,
        rootState: store.state
      }, payload);
      if (!isPromise(res)) {
        res = Promise.resolve(res);
      }
      if (store._devtoolHook) {
        return res.catch(function (err) {
          store._devtoolHook.emit('vuex:error', err);
          throw err;
        });
      } else {
        return res;
      }
    });
  }
  function registerGetter(store, type, rawGetter, local) {
    if (store._wrappedGetters[type]) {
      return;
    }
    store._wrappedGetters[type] = function wrappedGetter(store) {
      return rawGetter(local.state,
      // local state
      local.getters,
      // local getters
      store.state,
      // root state
      store.getters // root getters
      );
    };
  }

  function enableStrictMode(store) {
    watch(function () {
      return store._state.data;
    }, function () {}, {
      deep: true,
      flush: 'sync'
    });
  }
  function getNestedState(state, path) {
    return path.reduce(function (state, key) {
      return state[key];
    }, state);
  }
  function unifyObjectStyle(type, payload, options) {
    if (isObject(type) && type.type) {
      options = payload;
      payload = type;
      type = type.type;
    }
    return {
      type: type,
      payload: payload,
      options: options
    };
  }
  var LABEL_VUEX_BINDINGS = 'vuex bindings';
  var MUTATIONS_LAYER_ID = 'vuex:mutations';
  var ACTIONS_LAYER_ID = 'vuex:actions';
  var INSPECTOR_ID = 'vuex';
  var actionId = 0;
  function addDevtools(app, store) {
    setupDevtoolsPlugin({
      id: 'org.vuejs.vuex',
      app: app,
      label: 'Vuex',
      homepage: 'https://next.vuex.vuejs.org/',
      logo: 'https://vuejs.org/images/icons/favicon-96x96.png',
      packageName: 'vuex',
      componentStateTypes: [LABEL_VUEX_BINDINGS]
    }, function (api) {
      api.addTimelineLayer({
        id: MUTATIONS_LAYER_ID,
        label: 'Vuex Mutations',
        color: COLOR_LIME_500
      });
      api.addTimelineLayer({
        id: ACTIONS_LAYER_ID,
        label: 'Vuex Actions',
        color: COLOR_LIME_500
      });
      api.addInspector({
        id: INSPECTOR_ID,
        label: 'Vuex',
        icon: 'storage',
        treeFilterPlaceholder: 'Filter stores...'
      });
      api.on.getInspectorTree(function (payload) {
        if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
          if (payload.filter) {
            var nodes = [];
            flattenStoreForInspectorTree(nodes, store._modules.root, payload.filter, '');
            payload.rootNodes = nodes;
          } else {
            payload.rootNodes = [formatStoreForInspectorTree(store._modules.root, '')];
          }
        }
      });
      api.on.getInspectorState(function (payload) {
        if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
          var modulePath = payload.nodeId;
          makeLocalGetters(store, modulePath);
          payload.state = formatStoreForInspectorState(getStoreModule(store._modules, modulePath), modulePath === 'root' ? store.getters : store._makeLocalGettersCache, modulePath);
        }
      });
      api.on.editInspectorState(function (payload) {
        if (payload.app === app && payload.inspectorId === INSPECTOR_ID) {
          var modulePath = payload.nodeId;
          var path = payload.path;
          if (modulePath !== 'root') {
            path = modulePath.split('/').filter(Boolean).concat(path);
          }
          store._withCommit(function () {
            payload.set(store._state.data, path, payload.state.value);
          });
        }
      });
      store.subscribe(function (mutation, state) {
        var data = {};
        if (mutation.payload) {
          data.payload = mutation.payload;
        }
        data.state = state;
        api.notifyComponentUpdate();
        api.sendInspectorTree(INSPECTOR_ID);
        api.sendInspectorState(INSPECTOR_ID);
        api.addTimelineEvent({
          layerId: MUTATIONS_LAYER_ID,
          event: {
            time: Date.now(),
            title: mutation.type,
            data: data
          }
        });
      });
      store.subscribeAction({
        before: function before(action, state) {
          var data = {};
          if (action.payload) {
            data.payload = action.payload;
          }
          action._id = actionId++;
          action._time = Date.now();
          data.state = state;
          api.addTimelineEvent({
            layerId: ACTIONS_LAYER_ID,
            event: {
              time: action._time,
              title: action.type,
              groupId: action._id,
              subtitle: 'start',
              data: data
            }
          });
        },
        after: function after(action, state) {
          var data = {};
          var duration = Date.now() - action._time;
          data.duration = {
            _custom: {
              type: 'duration',
              display: duration + "ms",
              tooltip: 'Action duration',
              value: duration
            }
          };
          if (action.payload) {
            data.payload = action.payload;
          }
          data.state = state;
          api.addTimelineEvent({
            layerId: ACTIONS_LAYER_ID,
            event: {
              time: Date.now(),
              title: action.type,
              groupId: action._id,
              subtitle: 'end',
              data: data
            }
          });
        }
      });
    });
  }

  // extracted from tailwind palette
  var COLOR_LIME_500 = 0x84cc16;
  var COLOR_DARK = 0x666666;
  var COLOR_WHITE = 0xffffff;
  var TAG_NAMESPACED = {
    label: 'namespaced',
    textColor: COLOR_WHITE,
    backgroundColor: COLOR_DARK
  };

  /**
   * @param {string} path
   */
  function extractNameFromPath(path) {
    return path && path !== 'root' ? path.split('/').slice(-2, -1)[0] : 'Root';
  }

  /**
   * @param {*} module
   * @return {import('@vue/devtools-api').CustomInspectorNode}
   */
  function formatStoreForInspectorTree(module, path) {
    return {
      id: path || 'root',
      // all modules end with a `/`, we want the last segment only
      // cart/ -> cart
      // nested/cart/ -> cart
      label: extractNameFromPath(path),
      tags: module.namespaced ? [TAG_NAMESPACED] : [],
      children: Object.keys(module._children).map(function (moduleName) {
        return formatStoreForInspectorTree(module._children[moduleName], path + moduleName + '/');
      })
    };
  }

  /**
   * @param {import('@vue/devtools-api').CustomInspectorNode[]} result
   * @param {*} module
   * @param {string} filter
   * @param {string} path
   */
  function flattenStoreForInspectorTree(result, module, filter, path) {
    if (path.includes(filter)) {
      result.push({
        id: path || 'root',
        label: path.endsWith('/') ? path.slice(0, path.length - 1) : path || 'Root',
        tags: module.namespaced ? [TAG_NAMESPACED] : []
      });
    }
    Object.keys(module._children).forEach(function (moduleName) {
      flattenStoreForInspectorTree(result, module._children[moduleName], filter, path + moduleName + '/');
    });
  }

  /**
   * @param {*} module
   * @return {import('@vue/devtools-api').CustomInspectorState}
   */
  function formatStoreForInspectorState(module, getters, path) {
    getters = path === 'root' ? getters : getters[path];
    var gettersKeys = Object.keys(getters);
    var storeState = {
      state: Object.keys(module.state).map(function (key) {
        return {
          key: key,
          editable: true,
          value: module.state[key]
        };
      })
    };
    if (gettersKeys.length) {
      var tree = transformPathsToObjectTree(getters);
      storeState.getters = Object.keys(tree).map(function (key) {
        return {
          key: key.endsWith('/') ? extractNameFromPath(key) : key,
          editable: false,
          value: canThrow(function () {
            return tree[key];
          })
        };
      });
    }
    return storeState;
  }
  function transformPathsToObjectTree(getters) {
    var result = {};
    Object.keys(getters).forEach(function (key) {
      var path = key.split('/');
      if (path.length > 1) {
        var target = result;
        var leafKey = path.pop();
        path.forEach(function (p) {
          if (!target[p]) {
            target[p] = {
              _custom: {
                value: {},
                display: p,
                tooltip: 'Module',
                abstract: true
              }
            };
          }
          target = target[p]._custom.value;
        });
        target[leafKey] = canThrow(function () {
          return getters[key];
        });
      } else {
        result[key] = canThrow(function () {
          return getters[key];
        });
      }
    });
    return result;
  }
  function getStoreModule(moduleMap, path) {
    var names = path.split('/').filter(function (n) {
      return n;
    });
    return names.reduce(function (module, moduleName, i) {
      var child = module[moduleName];
      if (!child) {
        throw new Error("Missing module \"" + moduleName + "\" for path \"" + path + "\".");
      }
      return i === names.length - 1 ? child : child._children;
    }, path === 'root' ? moduleMap : moduleMap.root._children);
  }
  function canThrow(cb) {
    try {
      return cb();
    } catch (e) {
      return e;
    }
  }

  // Base data struct for store's module, package with some attribute and method
  var Module = function Module(rawModule, runtime) {
    this.runtime = runtime;
    // Store some children item
    this._children = Object.create(null);
    // Store the origin module object which passed by programmer
    this._rawModule = rawModule;
    var rawState = rawModule.state;

    // Store the origin module's state
    this.state = (typeof rawState === 'function' ? rawState() : rawState) || {};
  };
  var prototypeAccessors$1 = {
    namespaced: {
      configurable: true
    }
  };
  prototypeAccessors$1.namespaced.get = function () {
    return !!this._rawModule.namespaced;
  };
  Module.prototype.addChild = function addChild(key, module) {
    this._children[key] = module;
  };
  Module.prototype.removeChild = function removeChild(key) {
    delete this._children[key];
  };
  Module.prototype.getChild = function getChild(key) {
    return this._children[key];
  };
  Module.prototype.hasChild = function hasChild(key) {
    return key in this._children;
  };
  Module.prototype.update = function update(rawModule) {
    this._rawModule.namespaced = rawModule.namespaced;
    if (rawModule.actions) {
      this._rawModule.actions = rawModule.actions;
    }
    if (rawModule.mutations) {
      this._rawModule.mutations = rawModule.mutations;
    }
    if (rawModule.getters) {
      this._rawModule.getters = rawModule.getters;
    }
  };
  Module.prototype.forEachChild = function forEachChild(fn) {
    forEachValue(this._children, fn);
  };
  Module.prototype.forEachGetter = function forEachGetter(fn) {
    if (this._rawModule.getters) {
      forEachValue(this._rawModule.getters, fn);
    }
  };
  Module.prototype.forEachAction = function forEachAction(fn) {
    if (this._rawModule.actions) {
      forEachValue(this._rawModule.actions, fn);
    }
  };
  Module.prototype.forEachMutation = function forEachMutation(fn) {
    if (this._rawModule.mutations) {
      forEachValue(this._rawModule.mutations, fn);
    }
  };
  Object.defineProperties(Module.prototype, prototypeAccessors$1);
  var ModuleCollection = function ModuleCollection(rawRootModule) {
    // register root module (Vuex.Store options)
    this.register([], rawRootModule, false);
  };
  ModuleCollection.prototype.get = function get(path) {
    return path.reduce(function (module, key) {
      return module.getChild(key);
    }, this.root);
  };
  ModuleCollection.prototype.getNamespace = function getNamespace(path) {
    var module = this.root;
    return path.reduce(function (namespace, key) {
      module = module.getChild(key);
      return namespace + (module.namespaced ? key + '/' : '');
    }, '');
  };
  ModuleCollection.prototype.update = function update$1(rawRootModule) {
    update([], this.root, rawRootModule);
  };
  ModuleCollection.prototype.register = function register(path, rawModule, runtime) {
    var this$1$1 = this;
    if (runtime === void 0) runtime = true;
    var newModule = new Module(rawModule, runtime);
    if (path.length === 0) {
      this.root = newModule;
    } else {
      var parent = this.get(path.slice(0, -1));
      parent.addChild(path[path.length - 1], newModule);
    }

    // register nested modules
    if (rawModule.modules) {
      forEachValue(rawModule.modules, function (rawChildModule, key) {
        this$1$1.register(path.concat(key), rawChildModule, runtime);
      });
    }
  };
  ModuleCollection.prototype.unregister = function unregister(path) {
    var parent = this.get(path.slice(0, -1));
    var key = path[path.length - 1];
    var child = parent.getChild(key);
    if (!child) {
      return;
    }
    if (!child.runtime) {
      return;
    }
    parent.removeChild(key);
  };
  ModuleCollection.prototype.isRegistered = function isRegistered(path) {
    var parent = this.get(path.slice(0, -1));
    var key = path[path.length - 1];
    if (parent) {
      return parent.hasChild(key);
    }
    return false;
  };
  function update(path, targetModule, newModule) {
    // update target module
    targetModule.update(newModule);

    // update nested modules
    if (newModule.modules) {
      for (var key in newModule.modules) {
        if (!targetModule.getChild(key)) {
          return;
        }
        update(path.concat(key), targetModule.getChild(key), newModule.modules[key]);
      }
    }
  }
  function createStore(options) {
    return new Store(options);
  }
  var Store = function Store(options) {
    var this$1$1 = this;
    if (options === void 0) options = {};
    var plugins = options.plugins;
    if (plugins === void 0) plugins = [];
    var strict = options.strict;
    if (strict === void 0) strict = false;
    var devtools = options.devtools;

    // store internal state
    this._committing = false;
    this._actions = Object.create(null);
    this._actionSubscribers = [];
    this._mutations = Object.create(null);
    this._wrappedGetters = Object.create(null);
    this._modules = new ModuleCollection(options);
    this._modulesNamespaceMap = Object.create(null);
    this._subscribers = [];
    this._makeLocalGettersCache = Object.create(null);

    // EffectScope instance. when registering new getters, we wrap them inside
    // EffectScope so that getters (computed) would not be destroyed on
    // component unmount.
    this._scope = null;
    this._devtools = devtools;

    // bind commit and dispatch to self
    var store = this;
    var ref = this;
    var dispatch = ref.dispatch;
    var commit = ref.commit;
    this.dispatch = function boundDispatch(type, payload) {
      return dispatch.call(store, type, payload);
    };
    this.commit = function boundCommit(type, payload, options) {
      return commit.call(store, type, payload, options);
    };

    // strict mode
    this.strict = strict;
    var state = this._modules.root.state;

    // init root module.
    // this also recursively registers all sub-modules
    // and collects all module getters inside this._wrappedGetters
    installModule(this, state, [], this._modules.root);

    // initialize the store state, which is responsible for the reactivity
    // (also registers _wrappedGetters as computed properties)
    resetStoreState(this, state);

    // apply plugins
    plugins.forEach(function (plugin) {
      return plugin(this$1$1);
    });
  };
  var prototypeAccessors = {
    state: {
      configurable: true
    }
  };
  Store.prototype.install = function install(app, injectKey) {
    app.provide(injectKey || storeKey, this);
    app.config.globalProperties.$store = this;
    var useDevtools = this._devtools !== undefined ? this._devtools : false;
    if (useDevtools) {
      addDevtools(app, this);
    }
  };
  prototypeAccessors.state.get = function () {
    return this._state.data;
  };
  prototypeAccessors.state.set = function (v) {};
  Store.prototype.commit = function commit(_type, _payload, _options) {
    var this$1$1 = this;

    // check object-style commit
    var ref = unifyObjectStyle(_type, _payload, _options);
    var type = ref.type;
    var payload = ref.payload;
    var mutation = {
      type: type,
      payload: payload
    };
    var entry = this._mutations[type];
    if (!entry) {
      return;
    }
    this._withCommit(function () {
      entry.forEach(function commitIterator(handler) {
        handler(payload);
      });
    });
    this._subscribers.slice() // shallow copy to prevent iterator invalidation if subscriber synchronously calls unsubscribe
    .forEach(function (sub) {
      return sub(mutation, this$1$1.state);
    });
  };
  Store.prototype.dispatch = function dispatch(_type, _payload) {
    var this$1$1 = this;

    // check object-style dispatch
    var ref = unifyObjectStyle(_type, _payload);
    var type = ref.type;
    var payload = ref.payload;
    var action = {
      type: type,
      payload: payload
    };
    var entry = this._actions[type];
    if (!entry) {
      return;
    }
    try {
      this._actionSubscribers.slice() // shallow copy to prevent iterator invalidation if subscriber synchronously calls unsubscribe
      .filter(function (sub) {
        return sub.before;
      }).forEach(function (sub) {
        return sub.before(action, this$1$1.state);
      });
    } catch (e) {}
    var result = entry.length > 1 ? Promise.all(entry.map(function (handler) {
      return handler(payload);
    })) : entry[0](payload);
    return new Promise(function (resolve, reject) {
      result.then(function (res) {
        try {
          this$1$1._actionSubscribers.filter(function (sub) {
            return sub.after;
          }).forEach(function (sub) {
            return sub.after(action, this$1$1.state);
          });
        } catch (e) {}
        resolve(res);
      }, function (error) {
        try {
          this$1$1._actionSubscribers.filter(function (sub) {
            return sub.error;
          }).forEach(function (sub) {
            return sub.error(action, this$1$1.state, error);
          });
        } catch (e) {}
        reject(error);
      });
    });
  };
  Store.prototype.subscribe = function subscribe(fn, options) {
    return genericSubscribe(fn, this._subscribers, options);
  };
  Store.prototype.subscribeAction = function subscribeAction(fn, options) {
    var subs = typeof fn === 'function' ? {
      before: fn
    } : fn;
    return genericSubscribe(subs, this._actionSubscribers, options);
  };
  Store.prototype.watch = function watch$1(getter, cb, options) {
    var this$1$1 = this;
    return watch(function () {
      return getter(this$1$1.state, this$1$1.getters);
    }, cb, Object.assign({}, options));
  };
  Store.prototype.replaceState = function replaceState(state) {
    var this$1$1 = this;
    this._withCommit(function () {
      this$1$1._state.data = state;
    });
  };
  Store.prototype.registerModule = function registerModule(path, rawModule, options) {
    if (options === void 0) options = {};
    if (typeof path === 'string') {
      path = [path];
    }
    this._modules.register(path, rawModule);
    installModule(this, this.state, path, this._modules.get(path), options.preserveState);
    // reset store to update getters...
    resetStoreState(this, this.state);
  };
  Store.prototype.unregisterModule = function unregisterModule(path) {
    var this$1$1 = this;
    if (typeof path === 'string') {
      path = [path];
    }
    this._modules.unregister(path);
    this._withCommit(function () {
      var parentState = getNestedState(this$1$1.state, path.slice(0, -1));
      delete parentState[path[path.length - 1]];
    });
    resetStore(this);
  };
  Store.prototype.hasModule = function hasModule(path) {
    if (typeof path === 'string') {
      path = [path];
    }
    return this._modules.isRegistered(path);
  };
  Store.prototype.hotUpdate = function hotUpdate(newOptions) {
    this._modules.update(newOptions);
    resetStore(this, true);
  };
  Store.prototype._withCommit = function _withCommit(fn) {
    var committing = this._committing;
    this._committing = true;
    fn();
    this._committing = committing;
  };
  Object.defineProperties(Store.prototype, prototypeAccessors);
  var isMergeableObject = function isMergeableObject(value) {
    return isNonNullObject(value) && !isSpecial(value);
  };
  function isNonNullObject(value) {
    return !!value && typeof value === 'object';
  }
  function isSpecial(value) {
    var stringValue = Object.prototype.toString.call(value);
    return stringValue === '[object RegExp]' || stringValue === '[object Date]' || isReactElement(value);
  }

  // see https://github.com/facebook/react/blob/b5ac963fb791d1298e7f396236383bc955f916c1/src/isomorphic/classic/element/ReactElement.js#L21-L25
  var canUseSymbol = typeof Symbol === 'function' && Symbol.for;
  var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for('react.element') : 0xeac7;
  function isReactElement(value) {
    return value.$$typeof === REACT_ELEMENT_TYPE;
  }
  function emptyTarget(val) {
    return Array.isArray(val) ? [] : {};
  }
  function cloneUnlessOtherwiseSpecified(value, options) {
    return options.clone !== false && options.isMergeableObject(value) ? deepmerge(emptyTarget(value), value, options) : value;
  }
  function defaultArrayMerge(target, source, options) {
    return target.concat(source).map(function (element) {
      return cloneUnlessOtherwiseSpecified(element, options);
    });
  }
  function getMergeFunction(key, options) {
    if (!options.customMerge) {
      return deepmerge;
    }
    var customMerge = options.customMerge(key);
    return typeof customMerge === 'function' ? customMerge : deepmerge;
  }
  function getEnumerableOwnPropertySymbols(target) {
    return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function (symbol) {
      return Object.propertyIsEnumerable.call(target, symbol);
    }) : [];
  }
  function getKeys(target) {
    return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
  }
  function propertyIsOnObject(object, property) {
    try {
      return property in object;
    } catch (_) {
      return false;
    }
  }

  // Protects from prototype poisoning and unexpected merging up the prototype chain.
  function propertyIsUnsafe(target, key) {
    return propertyIsOnObject(target, key) // Properties are safe to merge if they don't exist in the target yet,
    && !(Object.hasOwnProperty.call(target, key) // unsafe if they exist up the prototype chain,
    && Object.propertyIsEnumerable.call(target, key)); // and also unsafe if they're nonenumerable.
  }

  function mergeObject(target, source, options) {
    var destination = {};
    if (options.isMergeableObject(target)) {
      getKeys(target).forEach(function (key) {
        destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
      });
    }
    getKeys(source).forEach(function (key) {
      if (propertyIsUnsafe(target, key)) {
        return;
      }
      if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
        destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
      } else {
        destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
      }
    });
    return destination;
  }
  function deepmerge(target, source, options) {
    options = options || {};
    options.arrayMerge = options.arrayMerge || defaultArrayMerge;
    options.isMergeableObject = options.isMergeableObject || isMergeableObject;
    // cloneUnlessOtherwiseSpecified is added to `options` so that custom arrayMerge()
    // implementations can use it. The caller may not replace it.
    options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
    var sourceIsArray = Array.isArray(source);
    var targetIsArray = Array.isArray(target);
    var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;
    if (!sourceAndTargetTypesMatch) {
      return cloneUnlessOtherwiseSpecified(source, options);
    } else if (sourceIsArray) {
      return options.arrayMerge(target, source, options);
    } else {
      return mergeObject(target, source, options);
    }
  }
  deepmerge.all = function deepmergeAll(array, options) {
    if (!Array.isArray(array)) {
      throw new Error('first argument should be an array');
    }
    return array.reduce(function (prev, next) {
      return deepmerge(prev, next, options);
    }, {});
  };
  var deepmerge_1 = deepmerge;
  var cjs = deepmerge_1;

  /**
   * Created by championswimmer on 22/07/17.
   */
  var MockStorage;
  // @ts-ignore
  {
    MockStorage = /*#__PURE__*/function () {
      function MockStorage() {}
      var _proto6 = MockStorage.prototype;
      _proto6.key = function key(index) {
        return Object.keys(this)[index];
      };
      _proto6.setItem = function setItem(key, data) {
        this[key] = data.toString();
      };
      _proto6.getItem = function getItem(key) {
        return this[key];
      };
      _proto6.removeItem = function removeItem(key) {
        delete this[key];
      };
      _proto6.clear = function clear() {
        for (var _i4 = 0, _Object$keys = Object.keys(this); _i4 < _Object$keys.length; _i4++) {
          var key = _Object$keys[_i4];
          delete this[key];
        }
      };
      _createClass(MockStorage, [{
        key: "length",
        get: function get() {
          return Object.keys(this).length;
        }
      }]);
      return MockStorage;
    }();
  }

  // tslint:disable: variable-name
  var SimplePromiseQueue = /*#__PURE__*/function () {
    function SimplePromiseQueue() {
      this._queue = [];
      this._flushing = false;
    }
    var _proto7 = SimplePromiseQueue.prototype;
    _proto7.enqueue = function enqueue(promise) {
      this._queue.push(promise);
      if (!this._flushing) {
        return this.flushQueue();
      }
      return Promise.resolve();
    };
    _proto7.flushQueue = function flushQueue() {
      var _this22 = this;
      this._flushing = true;
      var chain = function chain() {
        var nextTask = _this22._queue.shift();
        if (nextTask) {
          return nextTask.then(chain);
        } else {
          _this22._flushing = false;
        }
      };
      return Promise.resolve(chain());
    };
    return SimplePromiseQueue;
  }();
  var options$1 = {
    replaceArrays: {
      arrayMerge: function arrayMerge(destinationArray, sourceArray, options) {
        return sourceArray;
      }
    },
    concatArrays: {
      arrayMerge: function arrayMerge(target, source, options) {
        return target.concat.apply(target, source);
      }
    }
  };
  function merge(into, from, mergeOption) {
    return cjs(into, from, options$1[mergeOption]);
  }
  var FlattedJSON = JSON;
  /**
   * A class that implements the vuex persistence.
   * @type S type of the 'state' inside the store (default: any)
   */
  var VuexPersistence =
  /**
   * Create a {@link VuexPersistence} object.
   * Use the <code>plugin</code> function of this class as a
   * Vuex plugin.
   * @param {PersistOptions} options
   */
  function VuexPersistence(options) {
    var _this23 = this;
    // tslint:disable-next-line:variable-name
    this._mutex = new SimplePromiseQueue();
    /**
     * Creates a subscriber on the store. automatically is used
     * when this is used a vuex plugin. Not for manual usage.
     * @param store
     */
    this.subscriber = function (store) {
      return function (handler) {
        return store.subscribe(handler);
      };
    };
    if (typeof options === 'undefined') options = {};
    this.key = options.key != null ? options.key : 'vuex';
    this.subscribed = false;
    this.supportCircular = options.supportCircular || false;
    if (this.supportCircular) {
      FlattedJSON = cjs$1;
    }
    this.mergeOption = options.mergeOption || 'replaceArrays';
    var localStorageLitmus = true;
    try {
      window.localStorage.getItem('');
    } catch (err) {
      localStorageLitmus = false;
    }
    /**
     * 1. First, prefer storage sent in optinos
     * 2. Otherwise, use window.localStorage if available
     * 3. Finally, try to use MockStorage
     * 4. None of above? Well we gotta fail.
     */
    if (options.storage) {
      this.storage = options.storage;
    } else if (localStorageLitmus) {
      this.storage = window.localStorage;
    } else if (MockStorage) {
      this.storage = new MockStorage();
    } else {
      throw new Error("Neither 'window' is defined, nor 'MockStorage' is available");
    }
    /**
     * How this works is -
     *  1. If there is options.reducer function, we use that, if not;
     *  2. We check options.modules;
     *    1. If there is no options.modules array, we use entire state in reducer
     *    2. Otherwise, we create a reducer that merges all those state modules that are
     *        defined in the options.modules[] array
     * @type {((state: S) => {}) | ((state: S) => S) | ((state: any) => {})}
     */
    this.reducer = options.reducer != null ? options.reducer : options.modules == null ? function (state) {
      return state;
    } : function (state) {
      return options.modules.reduce(function (a, i) {
        var _merge;
        return merge(a, (_merge = {}, _merge[i] = state[i], _merge), _this23.mergeOption);
      }, {/* start empty accumulator*/});
    };
    this.filter = options.filter || function (mutation) {
      return true;
    };
    this.strictMode = options.strictMode || false;
    this.RESTORE_MUTATION = function RESTORE_MUTATION(state, savedState) {
      var mergedState = merge(state, savedState || {}, this.mergeOption);
      for (var _i5 = 0, _Object$keys2 = Object.keys(mergedState); _i5 < _Object$keys2.length; _i5++) {
        var propertyName = _Object$keys2[_i5];
        this._vm.$set(state, propertyName, mergedState[propertyName]);
      }
    };
    this.asyncStorage = options.asyncStorage || false;
    if (this.asyncStorage) {
      /**
       * Async {@link #VuexPersistence.restoreState} implementation
       * @type {((key: string, storage?: Storage) =>
       *      (Promise<S> | S)) | ((key: string, storage: AsyncStorage) => Promise<any>)}
       */
      this.restoreState = options.restoreState != null ? options.restoreState : function (key, storage) {
        return storage.getItem(key).then(function (value) {
          return typeof value === 'string' // If string, parse, or else, just return
          ? _this23.supportCircular ? FlattedJSON.parse(value || '{}') : JSON.parse(value || '{}') : value || {};
        });
      };
      /**
       * Async {@link #VuexPersistence.saveState} implementation
       * @type {((key: string, state: {}, storage?: Storage) =>
       *    (Promise<void> | void)) | ((key: string, state: {}, storage?: Storage) => Promise<void>)}
       */
      this.saveState = options.saveState != null ? options.saveState : function (key, state, storage) {
        return storage.setItem(key,
        // Second argument is state _object_ if asyc storage, stringified otherwise
        // do not stringify the state if the storage type is async
        _this23.asyncStorage ? merge({}, state || {}, _this23.mergeOption) : _this23.supportCircular ? FlattedJSON.stringify(state) : JSON.stringify(state));
      };
      /**
       * Async version of plugin
       * @param {Store<S>} store
       */
      this.plugin = function (store) {
        /**
         * For async stores, we're capturing the Promise returned
         * by the `restoreState()` function in a `restored` property
         * on the store itself. This would allow app developers to
         * determine when and if the store's state has indeed been
         * refreshed. This approach was suggested by GitHub user @hotdogee.
         * See https://github.com/championswimmer/vuex-persist/pull/118#issuecomment-500914963
         * @since 2.1.0
         */
        store.restored = _this23.restoreState(_this23.key, _this23.storage).then(function (savedState) {
          /**
           * If in strict mode, do only via mutation
           */
          if (_this23.strictMode) {
            store.commit('RESTORE_MUTATION', savedState);
          } else {
            store.replaceState(merge(store.state, savedState || {}, _this23.mergeOption));
          }
          _this23.subscriber(store)(function (mutation, state) {
            if (_this23.filter(mutation)) {
              _this23._mutex.enqueue(_this23.saveState(_this23.key, _this23.reducer(state), _this23.storage));
            }
          });
          _this23.subscribed = true;
        });
      };
    } else {
      /**
       * Sync {@link #VuexPersistence.restoreState} implementation
       * @type {((key: string, storage?: Storage) =>
       *    (Promise<S> | S)) | ((key: string, storage: Storage) => (any | string | {}))}
       */
      this.restoreState = options.restoreState != null ? options.restoreState : function (key, storage) {
        var value = storage.getItem(key);
        if (typeof value === 'string') {
          // If string, parse, or else, just return
          return _this23.supportCircular ? FlattedJSON.parse(value || '{}') : JSON.parse(value || '{}');
        } else {
          return value || {};
        }
      };
      /**
       * Sync {@link #VuexPersistence.saveState} implementation
       * @type {((key: string, state: {}, storage?: Storage) =>
       *     (Promise<void> | void)) | ((key: string, state: {}, storage?: Storage) => Promise<void>)}
       */
      this.saveState = options.saveState != null ? options.saveState : function (key, state, storage) {
        return storage.setItem(key,
        // Second argument is state _object_ if localforage, stringified otherwise
        _this23.supportCircular ? FlattedJSON.stringify(state) : JSON.stringify(state));
      };
      /**
       * Sync version of plugin
       * @param {Store<S>} store
       */
      this.plugin = function (store) {
        var savedState = _this23.restoreState(_this23.key, _this23.storage);
        if (_this23.strictMode) {
          store.commit('RESTORE_MUTATION', savedState);
        } else {
          store.replaceState(merge(store.state, savedState || {}, _this23.mergeOption));
        }
        _this23.subscriber(store)(function (mutation, state) {
          if (_this23.filter(mutation)) {
            _this23.saveState(_this23.key, _this23.reducer(state), _this23.storage);
          }
        });
        _this23.subscribed = true;
      };
    }
  };
  var VuexPersistence$1 = VuexPersistence;

  // The options for persisting state
  var persistedStateOptions = {
    storage: window.sessionStorage,
    key: 'joomla.mediamanager',
    reducer: function reducer(state) {
      return {
        selectedDirectory: state.selectedDirectory,
        showInfoBar: state.showInfoBar,
        listView: state.listView,
        gridSize: state.gridSize,
        search: state.search,
        sortBy: state.sortBy,
        sortDirection: state.sortDirection
      };
    }
  };

  // Get the disks from joomla option storage
  var options = Joomla.getOptions('com_media', {});
  if (options.providers === undefined || options.providers.length === 0) {
    throw new TypeError('Media providers are not defined.');
  }

  /**
   * Get the drives
   *
   * @param  {Array}  adapterNames
   * @param  {String} provider
   *
   * @return {Array}
   */
  var getDrives = function getDrives(adapterNames, provider) {
    return adapterNames.map(function (name) {
      return {
        root: provider + "-" + name + ":/",
        displayName: name
      };
    });
  };

  // Load disks from options
  var loadedDisks = options.providers.map(function (disk) {
    return {
      displayName: disk.displayName,
      drives: getDrives(disk.adapterNames, disk.name)
    };
  });
  var defaultDisk = loadedDisks.find(function (disk) {
    return disk.drives.length > 0 && disk.drives[0] !== undefined;
  });
  if (!defaultDisk) {
    throw new TypeError('No default media drive was found');
  }
  var storedState = JSON.parse(persistedStateOptions.storage.getItem(persistedStateOptions.key));
  function setSession(path) {
    persistedStateOptions.storage.setItem(persistedStateOptions.key, JSON.stringify(Object.assign({}, storedState, {
      selectedDirectory: path
    })));
  }

  // Gracefully use the given path, the session storage state or fall back to sensible default
  function getCurrentPath() {
    var path = options.currentPath;

    // Set the path from the session when available
    if (!path && storedState && storedState.selectedDirectory) {
      path = storedState.selectedDirectory;
    }

    // No path available, use the root of the first drive
    if (!path) {
      setSession(defaultDisk.drives[0].root);
      return defaultDisk.drives[0].root;
    }

    // Get the fragments
    var fragment = path.split(':/');

    // Check that we have a drive
    if (!fragment.length) {
      setSession(defaultDisk.drives[0].root);
      return defaultDisk.drives[0].root;
    }
    var drivesTmp = Object.values(loadedDisks).map(function (drive) {
      return drive.drives;
    });

    // Drive doesn't exist
    if (!drivesTmp.flat().find(function (drive) {
      return drive.root.startsWith(fragment[0]);
    })) {
      setSession(defaultDisk.drives[0].root);
      return defaultDisk.drives[0].root;
    }

    // Session missmatch
    setSession(path);
    return path;
  }

  // The initial state
  var state = {
    // The general loading state
    isLoading: false,
    // Will hold the activated filesystem disks
    disks: loadedDisks,
    // The loaded directories
    directories: loadedDisks.map(function () {
      return {
        path: defaultDisk.drives[0].root,
        name: defaultDisk.displayName,
        directories: [],
        files: [],
        directory: null
      };
    }),
    // The loaded files
    files: [],
    // The selected disk. Providers are ordered by plugin ordering, so we set the first provider
    // in the list as the default provider and load first drive on it as default
    selectedDirectory: getCurrentPath(),
    // The currently selected items
    selectedItems: [],
    // The state of the infobar
    showInfoBar: false,
    // List view
    listView: 'grid',
    // The size of the grid items
    gridSize: 'md',
    // The state of confirm delete model
    showConfirmDeleteModal: false,
    // The state of create folder model
    showCreateFolderModal: false,
    // The state of preview model
    showPreviewModal: false,
    // The state of share model
    showShareModal: false,
    // The state of  model
    showRenameModal: false,
    // The preview item
    previewItem: null,
    // The Search Query
    search: '',
    // The sorting by
    sortBy: storedState && storedState.sortBy ? storedState.sortBy : 'name',
    // The sorting direction
    sortDirection: storedState && storedState.sortDirection ? storedState.sortDirection : 'asc'
  };

  // Sometimes we may need to compute derived state based on store state,
  // for example filtering through a list of items and counting them.
  /**
   * Get the currently selected directory
   * @param state
   * @returns {*}
   */
  var getSelectedDirectory = function getSelectedDirectory(state) {
    return state.directories.find(function (directory) {
      return directory.path === state.selectedDirectory;
    });
  };

  /**
   * Get the sudirectories of the currently selected directory
   * @param state
   *
   * @returns {Array|directories|{/}|computed.directories|*|Object}
   */
  var getSelectedDirectoryDirectories = function getSelectedDirectoryDirectories(state) {
    return state.directories.filter(function (directory) {
      return directory.directory === state.selectedDirectory;
    });
  };

  /**
   * Get the files of the currently selected directory
   * @param state
   *
   * @returns {Array|files|{}|FileList|*}
   */
  var getSelectedDirectoryFiles = function getSelectedDirectoryFiles(state) {
    return state.files.filter(function (file) {
      return file.directory === state.selectedDirectory;
    });
  };

  /**
   * Whether or not all items of the current directory are selected
   * @param state
   * @param getters
   * @returns Array
   */
  var getSelectedDirectoryContents = function getSelectedDirectoryContents(state, getters) {
    return [].concat(getters.getSelectedDirectoryDirectories, getters.getSelectedDirectoryFiles);
  };
  var getters = /*#__PURE__*/Object.freeze({
    __proto__: null,
    getSelectedDirectory: getSelectedDirectory,
    getSelectedDirectoryDirectories: getSelectedDirectoryDirectories,
    getSelectedDirectoryFiles: getSelectedDirectoryFiles,
    getSelectedDirectoryContents: getSelectedDirectoryContents
  });
  var updateUrlPath = function updateUrlPath(path) {
    var currentPath = path === null ? '' : path;
    var url = new URL(window.location.href);
    if (url.searchParams.has('path')) {
      window.history.pushState(null, '', url.href.replace(/\b(path=).*?(&|$)/, "$1" + currentPath + "$2"));
    } else {
      window.history.pushState(null, '', url.href + (url.href.indexOf('?') > 0 ? '&' : '?') + "path=" + currentPath);
    }
  };

  /**
   * Actions are similar to mutations, the difference being that:
   * Instead of mutating the state, actions commit mutations.
   * Actions can contain arbitrary asynchronous operations.
   */

  /**
   * Get contents of a directory from the api
   * @param context
   * @param payload
   */
  var getContents = function getContents(context, payload) {
    // Update the url
    updateUrlPath(payload);
    context.commit(SET_IS_LOADING, true);
    api.getContents(payload, false, false).then(function (contents) {
      context.commit(LOAD_CONTENTS_SUCCESS, contents);
      context.commit(UNSELECT_ALL_BROWSER_ITEMS);
      context.commit(SELECT_DIRECTORY, payload);
      context.commit(SET_IS_LOADING, false);
    }).catch(function (error) {
      // @todo error handling
      context.commit(SET_IS_LOADING, false);
      throw new Error(error);
    });
  };

  /**
   * Get the full contents of a directory
   * @param context
   * @param payload
   */
  var getFullContents = function getFullContents(context, payload) {
    context.commit(SET_IS_LOADING, true);
    api.getContents(payload.path, true, true).then(function (contents) {
      context.commit(LOAD_FULL_CONTENTS_SUCCESS, contents.files[0]);
      context.commit(SET_IS_LOADING, false);
    }).catch(function (error) {
      // @todo error handling
      context.commit(SET_IS_LOADING, false);
      throw new Error(error);
    });
  };

  /**
   * Download a file
   * @param context
   * @param payload
   */
  var download = function download(context, payload) {
    api.getContents(payload.path, false, true).then(function (contents) {
      var file = contents.files[0];

      // Download file
      var a = document.createElement('a');
      a.href = "data:" + file.mime_type + ";base64," + file.content;
      a.download = file.name;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }).catch(function (error) {
      throw new Error(error);
    });
  };

  /**
   * Toggle the selection state of an item
   * @param context
   * @param payload
   */
  var toggleBrowserItemSelect = function toggleBrowserItemSelect(context, payload) {
    var item = payload;
    var isSelected = context.state.selectedItems.some(function (selected) {
      return selected.path === item.path;
    });
    if (!isSelected) {
      context.commit(SELECT_BROWSER_ITEM, item);
    } else {
      context.commit(UNSELECT_BROWSER_ITEM, item);
    }
  };

  /**
   * Create a new folder
   * @param context
   * @param payload object with the new folder name and its parent directory
   */
  var createDirectory = function createDirectory(context, payload) {
    if (!api.canCreate) {
      return;
    }
    context.commit(SET_IS_LOADING, true);
    api.createDirectory(payload.name, payload.parent).then(function (folder) {
      context.commit(CREATE_DIRECTORY_SUCCESS, folder);
      context.commit(HIDE_CREATE_FOLDER_MODAL);
      context.commit(SET_IS_LOADING, false);
    }).catch(function (error) {
      // @todo error handling
      context.commit(SET_IS_LOADING, false);
      throw new Error(error);
    });
  };

  /**
   * Create a new folder
   * @param context
   * @param payload object with the new folder name and its parent directory
   */
  var uploadFile = function uploadFile(context, payload) {
    if (!api.canCreate) {
      return;
    }
    context.commit(SET_IS_LOADING, true);
    api.upload(payload.name, payload.parent, payload.content, payload.override || false).then(function (file) {
      context.commit(UPLOAD_SUCCESS, file);
      context.commit(SET_IS_LOADING, false);
    }).catch(function (error) {
      context.commit(SET_IS_LOADING, false);

      // Handle file exists
      if (error.status === 409) {
        if (notifications.ask(Translate.sprintf('COM_MEDIA_FILE_EXISTS_AND_OVERRIDE', payload.name), {})) {
          payload.override = true;
          uploadFile(context, payload);
        }
      }
    });
  };

  /**
   * Rename an item
   * @param context
   * @param payload object: the item and the new path
   */
  var renameItem = function renameItem(context, payload) {
    if (!api.canEdit) {
      return;
    }
    if (typeof payload.item.canEdit !== 'undefined' && payload.item.canEdit === false) {
      return;
    }
    context.commit(SET_IS_LOADING, true);
    api.rename(payload.item.path, payload.newPath).then(function (item) {
      context.commit(RENAME_SUCCESS, {
        item: item,
        oldPath: payload.item.path,
        newName: payload.newName
      });
      context.commit(HIDE_RENAME_MODAL);
      context.commit(SET_IS_LOADING, false);
    }).catch(function (error) {
      // @todo error handling
      context.commit(SET_IS_LOADING, false);
      throw new Error(error);
    });
  };

  /**
   * Delete the selected items
   * @param context
   */
  var deleteSelectedItems = function deleteSelectedItems(context) {
    if (!api.canDelete) {
      return;
    }
    context.commit(SET_IS_LOADING, true);
    // Get the selected items from the store
    var _context$state = context.state,
      selectedItems = _context$state.selectedItems,
      search = _context$state.search;
    if (selectedItems.length > 0) {
      selectedItems.forEach(function (item) {
        if (typeof item.canDelete !== 'undefined' && item.canDelete === false || search && !item.name.toLowerCase().includes(search.toLowerCase())) {
          return;
        }
        api.delete(item.path).then(function () {
          context.commit(DELETE_SUCCESS, item);
          context.commit(UNSELECT_ALL_BROWSER_ITEMS);
          context.commit(SET_IS_LOADING, false);
        }).catch(function (error) {
          // @todo error handling
          context.commit(SET_IS_LOADING, false);
          throw new Error(error);
        });
      });
    }
  };

  /**
   * Update item properties
   * @param context
   * @param payload object: the item, the width and the height
   */
  var updateItemProperties = function updateItemProperties(context, payload) {
    return context.commit(UPDATE_ITEM_PROPERTIES, payload);
  };
  var actions = /*#__PURE__*/Object.freeze({
    __proto__: null,
    getContents: getContents,
    getFullContents: getFullContents,
    download: download,
    toggleBrowserItemSelect: toggleBrowserItemSelect,
    createDirectory: createDirectory,
    uploadFile: uploadFile,
    renameItem: renameItem,
    deleteSelectedItems: deleteSelectedItems,
    updateItemProperties: updateItemProperties
  });

  // The only way to actually change state in a store is by committing a mutation.
  // Mutations are very similar to events: each mutation has a string type and a handler.
  // The handler function is where we perform actual state modifications,
  // and it will receive the state as the first argument.

  // The grid item sizes
  var gridItemSizes = ['sm', 'md', 'lg', 'xl'];
  var mutations = (_mutations = {}, _mutations[SELECT_DIRECTORY] = function (state, payload) {
    state.selectedDirectory = payload;
    state.search = '';
  }, _mutations[LOAD_CONTENTS_SUCCESS] = function (state, payload) {
    /**
     * Create a directory from a path
     * @param path
     */
    function directoryFromPath(path) {
      var parts = path.split('/');
      var directory = dirname(path);
      if (directory.indexOf(':', directory.length - 1) !== -1) {
        directory += '/';
      }
      return {
        path: path,
        name: parts[parts.length - 1],
        directories: [],
        files: [],
        directory: directory !== '.' ? directory : null,
        type: 'dir',
        mime_type: 'directory'
      };
    }

    /**
     * Create the directory structure
     * @param path
     */
    function createDirectoryStructureFromPath(path) {
      var exists = state.directories.some(function (existing) {
        return existing.path === path;
      });
      if (!exists) {
        var directory = directoryFromPath(path);

        // Add the sub directories and files
        directory.directories = state.directories.filter(function (existing) {
          return existing.directory === directory.path;
        }).map(function (existing) {
          return existing.path;
        });

        // Add the directory
        state.directories.push(directory);
        if (directory.directory) {
          createDirectoryStructureFromPath(directory.directory);
        }
      }
    }

    /**
     * Add a directory
     * @param unused
     * @param directory
     */
    function addDirectory(unused, directory) {
      var parentDirectory = state.directories.find(function (existing) {
        return existing.path === directory.directory;
      });
      var parentDirectoryIndex = state.directories.indexOf(parentDirectory);
      var index = state.directories.findIndex(function (existing) {
        return existing.path === directory.path;
      });
      if (index === -1) {
        index = state.directories.length;
      }

      // Add the directory
      state.directories.splice(index, 1, directory);

      // Update the relation to the parent directory
      if (parentDirectoryIndex !== -1) {
        state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
          directories: [].concat(parentDirectory.directories, [directory.path])
        }));
      }
    }

    /**
     * Add a file
     * @param unused
     * @param directory
     */
    function addFile(unused, file) {
      var parentDirectory = state.directories.find(function (directory) {
        return directory.path === file.directory;
      });
      var parentDirectoryIndex = state.directories.indexOf(parentDirectory);
      var index = state.files.findIndex(function (existing) {
        return existing.path === file.path;
      });
      if (index === -1) {
        index = state.files.length;
      }

      // Add the file
      state.files.splice(index, 1, file);

      // Update the relation to the parent directory
      if (parentDirectoryIndex !== -1) {
        state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
          files: [].concat(parentDirectory.files, [file.path])
        }));
      }
    }

    // Create the parent directory structure if it does not exist
    createDirectoryStructureFromPath(state.selectedDirectory);

    // Add directories
    payload.directories.forEach(function (directory) {
      return addDirectory(null, directory);
    });

    // Add files
    payload.files.forEach(function (file) {
      return addFile(null, file);
    });
  }, _mutations[UPLOAD_SUCCESS] = function (state, payload) {
    var file = payload;
    var isNew = !state.files.some(function (existing) {
      return existing.path === file.path;
    });

    // @todo handle file_exists
    if (isNew) {
      var parentDirectory = state.directories.find(function (existing) {
        return existing.path === file.directory;
      });
      var parentDirectoryIndex = state.directories.indexOf(parentDirectory);

      // Add the new file to the files array
      state.files.push(file);

      // Update the relation to the parent directory
      state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
        files: [].concat(parentDirectory.files, [file.path])
      }));
    }
  }, _mutations[CREATE_DIRECTORY_SUCCESS] = function (state, payload) {
    var directory = payload;
    var isNew = !state.directories.some(function (existing) {
      return existing.path === directory.path;
    });
    if (isNew) {
      var parentDirectory = state.directories.find(function (existing) {
        return existing.path === directory.directory;
      });
      var parentDirectoryIndex = state.directories.indexOf(parentDirectory);

      // Add the new directory to the directory
      state.directories.push(directory);

      // Update the relation to the parent directory
      state.directories.splice(parentDirectoryIndex, 1, Object.assign({}, parentDirectory, {
        directories: [].concat(parentDirectory.directories, [directory.path])
      }));
    }
  }, _mutations[RENAME_SUCCESS] = function (state, payload) {
    state.selectedItems[state.selectedItems.length - 1].name = payload.newName;
    var item = payload.item;
    var oldPath = payload.oldPath;
    if (item.type === 'file') {
      var index = state.files.findIndex(function (file) {
        return file.path === oldPath;
      });
      state.files.splice(index, 1, item);
    } else {
      var _index = state.directories.findIndex(function (directory) {
        return directory.path === oldPath;
      });
      state.directories.splice(_index, 1, item);
    }
  }, _mutations[DELETE_SUCCESS] = function (state, payload) {
    var item = payload;

    // Delete file
    if (item.type === 'file') {
      state.files.splice(state.files.findIndex(function (file) {
        return file.path === item.path;
      }), 1);
    }

    // Delete dir
    if (item.type === 'dir') {
      state.directories.splice(state.directories.findIndex(function (directory) {
        return directory.path === item.path;
      }), 1);
    }
  }, _mutations[SELECT_BROWSER_ITEM] = function (state, payload) {
    state.selectedItems.push(payload);
  }, _mutations[SELECT_BROWSER_ITEMS] = function (state, payload) {
    state.selectedItems = payload;
  }, _mutations[UNSELECT_BROWSER_ITEM] = function (state, payload) {
    var item = payload;
    state.selectedItems.splice(state.selectedItems.findIndex(function (selectedItem) {
      return selectedItem.path === item.path;
    }), 1);
  }, _mutations[UNSELECT_ALL_BROWSER_ITEMS] = function (state) {
    state.selectedItems = [];
  }, _mutations[SHOW_CREATE_FOLDER_MODAL] = function (state) {
    state.showCreateFolderModal = true;
  }, _mutations[HIDE_CREATE_FOLDER_MODAL] = function (state) {
    state.showCreateFolderModal = false;
  }, _mutations[SHOW_INFOBAR] = function (state) {
    state.showInfoBar = true;
  }, _mutations[HIDE_INFOBAR] = function (state) {
    state.showInfoBar = false;
  }, _mutations[CHANGE_LIST_VIEW] = function (state, view) {
    state.listView = view;
  }, _mutations[LOAD_FULL_CONTENTS_SUCCESS] = function (state, payload) {
    state.previewItem = payload;
  }, _mutations[SHOW_PREVIEW_MODAL] = function (state) {
    state.showPreviewModal = true;
  }, _mutations[HIDE_PREVIEW_MODAL] = function (state) {
    state.showPreviewModal = false;
  }, _mutations[SET_IS_LOADING] = function (state, payload) {
    state.isLoading = payload;
  }, _mutations[SHOW_RENAME_MODAL] = function (state) {
    state.showRenameModal = true;
  }, _mutations[HIDE_RENAME_MODAL] = function (state) {
    state.showRenameModal = false;
  }, _mutations[SHOW_SHARE_MODAL] = function (state) {
    state.showShareModal = true;
  }, _mutations[HIDE_SHARE_MODAL] = function (state) {
    state.showShareModal = false;
  }, _mutations[INCREASE_GRID_SIZE] = function (state) {
    var currentSizeIndex = gridItemSizes.indexOf(state.gridSize);
    if (currentSizeIndex >= 0 && currentSizeIndex < gridItemSizes.length - 1) {
      state.gridSize = gridItemSizes[currentSizeIndex + 1];
    }
  }, _mutations[DECREASE_GRID_SIZE] = function (state) {
    var currentSizeIndex = gridItemSizes.indexOf(state.gridSize);
    if (currentSizeIndex > 0 && currentSizeIndex < gridItemSizes.length) {
      state.gridSize = gridItemSizes[currentSizeIndex - 1];
    }
  }, _mutations[SET_SEARCH_QUERY] = function (state, query) {
    state.search = query;
  }, _mutations[SHOW_CONFIRM_DELETE_MODAL] = function (state) {
    state.showConfirmDeleteModal = true;
  }, _mutations[HIDE_CONFIRM_DELETE_MODAL] = function (state) {
    state.showConfirmDeleteModal = false;
  }, _mutations[UPDATE_ITEM_PROPERTIES] = function (state, payload) {
    var item = payload.item,
      width = payload.width,
      height = payload.height;
    var index = state.files.findIndex(function (file) {
      return file.path === item.path;
    });
    state.files[index].width = width;
    state.files[index].height = height;
  }, _mutations[UPDATE_SORT_BY] = function (state, payload) {
    state.sortBy = payload;
  }, _mutations[UPDATE_SORT_DIRECTION] = function (state, payload) {
    state.sortDirection = payload === 'asc' ? 'asc' : 'desc';
  }, _mutations);

  // A Vuex instance is created by combining the state, mutations, actions, and getters.
  var store = createStore({
    state: state,
    getters: getters,
    actions: actions,
    mutations: mutations,
    plugins: [new VuexPersistence$1(persistedStateOptions).plugin],
    strict: "production" !== 'production'
  });

  // Register MediaManager namespace
  window.MediaManager = window.MediaManager || {};
  // Register the media manager event bus
  window.MediaManager.Event = new Event$1();

  // Create the Vue app instance
  createApp(script).use(store).use(Translate).mount('#com-media');

  return mediaManager;

})();
