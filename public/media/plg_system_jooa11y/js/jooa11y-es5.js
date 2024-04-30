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

  var top = 'top';
  var bottom = 'bottom';
  var right = 'right';
  var left = 'left';
  var auto = 'auto';
  var basePlacements = [top, bottom, right, left];
  var start = 'start';
  var end = 'end';
  var clippingParents = 'clippingParents';
  var viewport = 'viewport';
  var popper = 'popper';
  var reference = 'reference';
  var variationPlacements = /*#__PURE__*/basePlacements.reduce(function (acc, placement) {
    return acc.concat([placement + "-" + start, placement + "-" + end]);
  }, []);
  var placements = /*#__PURE__*/[].concat(basePlacements, [auto]).reduce(function (acc, placement) {
    return acc.concat([placement, placement + "-" + start, placement + "-" + end]);
  }, []); // modifiers that need to read the DOM

  var beforeRead = 'beforeRead';
  var read = 'read';
  var afterRead = 'afterRead'; // pure-logic modifiers

  var beforeMain = 'beforeMain';
  var main = 'main';
  var afterMain = 'afterMain'; // modifier with the purpose to write to the DOM (or write into a framework state)

  var beforeWrite = 'beforeWrite';
  var write = 'write';
  var afterWrite = 'afterWrite';
  var modifierPhases = [beforeRead, read, afterRead, beforeMain, main, afterMain, beforeWrite, write, afterWrite];
  function getNodeName(element) {
    return element ? (element.nodeName || '').toLowerCase() : null;
  }
  function getWindow(node) {
    if (node == null) {
      return window;
    }
    if (node.toString() !== '[object Window]') {
      var ownerDocument = node.ownerDocument;
      return ownerDocument ? ownerDocument.defaultView || window : window;
    }
    return node;
  }
  function isElement$1(node) {
    var OwnElement = getWindow(node).Element;
    return node instanceof OwnElement || node instanceof Element;
  }
  function isHTMLElement(node) {
    var OwnElement = getWindow(node).HTMLElement;
    return node instanceof OwnElement || node instanceof HTMLElement;
  }
  function isShadowRoot(node) {
    // IE 11 has no ShadowRoot
    if (typeof ShadowRoot === 'undefined') {
      return false;
    }
    var OwnElement = getWindow(node).ShadowRoot;
    return node instanceof OwnElement || node instanceof ShadowRoot;
  }

  // and applies them to the HTMLElements such as popper and arrow

  function applyStyles(_ref) {
    var state = _ref.state;
    Object.keys(state.elements).forEach(function (name) {
      var style = state.styles[name] || {};
      var attributes = state.attributes[name] || {};
      var element = state.elements[name]; // arrow is optional + virtual elements

      if (!isHTMLElement(element) || !getNodeName(element)) {
        return;
      } // Flow doesn't support to extend this property, but it's the most
      // effective way to apply styles to an HTMLElement
      // $FlowFixMe[cannot-write]

      Object.assign(element.style, style);
      Object.keys(attributes).forEach(function (name) {
        var value = attributes[name];
        if (value === false) {
          element.removeAttribute(name);
        } else {
          element.setAttribute(name, value === true ? '' : value);
        }
      });
    });
  }
  function effect$2(_ref2) {
    var state = _ref2.state;
    var initialStyles = {
      popper: {
        position: state.options.strategy,
        left: '0',
        top: '0',
        margin: '0'
      },
      arrow: {
        position: 'absolute'
      },
      reference: {}
    };
    Object.assign(state.elements.popper.style, initialStyles.popper);
    state.styles = initialStyles;
    if (state.elements.arrow) {
      Object.assign(state.elements.arrow.style, initialStyles.arrow);
    }
    return function () {
      Object.keys(state.elements).forEach(function (name) {
        var element = state.elements[name];
        var attributes = state.attributes[name] || {};
        var styleProperties = Object.keys(state.styles.hasOwnProperty(name) ? state.styles[name] : initialStyles[name]); // Set all values to an empty string to unset them

        var style = styleProperties.reduce(function (style, property) {
          style[property] = '';
          return style;
        }, {}); // arrow is optional + virtual elements

        if (!isHTMLElement(element) || !getNodeName(element)) {
          return;
        }
        Object.assign(element.style, style);
        Object.keys(attributes).forEach(function (attribute) {
          element.removeAttribute(attribute);
        });
      });
    };
  } // eslint-disable-next-line import/no-unused-modules

  var applyStyles$1 = {
    name: 'applyStyles',
    enabled: true,
    phase: 'write',
    fn: applyStyles,
    effect: effect$2,
    requires: ['computeStyles']
  };
  function getBasePlacement$1(placement) {
    return placement.split('-')[0];
  }
  var max = Math.max;
  var min = Math.min;
  var round = Math.round;
  function getBoundingClientRect(element, includeScale) {
    if (includeScale === void 0) {
      includeScale = false;
    }
    var rect = element.getBoundingClientRect();
    var scaleX = 1;
    var scaleY = 1;
    if (isHTMLElement(element) && includeScale) {
      var offsetHeight = element.offsetHeight;
      var offsetWidth = element.offsetWidth; // Do not attempt to divide by 0, otherwise we get `Infinity` as scale
      // Fallback to 1 in case both values are `0`

      if (offsetWidth > 0) {
        scaleX = round(rect.width) / offsetWidth || 1;
      }
      if (offsetHeight > 0) {
        scaleY = round(rect.height) / offsetHeight || 1;
      }
    }
    return {
      width: rect.width / scaleX,
      height: rect.height / scaleY,
      top: rect.top / scaleY,
      right: rect.right / scaleX,
      bottom: rect.bottom / scaleY,
      left: rect.left / scaleX,
      x: rect.left / scaleX,
      y: rect.top / scaleY
    };
  }

  // means it doesn't take into account transforms.

  function getLayoutRect(element) {
    var clientRect = getBoundingClientRect(element); // Use the clientRect sizes if it's not been transformed.
    // Fixes https://github.com/popperjs/popper-core/issues/1223

    var width = element.offsetWidth;
    var height = element.offsetHeight;
    if (Math.abs(clientRect.width - width) <= 1) {
      width = clientRect.width;
    }
    if (Math.abs(clientRect.height - height) <= 1) {
      height = clientRect.height;
    }
    return {
      x: element.offsetLeft,
      y: element.offsetTop,
      width: width,
      height: height
    };
  }
  function contains(parent, child) {
    var rootNode = child.getRootNode && child.getRootNode(); // First, attempt with faster native method

    if (parent.contains(child)) {
      return true;
    } // then fallback to custom implementation with Shadow DOM support
    else if (rootNode && isShadowRoot(rootNode)) {
      var next = child;
      do {
        if (next && parent.isSameNode(next)) {
          return true;
        } // $FlowFixMe[prop-missing]: need a better way to handle this...

        next = next.parentNode || next.host;
      } while (next);
    } // Give up, the result is false

    return false;
  }
  function getComputedStyle$1(element) {
    return getWindow(element).getComputedStyle(element);
  }
  function isTableElement(element) {
    return ['table', 'td', 'th'].indexOf(getNodeName(element)) >= 0;
  }
  function getDocumentElement(element) {
    // $FlowFixMe[incompatible-return]: assume body is always available
    return ((isElement$1(element) ? element.ownerDocument :
    // $FlowFixMe[prop-missing]
    element.document) || window.document).documentElement;
  }
  function getParentNode(element) {
    if (getNodeName(element) === 'html') {
      return element;
    }
    return (
      // this is a quicker (but less type safe) way to save quite some bytes from the bundle
      // $FlowFixMe[incompatible-return]
      // $FlowFixMe[prop-missing]
      element.assignedSlot ||
      // step into the shadow DOM of the parent of a slotted node
      element.parentNode || (
      // DOM Element detected
      isShadowRoot(element) ? element.host : null) ||
      // ShadowRoot detected
      // $FlowFixMe[incompatible-call]: HTMLElement is a Node
      getDocumentElement(element) // fallback
    );
  }

  function getTrueOffsetParent(element) {
    if (!isHTMLElement(element) ||
    // https://github.com/popperjs/popper-core/issues/837
    getComputedStyle$1(element).position === 'fixed') {
      return null;
    }
    return element.offsetParent;
  } // `.offsetParent` reports `null` for fixed elements, while absolute elements
  // return the containing block

  function getContainingBlock(element) {
    var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') !== -1;
    var isIE = navigator.userAgent.indexOf('Trident') !== -1;
    if (isIE && isHTMLElement(element)) {
      // In IE 9, 10 and 11 fixed elements containing block is always established by the viewport
      var elementCss = getComputedStyle$1(element);
      if (elementCss.position === 'fixed') {
        return null;
      }
    }
    var currentNode = getParentNode(element);
    while (isHTMLElement(currentNode) && ['html', 'body'].indexOf(getNodeName(currentNode)) < 0) {
      var css = getComputedStyle$1(currentNode); // This is non-exhaustive but covers the most common CSS properties that
      // create a containing block.
      // https://developer.mozilla.org/en-US/docs/Web/CSS/Containing_block#identifying_the_containing_block

      if (css.transform !== 'none' || css.perspective !== 'none' || css.contain === 'paint' || ['transform', 'perspective'].indexOf(css.willChange) !== -1 || isFirefox && css.willChange === 'filter' || isFirefox && css.filter && css.filter !== 'none') {
        return currentNode;
      } else {
        currentNode = currentNode.parentNode;
      }
    }
    return null;
  } // Gets the closest ancestor positioned element. Handles some edge cases,
  // such as table ancestors and cross browser bugs.

  function getOffsetParent(element) {
    var window = getWindow(element);
    var offsetParent = getTrueOffsetParent(element);
    while (offsetParent && isTableElement(offsetParent) && getComputedStyle$1(offsetParent).position === 'static') {
      offsetParent = getTrueOffsetParent(offsetParent);
    }
    if (offsetParent && (getNodeName(offsetParent) === 'html' || getNodeName(offsetParent) === 'body' && getComputedStyle$1(offsetParent).position === 'static')) {
      return window;
    }
    return offsetParent || getContainingBlock(element) || window;
  }
  function getMainAxisFromPlacement(placement) {
    return ['top', 'bottom'].indexOf(placement) >= 0 ? 'x' : 'y';
  }
  function within(min$1, value, max$1) {
    return max(min$1, min(value, max$1));
  }
  function withinMaxClamp(min, value, max) {
    var v = within(min, value, max);
    return v > max ? max : v;
  }
  function getFreshSideObject() {
    return {
      top: 0,
      right: 0,
      bottom: 0,
      left: 0
    };
  }
  function mergePaddingObject(paddingObject) {
    return Object.assign({}, getFreshSideObject(), paddingObject);
  }
  function expandToHashMap(value, keys) {
    return keys.reduce(function (hashMap, key) {
      hashMap[key] = value;
      return hashMap;
    }, {});
  }
  var toPaddingObject = function toPaddingObject(padding, state) {
    padding = typeof padding === 'function' ? padding(Object.assign({}, state.rects, {
      placement: state.placement
    })) : padding;
    return mergePaddingObject(typeof padding !== 'number' ? padding : expandToHashMap(padding, basePlacements));
  };
  function arrow(_ref) {
    var _state$modifiersData$;
    var state = _ref.state,
      name = _ref.name,
      options = _ref.options;
    var arrowElement = state.elements.arrow;
    var popperOffsets = state.modifiersData.popperOffsets;
    var basePlacement = getBasePlacement$1(state.placement);
    var axis = getMainAxisFromPlacement(basePlacement);
    var isVertical = [left, right].indexOf(basePlacement) >= 0;
    var len = isVertical ? 'height' : 'width';
    if (!arrowElement || !popperOffsets) {
      return;
    }
    var paddingObject = toPaddingObject(options.padding, state);
    var arrowRect = getLayoutRect(arrowElement);
    var minProp = axis === 'y' ? top : left;
    var maxProp = axis === 'y' ? bottom : right;
    var endDiff = state.rects.reference[len] + state.rects.reference[axis] - popperOffsets[axis] - state.rects.popper[len];
    var startDiff = popperOffsets[axis] - state.rects.reference[axis];
    var arrowOffsetParent = getOffsetParent(arrowElement);
    var clientSize = arrowOffsetParent ? axis === 'y' ? arrowOffsetParent.clientHeight || 0 : arrowOffsetParent.clientWidth || 0 : 0;
    var centerToReference = endDiff / 2 - startDiff / 2; // Make sure the arrow doesn't overflow the popper if the center point is
    // outside of the popper bounds

    var min = paddingObject[minProp];
    var max = clientSize - arrowRect[len] - paddingObject[maxProp];
    var center = clientSize / 2 - arrowRect[len] / 2 + centerToReference;
    var offset = within(min, center, max); // Prevents breaking syntax highlighting...

    var axisProp = axis;
    state.modifiersData[name] = (_state$modifiersData$ = {}, _state$modifiersData$[axisProp] = offset, _state$modifiersData$.centerOffset = offset - center, _state$modifiersData$);
  }
  function effect$1(_ref2) {
    var state = _ref2.state,
      options = _ref2.options;
    var _options$element = options.element,
      arrowElement = _options$element === void 0 ? '[data-popper-arrow]' : _options$element;
    if (arrowElement == null) {
      return;
    } // CSS selector

    if (typeof arrowElement === 'string') {
      arrowElement = state.elements.popper.querySelector(arrowElement);
      if (!arrowElement) {
        return;
      }
    }
    if (!contains(state.elements.popper, arrowElement)) {
      return;
    }
    state.elements.arrow = arrowElement;
  } // eslint-disable-next-line import/no-unused-modules

  var arrow$1 = {
    name: 'arrow',
    enabled: true,
    phase: 'main',
    fn: arrow,
    effect: effect$1,
    requires: ['popperOffsets'],
    requiresIfExists: ['preventOverflow']
  };
  function getVariation(placement) {
    return placement.split('-')[1];
  }
  var unsetSides = {
    top: 'auto',
    right: 'auto',
    bottom: 'auto',
    left: 'auto'
  }; // Round the offsets to the nearest suitable subpixel based on the DPR.
  // Zooming can change the DPR, but it seems to report a value that will
  // cleanly divide the values into the appropriate subpixels.

  function roundOffsetsByDPR(_ref) {
    var x = _ref.x,
      y = _ref.y;
    var win = window;
    var dpr = win.devicePixelRatio || 1;
    return {
      x: round(x * dpr) / dpr || 0,
      y: round(y * dpr) / dpr || 0
    };
  }
  function mapToStyles(_ref2) {
    var _Object$assign2;
    var popper = _ref2.popper,
      popperRect = _ref2.popperRect,
      placement = _ref2.placement,
      variation = _ref2.variation,
      offsets = _ref2.offsets,
      position = _ref2.position,
      gpuAcceleration = _ref2.gpuAcceleration,
      adaptive = _ref2.adaptive,
      roundOffsets = _ref2.roundOffsets,
      isFixed = _ref2.isFixed;
    var _offsets$x = offsets.x,
      x = _offsets$x === void 0 ? 0 : _offsets$x,
      _offsets$y = offsets.y,
      y = _offsets$y === void 0 ? 0 : _offsets$y;
    var _ref3 = typeof roundOffsets === 'function' ? roundOffsets({
      x: x,
      y: y
    }) : {
      x: x,
      y: y
    };
    x = _ref3.x;
    y = _ref3.y;
    var hasX = offsets.hasOwnProperty('x');
    var hasY = offsets.hasOwnProperty('y');
    var sideX = left;
    var sideY = top;
    var win = window;
    if (adaptive) {
      var offsetParent = getOffsetParent(popper);
      var heightProp = 'clientHeight';
      var widthProp = 'clientWidth';
      if (offsetParent === getWindow(popper)) {
        offsetParent = getDocumentElement(popper);
        if (getComputedStyle$1(offsetParent).position !== 'static' && position === 'absolute') {
          heightProp = 'scrollHeight';
          widthProp = 'scrollWidth';
        }
      } // $FlowFixMe[incompatible-cast]: force type refinement, we compare offsetParent with window above, but Flow doesn't detect it

      offsetParent = offsetParent;
      if (placement === top || (placement === left || placement === right) && variation === end) {
        sideY = bottom;
        var offsetY = isFixed && win.visualViewport ? win.visualViewport.height :
        // $FlowFixMe[prop-missing]
        offsetParent[heightProp];
        y -= offsetY - popperRect.height;
        y *= gpuAcceleration ? 1 : -1;
      }
      if (placement === left || (placement === top || placement === bottom) && variation === end) {
        sideX = right;
        var offsetX = isFixed && win.visualViewport ? win.visualViewport.width :
        // $FlowFixMe[prop-missing]
        offsetParent[widthProp];
        x -= offsetX - popperRect.width;
        x *= gpuAcceleration ? 1 : -1;
      }
    }
    var commonStyles = Object.assign({
      position: position
    }, adaptive && unsetSides);
    var _ref4 = roundOffsets === true ? roundOffsetsByDPR({
      x: x,
      y: y
    }) : {
      x: x,
      y: y
    };
    x = _ref4.x;
    y = _ref4.y;
    if (gpuAcceleration) {
      var _Object$assign;
      return Object.assign({}, commonStyles, (_Object$assign = {}, _Object$assign[sideY] = hasY ? '0' : '', _Object$assign[sideX] = hasX ? '0' : '', _Object$assign.transform = (win.devicePixelRatio || 1) <= 1 ? "translate(" + x + "px, " + y + "px)" : "translate3d(" + x + "px, " + y + "px, 0)", _Object$assign));
    }
    return Object.assign({}, commonStyles, (_Object$assign2 = {}, _Object$assign2[sideY] = hasY ? y + "px" : '', _Object$assign2[sideX] = hasX ? x + "px" : '', _Object$assign2.transform = '', _Object$assign2));
  }
  function computeStyles(_ref5) {
    var state = _ref5.state,
      options = _ref5.options;
    var _options$gpuAccelerat = options.gpuAcceleration,
      gpuAcceleration = _options$gpuAccelerat === void 0 ? true : _options$gpuAccelerat,
      _options$adaptive = options.adaptive,
      adaptive = _options$adaptive === void 0 ? true : _options$adaptive,
      _options$roundOffsets = options.roundOffsets,
      roundOffsets = _options$roundOffsets === void 0 ? true : _options$roundOffsets;
    var commonStyles = {
      placement: getBasePlacement$1(state.placement),
      variation: getVariation(state.placement),
      popper: state.elements.popper,
      popperRect: state.rects.popper,
      gpuAcceleration: gpuAcceleration,
      isFixed: state.options.strategy === 'fixed'
    };
    if (state.modifiersData.popperOffsets != null) {
      state.styles.popper = Object.assign({}, state.styles.popper, mapToStyles(Object.assign({}, commonStyles, {
        offsets: state.modifiersData.popperOffsets,
        position: state.options.strategy,
        adaptive: adaptive,
        roundOffsets: roundOffsets
      })));
    }
    if (state.modifiersData.arrow != null) {
      state.styles.arrow = Object.assign({}, state.styles.arrow, mapToStyles(Object.assign({}, commonStyles, {
        offsets: state.modifiersData.arrow,
        position: 'absolute',
        adaptive: false,
        roundOffsets: roundOffsets
      })));
    }
    state.attributes.popper = Object.assign({}, state.attributes.popper, {
      'data-popper-placement': state.placement
    });
  } // eslint-disable-next-line import/no-unused-modules

  var computeStyles$1 = {
    name: 'computeStyles',
    enabled: true,
    phase: 'beforeWrite',
    fn: computeStyles,
    data: {}
  };
  var passive = {
    passive: true
  };
  function effect(_ref) {
    var state = _ref.state,
      instance = _ref.instance,
      options = _ref.options;
    var _options$scroll = options.scroll,
      scroll = _options$scroll === void 0 ? true : _options$scroll,
      _options$resize = options.resize,
      resize = _options$resize === void 0 ? true : _options$resize;
    var window = getWindow(state.elements.popper);
    var scrollParents = [].concat(state.scrollParents.reference, state.scrollParents.popper);
    if (scroll) {
      scrollParents.forEach(function (scrollParent) {
        scrollParent.addEventListener('scroll', instance.update, passive);
      });
    }
    if (resize) {
      window.addEventListener('resize', instance.update, passive);
    }
    return function () {
      if (scroll) {
        scrollParents.forEach(function (scrollParent) {
          scrollParent.removeEventListener('scroll', instance.update, passive);
        });
      }
      if (resize) {
        window.removeEventListener('resize', instance.update, passive);
      }
    };
  } // eslint-disable-next-line import/no-unused-modules

  var eventListeners = {
    name: 'eventListeners',
    enabled: true,
    phase: 'write',
    fn: function fn() {},
    effect: effect,
    data: {}
  };
  var hash$1 = {
    left: 'right',
    right: 'left',
    bottom: 'top',
    top: 'bottom'
  };
  function getOppositePlacement(placement) {
    return placement.replace(/left|right|bottom|top/g, function (matched) {
      return hash$1[matched];
    });
  }
  var hash = {
    start: 'end',
    end: 'start'
  };
  function getOppositeVariationPlacement(placement) {
    return placement.replace(/start|end/g, function (matched) {
      return hash[matched];
    });
  }
  function getWindowScroll(node) {
    var win = getWindow(node);
    var scrollLeft = win.pageXOffset;
    var scrollTop = win.pageYOffset;
    return {
      scrollLeft: scrollLeft,
      scrollTop: scrollTop
    };
  }
  function getWindowScrollBarX(element) {
    // If <html> has a CSS width greater than the viewport, then this will be
    // incorrect for RTL.
    // Popper 1 is broken in this case and never had a bug report so let's assume
    // it's not an issue. I don't think anyone ever specifies width on <html>
    // anyway.
    // Browsers where the left scrollbar doesn't cause an issue report `0` for
    // this (e.g. Edge 2019, IE11, Safari)
    return getBoundingClientRect(getDocumentElement(element)).left + getWindowScroll(element).scrollLeft;
  }
  function getViewportRect(element) {
    var win = getWindow(element);
    var html = getDocumentElement(element);
    var visualViewport = win.visualViewport;
    var width = html.clientWidth;
    var height = html.clientHeight;
    var x = 0;
    var y = 0; // NB: This isn't supported on iOS <= 12. If the keyboard is open, the popper
    // can be obscured underneath it.
    // Also, `html.clientHeight` adds the bottom bar height in Safari iOS, even
    // if it isn't open, so if this isn't available, the popper will be detected
    // to overflow the bottom of the screen too early.

    if (visualViewport) {
      width = visualViewport.width;
      height = visualViewport.height; // Uses Layout Viewport (like Chrome; Safari does not currently)
      // In Chrome, it returns a value very close to 0 (+/-) but contains rounding
      // errors due to floating point numbers, so we need to check precision.
      // Safari returns a number <= 0, usually < -1 when pinch-zoomed
      // Feature detection fails in mobile emulation mode in Chrome.
      // Math.abs(win.innerWidth / visualViewport.scale - visualViewport.width) <
      // 0.001
      // Fallback here: "Not Safari" userAgent

      if (!/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
        x = visualViewport.offsetLeft;
        y = visualViewport.offsetTop;
      }
    }
    return {
      width: width,
      height: height,
      x: x + getWindowScrollBarX(element),
      y: y
    };
  }

  // of the `<html>` and `<body>` rect bounds if horizontally scrollable

  function getDocumentRect(element) {
    var _element$ownerDocumen;
    var html = getDocumentElement(element);
    var winScroll = getWindowScroll(element);
    var body = (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body;
    var width = max(html.scrollWidth, html.clientWidth, body ? body.scrollWidth : 0, body ? body.clientWidth : 0);
    var height = max(html.scrollHeight, html.clientHeight, body ? body.scrollHeight : 0, body ? body.clientHeight : 0);
    var x = -winScroll.scrollLeft + getWindowScrollBarX(element);
    var y = -winScroll.scrollTop;
    if (getComputedStyle$1(body || html).direction === 'rtl') {
      x += max(html.clientWidth, body ? body.clientWidth : 0) - width;
    }
    return {
      width: width,
      height: height,
      x: x,
      y: y
    };
  }
  function isScrollParent(element) {
    // Firefox wants us to check `-x` and `-y` variations as well
    var _getComputedStyle = getComputedStyle$1(element),
      overflow = _getComputedStyle.overflow,
      overflowX = _getComputedStyle.overflowX,
      overflowY = _getComputedStyle.overflowY;
    return /auto|scroll|overlay|hidden/.test(overflow + overflowY + overflowX);
  }
  function getScrollParent(node) {
    if (['html', 'body', '#document'].indexOf(getNodeName(node)) >= 0) {
      // $FlowFixMe[incompatible-return]: assume body is always available
      return node.ownerDocument.body;
    }
    if (isHTMLElement(node) && isScrollParent(node)) {
      return node;
    }
    return getScrollParent(getParentNode(node));
  }

  /*
  given a DOM element, return the list of all scroll parents, up the list of ancesors
  until we get to the top window object. This list is what we attach scroll listeners
  to, because if any of these parent elements scroll, we'll need to re-calculate the
  reference element's position.
  */

  function listScrollParents(element, list) {
    var _element$ownerDocumen;
    if (list === void 0) {
      list = [];
    }
    var scrollParent = getScrollParent(element);
    var isBody = scrollParent === ((_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body);
    var win = getWindow(scrollParent);
    var target = isBody ? [win].concat(win.visualViewport || [], isScrollParent(scrollParent) ? scrollParent : []) : scrollParent;
    var updatedList = list.concat(target);
    return isBody ? updatedList :
    // $FlowFixMe[incompatible-call]: isBody tells us target will be an HTMLElement here
    updatedList.concat(listScrollParents(getParentNode(target)));
  }
  function rectToClientRect(rect) {
    return Object.assign({}, rect, {
      left: rect.x,
      top: rect.y,
      right: rect.x + rect.width,
      bottom: rect.y + rect.height
    });
  }
  function getInnerBoundingClientRect(element) {
    var rect = getBoundingClientRect(element);
    rect.top = rect.top + element.clientTop;
    rect.left = rect.left + element.clientLeft;
    rect.bottom = rect.top + element.clientHeight;
    rect.right = rect.left + element.clientWidth;
    rect.width = element.clientWidth;
    rect.height = element.clientHeight;
    rect.x = rect.left;
    rect.y = rect.top;
    return rect;
  }
  function getClientRectFromMixedType(element, clippingParent) {
    return clippingParent === viewport ? rectToClientRect(getViewportRect(element)) : isElement$1(clippingParent) ? getInnerBoundingClientRect(clippingParent) : rectToClientRect(getDocumentRect(getDocumentElement(element)));
  } // A "clipping parent" is an overflowable container with the characteristic of
  // clipping (or hiding) overflowing elements with a position different from
  // `initial`

  function getClippingParents(element) {
    var clippingParents = listScrollParents(getParentNode(element));
    var canEscapeClipping = ['absolute', 'fixed'].indexOf(getComputedStyle$1(element).position) >= 0;
    var clipperElement = canEscapeClipping && isHTMLElement(element) ? getOffsetParent(element) : element;
    if (!isElement$1(clipperElement)) {
      return [];
    } // $FlowFixMe[incompatible-return]: https://github.com/facebook/flow/issues/1414

    return clippingParents.filter(function (clippingParent) {
      return isElement$1(clippingParent) && contains(clippingParent, clipperElement) && getNodeName(clippingParent) !== 'body';
    });
  } // Gets the maximum area that the element is visible in due to any number of
  // clipping parents

  function getClippingRect(element, boundary, rootBoundary) {
    var mainClippingParents = boundary === 'clippingParents' ? getClippingParents(element) : [].concat(boundary);
    var clippingParents = [].concat(mainClippingParents, [rootBoundary]);
    var firstClippingParent = clippingParents[0];
    var clippingRect = clippingParents.reduce(function (accRect, clippingParent) {
      var rect = getClientRectFromMixedType(element, clippingParent);
      accRect.top = max(rect.top, accRect.top);
      accRect.right = min(rect.right, accRect.right);
      accRect.bottom = min(rect.bottom, accRect.bottom);
      accRect.left = max(rect.left, accRect.left);
      return accRect;
    }, getClientRectFromMixedType(element, firstClippingParent));
    clippingRect.width = clippingRect.right - clippingRect.left;
    clippingRect.height = clippingRect.bottom - clippingRect.top;
    clippingRect.x = clippingRect.left;
    clippingRect.y = clippingRect.top;
    return clippingRect;
  }
  function computeOffsets(_ref) {
    var reference = _ref.reference,
      element = _ref.element,
      placement = _ref.placement;
    var basePlacement = placement ? getBasePlacement$1(placement) : null;
    var variation = placement ? getVariation(placement) : null;
    var commonX = reference.x + reference.width / 2 - element.width / 2;
    var commonY = reference.y + reference.height / 2 - element.height / 2;
    var offsets;
    switch (basePlacement) {
      case top:
        offsets = {
          x: commonX,
          y: reference.y - element.height
        };
        break;
      case bottom:
        offsets = {
          x: commonX,
          y: reference.y + reference.height
        };
        break;
      case right:
        offsets = {
          x: reference.x + reference.width,
          y: commonY
        };
        break;
      case left:
        offsets = {
          x: reference.x - element.width,
          y: commonY
        };
        break;
      default:
        offsets = {
          x: reference.x,
          y: reference.y
        };
    }
    var mainAxis = basePlacement ? getMainAxisFromPlacement(basePlacement) : null;
    if (mainAxis != null) {
      var len = mainAxis === 'y' ? 'height' : 'width';
      switch (variation) {
        case start:
          offsets[mainAxis] = offsets[mainAxis] - (reference[len] / 2 - element[len] / 2);
          break;
        case end:
          offsets[mainAxis] = offsets[mainAxis] + (reference[len] / 2 - element[len] / 2);
          break;
      }
    }
    return offsets;
  }
  function detectOverflow(state, options) {
    if (options === void 0) {
      options = {};
    }
    var _options = options,
      _options$placement = _options.placement,
      placement = _options$placement === void 0 ? state.placement : _options$placement,
      _options$boundary = _options.boundary,
      boundary = _options$boundary === void 0 ? clippingParents : _options$boundary,
      _options$rootBoundary = _options.rootBoundary,
      rootBoundary = _options$rootBoundary === void 0 ? viewport : _options$rootBoundary,
      _options$elementConte = _options.elementContext,
      elementContext = _options$elementConte === void 0 ? popper : _options$elementConte,
      _options$altBoundary = _options.altBoundary,
      altBoundary = _options$altBoundary === void 0 ? false : _options$altBoundary,
      _options$padding = _options.padding,
      padding = _options$padding === void 0 ? 0 : _options$padding;
    var paddingObject = mergePaddingObject(typeof padding !== 'number' ? padding : expandToHashMap(padding, basePlacements));
    var altContext = elementContext === popper ? reference : popper;
    var popperRect = state.rects.popper;
    var element = state.elements[altBoundary ? altContext : elementContext];
    var clippingClientRect = getClippingRect(isElement$1(element) ? element : element.contextElement || getDocumentElement(state.elements.popper), boundary, rootBoundary);
    var referenceClientRect = getBoundingClientRect(state.elements.reference);
    var popperOffsets = computeOffsets({
      reference: referenceClientRect,
      element: popperRect,
      strategy: 'absolute',
      placement: placement
    });
    var popperClientRect = rectToClientRect(Object.assign({}, popperRect, popperOffsets));
    var elementClientRect = elementContext === popper ? popperClientRect : referenceClientRect; // positive = overflowing the clipping rect
    // 0 or negative = within the clipping rect

    var overflowOffsets = {
      top: clippingClientRect.top - elementClientRect.top + paddingObject.top,
      bottom: elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom,
      left: clippingClientRect.left - elementClientRect.left + paddingObject.left,
      right: elementClientRect.right - clippingClientRect.right + paddingObject.right
    };
    var offsetData = state.modifiersData.offset; // Offsets can be applied only to the popper element

    if (elementContext === popper && offsetData) {
      var offset = offsetData[placement];
      Object.keys(overflowOffsets).forEach(function (key) {
        var multiply = [right, bottom].indexOf(key) >= 0 ? 1 : -1;
        var axis = [top, bottom].indexOf(key) >= 0 ? 'y' : 'x';
        overflowOffsets[key] += offset[axis] * multiply;
      });
    }
    return overflowOffsets;
  }
  function computeAutoPlacement(state, options) {
    if (options === void 0) {
      options = {};
    }
    var _options = options,
      placement = _options.placement,
      boundary = _options.boundary,
      rootBoundary = _options.rootBoundary,
      padding = _options.padding,
      flipVariations = _options.flipVariations,
      _options$allowedAutoP = _options.allowedAutoPlacements,
      allowedAutoPlacements = _options$allowedAutoP === void 0 ? placements : _options$allowedAutoP;
    var variation = getVariation(placement);
    var placements$1 = variation ? flipVariations ? variationPlacements : variationPlacements.filter(function (placement) {
      return getVariation(placement) === variation;
    }) : basePlacements;
    var allowedPlacements = placements$1.filter(function (placement) {
      return allowedAutoPlacements.indexOf(placement) >= 0;
    });
    if (allowedPlacements.length === 0) {
      allowedPlacements = placements$1;
    } // $FlowFixMe[incompatible-type]: Flow seems to have problems with two array unions...

    var overflows = allowedPlacements.reduce(function (acc, placement) {
      acc[placement] = detectOverflow(state, {
        placement: placement,
        boundary: boundary,
        rootBoundary: rootBoundary,
        padding: padding
      })[getBasePlacement$1(placement)];
      return acc;
    }, {});
    return Object.keys(overflows).sort(function (a, b) {
      return overflows[a] - overflows[b];
    });
  }
  function getExpandedFallbackPlacements(placement) {
    if (getBasePlacement$1(placement) === auto) {
      return [];
    }
    var oppositePlacement = getOppositePlacement(placement);
    return [getOppositeVariationPlacement(placement), oppositePlacement, getOppositeVariationPlacement(oppositePlacement)];
  }
  function flip(_ref) {
    var state = _ref.state,
      options = _ref.options,
      name = _ref.name;
    if (state.modifiersData[name]._skip) {
      return;
    }
    var _options$mainAxis = options.mainAxis,
      checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis,
      _options$altAxis = options.altAxis,
      checkAltAxis = _options$altAxis === void 0 ? true : _options$altAxis,
      specifiedFallbackPlacements = options.fallbackPlacements,
      padding = options.padding,
      boundary = options.boundary,
      rootBoundary = options.rootBoundary,
      altBoundary = options.altBoundary,
      _options$flipVariatio = options.flipVariations,
      flipVariations = _options$flipVariatio === void 0 ? true : _options$flipVariatio,
      allowedAutoPlacements = options.allowedAutoPlacements;
    var preferredPlacement = state.options.placement;
    var basePlacement = getBasePlacement$1(preferredPlacement);
    var isBasePlacement = basePlacement === preferredPlacement;
    var fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipVariations ? [getOppositePlacement(preferredPlacement)] : getExpandedFallbackPlacements(preferredPlacement));
    var placements = [preferredPlacement].concat(fallbackPlacements).reduce(function (acc, placement) {
      return acc.concat(getBasePlacement$1(placement) === auto ? computeAutoPlacement(state, {
        placement: placement,
        boundary: boundary,
        rootBoundary: rootBoundary,
        padding: padding,
        flipVariations: flipVariations,
        allowedAutoPlacements: allowedAutoPlacements
      }) : placement);
    }, []);
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var checksMap = new Map();
    var makeFallbackChecks = true;
    var firstFittingPlacement = placements[0];
    for (var i = 0; i < placements.length; i++) {
      var placement = placements[i];
      var _basePlacement = getBasePlacement$1(placement);
      var isStartVariation = getVariation(placement) === start;
      var isVertical = [top, bottom].indexOf(_basePlacement) >= 0;
      var len = isVertical ? 'width' : 'height';
      var overflow = detectOverflow(state, {
        placement: placement,
        boundary: boundary,
        rootBoundary: rootBoundary,
        altBoundary: altBoundary,
        padding: padding
      });
      var mainVariationSide = isVertical ? isStartVariation ? right : left : isStartVariation ? bottom : top;
      if (referenceRect[len] > popperRect[len]) {
        mainVariationSide = getOppositePlacement(mainVariationSide);
      }
      var altVariationSide = getOppositePlacement(mainVariationSide);
      var checks = [];
      if (checkMainAxis) {
        checks.push(overflow[_basePlacement] <= 0);
      }
      if (checkAltAxis) {
        checks.push(overflow[mainVariationSide] <= 0, overflow[altVariationSide] <= 0);
      }
      if (checks.every(function (check) {
        return check;
      })) {
        firstFittingPlacement = placement;
        makeFallbackChecks = false;
        break;
      }
      checksMap.set(placement, checks);
    }
    if (makeFallbackChecks) {
      // `2` may be desired in some cases – research later
      var numberOfChecks = flipVariations ? 3 : 1;
      var _loop = function _loop(_i) {
        var fittingPlacement = placements.find(function (placement) {
          var checks = checksMap.get(placement);
          if (checks) {
            return checks.slice(0, _i).every(function (check) {
              return check;
            });
          }
        });
        if (fittingPlacement) {
          firstFittingPlacement = fittingPlacement;
          return "break";
        }
      };
      for (var _i = numberOfChecks; _i > 0; _i--) {
        var _ret = _loop(_i);
        if (_ret === "break") break;
      }
    }
    if (state.placement !== firstFittingPlacement) {
      state.modifiersData[name]._skip = true;
      state.placement = firstFittingPlacement;
      state.reset = true;
    }
  } // eslint-disable-next-line import/no-unused-modules

  var flip$1 = {
    name: 'flip',
    enabled: true,
    phase: 'main',
    fn: flip,
    requiresIfExists: ['offset'],
    data: {
      _skip: false
    }
  };
  function getSideOffsets(overflow, rect, preventedOffsets) {
    if (preventedOffsets === void 0) {
      preventedOffsets = {
        x: 0,
        y: 0
      };
    }
    return {
      top: overflow.top - rect.height - preventedOffsets.y,
      right: overflow.right - rect.width + preventedOffsets.x,
      bottom: overflow.bottom - rect.height + preventedOffsets.y,
      left: overflow.left - rect.width - preventedOffsets.x
    };
  }
  function isAnySideFullyClipped(overflow) {
    return [top, right, bottom, left].some(function (side) {
      return overflow[side] >= 0;
    });
  }
  function hide(_ref) {
    var state = _ref.state,
      name = _ref.name;
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var preventedOffsets = state.modifiersData.preventOverflow;
    var referenceOverflow = detectOverflow(state, {
      elementContext: 'reference'
    });
    var popperAltOverflow = detectOverflow(state, {
      altBoundary: true
    });
    var referenceClippingOffsets = getSideOffsets(referenceOverflow, referenceRect);
    var popperEscapeOffsets = getSideOffsets(popperAltOverflow, popperRect, preventedOffsets);
    var isReferenceHidden = isAnySideFullyClipped(referenceClippingOffsets);
    var hasPopperEscaped = isAnySideFullyClipped(popperEscapeOffsets);
    state.modifiersData[name] = {
      referenceClippingOffsets: referenceClippingOffsets,
      popperEscapeOffsets: popperEscapeOffsets,
      isReferenceHidden: isReferenceHidden,
      hasPopperEscaped: hasPopperEscaped
    };
    state.attributes.popper = Object.assign({}, state.attributes.popper, {
      'data-popper-reference-hidden': isReferenceHidden,
      'data-popper-escaped': hasPopperEscaped
    });
  } // eslint-disable-next-line import/no-unused-modules

  var hide$1 = {
    name: 'hide',
    enabled: true,
    phase: 'main',
    requiresIfExists: ['preventOverflow'],
    fn: hide
  };
  function distanceAndSkiddingToXY(placement, rects, offset) {
    var basePlacement = getBasePlacement$1(placement);
    var invertDistance = [left, top].indexOf(basePlacement) >= 0 ? -1 : 1;
    var _ref = typeof offset === 'function' ? offset(Object.assign({}, rects, {
        placement: placement
      })) : offset,
      skidding = _ref[0],
      distance = _ref[1];
    skidding = skidding || 0;
    distance = (distance || 0) * invertDistance;
    return [left, right].indexOf(basePlacement) >= 0 ? {
      x: distance,
      y: skidding
    } : {
      x: skidding,
      y: distance
    };
  }
  function offset(_ref2) {
    var state = _ref2.state,
      options = _ref2.options,
      name = _ref2.name;
    var _options$offset = options.offset,
      offset = _options$offset === void 0 ? [0, 0] : _options$offset;
    var data = placements.reduce(function (acc, placement) {
      acc[placement] = distanceAndSkiddingToXY(placement, state.rects, offset);
      return acc;
    }, {});
    var _data$state$placement = data[state.placement],
      x = _data$state$placement.x,
      y = _data$state$placement.y;
    if (state.modifiersData.popperOffsets != null) {
      state.modifiersData.popperOffsets.x += x;
      state.modifiersData.popperOffsets.y += y;
    }
    state.modifiersData[name] = data;
  } // eslint-disable-next-line import/no-unused-modules

  var offset$1 = {
    name: 'offset',
    enabled: true,
    phase: 'main',
    requires: ['popperOffsets'],
    fn: offset
  };
  function popperOffsets(_ref) {
    var state = _ref.state,
      name = _ref.name;
    // Offsets are the actual position the popper needs to have to be
    // properly positioned near its reference element
    // This is the most basic placement, and will be adjusted by
    // the modifiers in the next step
    state.modifiersData[name] = computeOffsets({
      reference: state.rects.reference,
      element: state.rects.popper,
      strategy: 'absolute',
      placement: state.placement
    });
  } // eslint-disable-next-line import/no-unused-modules

  var popperOffsets$1 = {
    name: 'popperOffsets',
    enabled: true,
    phase: 'read',
    fn: popperOffsets,
    data: {}
  };
  function getAltAxis(axis) {
    return axis === 'x' ? 'y' : 'x';
  }
  function preventOverflow(_ref) {
    var state = _ref.state,
      options = _ref.options,
      name = _ref.name;
    var _options$mainAxis = options.mainAxis,
      checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis,
      _options$altAxis = options.altAxis,
      checkAltAxis = _options$altAxis === void 0 ? false : _options$altAxis,
      boundary = options.boundary,
      rootBoundary = options.rootBoundary,
      altBoundary = options.altBoundary,
      padding = options.padding,
      _options$tether = options.tether,
      tether = _options$tether === void 0 ? true : _options$tether,
      _options$tetherOffset = options.tetherOffset,
      tetherOffset = _options$tetherOffset === void 0 ? 0 : _options$tetherOffset;
    var overflow = detectOverflow(state, {
      boundary: boundary,
      rootBoundary: rootBoundary,
      padding: padding,
      altBoundary: altBoundary
    });
    var basePlacement = getBasePlacement$1(state.placement);
    var variation = getVariation(state.placement);
    var isBasePlacement = !variation;
    var mainAxis = getMainAxisFromPlacement(basePlacement);
    var altAxis = getAltAxis(mainAxis);
    var popperOffsets = state.modifiersData.popperOffsets;
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var tetherOffsetValue = typeof tetherOffset === 'function' ? tetherOffset(Object.assign({}, state.rects, {
      placement: state.placement
    })) : tetherOffset;
    var normalizedTetherOffsetValue = typeof tetherOffsetValue === 'number' ? {
      mainAxis: tetherOffsetValue,
      altAxis: tetherOffsetValue
    } : Object.assign({
      mainAxis: 0,
      altAxis: 0
    }, tetherOffsetValue);
    var offsetModifierState = state.modifiersData.offset ? state.modifiersData.offset[state.placement] : null;
    var data = {
      x: 0,
      y: 0
    };
    if (!popperOffsets) {
      return;
    }
    if (checkMainAxis) {
      var _offsetModifierState$;
      var mainSide = mainAxis === 'y' ? top : left;
      var altSide = mainAxis === 'y' ? bottom : right;
      var len = mainAxis === 'y' ? 'height' : 'width';
      var offset = popperOffsets[mainAxis];
      var min$1 = offset + overflow[mainSide];
      var max$1 = offset - overflow[altSide];
      var additive = tether ? -popperRect[len] / 2 : 0;
      var minLen = variation === start ? referenceRect[len] : popperRect[len];
      var maxLen = variation === start ? -popperRect[len] : -referenceRect[len]; // We need to include the arrow in the calculation so the arrow doesn't go
      // outside the reference bounds

      var arrowElement = state.elements.arrow;
      var arrowRect = tether && arrowElement ? getLayoutRect(arrowElement) : {
        width: 0,
        height: 0
      };
      var arrowPaddingObject = state.modifiersData['arrow#persistent'] ? state.modifiersData['arrow#persistent'].padding : getFreshSideObject();
      var arrowPaddingMin = arrowPaddingObject[mainSide];
      var arrowPaddingMax = arrowPaddingObject[altSide]; // If the reference length is smaller than the arrow length, we don't want
      // to include its full size in the calculation. If the reference is small
      // and near the edge of a boundary, the popper can overflow even if the
      // reference is not overflowing as well (e.g. virtual elements with no
      // width or height)

      var arrowLen = within(0, referenceRect[len], arrowRect[len]);
      var minOffset = isBasePlacement ? referenceRect[len] / 2 - additive - arrowLen - arrowPaddingMin - normalizedTetherOffsetValue.mainAxis : minLen - arrowLen - arrowPaddingMin - normalizedTetherOffsetValue.mainAxis;
      var maxOffset = isBasePlacement ? -referenceRect[len] / 2 + additive + arrowLen + arrowPaddingMax + normalizedTetherOffsetValue.mainAxis : maxLen + arrowLen + arrowPaddingMax + normalizedTetherOffsetValue.mainAxis;
      var arrowOffsetParent = state.elements.arrow && getOffsetParent(state.elements.arrow);
      var clientOffset = arrowOffsetParent ? mainAxis === 'y' ? arrowOffsetParent.clientTop || 0 : arrowOffsetParent.clientLeft || 0 : 0;
      var offsetModifierValue = (_offsetModifierState$ = offsetModifierState == null ? void 0 : offsetModifierState[mainAxis]) != null ? _offsetModifierState$ : 0;
      var tetherMin = offset + minOffset - offsetModifierValue - clientOffset;
      var tetherMax = offset + maxOffset - offsetModifierValue;
      var preventedOffset = within(tether ? min(min$1, tetherMin) : min$1, offset, tether ? max(max$1, tetherMax) : max$1);
      popperOffsets[mainAxis] = preventedOffset;
      data[mainAxis] = preventedOffset - offset;
    }
    if (checkAltAxis) {
      var _offsetModifierState$2;
      var _mainSide = mainAxis === 'x' ? top : left;
      var _altSide = mainAxis === 'x' ? bottom : right;
      var _offset = popperOffsets[altAxis];
      var _len = altAxis === 'y' ? 'height' : 'width';
      var _min = _offset + overflow[_mainSide];
      var _max = _offset - overflow[_altSide];
      var isOriginSide = [top, left].indexOf(basePlacement) !== -1;
      var _offsetModifierValue = (_offsetModifierState$2 = offsetModifierState == null ? void 0 : offsetModifierState[altAxis]) != null ? _offsetModifierState$2 : 0;
      var _tetherMin = isOriginSide ? _min : _offset - referenceRect[_len] - popperRect[_len] - _offsetModifierValue + normalizedTetherOffsetValue.altAxis;
      var _tetherMax = isOriginSide ? _offset + referenceRect[_len] + popperRect[_len] - _offsetModifierValue - normalizedTetherOffsetValue.altAxis : _max;
      var _preventedOffset = tether && isOriginSide ? withinMaxClamp(_tetherMin, _offset, _tetherMax) : within(tether ? _tetherMin : _min, _offset, tether ? _tetherMax : _max);
      popperOffsets[altAxis] = _preventedOffset;
      data[altAxis] = _preventedOffset - _offset;
    }
    state.modifiersData[name] = data;
  } // eslint-disable-next-line import/no-unused-modules

  var preventOverflow$1 = {
    name: 'preventOverflow',
    enabled: true,
    phase: 'main',
    fn: preventOverflow,
    requiresIfExists: ['offset']
  };
  function getHTMLElementScroll(element) {
    return {
      scrollLeft: element.scrollLeft,
      scrollTop: element.scrollTop
    };
  }
  function getNodeScroll(node) {
    if (node === getWindow(node) || !isHTMLElement(node)) {
      return getWindowScroll(node);
    } else {
      return getHTMLElementScroll(node);
    }
  }
  function isElementScaled(element) {
    var rect = element.getBoundingClientRect();
    var scaleX = round(rect.width) / element.offsetWidth || 1;
    var scaleY = round(rect.height) / element.offsetHeight || 1;
    return scaleX !== 1 || scaleY !== 1;
  } // Returns the composite rect of an element relative to its offsetParent.
  // Composite means it takes into account transforms as well as layout.

  function getCompositeRect(elementOrVirtualElement, offsetParent, isFixed) {
    if (isFixed === void 0) {
      isFixed = false;
    }
    var isOffsetParentAnElement = isHTMLElement(offsetParent);
    var offsetParentIsScaled = isHTMLElement(offsetParent) && isElementScaled(offsetParent);
    var documentElement = getDocumentElement(offsetParent);
    var rect = getBoundingClientRect(elementOrVirtualElement, offsetParentIsScaled);
    var scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    var offsets = {
      x: 0,
      y: 0
    };
    if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
      if (getNodeName(offsetParent) !== 'body' ||
      // https://github.com/popperjs/popper-core/issues/1078
      isScrollParent(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isHTMLElement(offsetParent)) {
        offsets = getBoundingClientRect(offsetParent, true);
        offsets.x += offsetParent.clientLeft;
        offsets.y += offsetParent.clientTop;
      } else if (documentElement) {
        offsets.x = getWindowScrollBarX(documentElement);
      }
    }
    return {
      x: rect.left + scroll.scrollLeft - offsets.x,
      y: rect.top + scroll.scrollTop - offsets.y,
      width: rect.width,
      height: rect.height
    };
  }
  function order(modifiers) {
    var map = new Map();
    var visited = new Set();
    var result = [];
    modifiers.forEach(function (modifier) {
      map.set(modifier.name, modifier);
    }); // On visiting object, check for its dependencies and visit them recursively

    function sort(modifier) {
      visited.add(modifier.name);
      var requires = [].concat(modifier.requires || [], modifier.requiresIfExists || []);
      requires.forEach(function (dep) {
        if (!visited.has(dep)) {
          var depModifier = map.get(dep);
          if (depModifier) {
            sort(depModifier);
          }
        }
      });
      result.push(modifier);
    }
    modifiers.forEach(function (modifier) {
      if (!visited.has(modifier.name)) {
        // check for visited object
        sort(modifier);
      }
    });
    return result;
  }
  function orderModifiers(modifiers) {
    // order based on dependencies
    var orderedModifiers = order(modifiers); // order based on phase

    return modifierPhases.reduce(function (acc, phase) {
      return acc.concat(orderedModifiers.filter(function (modifier) {
        return modifier.phase === phase;
      }));
    }, []);
  }
  function debounce$1(fn) {
    var pending;
    return function () {
      if (!pending) {
        pending = new Promise(function (resolve) {
          Promise.resolve().then(function () {
            pending = undefined;
            resolve(fn());
          });
        });
      }
      return pending;
    };
  }
  function mergeByName(modifiers) {
    var merged = modifiers.reduce(function (merged, current) {
      var existing = merged[current.name];
      merged[current.name] = existing ? Object.assign({}, existing, current, {
        options: Object.assign({}, existing.options, current.options),
        data: Object.assign({}, existing.data, current.data)
      }) : current;
      return merged;
    }, {}); // IE11 does not support Object.values

    return Object.keys(merged).map(function (key) {
      return merged[key];
    });
  }
  var DEFAULT_OPTIONS = {
    placement: 'bottom',
    modifiers: [],
    strategy: 'absolute'
  };
  function areValidElements() {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    return !args.some(function (element) {
      return !(element && typeof element.getBoundingClientRect === 'function');
    });
  }
  function popperGenerator(generatorOptions) {
    if (generatorOptions === void 0) {
      generatorOptions = {};
    }
    var _generatorOptions = generatorOptions,
      _generatorOptions$def = _generatorOptions.defaultModifiers,
      defaultModifiers = _generatorOptions$def === void 0 ? [] : _generatorOptions$def,
      _generatorOptions$def2 = _generatorOptions.defaultOptions,
      defaultOptions = _generatorOptions$def2 === void 0 ? DEFAULT_OPTIONS : _generatorOptions$def2;
    return function createPopper(reference, popper, options) {
      if (options === void 0) {
        options = defaultOptions;
      }
      var state = {
        placement: 'bottom',
        orderedModifiers: [],
        options: Object.assign({}, DEFAULT_OPTIONS, defaultOptions),
        modifiersData: {},
        elements: {
          reference: reference,
          popper: popper
        },
        attributes: {},
        styles: {}
      };
      var effectCleanupFns = [];
      var isDestroyed = false;
      var instance = {
        state: state,
        setOptions: function setOptions(setOptionsAction) {
          var options = typeof setOptionsAction === 'function' ? setOptionsAction(state.options) : setOptionsAction;
          cleanupModifierEffects();
          state.options = Object.assign({}, defaultOptions, state.options, options);
          state.scrollParents = {
            reference: isElement$1(reference) ? listScrollParents(reference) : reference.contextElement ? listScrollParents(reference.contextElement) : [],
            popper: listScrollParents(popper)
          }; // Orders the modifiers based on their dependencies and `phase`
          // properties

          var orderedModifiers = orderModifiers(mergeByName([].concat(defaultModifiers, state.options.modifiers))); // Strip out disabled modifiers

          state.orderedModifiers = orderedModifiers.filter(function (m) {
            return m.enabled;
          }); // Validate the provided modifiers so that the consumer will get warned

          runModifierEffects();
          return instance.update();
        },
        // Sync update – it will always be executed, even if not necessary. This
        // is useful for low frequency updates where sync behavior simplifies the
        // logic.
        // For high frequency updates (e.g. `resize` and `scroll` events), always
        // prefer the async Popper#update method
        forceUpdate: function forceUpdate() {
          if (isDestroyed) {
            return;
          }
          var _state$elements = state.elements,
            reference = _state$elements.reference,
            popper = _state$elements.popper; // Don't proceed if `reference` or `popper` are not valid elements
          // anymore

          if (!areValidElements(reference, popper)) {
            return;
          } // Store the reference and popper rects to be read by modifiers

          state.rects = {
            reference: getCompositeRect(reference, getOffsetParent(popper), state.options.strategy === 'fixed'),
            popper: getLayoutRect(popper)
          }; // Modifiers have the ability to reset the current update cycle. The
          // most common use case for this is the `flip` modifier changing the
          // placement, which then needs to re-run all the modifiers, because the
          // logic was previously ran for the previous placement and is therefore
          // stale/incorrect

          state.reset = false;
          state.placement = state.options.placement; // On each update cycle, the `modifiersData` property for each modifier
          // is filled with the initial data specified by the modifier. This means
          // it doesn't persist and is fresh on each update.
          // To ensure persistent data, use `${name}#persistent`

          state.orderedModifiers.forEach(function (modifier) {
            return state.modifiersData[modifier.name] = Object.assign({}, modifier.data);
          });
          for (var index = 0; index < state.orderedModifiers.length; index++) {
            if (state.reset === true) {
              state.reset = false;
              index = -1;
              continue;
            }
            var _state$orderedModifie = state.orderedModifiers[index],
              fn = _state$orderedModifie.fn,
              _state$orderedModifie2 = _state$orderedModifie.options,
              _options = _state$orderedModifie2 === void 0 ? {} : _state$orderedModifie2,
              name = _state$orderedModifie.name;
            if (typeof fn === 'function') {
              state = fn({
                state: state,
                options: _options,
                name: name,
                instance: instance
              }) || state;
            }
          }
        },
        // Async and optimistically optimized update – it will not be executed if
        // not necessary (debounced to run at most once-per-tick)
        update: debounce$1(function () {
          return new Promise(function (resolve) {
            instance.forceUpdate();
            resolve(state);
          });
        }),
        destroy: function destroy() {
          cleanupModifierEffects();
          isDestroyed = true;
        }
      };
      if (!areValidElements(reference, popper)) {
        return instance;
      }
      instance.setOptions(options).then(function (state) {
        if (!isDestroyed && options.onFirstUpdate) {
          options.onFirstUpdate(state);
        }
      }); // Modifiers have the ability to execute arbitrary code before the first
      // update cycle runs. They will be executed in the same order as the update
      // cycle. This is useful when a modifier adds some persistent data that
      // other modifiers need to use, but the modifier is run after the dependent
      // one.

      function runModifierEffects() {
        state.orderedModifiers.forEach(function (_ref3) {
          var name = _ref3.name,
            _ref3$options = _ref3.options,
            options = _ref3$options === void 0 ? {} : _ref3$options,
            effect = _ref3.effect;
          if (typeof effect === 'function') {
            var cleanupFn = effect({
              state: state,
              name: name,
              instance: instance,
              options: options
            });
            var noopFn = function noopFn() {};
            effectCleanupFns.push(cleanupFn || noopFn);
          }
        });
      }
      function cleanupModifierEffects() {
        effectCleanupFns.forEach(function (fn) {
          return fn();
        });
        effectCleanupFns = [];
      }
      return instance;
    };
  }
  var defaultModifiers = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1, offset$1, flip$1, preventOverflow$1, arrow$1, hide$1];
  var createPopper = /*#__PURE__*/popperGenerator({
    defaultModifiers: defaultModifiers
  }); // eslint-disable-next-line import/no-unused-modules

  /**!
  * tippy.js v6.3.7
  * (c) 2017-2021 atomiks
  * MIT License
  */
  var BOX_CLASS = "tippy-box";
  var CONTENT_CLASS = "tippy-content";
  var BACKDROP_CLASS = "tippy-backdrop";
  var ARROW_CLASS = "tippy-arrow";
  var SVG_ARROW_CLASS = "tippy-svg-arrow";
  var TOUCH_OPTIONS = {
    passive: true,
    capture: true
  };
  var TIPPY_DEFAULT_APPEND_TO = function TIPPY_DEFAULT_APPEND_TO() {
    return document.body;
  };
  function getValueAtIndexOrReturn(value, index, defaultValue) {
    if (Array.isArray(value)) {
      var v = value[index];
      return v == null ? Array.isArray(defaultValue) ? defaultValue[index] : defaultValue : v;
    }
    return value;
  }
  function isType(value, type) {
    var str = {}.toString.call(value);
    return str.indexOf('[object') === 0 && str.indexOf(type + "]") > -1;
  }
  function invokeWithArgsOrReturn(value, args) {
    return typeof value === 'function' ? value.apply(void 0, args) : value;
  }
  function debounce(fn, ms) {
    // Avoid wrapping in `setTimeout` if ms is 0 anyway
    if (ms === 0) {
      return fn;
    }
    var timeout;
    return function (arg) {
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        fn(arg);
      }, ms);
    };
  }
  function splitBySpaces(value) {
    return value.split(/\s+/).filter(Boolean);
  }
  function normalizeToArray(value) {
    return [].concat(value);
  }
  function pushIfUnique(arr, value) {
    if (arr.indexOf(value) === -1) {
      arr.push(value);
    }
  }
  function unique(arr) {
    return arr.filter(function (item, index) {
      return arr.indexOf(item) === index;
    });
  }
  function getBasePlacement(placement) {
    return placement.split('-')[0];
  }
  function arrayFrom(value) {
    return [].slice.call(value);
  }
  function removeUndefinedProps(obj) {
    return Object.keys(obj).reduce(function (acc, key) {
      if (obj[key] !== undefined) {
        acc[key] = obj[key];
      }
      return acc;
    }, {});
  }
  function div() {
    return document.createElement('div');
  }
  function isElement(value) {
    return ['Element', 'Fragment'].some(function (type) {
      return isType(value, type);
    });
  }
  function isNodeList(value) {
    return isType(value, 'NodeList');
  }
  function isMouseEvent(value) {
    return isType(value, 'MouseEvent');
  }
  function isReferenceElement(value) {
    return !!(value && value._tippy && value._tippy.reference === value);
  }
  function getArrayOfElements(value) {
    if (isElement(value)) {
      return [value];
    }
    if (isNodeList(value)) {
      return arrayFrom(value);
    }
    if (Array.isArray(value)) {
      return value;
    }
    return arrayFrom(document.querySelectorAll(value));
  }
  function setTransitionDuration(els, value) {
    els.forEach(function (el) {
      if (el) {
        el.style.transitionDuration = value + "ms";
      }
    });
  }
  function setVisibilityState(els, state) {
    els.forEach(function (el) {
      if (el) {
        el.setAttribute('data-state', state);
      }
    });
  }
  function getOwnerDocument(elementOrElements) {
    var _element$ownerDocumen;
    var _normalizeToArray = normalizeToArray(elementOrElements),
      element = _normalizeToArray[0]; // Elements created via a <template> have an ownerDocument with no reference to the body

    return element != null && (_element$ownerDocumen = element.ownerDocument) != null && _element$ownerDocumen.body ? element.ownerDocument : document;
  }
  function isCursorOutsideInteractiveBorder(popperTreeData, event) {
    var clientX = event.clientX,
      clientY = event.clientY;
    return popperTreeData.every(function (_ref) {
      var popperRect = _ref.popperRect,
        popperState = _ref.popperState,
        props = _ref.props;
      var interactiveBorder = props.interactiveBorder;
      var basePlacement = getBasePlacement(popperState.placement);
      var offsetData = popperState.modifiersData.offset;
      if (!offsetData) {
        return true;
      }
      var topDistance = basePlacement === 'bottom' ? offsetData.top.y : 0;
      var bottomDistance = basePlacement === 'top' ? offsetData.bottom.y : 0;
      var leftDistance = basePlacement === 'right' ? offsetData.left.x : 0;
      var rightDistance = basePlacement === 'left' ? offsetData.right.x : 0;
      var exceedsTop = popperRect.top - clientY + topDistance > interactiveBorder;
      var exceedsBottom = clientY - popperRect.bottom - bottomDistance > interactiveBorder;
      var exceedsLeft = popperRect.left - clientX + leftDistance > interactiveBorder;
      var exceedsRight = clientX - popperRect.right - rightDistance > interactiveBorder;
      return exceedsTop || exceedsBottom || exceedsLeft || exceedsRight;
    });
  }
  function updateTransitionEndListener(box, action, listener) {
    var method = action + "EventListener"; // some browsers apparently support `transition` (unprefixed) but only fire
    // `webkitTransitionEnd`...

    ['transitionend', 'webkitTransitionEnd'].forEach(function (event) {
      box[method](event, listener);
    });
  }
  /**
   * Compared to xxx.contains, this function works for dom structures with shadow
   * dom
   */

  function actualContains(parent, child) {
    var target = child;
    while (target) {
      var _target$getRootNode;
      if (parent.contains(target)) {
        return true;
      }
      target = target.getRootNode == null ? void 0 : (_target$getRootNode = target.getRootNode()) == null ? void 0 : _target$getRootNode.host;
    }
    return false;
  }
  var currentInput = {
    isTouch: false
  };
  var lastMouseMoveTime = 0;
  /**
   * When a `touchstart` event is fired, it's assumed the user is using touch
   * input. We'll bind a `mousemove` event listener to listen for mouse input in
   * the future. This way, the `isTouch` property is fully dynamic and will handle
   * hybrid devices that use a mix of touch + mouse input.
   */

  function onDocumentTouchStart() {
    if (currentInput.isTouch) {
      return;
    }
    currentInput.isTouch = true;
    if (window.performance) {
      document.addEventListener('mousemove', onDocumentMouseMove);
    }
  }
  /**
   * When two `mousemove` event are fired consecutively within 20ms, it's assumed
   * the user is using mouse input again. `mousemove` can fire on touch devices as
   * well, but very rarely that quickly.
   */

  function onDocumentMouseMove() {
    var now = performance.now();
    if (now - lastMouseMoveTime < 20) {
      currentInput.isTouch = false;
      document.removeEventListener('mousemove', onDocumentMouseMove);
    }
    lastMouseMoveTime = now;
  }
  /**
   * When an element is in focus and has a tippy, leaving the tab/window and
   * returning causes it to show again. For mouse users this is unexpected, but
   * for keyboard use it makes sense.
   * TODO: find a better technique to solve this problem
   */

  function onWindowBlur() {
    var activeElement = document.activeElement;
    if (isReferenceElement(activeElement)) {
      var instance = activeElement._tippy;
      if (activeElement.blur && !instance.state.isVisible) {
        activeElement.blur();
      }
    }
  }
  function bindGlobalEventListeners() {
    document.addEventListener('touchstart', onDocumentTouchStart, TOUCH_OPTIONS);
    window.addEventListener('blur', onWindowBlur);
  }
  var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined';
  var isIE11 = isBrowser ?
  // @ts-ignore
  !!window.msCrypto : false;
  var pluginProps = {
    animateFill: false,
    followCursor: false,
    inlinePositioning: false,
    sticky: false
  };
  var renderProps = {
    allowHTML: false,
    animation: 'fade',
    arrow: true,
    content: '',
    inertia: false,
    maxWidth: 350,
    role: 'tooltip',
    theme: '',
    zIndex: 9999
  };
  var defaultProps = Object.assign({
    appendTo: TIPPY_DEFAULT_APPEND_TO,
    aria: {
      content: 'auto',
      expanded: 'auto'
    },
    delay: 0,
    duration: [300, 250],
    getReferenceClientRect: null,
    hideOnClick: true,
    ignoreAttributes: false,
    interactive: false,
    interactiveBorder: 2,
    interactiveDebounce: 0,
    moveTransition: '',
    offset: [0, 10],
    onAfterUpdate: function onAfterUpdate() {},
    onBeforeUpdate: function onBeforeUpdate() {},
    onCreate: function onCreate() {},
    onDestroy: function onDestroy() {},
    onHidden: function onHidden() {},
    onHide: function onHide() {},
    onMount: function onMount() {},
    onShow: function onShow() {},
    onShown: function onShown() {},
    onTrigger: function onTrigger() {},
    onUntrigger: function onUntrigger() {},
    onClickOutside: function onClickOutside() {},
    placement: 'top',
    plugins: [],
    popperOptions: {},
    render: null,
    showOnCreate: false,
    touch: true,
    trigger: 'mouseenter focus',
    triggerTarget: null
  }, pluginProps, renderProps);
  var defaultKeys = Object.keys(defaultProps);
  var setDefaultProps = function setDefaultProps(partialProps) {
    var keys = Object.keys(partialProps);
    keys.forEach(function (key) {
      defaultProps[key] = partialProps[key];
    });
  };
  function getExtendedPassedProps(passedProps) {
    var plugins = passedProps.plugins || [];
    var pluginProps = plugins.reduce(function (acc, plugin) {
      var name = plugin.name,
        defaultValue = plugin.defaultValue;
      if (name) {
        var _name;
        acc[name] = passedProps[name] !== undefined ? passedProps[name] : (_name = defaultProps[name]) != null ? _name : defaultValue;
      }
      return acc;
    }, {});
    return Object.assign({}, passedProps, pluginProps);
  }
  function getDataAttributeProps(reference, plugins) {
    var propKeys = plugins ? Object.keys(getExtendedPassedProps(Object.assign({}, defaultProps, {
      plugins: plugins
    }))) : defaultKeys;
    var props = propKeys.reduce(function (acc, key) {
      var valueAsString = (reference.getAttribute("data-tippy-" + key) || '').trim();
      if (!valueAsString) {
        return acc;
      }
      if (key === 'content') {
        acc[key] = valueAsString;
      } else {
        try {
          acc[key] = JSON.parse(valueAsString);
        } catch (e) {
          acc[key] = valueAsString;
        }
      }
      return acc;
    }, {});
    return props;
  }
  function evaluateProps(reference, props) {
    var out = Object.assign({}, props, {
      content: invokeWithArgsOrReturn(props.content, [reference])
    }, props.ignoreAttributes ? {} : getDataAttributeProps(reference, props.plugins));
    out.aria = Object.assign({}, defaultProps.aria, out.aria);
    out.aria = {
      expanded: out.aria.expanded === 'auto' ? props.interactive : out.aria.expanded,
      content: out.aria.content === 'auto' ? props.interactive ? null : 'describedby' : out.aria.content
    };
    return out;
  }
  var innerHTML = function innerHTML() {
    return 'innerHTML';
  };
  function dangerouslySetInnerHTML(element, html) {
    element[innerHTML()] = html;
  }
  function createArrowElement(value) {
    var arrow = div();
    if (value === true) {
      arrow.className = ARROW_CLASS;
    } else {
      arrow.className = SVG_ARROW_CLASS;
      if (isElement(value)) {
        arrow.appendChild(value);
      } else {
        dangerouslySetInnerHTML(arrow, value);
      }
    }
    return arrow;
  }
  function setContent(content, props) {
    if (isElement(props.content)) {
      dangerouslySetInnerHTML(content, '');
      content.appendChild(props.content);
    } else if (typeof props.content !== 'function') {
      if (props.allowHTML) {
        dangerouslySetInnerHTML(content, props.content);
      } else {
        content.textContent = props.content;
      }
    }
  }
  function getChildren(popper) {
    var box = popper.firstElementChild;
    var boxChildren = arrayFrom(box.children);
    return {
      box: box,
      content: boxChildren.find(function (node) {
        return node.classList.contains(CONTENT_CLASS);
      }),
      arrow: boxChildren.find(function (node) {
        return node.classList.contains(ARROW_CLASS) || node.classList.contains(SVG_ARROW_CLASS);
      }),
      backdrop: boxChildren.find(function (node) {
        return node.classList.contains(BACKDROP_CLASS);
      })
    };
  }
  function render(instance) {
    var popper = div();
    var box = div();
    box.className = BOX_CLASS;
    box.setAttribute('data-state', 'hidden');
    box.setAttribute('tabindex', '-1');
    var content = div();
    content.className = CONTENT_CLASS;
    content.setAttribute('data-state', 'hidden');
    setContent(content, instance.props);
    popper.appendChild(box);
    box.appendChild(content);
    onUpdate(instance.props, instance.props);
    function onUpdate(prevProps, nextProps) {
      var _getChildren = getChildren(popper),
        box = _getChildren.box,
        content = _getChildren.content,
        arrow = _getChildren.arrow;
      if (nextProps.theme) {
        box.setAttribute('data-theme', nextProps.theme);
      } else {
        box.removeAttribute('data-theme');
      }
      if (typeof nextProps.animation === 'string') {
        box.setAttribute('data-animation', nextProps.animation);
      } else {
        box.removeAttribute('data-animation');
      }
      if (nextProps.inertia) {
        box.setAttribute('data-inertia', '');
      } else {
        box.removeAttribute('data-inertia');
      }
      box.style.maxWidth = typeof nextProps.maxWidth === 'number' ? nextProps.maxWidth + "px" : nextProps.maxWidth;
      if (nextProps.role) {
        box.setAttribute('role', nextProps.role);
      } else {
        box.removeAttribute('role');
      }
      if (prevProps.content !== nextProps.content || prevProps.allowHTML !== nextProps.allowHTML) {
        setContent(content, instance.props);
      }
      if (nextProps.arrow) {
        if (!arrow) {
          box.appendChild(createArrowElement(nextProps.arrow));
        } else if (prevProps.arrow !== nextProps.arrow) {
          box.removeChild(arrow);
          box.appendChild(createArrowElement(nextProps.arrow));
        }
      } else if (arrow) {
        box.removeChild(arrow);
      }
    }
    return {
      popper: popper,
      onUpdate: onUpdate
    };
  } // Runtime check to identify if the render function is the default one; this
  // way we can apply default CSS transitions logic and it can be tree-shaken away

  render.$$tippy = true;
  var idCounter = 1;
  var mouseMoveListeners = []; // Used by `hideAll()`

  var mountedInstances = [];
  function createTippy(reference, passedProps) {
    var props = evaluateProps(reference, Object.assign({}, defaultProps, getExtendedPassedProps(removeUndefinedProps(passedProps)))); // ===========================================================================
    // 🔒 Private members
    // ===========================================================================

    var showTimeout;
    var hideTimeout;
    var scheduleHideAnimationFrame;
    var isVisibleFromClick = false;
    var didHideDueToDocumentMouseDown = false;
    var didTouchMove = false;
    var ignoreOnFirstUpdate = false;
    var lastTriggerEvent;
    var currentTransitionEndListener;
    var onFirstUpdate;
    var listeners = [];
    var debouncedOnMouseMove = debounce(onMouseMove, props.interactiveDebounce);
    var currentTarget; // ===========================================================================
    // 🔑 Public members
    // ===========================================================================

    var id = idCounter++;
    var popperInstance = null;
    var plugins = unique(props.plugins);
    var state = {
      // Is the instance currently enabled?
      isEnabled: true,
      // Is the tippy currently showing and not transitioning out?
      isVisible: false,
      // Has the instance been destroyed?
      isDestroyed: false,
      // Is the tippy currently mounted to the DOM?
      isMounted: false,
      // Has the tippy finished transitioning in?
      isShown: false
    };
    var instance = {
      // properties
      id: id,
      reference: reference,
      popper: div(),
      popperInstance: popperInstance,
      props: props,
      state: state,
      plugins: plugins,
      // methods
      clearDelayTimeouts: clearDelayTimeouts,
      setProps: setProps,
      setContent: setContent,
      show: show,
      hide: hide,
      hideWithInteractivity: hideWithInteractivity,
      enable: enable,
      disable: disable,
      unmount: unmount,
      destroy: destroy
    }; // TODO: Investigate why this early return causes a TDZ error in the tests —
    // it doesn't seem to happen in the browser

    /* istanbul ignore if */

    if (!props.render) {
      return instance;
    } // ===========================================================================
    // Initial mutations
    // ===========================================================================

    var _props$render = props.render(instance),
      popper = _props$render.popper,
      onUpdate = _props$render.onUpdate;
    popper.setAttribute('data-tippy-root', '');
    popper.id = "tippy-" + instance.id;
    instance.popper = popper;
    reference._tippy = instance;
    popper._tippy = instance;
    var pluginsHooks = plugins.map(function (plugin) {
      return plugin.fn(instance);
    });
    var hasAriaExpanded = reference.hasAttribute('aria-expanded');
    addListeners();
    handleAriaExpandedAttribute();
    handleStyles();
    invokeHook('onCreate', [instance]);
    if (props.showOnCreate) {
      scheduleShow();
    } // Prevent a tippy with a delay from hiding if the cursor left then returned
    // before it started hiding

    popper.addEventListener('mouseenter', function () {
      if (instance.props.interactive && instance.state.isVisible) {
        instance.clearDelayTimeouts();
      }
    });
    popper.addEventListener('mouseleave', function () {
      if (instance.props.interactive && instance.props.trigger.indexOf('mouseenter') >= 0) {
        getDocument().addEventListener('mousemove', debouncedOnMouseMove);
      }
    });
    return instance; // ===========================================================================
    // 🔒 Private methods
    // ===========================================================================

    function getNormalizedTouchSettings() {
      var touch = instance.props.touch;
      return Array.isArray(touch) ? touch : [touch, 0];
    }
    function getIsCustomTouchBehavior() {
      return getNormalizedTouchSettings()[0] === 'hold';
    }
    function getIsDefaultRenderFn() {
      var _instance$props$rende;

      // @ts-ignore
      return !!((_instance$props$rende = instance.props.render) != null && _instance$props$rende.$$tippy);
    }
    function getCurrentTarget() {
      return currentTarget || reference;
    }
    function getDocument() {
      var parent = getCurrentTarget().parentNode;
      return parent ? getOwnerDocument(parent) : document;
    }
    function getDefaultTemplateChildren() {
      return getChildren(popper);
    }
    function getDelay(isShow) {
      // For touch or keyboard input, force `0` delay for UX reasons
      // Also if the instance is mounted but not visible (transitioning out),
      // ignore delay
      if (instance.state.isMounted && !instance.state.isVisible || currentInput.isTouch || lastTriggerEvent && lastTriggerEvent.type === 'focus') {
        return 0;
      }
      return getValueAtIndexOrReturn(instance.props.delay, isShow ? 0 : 1, defaultProps.delay);
    }
    function handleStyles(fromHide) {
      if (fromHide === void 0) {
        fromHide = false;
      }
      popper.style.pointerEvents = instance.props.interactive && !fromHide ? '' : 'none';
      popper.style.zIndex = "" + instance.props.zIndex;
    }
    function invokeHook(hook, args, shouldInvokePropsHook) {
      if (shouldInvokePropsHook === void 0) {
        shouldInvokePropsHook = true;
      }
      pluginsHooks.forEach(function (pluginHooks) {
        if (pluginHooks[hook]) {
          pluginHooks[hook].apply(pluginHooks, args);
        }
      });
      if (shouldInvokePropsHook) {
        var _instance$props;
        (_instance$props = instance.props)[hook].apply(_instance$props, args);
      }
    }
    function handleAriaContentAttribute() {
      var aria = instance.props.aria;
      if (!aria.content) {
        return;
      }
      var attr = "aria-" + aria.content;
      var id = popper.id;
      var nodes = normalizeToArray(instance.props.triggerTarget || reference);
      nodes.forEach(function (node) {
        var currentValue = node.getAttribute(attr);
        if (instance.state.isVisible) {
          node.setAttribute(attr, currentValue ? currentValue + " " + id : id);
        } else {
          var nextValue = currentValue && currentValue.replace(id, '').trim();
          if (nextValue) {
            node.setAttribute(attr, nextValue);
          } else {
            node.removeAttribute(attr);
          }
        }
      });
    }
    function handleAriaExpandedAttribute() {
      if (hasAriaExpanded || !instance.props.aria.expanded) {
        return;
      }
      var nodes = normalizeToArray(instance.props.triggerTarget || reference);
      nodes.forEach(function (node) {
        if (instance.props.interactive) {
          node.setAttribute('aria-expanded', instance.state.isVisible && node === getCurrentTarget() ? 'true' : 'false');
        } else {
          node.removeAttribute('aria-expanded');
        }
      });
    }
    function cleanupInteractiveMouseListeners() {
      getDocument().removeEventListener('mousemove', debouncedOnMouseMove);
      mouseMoveListeners = mouseMoveListeners.filter(function (listener) {
        return listener !== debouncedOnMouseMove;
      });
    }
    function onDocumentPress(event) {
      // Moved finger to scroll instead of an intentional tap outside
      if (currentInput.isTouch) {
        if (didTouchMove || event.type === 'mousedown') {
          return;
        }
      }
      var actualTarget = event.composedPath && event.composedPath()[0] || event.target; // Clicked on interactive popper

      if (instance.props.interactive && actualContains(popper, actualTarget)) {
        return;
      } // Clicked on the event listeners target

      if (normalizeToArray(instance.props.triggerTarget || reference).some(function (el) {
        return actualContains(el, actualTarget);
      })) {
        if (currentInput.isTouch) {
          return;
        }
        if (instance.state.isVisible && instance.props.trigger.indexOf('click') >= 0) {
          return;
        }
      } else {
        invokeHook('onClickOutside', [instance, event]);
      }
      if (instance.props.hideOnClick === true) {
        instance.clearDelayTimeouts();
        instance.hide(); // `mousedown` event is fired right before `focus` if pressing the
        // currentTarget. This lets a tippy with `focus` trigger know that it
        // should not show

        didHideDueToDocumentMouseDown = true;
        setTimeout(function () {
          didHideDueToDocumentMouseDown = false;
        }); // The listener gets added in `scheduleShow()`, but this may be hiding it
        // before it shows, and hide()'s early bail-out behavior can prevent it
        // from being cleaned up

        if (!instance.state.isMounted) {
          removeDocumentPress();
        }
      }
    }
    function onTouchMove() {
      didTouchMove = true;
    }
    function onTouchStart() {
      didTouchMove = false;
    }
    function addDocumentPress() {
      var doc = getDocument();
      doc.addEventListener('mousedown', onDocumentPress, true);
      doc.addEventListener('touchend', onDocumentPress, TOUCH_OPTIONS);
      doc.addEventListener('touchstart', onTouchStart, TOUCH_OPTIONS);
      doc.addEventListener('touchmove', onTouchMove, TOUCH_OPTIONS);
    }
    function removeDocumentPress() {
      var doc = getDocument();
      doc.removeEventListener('mousedown', onDocumentPress, true);
      doc.removeEventListener('touchend', onDocumentPress, TOUCH_OPTIONS);
      doc.removeEventListener('touchstart', onTouchStart, TOUCH_OPTIONS);
      doc.removeEventListener('touchmove', onTouchMove, TOUCH_OPTIONS);
    }
    function onTransitionedOut(duration, callback) {
      onTransitionEnd(duration, function () {
        if (!instance.state.isVisible && popper.parentNode && popper.parentNode.contains(popper)) {
          callback();
        }
      });
    }
    function onTransitionedIn(duration, callback) {
      onTransitionEnd(duration, callback);
    }
    function onTransitionEnd(duration, callback) {
      var box = getDefaultTemplateChildren().box;
      function listener(event) {
        if (event.target === box) {
          updateTransitionEndListener(box, 'remove', listener);
          callback();
        }
      } // Make callback synchronous if duration is 0
      // `transitionend` won't fire otherwise

      if (duration === 0) {
        return callback();
      }
      updateTransitionEndListener(box, 'remove', currentTransitionEndListener);
      updateTransitionEndListener(box, 'add', listener);
      currentTransitionEndListener = listener;
    }
    function on(eventType, handler, options) {
      if (options === void 0) {
        options = false;
      }
      var nodes = normalizeToArray(instance.props.triggerTarget || reference);
      nodes.forEach(function (node) {
        node.addEventListener(eventType, handler, options);
        listeners.push({
          node: node,
          eventType: eventType,
          handler: handler,
          options: options
        });
      });
    }
    function addListeners() {
      if (getIsCustomTouchBehavior()) {
        on('touchstart', onTrigger, {
          passive: true
        });
        on('touchend', onMouseLeave, {
          passive: true
        });
      }
      splitBySpaces(instance.props.trigger).forEach(function (eventType) {
        if (eventType === 'manual') {
          return;
        }
        on(eventType, onTrigger);
        switch (eventType) {
          case 'mouseenter':
            on('mouseleave', onMouseLeave);
            break;
          case 'focus':
            on(isIE11 ? 'focusout' : 'blur', onBlurOrFocusOut);
            break;
          case 'focusin':
            on('focusout', onBlurOrFocusOut);
            break;
        }
      });
    }
    function removeListeners() {
      listeners.forEach(function (_ref) {
        var node = _ref.node,
          eventType = _ref.eventType,
          handler = _ref.handler,
          options = _ref.options;
        node.removeEventListener(eventType, handler, options);
      });
      listeners = [];
    }
    function onTrigger(event) {
      var _lastTriggerEvent;
      var shouldScheduleClickHide = false;
      if (!instance.state.isEnabled || isEventListenerStopped(event) || didHideDueToDocumentMouseDown) {
        return;
      }
      var wasFocused = ((_lastTriggerEvent = lastTriggerEvent) == null ? void 0 : _lastTriggerEvent.type) === 'focus';
      lastTriggerEvent = event;
      currentTarget = event.currentTarget;
      handleAriaExpandedAttribute();
      if (!instance.state.isVisible && isMouseEvent(event)) {
        // If scrolling, `mouseenter` events can be fired if the cursor lands
        // over a new target, but `mousemove` events don't get fired. This
        // causes interactive tooltips to get stuck open until the cursor is
        // moved
        mouseMoveListeners.forEach(function (listener) {
          return listener(event);
        });
      } // Toggle show/hide when clicking click-triggered tooltips

      if (event.type === 'click' && (instance.props.trigger.indexOf('mouseenter') < 0 || isVisibleFromClick) && instance.props.hideOnClick !== false && instance.state.isVisible) {
        shouldScheduleClickHide = true;
      } else {
        scheduleShow(event);
      }
      if (event.type === 'click') {
        isVisibleFromClick = !shouldScheduleClickHide;
      }
      if (shouldScheduleClickHide && !wasFocused) {
        scheduleHide(event);
      }
    }
    function onMouseMove(event) {
      var target = event.target;
      var isCursorOverReferenceOrPopper = getCurrentTarget().contains(target) || popper.contains(target);
      if (event.type === 'mousemove' && isCursorOverReferenceOrPopper) {
        return;
      }
      var popperTreeData = getNestedPopperTree().concat(popper).map(function (popper) {
        var _instance$popperInsta;
        var instance = popper._tippy;
        var state = (_instance$popperInsta = instance.popperInstance) == null ? void 0 : _instance$popperInsta.state;
        if (state) {
          return {
            popperRect: popper.getBoundingClientRect(),
            popperState: state,
            props: props
          };
        }
        return null;
      }).filter(Boolean);
      if (isCursorOutsideInteractiveBorder(popperTreeData, event)) {
        cleanupInteractiveMouseListeners();
        scheduleHide(event);
      }
    }
    function onMouseLeave(event) {
      var shouldBail = isEventListenerStopped(event) || instance.props.trigger.indexOf('click') >= 0 && isVisibleFromClick;
      if (shouldBail) {
        return;
      }
      if (instance.props.interactive) {
        instance.hideWithInteractivity(event);
        return;
      }
      scheduleHide(event);
    }
    function onBlurOrFocusOut(event) {
      if (instance.props.trigger.indexOf('focusin') < 0 && event.target !== getCurrentTarget()) {
        return;
      } // If focus was moved to within the popper

      if (instance.props.interactive && event.relatedTarget && popper.contains(event.relatedTarget)) {
        return;
      }
      scheduleHide(event);
    }
    function isEventListenerStopped(event) {
      return currentInput.isTouch ? getIsCustomTouchBehavior() !== event.type.indexOf('touch') >= 0 : false;
    }
    function createPopperInstance() {
      destroyPopperInstance();
      var _instance$props2 = instance.props,
        popperOptions = _instance$props2.popperOptions,
        placement = _instance$props2.placement,
        offset = _instance$props2.offset,
        getReferenceClientRect = _instance$props2.getReferenceClientRect,
        moveTransition = _instance$props2.moveTransition;
      var arrow = getIsDefaultRenderFn() ? getChildren(popper).arrow : null;
      var computedReference = getReferenceClientRect ? {
        getBoundingClientRect: getReferenceClientRect,
        contextElement: getReferenceClientRect.contextElement || getCurrentTarget()
      } : reference;
      var tippyModifier = {
        name: '$$tippy',
        enabled: true,
        phase: 'beforeWrite',
        requires: ['computeStyles'],
        fn: function fn(_ref2) {
          var state = _ref2.state;
          if (getIsDefaultRenderFn()) {
            var _getDefaultTemplateCh = getDefaultTemplateChildren(),
              box = _getDefaultTemplateCh.box;
            ['placement', 'reference-hidden', 'escaped'].forEach(function (attr) {
              if (attr === 'placement') {
                box.setAttribute('data-placement', state.placement);
              } else {
                if (state.attributes.popper["data-popper-" + attr]) {
                  box.setAttribute("data-" + attr, '');
                } else {
                  box.removeAttribute("data-" + attr);
                }
              }
            });
            state.attributes.popper = {};
          }
        }
      };
      var modifiers = [{
        name: 'offset',
        options: {
          offset: offset
        }
      }, {
        name: 'preventOverflow',
        options: {
          padding: {
            top: 2,
            bottom: 2,
            left: 5,
            right: 5
          }
        }
      }, {
        name: 'flip',
        options: {
          padding: 5
        }
      }, {
        name: 'computeStyles',
        options: {
          adaptive: !moveTransition
        }
      }, tippyModifier];
      if (getIsDefaultRenderFn() && arrow) {
        modifiers.push({
          name: 'arrow',
          options: {
            element: arrow,
            padding: 3
          }
        });
      }
      modifiers.push.apply(modifiers, (popperOptions == null ? void 0 : popperOptions.modifiers) || []);
      instance.popperInstance = createPopper(computedReference, popper, Object.assign({}, popperOptions, {
        placement: placement,
        onFirstUpdate: onFirstUpdate,
        modifiers: modifiers
      }));
    }
    function destroyPopperInstance() {
      if (instance.popperInstance) {
        instance.popperInstance.destroy();
        instance.popperInstance = null;
      }
    }
    function mount() {
      var appendTo = instance.props.appendTo;
      var parentNode; // By default, we'll append the popper to the triggerTargets's parentNode so
      // it's directly after the reference element so the elements inside the
      // tippy can be tabbed to
      // If there are clipping issues, the user can specify a different appendTo
      // and ensure focus management is handled correctly manually

      var node = getCurrentTarget();
      if (instance.props.interactive && appendTo === TIPPY_DEFAULT_APPEND_TO || appendTo === 'parent') {
        parentNode = node.parentNode;
      } else {
        parentNode = invokeWithArgsOrReturn(appendTo, [node]);
      } // The popper element needs to exist on the DOM before its position can be
      // updated as Popper needs to read its dimensions

      if (!parentNode.contains(popper)) {
        parentNode.appendChild(popper);
      }
      instance.state.isMounted = true;
      createPopperInstance();
    }
    function getNestedPopperTree() {
      return arrayFrom(popper.querySelectorAll('[data-tippy-root]'));
    }
    function scheduleShow(event) {
      instance.clearDelayTimeouts();
      if (event) {
        invokeHook('onTrigger', [instance, event]);
      }
      addDocumentPress();
      var delay = getDelay(true);
      var _getNormalizedTouchSe = getNormalizedTouchSettings(),
        touchValue = _getNormalizedTouchSe[0],
        touchDelay = _getNormalizedTouchSe[1];
      if (currentInput.isTouch && touchValue === 'hold' && touchDelay) {
        delay = touchDelay;
      }
      if (delay) {
        showTimeout = setTimeout(function () {
          instance.show();
        }, delay);
      } else {
        instance.show();
      }
    }
    function scheduleHide(event) {
      instance.clearDelayTimeouts();
      invokeHook('onUntrigger', [instance, event]);
      if (!instance.state.isVisible) {
        removeDocumentPress();
        return;
      } // For interactive tippies, scheduleHide is added to a document.body handler
      // from onMouseLeave so must intercept scheduled hides from mousemove/leave
      // events when trigger contains mouseenter and click, and the tip is
      // currently shown as a result of a click.

      if (instance.props.trigger.indexOf('mouseenter') >= 0 && instance.props.trigger.indexOf('click') >= 0 && ['mouseleave', 'mousemove'].indexOf(event.type) >= 0 && isVisibleFromClick) {
        return;
      }
      var delay = getDelay(false);
      if (delay) {
        hideTimeout = setTimeout(function () {
          if (instance.state.isVisible) {
            instance.hide();
          }
        }, delay);
      } else {
        // Fixes a `transitionend` problem when it fires 1 frame too
        // late sometimes, we don't want hide() to be called.
        scheduleHideAnimationFrame = requestAnimationFrame(function () {
          instance.hide();
        });
      }
    } // ===========================================================================
    // 🔑 Public methods
    // ===========================================================================

    function enable() {
      instance.state.isEnabled = true;
    }
    function disable() {
      // Disabling the instance should also hide it
      // https://github.com/atomiks/tippy.js-react/issues/106
      instance.hide();
      instance.state.isEnabled = false;
    }
    function clearDelayTimeouts() {
      clearTimeout(showTimeout);
      clearTimeout(hideTimeout);
      cancelAnimationFrame(scheduleHideAnimationFrame);
    }
    function setProps(partialProps) {
      if (instance.state.isDestroyed) {
        return;
      }
      invokeHook('onBeforeUpdate', [instance, partialProps]);
      removeListeners();
      var prevProps = instance.props;
      var nextProps = evaluateProps(reference, Object.assign({}, prevProps, removeUndefinedProps(partialProps), {
        ignoreAttributes: true
      }));
      instance.props = nextProps;
      addListeners();
      if (prevProps.interactiveDebounce !== nextProps.interactiveDebounce) {
        cleanupInteractiveMouseListeners();
        debouncedOnMouseMove = debounce(onMouseMove, nextProps.interactiveDebounce);
      } // Ensure stale aria-expanded attributes are removed

      if (prevProps.triggerTarget && !nextProps.triggerTarget) {
        normalizeToArray(prevProps.triggerTarget).forEach(function (node) {
          node.removeAttribute('aria-expanded');
        });
      } else if (nextProps.triggerTarget) {
        reference.removeAttribute('aria-expanded');
      }
      handleAriaExpandedAttribute();
      handleStyles();
      if (onUpdate) {
        onUpdate(prevProps, nextProps);
      }
      if (instance.popperInstance) {
        createPopperInstance(); // Fixes an issue with nested tippies if they are all getting re-rendered,
        // and the nested ones get re-rendered first.
        // https://github.com/atomiks/tippyjs-react/issues/177
        // TODO: find a cleaner / more efficient solution(!)

        getNestedPopperTree().forEach(function (nestedPopper) {
          // React (and other UI libs likely) requires a rAF wrapper as it flushes
          // its work in one
          requestAnimationFrame(nestedPopper._tippy.popperInstance.forceUpdate);
        });
      }
      invokeHook('onAfterUpdate', [instance, partialProps]);
    }
    function setContent(content) {
      instance.setProps({
        content: content
      });
    }
    function show() {
      var isAlreadyVisible = instance.state.isVisible;
      var isDestroyed = instance.state.isDestroyed;
      var isDisabled = !instance.state.isEnabled;
      var isTouchAndTouchDisabled = currentInput.isTouch && !instance.props.touch;
      var duration = getValueAtIndexOrReturn(instance.props.duration, 0, defaultProps.duration);
      if (isAlreadyVisible || isDestroyed || isDisabled || isTouchAndTouchDisabled) {
        return;
      } // Normalize `disabled` behavior across browsers.
      // Firefox allows events on disabled elements, but Chrome doesn't.
      // Using a wrapper element (i.e. <span>) is recommended.

      if (getCurrentTarget().hasAttribute('disabled')) {
        return;
      }
      invokeHook('onShow', [instance], false);
      if (instance.props.onShow(instance) === false) {
        return;
      }
      instance.state.isVisible = true;
      if (getIsDefaultRenderFn()) {
        popper.style.visibility = 'visible';
      }
      handleStyles();
      addDocumentPress();
      if (!instance.state.isMounted) {
        popper.style.transition = 'none';
      } // If flipping to the opposite side after hiding at least once, the
      // animation will use the wrong placement without resetting the duration

      if (getIsDefaultRenderFn()) {
        var _getDefaultTemplateCh2 = getDefaultTemplateChildren(),
          box = _getDefaultTemplateCh2.box,
          content = _getDefaultTemplateCh2.content;
        setTransitionDuration([box, content], 0);
      }
      onFirstUpdate = function onFirstUpdate() {
        var _instance$popperInsta2;
        if (!instance.state.isVisible || ignoreOnFirstUpdate) {
          return;
        }
        ignoreOnFirstUpdate = true; // reflow

        void popper.offsetHeight;
        popper.style.transition = instance.props.moveTransition;
        if (getIsDefaultRenderFn() && instance.props.animation) {
          var _getDefaultTemplateCh3 = getDefaultTemplateChildren(),
            _box = _getDefaultTemplateCh3.box,
            _content = _getDefaultTemplateCh3.content;
          setTransitionDuration([_box, _content], duration);
          setVisibilityState([_box, _content], 'visible');
        }
        handleAriaContentAttribute();
        handleAriaExpandedAttribute();
        pushIfUnique(mountedInstances, instance); // certain modifiers (e.g. `maxSize`) require a second update after the
        // popper has been positioned for the first time

        (_instance$popperInsta2 = instance.popperInstance) == null ? void 0 : _instance$popperInsta2.forceUpdate();
        invokeHook('onMount', [instance]);
        if (instance.props.animation && getIsDefaultRenderFn()) {
          onTransitionedIn(duration, function () {
            instance.state.isShown = true;
            invokeHook('onShown', [instance]);
          });
        }
      };
      mount();
    }
    function hide() {
      var isAlreadyHidden = !instance.state.isVisible;
      var isDestroyed = instance.state.isDestroyed;
      var isDisabled = !instance.state.isEnabled;
      var duration = getValueAtIndexOrReturn(instance.props.duration, 1, defaultProps.duration);
      if (isAlreadyHidden || isDestroyed || isDisabled) {
        return;
      }
      invokeHook('onHide', [instance], false);
      if (instance.props.onHide(instance) === false) {
        return;
      }
      instance.state.isVisible = false;
      instance.state.isShown = false;
      ignoreOnFirstUpdate = false;
      isVisibleFromClick = false;
      if (getIsDefaultRenderFn()) {
        popper.style.visibility = 'hidden';
      }
      cleanupInteractiveMouseListeners();
      removeDocumentPress();
      handleStyles(true);
      if (getIsDefaultRenderFn()) {
        var _getDefaultTemplateCh4 = getDefaultTemplateChildren(),
          box = _getDefaultTemplateCh4.box,
          content = _getDefaultTemplateCh4.content;
        if (instance.props.animation) {
          setTransitionDuration([box, content], duration);
          setVisibilityState([box, content], 'hidden');
        }
      }
      handleAriaContentAttribute();
      handleAriaExpandedAttribute();
      if (instance.props.animation) {
        if (getIsDefaultRenderFn()) {
          onTransitionedOut(duration, instance.unmount);
        }
      } else {
        instance.unmount();
      }
    }
    function hideWithInteractivity(event) {
      getDocument().addEventListener('mousemove', debouncedOnMouseMove);
      pushIfUnique(mouseMoveListeners, debouncedOnMouseMove);
      debouncedOnMouseMove(event);
    }
    function unmount() {
      if (instance.state.isVisible) {
        instance.hide();
      }
      if (!instance.state.isMounted) {
        return;
      }
      destroyPopperInstance(); // If a popper is not interactive, it will be appended outside the popper
      // tree by default. This seems mainly for interactive tippies, but we should
      // find a workaround if possible

      getNestedPopperTree().forEach(function (nestedPopper) {
        nestedPopper._tippy.unmount();
      });
      if (popper.parentNode) {
        popper.parentNode.removeChild(popper);
      }
      mountedInstances = mountedInstances.filter(function (i) {
        return i !== instance;
      });
      instance.state.isMounted = false;
      invokeHook('onHidden', [instance]);
    }
    function destroy() {
      if (instance.state.isDestroyed) {
        return;
      }
      instance.clearDelayTimeouts();
      instance.unmount();
      removeListeners();
      delete reference._tippy;
      instance.state.isDestroyed = true;
      invokeHook('onDestroy', [instance]);
    }
  }
  function tippy(targets, optionalProps) {
    if (optionalProps === void 0) {
      optionalProps = {};
    }
    var plugins = defaultProps.plugins.concat(optionalProps.plugins || []);
    bindGlobalEventListeners();
    var passedProps = Object.assign({}, optionalProps, {
      plugins: plugins
    });
    var elements = getArrayOfElements(targets);
    var instances = elements.reduce(function (acc, reference) {
      var instance = reference && createTippy(reference, passedProps);
      if (instance) {
        acc.push(instance);
      }
      return acc;
    }, []);
    return isElement(targets) ? instances[0] : instances;
  }
  tippy.defaultProps = defaultProps;
  tippy.setDefaultProps = setDefaultProps;
  tippy.currentInput = currentInput;

  // every time the popper is destroyed (i.e. a new target), removing the styles
  // and causing transitions to break for singletons when the console is open, but
  // most notably for non-transform styles being used, `gpuAcceleration: false`.

  Object.assign({}, applyStyles$1, {
    effect: function effect(_ref) {
      var state = _ref.state;
      var initialStyles = {
        popper: {
          position: state.options.strategy,
          left: '0',
          top: '0',
          margin: '0'
        },
        arrow: {
          position: 'absolute'
        },
        reference: {}
      };
      Object.assign(state.elements.popper.style, initialStyles.popper);
      state.styles = initialStyles;
      if (state.elements.arrow) {
        Object.assign(state.elements.arrow.style, initialStyles.arrow);
      } // intentionally return no cleanup function
      // return () => { ... }
    }
  });

  tippy.setDefaultProps({
    render: render
  });

  // import 'tippy.js/dist/tippy.css';

  /**
   * Utility methods
   */

  // Determine element visibility
  var isElementHidden = function isElementHidden($el) {
    if ($el.getAttribute('hidden') || $el.offsetWidth === 0 && $el.offsetHeight === 0) {
      return true;
    } else {
      var compStyles = getComputedStyle($el);
      return compStyles.getPropertyValue('display') === 'none';
    }
  };

  // Escape HTML, encode HTML symbols
  var escapeHTML = function escapeHTML(text) {
    var $div = document.createElement('div');
    $div.textContent = text;
    return $div.innerHTML.replaceAll('"', '&quot;').replaceAll("'", '&#039;').replaceAll("`", '&#x60;');
  };

  /**
   * Jooa11y Translation object
   */
  var Lang = {
    langStrings: {},
    addI18n: function addI18n(strings) {
      this.langStrings = strings;
    },
    _: function _(string) {
      return this.translate(string);
    },
    sprintf: function sprintf(string) {
      var transString = this._(string);
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }
      if (args && args.length) {
        args.forEach(function (arg) {
          transString = transString.replace(/%\([a-zA-z]+\)/, arg);
        });
      }
      return transString;
    },
    translate: function translate(string) {
      return this.langStrings[string] || string;
    }
  };

  /**
   * Jooa11y default options
   */
  var defaultOptions = {
    langCode: 'en',
    // Target area to scan.
    checkRoot: 'main',
    // A content container

    // Readability configuration.
    readabilityRoot: 'main',
    readabilityLang: 'en',
    // Inclusions and exclusions. Use commas to seperate classes or elements.
    containerIgnore: '.jooa11y-ignore',
    // Ignore specific regions.
    outlineIgnore: '',
    // Exclude headings from outline panel.
    headerIgnore: '',
    // Ignore specific headings. E.g. "h1.jumbotron-heading"
    imageIgnore: '',
    // Ignore specific images.
    linkIgnore: '',
    // Ignore specific links.
    linkIgnoreSpan: 'noscript, span.sr-only-example',
    // Ignore specific classes within links. Example: <a href="#">learn more <span class="sr-only-example">(opens new tab)</span></a>.
    linksToFlag: '',
    // Links you don't want your content editors pointing to (e.g. development environments).

    // Embedded content.
    videoContent: "video, [src*='youtube.com'], [src*='vimeo.com'], [src*='yuja.com'], [src*='panopto.com']",
    audioContent: "audio, [src*='soundcloud.com'], [src*='simplecast.com'], [src*='podbean.com'], [src*='buzzsprout.com'], [src*='blubrry.com'], [src*='transistor.fm'], [src*='fusebox.fm'], [src*='libsyn.com']",
    embeddedContent: '',
    // Alt Text stop words.
    suspiciousAltWords: ['image', 'graphic', 'picture', 'photo'],
    placeholderAltStopWords: ['alt', 'image', 'photo', 'decorative', 'photo', 'placeholder', 'placeholder image', 'spacer', '.'],
    // Link Text stop words
    partialAltStopWords: ['click', 'click here', 'click here for more', 'click here to learn more', 'click here to learn more.', 'check out', 'download', 'download here', 'download here.', 'find out', 'find out more', 'find out more.', 'form', 'here', 'here.', 'info', 'information', 'link', 'learn', 'learn more', 'learn more.', 'learn to', 'more', 'page', 'paper', 'read more', 'read', 'read this', 'this', 'this page', 'this page.', 'this website', 'this website.', 'view', 'view our', 'website', '.'],
    warningAltWords: ['< ', ' >', 'click here'],
    // Link Text (Advanced)
    newWindowPhrases: ['external', 'new tab', 'new window', 'pop-up', 'pop up'],
    // Link Text (Advanced). Only some items in list would need to be translated.
    fileTypePhrases: ['document', 'pdf', 'doc', 'docx', 'word', 'mp3', 'ppt', 'text', 'pptx', 'powerpoint', 'txt', 'exe', 'dmg', 'rtf', 'install', 'windows', 'macos', 'spreadsheet', 'worksheet', 'csv', 'xls', 'xlsx', 'video', 'mp4', 'mov', 'avi']
  };
  defaultOptions.embeddedContent = defaultOptions.videoContent + ", " + defaultOptions.audioContent;

  /**
   * Load and validate options
   *
   * @param {Jooa11y}  instance
   * @param {Object} customOptions
   * @returns {Object}
   */
  var loadOptions = function loadOptions(instance, customOptions) {
    var options = customOptions ? Object.assign(defaultOptions, customOptions) : defaultOptions;

    // Check required options
    ['langCode', 'checkRoot'].forEach(function (option) {
      if (!options[option]) {
        throw new Error("Option [" + option + "] is required");
      }
    });
    if (!options.readabilityRoot) {
      options.readabilityRoot = options.checkRoot;
    }

    // Container ignores apply to self and children.
    if (options.containerIgnore) {
      var containerSelectors = options.containerIgnore.split(',').map(function (el) {
        return el + " *, " + el;
      });
      options.containerIgnore = '[aria-hidden="true"], #jooa11y-container *, .jooa11y-instance *, ' + containerSelectors.join(', ');
    } else {
      options.containerIgnore = '[aria-hidden="true"], #jooa11y-container *, .jooa11y-instance *';
    }
    instance.containerIgnore = options.containerIgnore;

    // Images ignore
    instance.imageIgnore = instance.containerIgnore + ', [role="presentation"], [src^="https://trck.youvisit.com"]';
    if (options.imageIgnore) {
      instance.imageIgnore = options.imageIgnore + ',' + instance.imageIgnore;
    }

    // Ignore specific headings
    instance.headerIgnore = options.containerIgnore;
    if (options.headerIgnore) {
      instance.headerIgnore = options.headerIgnore + ',' + instance.headerIgnore;
    }

    // Links ignore defaults plus jooa11y links.
    instance.linkIgnore = instance.containerIgnore + ', [aria-hidden="true"], .anchorjs-link';
    if (options.linkIgnore) {
      instance.linkIgnore = options.linkIgnore + ',' + instance.linkIgnore;
    }
    return options;
  };

  /**
   * Jooa11y class
   */
  var Jooa11y = /*#__PURE__*/function () {
    function Jooa11y(options) {
      var _this = this;
      // ----------------------------------------------------------------------
      // Check all
      // ----------------------------------------------------------------------
      this.checkAll = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _this.errorCount = 0;
              _this.warningCount = 0;
              _this.$root = document.querySelector(_this.options.checkRoot);
              _this.findElements();

              //Ruleset checks
              _this.checkHeaders();
              _this.checkLinkText();
              _this.checkUnderline();
              _this.checkAltText();
              if (localStorage.getItem("jooa11y-remember-contrast") === "On") {
                _this.checkContrast();
              }
              if (localStorage.getItem("jooa11y-remember-labels") === "On") {
                _this.checkLabels();
              }
              if (localStorage.getItem("jooa11y-remember-links-advanced") === "On") {
                _this.checkLinksAdvanced();
              }
              if (localStorage.getItem("jooa11y-remember-readability") === "On") {
                _this.checkReadability();
              }
              _this.checkEmbeddedContent();
              _this.checkQA();

              //Update panel
              if (_this.panelActive) {
                _this.resetAll();
              } else {
                _this.updatePanel();
              }
              _this.initializeTooltips();
              _this.detectOverflow();
              _this.nudge();

              //Don't show badge when panel is opened.
              if (!document.getElementsByClassName('jooa11y-on').length) {
                _this.updateBadge();
              }
            case 19:
            case "end":
              return _context.stop();
          }
        }, _callee);
      }));
      // ============================================================
      // Nudge buttons if they overlap.
      // ============================================================
      this.nudge = function () {
        var jooa11yInstance = document.querySelectorAll('.jooa11y-instance, .jooa11y-instance-inline');
        jooa11yInstance.forEach(function ($el) {
          var sibling = $el.nextElementSibling;
          if (sibling !== null && (sibling.classList.contains("jooa11y-instance") || sibling.classList.contains("jooa11y-instance-inline"))) {
            sibling.querySelector("button").setAttribute("style", "margin: -10px -20px !important;");
          }
        });
      };
      // ----------------------------------------------------------------------
      // Main panel: Build Show Outline and Settings tabs.
      // ----------------------------------------------------------------------
      this.buildPanel = function () {
        var $outlineToggle = document.getElementById("jooa11y-outline-toggle");
        var $outlinePanel = document.getElementById("jooa11y-outline-panel");
        var $outlineList = document.getElementById("jooa11y-outline-list");
        var $settingsToggle = document.getElementById("jooa11y-settings-toggle");
        var $settingsPanel = document.getElementById("jooa11y-settings-panel");
        var $settingsContent = document.getElementById("jooa11y-settings-content");
        var $headingAnnotations = document.querySelectorAll(".jooa11y-heading-label");

        //Show outline panel
        $outlineToggle.addEventListener('click', function () {
          if ($outlineToggle.getAttribute("aria-expanded") === "true") {
            $outlineToggle.classList.remove("jooa11y-outline-active");
            $outlinePanel.classList.remove("jooa11y-active");
            $outlineToggle.textContent = Lang._('SHOW_OUTLINE');
            $outlineToggle.setAttribute("aria-expanded", "false");
            localStorage.setItem("jooa11y-remember-outline", "Closed");
          } else {
            $outlineToggle.classList.add("jooa11y-outline-active");
            $outlinePanel.classList.add("jooa11y-active");
            $outlineToggle.textContent = Lang._('HIDE_OUTLINE');
            $outlineToggle.setAttribute("aria-expanded", "true");
            localStorage.setItem("jooa11y-remember-outline", "Opened");
          }

          //Set focus on Page Outline heading for accessibility.
          document.querySelector("#jooa11y-outline-header > h2").focus();

          //Show heading level annotations.
          $headingAnnotations.forEach(function ($el) {
            return $el.classList.toggle("jooa11y-label-visible");
          });

          //Close Settings panel when Show Outline is active.
          $settingsPanel.classList.remove("jooa11y-active");
          $settingsToggle.classList.remove("jooa11y-settings-active");
          $settingsToggle.setAttribute("aria-expanded", "false");
          $settingsToggle.textContent = Lang._('SHOW_SETTINGS');

          //Keyboard accessibility fix for scrollable panel content.
          if ($outlineList.clientHeight > 250) {
            $outlineList.setAttribute("tabindex", "0");
          }
        });

        //Remember to leave outline open
        if (localStorage.getItem("jooa11y-remember-outline") === "Opened") {
          $outlineToggle.classList.add("jooa11y-outline-active");
          $outlinePanel.classList.add("jooa11y-active");
          $outlineToggle.textContent = Lang._('HIDE_OUTLINE');
          $outlineToggle.setAttribute("aria-expanded", "true");
          $headingAnnotations.forEach(function ($el) {
            return $el.classList.toggle("jooa11y-label-visible");
          });
          //Keyboard accessibility fix for scrollable panel content.
          if ($outlineList.clientHeight > 250) {
            $outlineList.setAttribute("tabindex", "0");
          }
        }

        //Show settings panel
        $settingsToggle.addEventListener('click', function () {
          if ($settingsToggle.getAttribute("aria-expanded") === "true") {
            $settingsToggle.classList.remove("jooa11y-settings-active");
            $settingsPanel.classList.remove("jooa11y-active");
            $settingsToggle.textContent = Lang._('SHOW_SETTINGS');
            $settingsToggle.setAttribute("aria-expanded", "false");
          } else {
            $settingsToggle.classList.add("jooa11y-settings-active");
            $settingsPanel.classList.add("jooa11y-active");
            $settingsToggle.textContent = Lang._('HIDE_SETTINGS');
            $settingsToggle.setAttribute("aria-expanded", "true");
          }

          //Set focus on Settings heading for accessibility.
          document.querySelector("#jooa11y-settings-header > h2").focus();

          //Close Show Outline panel when Settings is active.
          $outlinePanel.classList.remove("jooa11y-active");
          $outlineToggle.classList.remove("jooa11y-outline-active");
          $outlineToggle.setAttribute("aria-expanded", "false");
          $outlineToggle.textContent = Lang._('SHOW_OUTLINE');
          $headingAnnotations.forEach(function ($el) {
            return $el.classList.remove("jooa11y-label-visible");
          });
          localStorage.setItem("jooa11y-remember-outline", "Closed");

          //Keyboard accessibility fix for scrollable panel content.
          if ($settingsContent.clientHeight > 350) {
            $settingsContent.setAttribute("tabindex", "0");
          }
        });

        //Enhanced keyboard accessibility for panel.
        document.getElementById('jooa11y-panel-controls').addEventListener('keydown', function (e) {
          var $tab = document.querySelectorAll('#jooa11y-outline-toggle[role=tab], #jooa11y-settings-toggle[role=tab]');
          if (e.key === 'ArrowRight') {
            for (var i = 0; i < $tab.length; i++) {
              if ($tab[i].getAttribute('aria-expanded') === "true" || $tab[i].getAttribute('aria-expanded') === "false") {
                $tab[i + 1].focus();
                e.preventDefault();
                break;
              }
            }
          }
          if (e.key === 'ArrowDown') {
            for (var _i2 = 0; _i2 < $tab.length; _i2++) {
              if ($tab[_i2].getAttribute('aria-expanded') === "true" || $tab[_i2].getAttribute('aria-expanded') === "false") {
                $tab[_i2 + 1].focus();
                e.preventDefault();
                break;
              }
            }
          }
          if (e.key === 'ArrowLeft') {
            for (var _i3 = $tab.length - 1; _i3 > 0; _i3--) {
              if ($tab[_i3].getAttribute('aria-expanded') === "true" || $tab[_i3].getAttribute('aria-expanded') === "false") {
                $tab[_i3 - 1].focus();
                e.preventDefault();
                break;
              }
            }
          }
          if (e.key === 'ArrowUp') {
            for (var _i4 = $tab.length - 1; _i4 > 0; _i4--) {
              if ($tab[_i4].getAttribute('aria-expanded') === "true" || $tab[_i4].getAttribute('aria-expanded') === "false") {
                $tab[_i4 - 1].focus();
                e.preventDefault();
                break;
              }
            }
          }
        });
        var $closeAlertToggle = document.getElementById("jooa11y-close-alert");
        var $alertPanel = document.getElementById("jooa11y-panel-alert");
        var $alertText = document.getElementById("jooa11y-panel-alert-text");
        var $jooa11ySkipBtn = document.getElementById("jooa11y-cycle-toggle");
        $closeAlertToggle.addEventListener('click', function () {
          $alertPanel.classList.remove("jooa11y-active");
          while ($alertText.firstChild) $alertText.removeChild($alertText.firstChild);
          document.querySelectorAll('.jooa11y-pulse-border').forEach(function (el) {
            return el.classList.remove('jooa11y-pulse-border');
          });
          $jooa11ySkipBtn.focus();
        });
      };
      // ============================================================
      // Main panel: Skip to issue button.
      // ============================================================
      this.skipToIssue = function () {
        /* Polyfill for scrollTo. scrollTo instead of .animate(), so Jooa11y could use jQuery slim build. Credit: https://stackoverflow.com/a/67108752 & https://github.com/iamdustan/smoothscroll */
        //let reducedMotionQuery = false;
        //let scrollBehavior = 'smooth';
        /*
        if (!('scrollBehavior' in document.documentElement.style)) {
            var js = document.createElement('script');
            js.src = "https://cdn.jsdelivr.net/npm/smoothscroll-polyfill@0.4.4/dist/smoothscroll.min.js";
            document.head.appendChild(js);
        }
        if (!(document.documentMode)) {
            if (typeof window.matchMedia === "function") {
                reducedMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
            }
            if (!reducedMotionQuery || reducedMotionQuery.matches) {
                scrollBehavior = "auto";
            }
        }
        */

        var jooa11yBtnLocation = 0;
        var findJooa11yBtn = document.querySelectorAll('.jooa11y-btn').length;

        //Jump to issue using keyboard shortcut.
        document.addEventListener('keyup', function (e) {
          if (e.altKey && e.code === "Period" || e.code == "KeyS") {
            skipToIssueToggle();
            e.preventDefault();
          }
        });

        //Jump to issue using click.
        var $skipToggle = document.getElementById("jooa11y-cycle-toggle");
        $skipToggle.addEventListener('click', function (e) {
          skipToIssueToggle();
          e.preventDefault();
        });
        var skipToIssueToggle = function skipToIssueToggle() {
          //Calculate location of both visible and hidden buttons.
          var $findButtons = document.querySelectorAll('.jooa11y-btn');
          var $alertPanel = document.getElementById("jooa11y-panel-alert");
          var $alertText = document.getElementById("jooa11y-panel-alert-text");
          var $alertPanelPreview = document.getElementById("jooa11y-panel-alert-preview");
          //const $closeAlertToggle = document.getElementById("jooa11y-close-alert");

          //Mini function: Find visibible parent of hidden element.
          var findVisibleParent = function findVisibleParent($el, property, value) {
            while ($el !== null) {
              var style = window.getComputedStyle($el);
              var propValue = style.getPropertyValue(property);
              if (propValue === value) {
                return $el;
              }
              $el = $el.parentElement;
            }
            return null;
          };

          //Mini function: Calculate top of element.
          var offset = function offset($el) {
            var rect = $el.getBoundingClientRect(),
              scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            return {
              top: rect.top + scrollTop
            };
          };

          //'offsetTop' will always return 0 if element is hidden. We rely on offsetTop to determine if element is hidden, although we use 'getBoundingClientRect' to set the scroll position.
          var scrollPosition;
          var offsetTopPosition = $findButtons[jooa11yBtnLocation].offsetTop;
          if (offsetTopPosition === 0) {
            var visiblePosition = findVisibleParent($findButtons[jooa11yBtnLocation], 'display', 'none');
            scrollPosition = offset(visiblePosition.previousElementSibling).top - 50;
          } else {
            scrollPosition = offset($findButtons[jooa11yBtnLocation]).top - 50;
          }

          //Scroll to element if offsetTop is less than or equal to 0.
          if (offsetTopPosition >= 0) {
            setTimeout(function () {
              window.scrollTo({
                top: scrollPosition,
                behavior: 'smooth'
              });
            }, 1);

            //Add pulsing border to visible parent of hidden element.
            $findButtons.forEach(function ($el) {
              var overflowing = findVisibleParent($el, 'display', 'none');
              if (overflowing !== null) {
                var hiddenparent = overflowing.previousElementSibling;
                hiddenparent.classList.add("jooa11y-pulse-border");
              }
            });
            $findButtons[jooa11yBtnLocation].focus();
          } else {
            $findButtons[jooa11yBtnLocation].focus();
          }

          //Alert if element is hidden.
          if (offsetTopPosition === 0) {
            $alertPanel.classList.add("jooa11y-active");
            $alertText.textContent = "" + Lang._('PANEL_STATUS_HIDDEN');
            $alertPanelPreview.innerHTML = $findButtons[jooa11yBtnLocation].getAttribute('data-tippy-content');
          } else if (offsetTopPosition < 1) {
            $alertPanel.classList.remove("jooa11y-active");
            document.querySelectorAll('.jooa11y-pulse-border').forEach(function ($el) {
              return $el.classList.remove('jooa11y-pulse-border');
            });
          }

          //Reset index so it scrolls back to top of page.
          jooa11yBtnLocation += 1;
          if (jooa11yBtnLocation >= findJooa11yBtn) {
            jooa11yBtnLocation = 0;
          }
        };
      };
      this.containerIgnore = '';
      this.imageIgnore = '';
      this.headerIgnore = '';
      this.linkIgnore = '';

      // Load options
      this.options = loadOptions(this, options);

      //Icon on the main toggle. Easy to replace.
      var MainToggleIcon = "<svg role='img' focusable='false' width='35px' height='35px' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='#ffffff' d='M256 48c114.953 0 208 93.029 208 208 0 114.953-93.029 208-208 208-114.953 0-208-93.029-208-208 0-114.953 93.029-208 208-208m0-40C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm0 56C149.961 64 64 149.961 64 256s85.961 192 192 192 192-85.961 192-192S362.039 64 256 64zm0 44c19.882 0 36 16.118 36 36s-16.118 36-36 36-36-16.118-36-36 16.118-36 36-36zm117.741 98.023c-28.712 6.779-55.511 12.748-82.14 15.807.851 101.023 12.306 123.052 25.037 155.621 3.617 9.26-.957 19.698-10.217 23.315-9.261 3.617-19.699-.957-23.316-10.217-8.705-22.308-17.086-40.636-22.261-78.549h-9.686c-5.167 37.851-13.534 56.208-22.262 78.549-3.615 9.255-14.05 13.836-23.315 10.217-9.26-3.617-13.834-14.056-10.217-23.315 12.713-32.541 24.185-54.541 25.037-155.621-26.629-3.058-53.428-9.027-82.141-15.807-8.6-2.031-13.926-10.648-11.895-19.249s10.647-13.926 19.249-11.895c96.686 22.829 124.283 22.783 220.775 0 8.599-2.03 17.218 3.294 19.249 11.895 2.029 8.601-3.297 17.219-11.897 19.249z'/></svg>";
      var jooa11ycontainer = document.createElement("div");
      jooa11ycontainer.setAttribute("id", "jooa11y-container");
      jooa11ycontainer.setAttribute("role", "region");
      jooa11ycontainer.setAttribute("lang", this.options.langCode);
      jooa11ycontainer.setAttribute("aria-label", Lang._('CONTAINER_LABEL'));
      var loadContrastPreference = localStorage.getItem("jooa11y-remember-contrast") === "On";
      var loadLabelsPreference = localStorage.getItem("jooa11y-remember-labels") === "On";
      var loadChangeRequestPreference = localStorage.getItem("jooa11y-remember-links-advanced") === "On";
      var loadReadabilityPreference = localStorage.getItem("jooa11y-remember-readability") === "On";
      jooa11ycontainer.innerHTML =
      //Main toggle button.
      "<button type=\"button\" aria-expanded=\"false\" id=\"jooa11y-toggle\" aria-describedby=\"jooa11y-notification-badge\" aria-label=\"" + Lang._('MAIN_TOGGLE_LABEL') + "\">\n                    " + MainToggleIcon + "\n                    <div id=\"jooa11y-notification-badge\">\n                        <span id=\"jooa11y-notification-count\"></span>\n                    </div>\n                </button>" + //Start of main container.
      "<div id=\"jooa11y-panel\">" + ( //Page Outline tab.
      "<div id=\"jooa11y-outline-panel\" role=\"tabpanel\" aria-labelledby=\"jooa11y-outline-header\">\n                <div id=\"jooa11y-outline-header\" class=\"jooa11y-header-text\">\n                    <h2 tabindex=\"-1\">" + Lang._('PAGE_OUTLINE') + "</h2>\n                </div>\n                <div id=\"jooa11y-outline-content\">\n                    <ul id=\"jooa11y-outline-list\"></ul>\n                </div>") + ( //Readability tab.
      "<div id=\"jooa11y-readability-panel\">\n                    <div id=\"jooa11y-readability-content\">\n                        <h2 class=\"jooa11y-header-text-inline\">" + Lang._('READABILITY') + "</h2>\n                        <p id=\"jooa11y-readability-info\"></p>\n                        <ul id=\"jooa11y-readability-details\"></ul>\n                    </div>\n                </div>\n            </div>") + ( //End of Page Outline tab.
      //Settings tab.
      "<div id=\"jooa11y-settings-panel\" role=\"tabpanel\" aria-labelledby=\"jooa11y-settings-header\">\n                <div id=\"jooa11y-settings-header\" class=\"jooa11y-header-text\">\n                    <h2 tabindex=\"-1\">" + Lang._('SETTINGS') + "</h2>\n                </div>\n                <div id=\"jooa11y-settings-content\">\n                    <ul id=\"jooa11y-settings-options\">\n                        <li>\n                            <label id=\"check-contrast\" for=\"jooa11y-contrast-toggle\">" + Lang._('CONTRAST') + "</label>\n                            <button id=\"jooa11y-contrast-toggle\"\n                            aria-labelledby=\"check-contrast\"\n                            class=\"jooa11y-settings-switch\"\n                            aria-pressed=\"" + (loadContrastPreference ? "true" : "false") + "\">" + (loadContrastPreference ? Lang._('ON') : Lang._('OFF')) + "</button>\n                        </li>\n                        <li>\n                            <label id=\"check-labels\" for=\"jooa11y-labels-toggle\">" + Lang._('FORM_LABELS') + "</label>\n                            <button id=\"jooa11y-labels-toggle\" aria-labelledby=\"check-labels\" class=\"jooa11y-settings-switch\"\n                            aria-pressed=\"" + (loadLabelsPreference ? "true" : "false") + "\">" + (loadLabelsPreference ? Lang._('ON') : Lang._('OFF')) + "</button>\n                        </li>\n                        <li>\n                            <label id=\"check-changerequest\" for=\"jooa11y-links-advanced-toggle\">" + Lang._('LINKS_ADVANCED') + "<span class=\"jooa11y-badge\">AAA</span></label>\n                            <button id=\"jooa11y-links-advanced-toggle\" aria-labelledby=\"check-changerequest\" class=\"jooa11y-settings-switch\"\n                            aria-pressed=\"" + (loadChangeRequestPreference ? "true" : "false") + "\">" + (loadChangeRequestPreference ? Lang._('ON') : Lang._('OFF')) + "</button>\n                        </li>\n                        <li>\n                            <label id=\"check-readability\" for=\"jooa11y-readability-toggle\">" + Lang._('READABILITY') + "<span class=\"jooa11y-badge\">AAA</span></label>\n                            <button id=\"jooa11y-readability-toggle\" aria-labelledby=\"check-readability\" class=\"jooa11y-settings-switch\"\n                            aria-pressed=\"" + (loadReadabilityPreference ? "true" : "false") + "\">" + (loadReadabilityPreference ? Lang._('ON') : Lang._('OFF')) + "</button>\n                        </li>\n                        <li>\n                            <label id=\"dark-mode\" for=\"jooa11y-theme-toggle\">" + Lang._('DARK_MODE') + "</label>\n                            <button id=\"jooa11y-theme-toggle\" aria-labelledby=\"dark-mode\" class=\"jooa11y-settings-switch\"></button>\n                        </li>\n                    </ul>\n                </div>\n            </div>") + ( //Console warning messages.
      "<div id=\"jooa11y-panel-alert\">\n                <div class=\"jooa11y-header-text\">\n                    <button id=\"jooa11y-close-alert\" class=\"jooa11y-close-btn\" aria-label=\"" + Lang._('ALERT_CLOSE') + "\" aria-describedby=\"jooa11y-alert-heading jooa11y-panel-alert-text\"></button>\n                    <h2 id=\"jooa11y-alert-heading\">" + Lang._('ALERT_TEXT') + "</h2>\n                </div>\n                <p id=\"jooa11y-panel-alert-text\"></p>\n                <div id=\"jooa11y-panel-alert-preview\"></div>\n            </div>") + ( //Main panel that conveys state of page.
      "<div id=\"jooa11y-panel-content\">\n                <button id=\"jooa11y-cycle-toggle\" type=\"button\" aria-label=\"" + Lang._('SHORTCUT_SR') + "\">\n                    <div class=\"jooa11y-panel-icon\"></div>\n                </button>\n                <div id=\"jooa11y-panel-text\"><p id=\"jooa11y-status\" aria-live=\"polite\"></p></div>\n            </div>") + ( //Show Outline & Show Settings button.
      "<div id=\"jooa11y-panel-controls\" role=\"tablist\" aria-orientation=\"horizontal\">\n                <button type=\"button\" role=\"tab\" aria-expanded=\"false\" id=\"jooa11y-outline-toggle\" aria-controls=\"jooa11y-outline-panel\">\n                    " + Lang._('SHOW_OUTLINE') + "\n                </button>\n                <button type=\"button\" role=\"tab\" aria-expanded=\"false\" id=\"jooa11y-settings-toggle\" aria-controls=\"jooa11y-settings-panel\">\n                    " + Lang._('SHOW_SETTINGS') + "\n                </button>\n                <div style=\"width:35px\"></div>\n            </div>") + //End of main container.
      "</div>";
      document.body.append(jooa11ycontainer);

      //Put before document.ready because of CSS flicker when dark mode is enabled.
      this.settingPanelToggles();

      // Preload before CheckAll function.
      this.jooa11yMainToggle();
      this.sanitizeHTMLandComputeARIA();
      this.initializeJumpToIssueTooltip();
    }

    //----------------------------------------------------------------------
    // Main toggle button
    //----------------------------------------------------------------------
    var _proto = Jooa11y.prototype;
    _proto.jooa11yMainToggle = function jooa11yMainToggle() {
      var _this2 = this;
      //Keeps checker active when navigating between pages until it is toggled off.
      var jooa11yToggle = document.getElementById("jooa11y-toggle");
      jooa11yToggle.addEventListener('click', function (e) {
        if (localStorage.getItem("jooa11y-remember-panel") === "Opened") {
          localStorage.setItem("jooa11y-remember-panel", "Closed");
          jooa11yToggle.classList.remove("jooa11y-on");
          jooa11yToggle.setAttribute("aria-expanded", "false");
          _this2.resetAll();
          _this2.updateBadge();
          e.preventDefault();
        } else {
          localStorage.setItem("jooa11y-remember-panel", "Opened");
          jooa11yToggle.classList.add("jooa11y-on");
          jooa11yToggle.setAttribute("aria-expanded", "true");
          _this2.checkAll();
          //Don't show badge when panel is opened.
          document.getElementById("jooa11y-notification-badge").style.display = 'none';
          e.preventDefault();
        }
      });

      //Remember to leave it open
      if (localStorage.getItem("jooa11y-remember-panel") === "Opened") {
        jooa11yToggle.classList.add("jooa11y-on");
        jooa11yToggle.setAttribute("aria-expanded", "true");
      }

      //Crudely give a little time to load any other content or slow post-rendered JS, iFrames, etc.
      if (jooa11yToggle.classList.contains("jooa11y-on")) {
        jooa11yToggle.classList.toggle("loading-jooa11y");
        jooa11yToggle.setAttribute("aria-expanded", "true");
        setTimeout(this.checkAll, 800);
      }

      //Keyboard commands
      document.onkeydown = function (evt) {
        evt = evt || window.event;

        //Escape key to close accessibility checker panel
        var isEscape = false;
        if ("key" in evt) {
          isEscape = evt.key === "Escape" || evt.key === "Esc";
        } else {
          isEscape = evt.keyCode === 27;
        }
        if (isEscape && document.getElementById("jooa11y-panel").classList.contains("jooa11y-active")) {
          jooa11yToggle.setAttribute("aria-expanded", "false");
          jooa11yToggle.classList.remove("jooa11y-on");
          jooa11yToggle.click();
          _this2.resetAll();
        }

        //Alt + A to open accessibility checker panel
        if (evt.altKey && evt.code == "KeyA") {
          var _jooa11yToggle = document.getElementById("jooa11y-toggle");
          _jooa11yToggle.click();
          _jooa11yToggle.focus();
          evt.preventDefault();
        }
      };
    }

    // ============================================================
    // Helpers: Sanitize HTML and compute ARIA for hyperlinks
    // ============================================================
    ;
    _proto.sanitizeHTMLandComputeARIA = function sanitizeHTMLandComputeARIA() {
      //Helper: Compute alt text on images within a text node.
      this.computeTextNodeWithImage = function ($el) {
        var imgArray = Array.from($el.querySelectorAll("img"));
        var returnText = "";
        //No image, has text.
        if (imgArray.length === 0 && $el.textContent.trim().length > 1) {
          returnText = $el.textContent.trim();
        }
        //Has image, no text.
        else if (imgArray.length && $el.textContent.trim().length === 0) {
          var imgalt = imgArray[0].getAttribute("alt");
          if (!imgalt || imgalt === " ") {
            returnText = " ";
          } else if (imgalt !== undefined) {
            returnText = imgalt;
          }
        }
        //Has image and text.
        //To-do: This is a hack? Any way to do this better?
        else if (imgArray.length && $el.textContent.trim().length) {
          imgArray.forEach(function (element) {
            element.insertAdjacentHTML("afterend", " <span class='jooa11y-clone-image-text' aria-hidden='true'>" + imgArray[0].getAttribute("alt") + "</span> ");
          });
          returnText = $el.textContent.trim();
        }
        return returnText;
      };

      //Helper: Handle ARIA labels for Link Text module.
      this.computeAriaLabel = function (el) {
        if (el.matches("[aria-label]")) {
          return el.getAttribute("aria-label");
        } else if (el.matches("[aria-labelledby]")) {
          var target = el.getAttribute("aria-labelledby").split(/\s+/);
          if (target.length > 0) {
            var returnText = "";
            target.forEach(function (x) {
              if (document.querySelector("#" + x) === null) {
                returnText += " ";
              } else {
                returnText += document.querySelector("#" + x).firstChild.nodeValue + " ";
              }
            });
            return returnText;
          } else {
            return "";
          }
        }
        //Children of element.
        else if (Array.from(el.children).filter(function (x) {
          return x.matches("[aria-label]");
        }).length > 0) {
          return Array.from(el.children)[0].getAttribute("aria-label");
        } else if (Array.from(el.children).filter(function (x) {
          return x.matches("[title]");
        }).length > 0) {
          return Array.from(el.children)[0].getAttribute("title");
        } else if (Array.from(el.children).filter(function (x) {
          return x.matches("[aria-labelledby]");
        }).length > 0) {
          var _target = Array.from(el.children)[0].getAttribute("aria-labelledby").split(/\s+/);
          if (_target.length > 0) {
            var _returnText = "";
            _target.forEach(function (x) {
              if (document.querySelector("#" + x) === null) {
                _returnText += " ";
              } else {
                _returnText += document.querySelector("#" + x).firstChild.nodeValue + " ";
              }
            });
            return _returnText;
          } else {
            return "";
          }
        } else {
          return "noAria";
        }
      };
    }

    //----------------------------------------------------------------------
    // Setting's panel: Additional ruleset toggles.
    //----------------------------------------------------------------------
    ;
    _proto.settingPanelToggles = function settingPanelToggles() {
      var _this3 = this;
      //Toggle: Contrast
      var $jooa11yContrastCheck = document.getElementById("jooa11y-contrast-toggle");
      $jooa11yContrastCheck.onclick = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              if (!(localStorage.getItem("jooa11y-remember-contrast") === "On")) {
                _context2.next = 9;
                break;
              }
              localStorage.setItem("jooa11y-remember-contrast", "Off");
              $jooa11yContrastCheck.textContent = Lang._('OFF');
              $jooa11yContrastCheck.setAttribute("aria-pressed", "false");
              _this3.resetAll(false);
              _context2.next = 7;
              return _this3.checkAll();
            case 7:
              _context2.next = 15;
              break;
            case 9:
              localStorage.setItem("jooa11y-remember-contrast", "On");
              $jooa11yContrastCheck.textContent = Lang._('ON');
              $jooa11yContrastCheck.setAttribute("aria-pressed", "true");
              _this3.resetAll(false);
              _context2.next = 15;
              return _this3.checkAll();
            case 15:
            case "end":
              return _context2.stop();
          }
        }, _callee2);
      }));

      //Toggle: Form labels
      var $jooa11yLabelsCheck = document.getElementById("jooa11y-labels-toggle");
      $jooa11yLabelsCheck.onclick = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              if (!(localStorage.getItem("jooa11y-remember-labels") === "On")) {
                _context3.next = 9;
                break;
              }
              localStorage.setItem("jooa11y-remember-labels", "Off");
              $jooa11yLabelsCheck.textContent = Lang._('OFF');
              $jooa11yLabelsCheck.setAttribute("aria-pressed", "false");
              _this3.resetAll(false);
              _context3.next = 7;
              return _this3.checkAll();
            case 7:
              _context3.next = 15;
              break;
            case 9:
              localStorage.setItem("jooa11y-remember-labels", "On");
              $jooa11yLabelsCheck.textContent = Lang._('ON');
              $jooa11yLabelsCheck.setAttribute("aria-pressed", "true");
              _this3.resetAll(false);
              _context3.next = 15;
              return _this3.checkAll();
            case 15:
            case "end":
              return _context3.stop();
          }
        }, _callee3);
      }));

      //Toggle: Links (Advanced)
      var $jooa11yChangeRequestCheck = document.getElementById("jooa11y-links-advanced-toggle");
      $jooa11yChangeRequestCheck.onclick = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              if (!(localStorage.getItem("jooa11y-remember-links-advanced") === "On")) {
                _context4.next = 9;
                break;
              }
              localStorage.setItem("jooa11y-remember-links-advanced", "Off");
              $jooa11yChangeRequestCheck.textContent = Lang._('OFF');
              $jooa11yChangeRequestCheck.setAttribute("aria-pressed", "false");
              _this3.resetAll(false);
              _context4.next = 7;
              return _this3.checkAll();
            case 7:
              _context4.next = 15;
              break;
            case 9:
              localStorage.setItem("jooa11y-remember-links-advanced", "On");
              $jooa11yChangeRequestCheck.textContent = Lang._('ON');
              $jooa11yChangeRequestCheck.setAttribute("aria-pressed", "true");
              _this3.resetAll(false);
              _context4.next = 15;
              return _this3.checkAll();
            case 15:
            case "end":
              return _context4.stop();
          }
        }, _callee4);
      }));

      //Toggle: Readability
      var $jooa11yReadabilityCheck = document.getElementById("jooa11y-readability-toggle");
      $jooa11yReadabilityCheck.onclick = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5() {
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              if (!(localStorage.getItem("jooa11y-remember-readability") === "On")) {
                _context5.next = 10;
                break;
              }
              localStorage.setItem("jooa11y-remember-readability", "Off");
              $jooa11yReadabilityCheck.textContent = Lang._('OFF');
              $jooa11yReadabilityCheck.setAttribute("aria-pressed", "false");
              document.getElementById("jooa11y-readability-panel").classList.remove("jooa11y-active");
              _this3.resetAll(false);
              _context5.next = 8;
              return _this3.checkAll();
            case 8:
              _context5.next = 17;
              break;
            case 10:
              localStorage.setItem("jooa11y-remember-readability", "On");
              $jooa11yReadabilityCheck.textContent = Lang._('ON');
              $jooa11yReadabilityCheck.setAttribute("aria-pressed", "true");
              document.getElementById("jooa11y-readability-panel").classList.add("jooa11y-active");
              _this3.resetAll(false);
              _context5.next = 17;
              return _this3.checkAll();
            case 17:
            case "end":
              return _context5.stop();
          }
        }, _callee5);
      }));
      if (localStorage.getItem("jooa11y-remember-readability") === "On") {
        document.getElementById("jooa11y-readability-panel").classList.add("jooa11y-active");
      }

      //Toggle: Dark mode. (Credits: https://derekkedziora.com/blog/dark-mode-revisited)

      var systemInitiatedDark = window.matchMedia("(prefers-color-scheme: dark)");
      var $jooa11yTheme = document.getElementById("jooa11y-theme-toggle");
      var html = document.querySelector("html");
      var theme = localStorage.getItem("jooa11y-remember-theme");
      if (systemInitiatedDark.matches) {
        $jooa11yTheme.textContent = Lang._('ON');
        $jooa11yTheme.setAttribute("aria-pressed", "true");
      } else {
        $jooa11yTheme.textContent = Lang._('OFF');
        $jooa11yTheme.setAttribute("aria-pressed", "false");
      }
      function prefersColorTest(systemInitiatedDark) {
        if (systemInitiatedDark.matches) {
          html.setAttribute("data-jooa11y-theme", "dark");
          $jooa11yTheme.textContent = Lang._('ON');
          $jooa11yTheme.setAttribute("aria-pressed", "true");
          localStorage.setItem("jooa11y-remember-theme", "");
        } else {
          html.setAttribute("data-jooa11y-theme", "light");
          $jooa11yTheme.textContent = Lang._('OFF');
          $jooa11yTheme.setAttribute("aria-pressed", "false");
          localStorage.setItem("jooa11y-remember-theme", "");
        }
      }
      systemInitiatedDark.addEventListener('change', prefersColorTest);
      $jooa11yTheme.onclick = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee6() {
        var theme;
        return _regeneratorRuntime().wrap(function _callee6$(_context6) {
          while (1) switch (_context6.prev = _context6.next) {
            case 0:
              theme = localStorage.getItem("jooa11y-remember-theme");
              if (theme === "dark") {
                html.setAttribute("data-jooa11y-theme", "light");
                localStorage.setItem("jooa11y-remember-theme", "light");
                $jooa11yTheme.textContent = Lang._('OFF');
                $jooa11yTheme.setAttribute("aria-pressed", "false");
              } else if (theme === "light") {
                html.setAttribute("data-jooa11y-theme", "dark");
                localStorage.setItem("jooa11y-remember-theme", "dark");
                $jooa11yTheme.textContent = Lang._('ON');
                $jooa11yTheme.setAttribute("aria-pressed", "true");
              } else if (systemInitiatedDark.matches) {
                html.setAttribute("data-jooa11y-theme", "light");
                localStorage.setItem("jooa11y-remember-theme", "light");
                $jooa11yTheme.textContent = Lang._('OFF');
                $jooa11yTheme.setAttribute("aria-pressed", "false");
              } else {
                html.setAttribute("data-jooa11y-theme", "dark");
                localStorage.setItem("jooa11y-remember-theme", "dark");
                $jooa11yTheme.textContent = Lang._('OFF');
                $jooa11yTheme.setAttribute("aria-pressed", "true");
              }
            case 2:
            case "end":
              return _context6.stop();
          }
        }, _callee6);
      }));
      if (theme === "dark") {
        html.setAttribute("data-jooa11y-theme", "dark");
        localStorage.setItem("jooa11y-remember-theme", "dark");
        $jooa11yTheme.textContent = Lang._('ON');
        $jooa11yTheme.setAttribute("aria-pressed", "true");
      } else if (theme === "light") {
        html.setAttribute("data-jooa11y-theme", "light");
        localStorage.setItem("jooa11y-remember-theme", "light");
        $jooa11yTheme.textContent = Lang._('OFF');
        $jooa11yTheme.setAttribute("aria-pressed", "false");
      }
    }

    //----------------------------------------------------------------------
    // Tooltip for Jump-to-Issue button.
    //----------------------------------------------------------------------
    ;
    _proto.initializeJumpToIssueTooltip = function initializeJumpToIssueTooltip() {
      tippy('#jooa11y-cycle-toggle', {
        content: "<div style=\"text-align:center\">" + Lang._('SHORTCUT_TOOLTIP') + " &raquo;<br><span class=\"jooa11y-shortcut-icon\"></span></div>",
        allowHTML: true,
        delay: [900, 0],
        trigger: "mouseenter focusin",
        arrow: true,
        placement: 'top',
        theme: "jooa11y-theme",
        aria: {
          content: null,
          expanded: false
        },
        appendTo: document.body
      });
    }

    // ----------------------------------------------------------------------
    // Do Initial check
    // ----------------------------------------------------------------------
    ;
    _proto.doInitialCheck = function doInitialCheck() {
      if (localStorage.getItem("jooa11y-remember-panel") === "Closed" || !localStorage.getItem("jooa11y-remember-panel")) {
        this.panelActive = true; // Prevent panel popping up after initial check
        this.checkAll();
      }
    };
    // ============================================================
    // Reset all
    // ============================================================
    _proto.resetAll = function resetAll(restartPanel) {
      if (restartPanel === void 0) {
        restartPanel = true;
      }
      this.panelActive = false;
      this.clearEverything();

      //Remove eventListeners on the Show Outline and Show Panel toggles.
      var $outlineToggle = document.getElementById("jooa11y-outline-toggle");
      var resetOutline = $outlineToggle.cloneNode(true);
      $outlineToggle.parentNode.replaceChild(resetOutline, $outlineToggle);
      var $settingsToggle = document.getElementById("jooa11y-settings-toggle");
      var resetSettings = $settingsToggle.cloneNode(true);
      $settingsToggle.parentNode.replaceChild(resetSettings, $settingsToggle);

      //Errors
      document.querySelectorAll('.jooa11y-error-border').forEach(function (el) {
        return el.classList.remove('jooa11y-error-border');
      });
      document.querySelectorAll('.jooa11y-error-text').forEach(function (el) {
        return el.classList.remove('jooa11y-error-text');
      });

      //Warnings
      document.querySelectorAll('.jooa11y-warning-border').forEach(function (el) {
        return el.classList.remove('jooa11y-warning-border');
      });
      document.querySelectorAll('.jooa11y-warning-text').forEach(function (el) {
        return el.classList.remove('jooa11y-warning-text');
      });
      document.querySelectorAll('p').forEach(function (el) {
        return el.classList.remove('jooa11y-fake-list');
      });
      var allcaps = document.querySelectorAll('.jooa11y-warning-uppercase');
      allcaps.forEach(function (el) {
        return el.outerHTML = el.innerHTML;
      });

      //Good
      document.querySelectorAll('.jooa11y-good-border').forEach(function (el) {
        return el.classList.remove('jooa11y-good-border');
      });
      document.querySelectorAll('.jooa11y-good-text').forEach(function (el) {
        return el.classList.remove('jooa11y-good-text');
      });

      //Remove
      document.querySelectorAll("\n                .jooa11y-instance,\n                .jooa11y-instance-inline,\n                .jooa11y-heading-label,\n                #jooa11y-outline-list li,\n                .jooa11y-readability-period,\n                #jooa11y-readability-info span,\n                #jooa11y-readability-details li,\n                .jooa11y-clone-image-text\n            ").forEach(function (el) {
        return el.parentNode.removeChild(el);
      });

      //Etc
      document.querySelectorAll('.jooa11y-overflow').forEach(function (el) {
        return el.classList.remove('jooa11y-overflow');
      });
      document.querySelectorAll('.jooa11y-fake-heading').forEach(function (el) {
        return el.classList.remove('jooa11y-fake-heading');
      });
      document.querySelectorAll('.jooa11y-pulse-border').forEach(function (el) {
        return el.classList.remove('jooa11y-pulse-border');
      });
      document.querySelector('#jooa11y-panel-alert').classList.remove("jooa11y-active");
      var empty = document.querySelector('#jooa11y-panel-alert-text');
      while (empty.firstChild) empty.removeChild(empty.firstChild);
      var clearStatus = document.querySelector('#jooa11y-status');
      while (clearStatus.firstChild) clearStatus.removeChild(clearStatus.firstChild);
      if (restartPanel) {
        document.querySelector('#jooa11y-panel').classList.remove("jooa11y-active");
      }
    };
    _proto.clearEverything = function clearEverything() {}
    // ============================================================
    // Initialize tooltips for error/warning/pass buttons: (Tippy.js)
    // Although you can also swap this with Bootstrap's tooltip library for example.
    // ============================================================
    ;
    _proto.initializeTooltips = function initializeTooltips() {
      tippy(".jooa11y-btn", {
        interactive: true,
        trigger: "mouseenter click focusin",
        //Focusin trigger to ensure "Jump to issue" button displays tooltip.
        arrow: true,
        delay: [200, 0],
        //Slight delay to ensure mouse doesn't quickly trigger and hide tooltip.
        theme: "jooa11y-theme",
        placement: 'bottom',
        allowHTML: true,
        aria: {
          content: 'describedby'
        },
        appendTo: document.body
      });
    }

    // ============================================================
    // Detect parent containers that have hidden overflow.
    // ============================================================
    ;
    _proto.detectOverflow = function detectOverflow() {
      var findParentWithOverflow = function findParentWithOverflow($el, property, value) {
        while ($el !== null) {
          var style = window.getComputedStyle($el);
          var propValue = style.getPropertyValue(property);
          if (propValue === value) {
            return $el;
          }
          $el = $el.parentElement;
        }
        return null;
      };
      var $findButtons = document.querySelectorAll('.jooa11y-btn');
      $findButtons.forEach(function ($el) {
        var overflowing = findParentWithOverflow($el, 'overflow', 'hidden');
        if (overflowing !== null) {
          overflowing.classList.add('jooa11y-overflow');
        }
      });
    };
    // ============================================================
    // Update iOS style notification badge on icon.
    // ============================================================
    _proto.updateBadge = function updateBadge() {
      var totalCount = this.errorCount + this.warningCount;
      var notifBadge = document.getElementById("jooa11y-notification-badge");
      if (totalCount === 0) {
        notifBadge.style.display = "none";
      } else {
        notifBadge.style.display = "flex";
        document.getElementById('jooa11y-notification-count').innerHTML = Lang.sprintf('PANEL_STATUS_ICON', totalCount);
      }
    }

    // ----------------------------------------------------------------------
    // Main panel: Display and update panel.
    // ----------------------------------------------------------------------
    ;
    _proto.updatePanel = function updatePanel() {
      this.panelActive = true;
      this.errorCount + this.warningCount;
      this.buildPanel();
      this.skipToIssue();
      var $jooa11ySkipBtn = document.getElementById("jooa11y-cycle-toggle");
      $jooa11ySkipBtn.disabled = false;
      $jooa11ySkipBtn.setAttribute("style", "cursor: pointer !important;");
      var $jooa11yPanel = document.getElementById("jooa11y-panel");
      $jooa11yPanel.classList.add("jooa11y-active");
      var $panelContent = document.getElementById("jooa11y-panel-content");
      var $jooa11yStatus = document.getElementById("jooa11y-status");
      var $findButtons = document.querySelectorAll('.jooa11y-btn');
      if (this.errorCount > 0 && this.warningCount > 0) {
        $panelContent.setAttribute("class", "jooa11y-errors");
        $jooa11yStatus.textContent = Lang.sprintf('PANEL_STATUS_BOTH', this.errorCount, this.warningCount);
      } else if (this.errorCount > 0) {
        $panelContent.setAttribute("class", "jooa11y-errors");
        $jooa11yStatus.textContent = Lang.sprintf('PANEL_STATUS_ERRORS', this.errorCount);
      } else if (this.warningCount > 0) {
        $panelContent.setAttribute("class", "jooa11y-warnings");
        $jooa11yStatus.textContent = Lang.sprintf('PANEL_STATUS_WARNINGS', this.warningCount);
      } else {
        $panelContent.setAttribute("class", "jooa11y-good");
        $jooa11yStatus.textContent = Lang._('PANEL_STATUS_NONE');
        if ($findButtons.length === 0) {
          $jooa11ySkipBtn.disabled = true;
          $jooa11ySkipBtn.setAttribute("style", "cursor: default !important;");
        }
      }
    };
    // ============================================================
    // Finds all elements and caches them
    // ============================================================
    _proto.findElements = function findElements() {
      var _this4 = this;
      var allHeadings = Array.from(this.$root.querySelectorAll("h1, h2, h3, h4, h5, h6, [role='heading'][aria-level]"));
      var allPs = Array.from(this.$root.querySelectorAll("p"));
      this.$containerExclusions = Array.from(document.querySelectorAll(this.containerIgnore));
      this.$h = allHeadings.filter(function (heading) {
        return !_this4.$containerExclusions.includes(heading);
      });
      this.$p = allPs.filter(function (p) {
        return !_this4.$containerExclusions.includes(p);
      });
    }
    // ============================================================
    // Rulesets: Check Headings
    // ============================================================
    ;
    _proto.checkHeaders = function checkHeaders() {
      var _this5 = this;
      var prevLevel;
      this.$h.forEach(function (el, i) {
        var text = _this5.computeTextNodeWithImage(el);
        var htext = escapeHTML(text);
        var level;
        if (el.getAttribute("aria-level")) {
          level = +el.getAttribute("aria-level");
        } else {
          level = +el.tagName.slice(1);
        }
        var headingLength = el.textContent.trim().length;
        var error = null;
        var warning = null;
        if (level - prevLevel > 1 && i !== 0) {
          error = Lang.sprintf('HEADING_NON_CONSECUTIVE_LEVEL', prevLevel, level);
        } else if (el.textContent.trim().length === 0) {
          if (el.querySelectorAll("img").length) {
            var imgalt = el.querySelector("img").getAttribute("alt");
            if (imgalt === undefined || imgalt === " " || imgalt === "") {
              error = Lang.sprintf('HEADING_EMPTY_WITH_IMAGE', level);
              el.classList.add("jooa11y-error-text");
            }
          } else {
            error = Lang.sprintf('HEADING_EMPTY', level);
            el.classList.add("jooa11y-error-text");
          }
        } else if (i === 0 && level !== 1 && level !== 2) {
          error = Lang._('HEADING_FIRST');
        } else if (el.textContent.trim().length > 170) {
          warning = Lang._('HEADING_LONG') + " . " + Lang.sprintf('HEADING_LONG_INFO', headingLength);
        }
        prevLevel = level;
        var li = "<li class='jooa11y-outline-" + level + "'>\n                <span class='jooa11y-badge'>" + level + "</span>\n                <span class='jooa11y-outline-list-item'>" + htext + "</span>\n            </li>";
        var liError = "<li class='jooa11y-outline-" + level + "'>\n                <span class='jooa11y-badge jooa11y-error-badge'>\n                <span aria-hidden='true'>&#10007;</span>\n                <span class='jooa11y-visually-hidden'>" + Lang._('ERROR') + "</span> " + level + "</span>\n                <span class='jooa11y-outline-list-item jooa11y-red-text jooa11y-bold'>" + htext + "</span>\n            </li>";
        var liWarning = "<li class='jooa11y-outline-" + level + "'>\n                <span class='jooa11y-badge jooa11y-warning-badge'>\n                <span aria-hidden='true'>&#x3f;</span>\n                <span class='jooa11y-visually-hidden'>" + Lang._('WARNING') + "</span> " + level + "</span>\n                <span class='jooa11y-outline-list-item jooa11y-yellow-text jooa11y-bold'>" + htext + "</span>\n            </li>";
        var ignoreArray = [];
        if (_this5.options.outlineIgnore) {
          ignoreArray = Array.from(document.querySelectorAll(_this5.options.outlineIgnore));
        }
        if (!ignoreArray.includes(el)) {
          //Append heading labels.
          el.insertAdjacentHTML("beforeend", "<span class='jooa11y-heading-label'>H" + level + "</span>");

          //Heading errors
          if (error != null && el.closest("a")) {
            _this5.errorCount++;
            el.classList.add("jooa11y-error-border");
            el.closest("a").insertAdjacentHTML("afterend", _this5.annotate(Lang._('ERROR'), error, true));
            document.querySelector("#jooa11y-outline-list").insertAdjacentHTML("beforeend", liError);
          } else if (error != null) {
            _this5.errorCount++;
            el.classList.add("jooa11y-error-border");
            el.insertAdjacentHTML("beforebegin", _this5.annotate(Lang._('ERROR'), error));
            document.querySelector("#jooa11y-outline-list").insertAdjacentHTML("beforeend", liError);
          }

          //Heading warnings
          else if (warning != null && el.closest("a")) {
            _this5.warningCount++;
            el.closest("a").insertAdjacentHTML("afterend", _this5.annotate(Lang._('WARNING'), warning));
            document.querySelector("#jooa11y-outline-list").insertAdjacentHTML("beforeend", liWarning);
          } else if (warning != null) {
            el.insertAdjacentHTML("beforebegin", _this5.annotate(Lang._('WARNING'), warning));
            document.querySelector("#jooa11y-outline-list").insertAdjacentHTML("beforeend", liWarning);
          }

          //Not an error or warning
          else if (error == null || warning == null) {
            document.querySelector("#jooa11y-outline-list").insertAdjacentHTML("beforeend", li);
          }
        }
      });

      //Check to see there is at least one H1 on the page.
      var $h1 = Array.from(this.$root.querySelectorAll('h1, [role="heading"][aria-level="1"]')).filter(function ($h) {
        return !_this5.$containerExclusions.includes($h);
      });
      if ($h1.length === 0) {
        this.errorCount++;
        document.querySelector('#jooa11y-outline-header').insertAdjacentHTML('afterend', "<div class='jooa11y-instance jooa11y-missing-h1'>\n                    <span class='jooa11y-badge jooa11y-error-badge'><span aria-hidden='true'>&#10007;</span><span class='jooa11y-visually-hidden'>" + Lang._('ERROR') + "</span></span>\n                    <span class='jooa11y-red-text jooa11y-bold'>" + Lang._('PANEL_HEADING_MISSING_ONE') + "</span>\n                </div>");
        document.querySelector("#jooa11y-container").insertAdjacentHTML('afterend', this.annotateBanner(Lang._('ERROR'), Lang._('HEADING_MISSING_ONE')));
      }
    }
    // ============================================================
    // Rulesets: Link text
    // ============================================================
    ;
    _proto.checkLinkText = function checkLinkText() {
      var _this6 = this;
      var containsLinkTextStopWords = function containsLinkTextStopWords(textContent) {
        var urlText = ["http", ".asp", ".htm", ".php", ".edu/", ".com/", ".net/", ".org/", ".us/", ".ca/", ".de/", ".icu/", ".uk/", ".ru/", ".info/", ".top/", ".xyz/", ".tk/", ".cn/", ".ga/", ".cf/", ".nl/", ".io/"];
        var hit = [null, null, null];

        // Flag partial stop words.
        _this6.options.partialAltStopWords.forEach(function (word) {
          if (textContent.length === word.length && textContent.toLowerCase().indexOf(word) >= 0) {
            hit[0] = word;
            return false;
          }
        });

        // Other warnings we want to add.
        _this6.options.warningAltWords.forEach(function (word) {
          if (textContent.toLowerCase().indexOf(word) >= 0) {
            hit[1] = word;
            return false;
          }
        });

        // Flag link text containing URLs.
        urlText.forEach(function (word) {
          if (textContent.toLowerCase().indexOf(word) >= 0) {
            hit[2] = word;
            return false;
          }
        });
        return hit;
      };

      /* Mini function if you need to exclude any text contained with a span. We created this function to ignore automatically appended sr-only text for external links and document filetypes.
        $.fn.ignore = function(sel){
          return this.clone().find(sel||">*").remove().end();
      };
        $el.ignore("span.sr-only").text().trim();
        Example: <a href="#">learn more <span class="sr-only">(external)</span></a>
        This function will ignore the text "(external)", and correctly flag this link as an error for non descript link text. */
      var fnIgnore = function fnIgnore(element, selector) {
        var $clone = element.cloneNode(true);
        var $excluded = Array.from(selector ? $clone.querySelectorAll(selector) : $clone.children);
        $excluded.forEach(function ($c) {
          $c.parentElement.removeChild($c);
        });
        return $clone;
      };
      var $linkIgnore = Array.from(this.$root.querySelectorAll(this.linkIgnore));
      var $links = Array.from(this.$root.querySelectorAll('a[href]')).filter(function ($a) {
        return !$linkIgnore.includes($a);
      });
      $links.forEach(function (el) {
        var linkText = _this6.computeAriaLabel(el);
        var hasAriaLabelledBy = el.getAttribute('aria-labelledby');
        var hasAriaLabel = el.getAttribute('aria-label');
        var hasTitle = el.getAttribute('title');
        var childAriaLabelledBy = null;
        var childAriaLabel = null;
        var childTitle = null;
        if (el.children.length) {
          var $firstChild = el.children[0];
          childAriaLabelledBy = $firstChild.getAttribute('aria-labelledby');
          childAriaLabel = $firstChild.getAttribute('aria-label');
          childTitle = $firstChild.getAttribute('title');
        }
        var error = containsLinkTextStopWords(fnIgnore(el, _this6.options.linkIgnoreSpan).textContent.trim());
        if (linkText === 'noAria') {
          linkText = el.textContent;
        }

        //Flag empty hyperlinks
        if (el.getAttribute('href') && !el.textContent.trim()) {
          if (el.querySelectorAll('img').length) ;else if (hasAriaLabelledBy || hasAriaLabel) {
            el.classList.add("jooa11y-good-border");
            el.insertAdjacentHTML('beforebegin', _this6.annotate(Lang._('GOOD'), Lang.sprintf('LINK_LABEL', linkText), true));
          } else if (hasTitle) {
            var _linkText = hasTitle;
            el.classList.add("jooa11y-good-border");
            el.insertAdjacentHTML('beforebegin', _this6.annotate(Lang._('GOOD'), Lang.sprintf('LINK_LABEL', _linkText), true));
          } else if (el.children.length) {
            if (childAriaLabelledBy || childAriaLabel || childTitle) {
              el.classList.add("jooa11y-good-border");
              el.insertAdjacentHTML('beforebegin', _this6.annotate(Lang._('GOOD'), Lang.sprintf('LINK_LABEL', linkText), true));
            } else {
              _this6.errorCount++;
              el.classList.add("jooa11y-error-border");
              el.insertAdjacentHTML('afterend', _this6.annotate(Lang._('ERROR'), Lang.sprintf('LINK_EMPTY_LINK_NO_LABEL'), true));
            }
          } else {
            _this6.errorCount++;
            el.classList.add("jooa11y-error-border");
            el.insertAdjacentHTML('afterend', _this6.annotate(Lang._('ERROR'), Lang._('LINK_EMPTY'), true));
          }
        } else if (error[0] !== null) {
          if (hasAriaLabelledBy) {
            el.insertAdjacentHTML('beforebegin', _this6.annotate(Lang._('GOOD'), Lang.sprintf('LINK_LABEL', linkText), true));
          } else if (hasAriaLabel) {
            el.insertAdjacentHTML('beforebegin', _this6.annotate(Lang._('GOOD'), Lang.sprintf('LINK_LABEL', hasAriaLabel), true));
          } else if (el.getAttribute('aria-hidden') === 'true' && el.getAttribute('tabindex') === '-1') ;else {
            _this6.errorCount++;
            el.classList.add("jooa11y-error-text");
            el.insertAdjacentHTML('afterend', _this6.annotate(Lang._('ERROR'), Lang.sprintf('LINK_STOPWORD', error[0]) + " <hr aria-hidden=\"true\"> " + Lang._('LINK_STOPWORD_TIP'), true));
          }
        } else if (error[1] !== null) {
          _this6.warningCount++;
          el.classList.add("jooa11y-warning-text");
          el.insertAdjacentHTML('afterend', _this6.annotate(Lang._('WARNING'), Lang.sprintf('LINK_BEST_PRACTICES', error[1]) + " <hr aria-hidden=\"true\"> " + Lang._('LINK_BEST_PRACTICES_DETAILS'), true));
        } else if (error[2] != null) {
          if (linkText.length > 40) {
            _this6.warningCount++;
            el.classList.add("jooa11y-warning-text");
            el.insertAdjacentHTML('afterend', _this6.annotate(Lang._('WARNING'), Lang._('LINK_URL') + " <hr aria-hidden=\"true\"> " + Lang._('LINK_URL_TIP'), true));
          }
        }
      });
    }
    // ============================================================
    // Rulesets: Links (Advanced)
    // ============================================================
    ;
    _proto.checkLinksAdvanced = function checkLinksAdvanced() {
      var _this7 = this;
      var $linkIgnore = Array.from(this.$root.querySelectorAll(this.linkIgnore + ', #jooa11y-container a, .jooa11y-exclude'));
      var $linksTargetBlank = Array.from(this.$root.querySelectorAll('a[href]')).filter(function ($a) {
        return !$linkIgnore.includes($a);
      });
      var seen = {};
      $linksTargetBlank.forEach(function (el) {
        var linkText = _this7.computeAriaLabel(el);
        if (linkText === 'noAria') {
          linkText = el.textContent;
        }
        var fileTypeMatch = el.matches("\n                    a[href$='.pdf'],\n                    a[href$='.doc'],\n                    a[href$='.zip'],\n                    a[href$='.mp3'],\n                    a[href$='.txt'],\n                    a[href$='.exe'],\n                    a[href$='.dmg'],\n                    a[href$='.rtf'],\n                    a[href$='.pptx'],\n                    a[href$='.ppt'],\n                    a[href$='.xls'],\n                    a[href$='.xlsx'],\n                    a[href$='.csv'],\n                    a[href$='.mp4'],\n                    a[href$='.mov'],\n                    a[href$='.avi']\n                ");

        //Links with identical accessible names have equivalent purpose.

        //If link has an image, process alt attribute,
        //To-do: Kinda hacky. Doesn't return accessible name of link in correct order.
        var $img = el.querySelector('img');
        var alt = $img ? $img.getAttribute('alt') || '' : '';

        //Return link text and image's alt text.
        var linkTextTrimmed = linkText.trim().toLowerCase() + " " + alt;
        var href = el.getAttribute("href");
        if (linkText.length !== 0) {
          if (seen[linkTextTrimmed] && linkTextTrimmed.length !== 0) {
            if (seen[href]) ;else {
              _this7.warningCount++;
              el.classList.add("jooa11y-warning-text");
              el.insertAdjacentHTML('afterend', _this7.annotate(Lang._('WARNING'), Lang._('LINK_IDENTICAL_NAME') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_IDENTICAL_NAME_TIP', linkText), true));
            }
          } else {
            seen[linkTextTrimmed] = true;
            seen[href] = true;
          }
        }

        //New tab or new window.
        var containsNewWindowPhrases = _this7.options.newWindowPhrases.some(function (pass) {
          return linkText.toLowerCase().indexOf(pass) >= 0;
        });

        //Link that points to a file type indicates that it does.
        var containsFileTypePhrases = _this7.options.fileTypePhrases.some(function (pass) {
          return linkText.toLowerCase().indexOf(pass) >= 0;
        });
        if (el.getAttribute("target") === "_blank" && !fileTypeMatch && !containsNewWindowPhrases) {
          _this7.warningCount++;
          el.classList.add("jooa11y-warning-text");
          el.insertAdjacentHTML('afterend', _this7.annotate(Lang._('WARNING'), Lang._('NEW_TAB_WARNING') + " <hr aria-hidden=\"true\"> " + Lang._('NEW_TAB_WARNING_TIP'), true));
        }
        if (fileTypeMatch && !containsFileTypePhrases) {
          _this7.warningCount++;
          el.classList.add("jooa11y-warning-text");
          el.insertAdjacentHTML('afterend', _this7.annotate(Lang._('WARNING'), Lang._('FILE_TYPE_WARNING') + " <hr aria-hidden=\"true\"> " + Lang._('FILE_TYPE_WARNING_TIP'), true));
        }
      });
    }
    // ============================================================
    // Ruleset: Underlined text
    // ============================================================
    // check text for <u>  tags
    ;
    _proto.checkUnderline = function checkUnderline() {
      var _this8 = this;
      var underline = Array.from(this.$root.querySelectorAll('u'));
      underline.forEach(function ($el) {
        _this8.warningCount++;
        $el.insertAdjacentHTML('beforebegin', _this8.annotate(Lang._('WARNING'), Lang._('TEXT_UNDERLINE_WARNING') + " <hr aria-hidden=\"true\"> " + Lang._('TEXT_UNDERLINE_WARNING_TIP'), true));
      });
      // check for text-decoration-line: underline
      var computed = Array.from(this.$root.querySelectorAll('h1, h2, h3, h4, h5, h6, p, div, span, li, blockquote'));
      computed.forEach(function ($el) {
        var style = getComputedStyle($el),
          decoration = style.textDecorationLine;
        if (decoration === 'underline') {
          _this8.warningCount++;
          $el.insertAdjacentHTML('beforebegin', _this8.annotate(Lang._('WARNING'), Lang._('TEXT_UNDERLINE_WARNING') + " <hr aria-hidden=\"true\"> " + Lang._('TEXT_UNDERLINE_WARNING_TIP'), true));
        }
      });
    }
    // ============================================================
    // Ruleset: Alternative text
    // ============================================================
    ;
    _proto.checkAltText = function checkAltText() {
      var _this9 = this;
      var containsAltTextStopWords = function containsAltTextStopWords(alt) {
        var altUrl = [".png", ".jpg", ".jpeg", ".gif", ".tiff", ".svg"];
        var hit = [null, null, null];
        altUrl.forEach(function (word) {
          if (alt.toLowerCase().indexOf(word) >= 0) {
            hit[0] = word;
          }
        });
        _this9.options.suspiciousAltWords.forEach(function (word) {
          if (alt.toLowerCase().indexOf(word) >= 0) {
            hit[1] = word;
          }
        });
        _this9.options.placeholderAltStopWords.forEach(function (word) {
          if (alt.length === word.length && alt.toLowerCase().indexOf(word) >= 0) {
            hit[2] = word;
          }
        });
        return hit;
      };

      // Stores the corresponding issue text to alternative text
      var images = Array.from(this.$root.querySelectorAll("img"));
      var excludeimages = Array.from(this.$root.querySelectorAll(this.imageIgnore));
      var $img = images.filter(function ($el) {
        return !excludeimages.includes($el);
      });
      $img.forEach(function ($el) {
        var alt = $el.getAttribute("alt");
        if (alt === null) {
          if ($el.closest('a[href]')) {
            if ($el.closest('a[href]').textContent.trim().length > 1) {
              $el.classList.add("jooa11y-error-border");
              $el.closest('a[href]').insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('MISSING_ALT_LINK_BUT_HAS_TEXT_MESSAGE'), false, true));
            } else if ($el.closest('a[href]').textContent.trim().length === 0) {
              $el.classList.add("jooa11y-error-border");
              $el.closest('a[href]').insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('MISSING_ALT_LINK_MESSAGE'), false, true));
            }
          }
          // General failure message if image is missing alt.
          else {
            $el.classList.add("jooa11y-error-border");
            $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('MISSING_ALT_MESSAGE'), false, true));
          }
        }
        // If alt attribute is present, further tests are done.
        else {
          var altText = escapeHTML(alt); //Prevent tooltip from breaking.
          var error = containsAltTextStopWords(altText);
          var altLength = alt.length;

          // Image fails if a stop word was found.
          if (error[0] != null && $el.closest("a[href]")) {
            _this9.errorCount++;
            $el.classList.add("jooa11y-error-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang.sprintf('LINK_IMAGE_BAD_ALT_MESSAGE', altText, error[0]) + " <hr aria-hidden=\"true\"> " + Lang._('LINK_IMAGE_BAD_ALT_MESSAGE_INFO'), false));
          } else if (error[2] != null && $el.closest("a[href]")) {
            _this9.errorCount++;
            $el.classList.add("jooa11y-error-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang.sprintf('LINK_IMAGE_PLACEHOLDER_ALT_MESSAGE', altText), false, true));
          } else if (error[1] != null && $el.closest("a[href]")) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang.sprintf('LINK_IMAGE_SUS_ALT_MESSAGE', altText, error[1]) + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_IMAGE_SUS_ALT_MESSAGE_INFO', altText), false));
          } else if (error[0] != null) {
            _this9.errorCount++;
            $el.classList.add("jooa11y-error-border");
            $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('LINK_ALT_HAS_BAD_WORD_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_ALT_HAS_BAD_WORD_MESSAGE_INFO', error[0], altText), false));
          } else if (error[2] != null) {
            _this9.errorCount++;
            $el.classList.add("jooa11y-error-border");
            $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang.sprintf('LINK_ALT_PLACEHOLDER_MESSAGE', altText), false));
          } else if (error[1] != null) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang.sprintf('LINK_ALT_HAS_SUS_WORD_MESSAGE', altText, error[1]) + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_ALT_HAS_SUS_WORD_MESSAGE_INFO', altText), false));
          } else if ((alt === "" || alt === " ") && $el.closest("a[href]")) {
            if ($el.closest("a[href]").getAttribute("tabindex") === "-1" && $el.closest("a[href]").getAttribute("aria-hidden") === "true") ;else if ($el.closest("a[href]").getAttribute("aria-hidden") === "true") {
              _this9.errorCount++;
              $el.classList.add("jooa11y-error-border");
              $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('LINK_HYPERLINKED_IMAGE_ARIA_HIDDEN'), false, true));
            } else if ($el.closest("a[href]").textContent.trim().length === 0) {
              _this9.errorCount++;
              $el.classList.add("jooa11y-error-border");
              $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('ERROR'), Lang._('LINK_IMAGE_LINK_NULL_ALT_NO_TEXT_MESSAGE'), false, true));
            } else {
              $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('GOOD'), Lang._('LINK_LINK_HAS_ALT_MESSAGE'), false, true));
            }
          }

          //Link and contains alt text.
          else if (alt.length > 250 && $el.closest("a[href]")) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('HYPERLINK_ALT_LENGTH_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('HYPERLINK_ALT_LENGTH_MESSAGE_INFO', altText, altLength), false));
          }

          //Link and contains an alt text.
          else if (alt !== "" && $el.closest("a[href]") && $el.closest("a[href]").textContent.trim().length === 0) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('LINK_IMAGE_LINK_ALT_TEXT_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_IMAGE_LINK_ALT_TEXT_MESSAGE_INFO', altText), false));
          }

          //Contains alt text & surrounding link text.
          else if (alt !== "" && $el.closest("a[href]") && $el.closest("a[href]").textContent.trim().length > 1) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.closest("a[href]").insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('LINK_ANCHOR_LINK_AND_ALT_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_ANCHOR_LINK_AND_ALT_MESSAGE_INFO', altText), false));
          }

          //Decorative alt and not a link.
          else if (alt === "" || alt === " ") {
            if ($el.closest("figure")) {
              var figcaption = $el.closest("figure").querySelector("figcaption");
              if (figcaption !== null && figcaption.textContent.trim().length >= 1) {
                _this9.warningCount++;
                $el.classList.add("jooa11y-warning-border");
                $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('IMAGE_FIGURE_DECORATIVE') + " <hr aria-hidden=\"true\"> " + Lang._('IMAGE_FIGURE_DECORATIVE_INFO'), false, true));
              }
            } else {
              _this9.warningCount++;
              $el.classList.add("jooa11y-warning-border");
              $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('LINK_DECORATIVE_MESSAGE'), false, true));
            }
          } else if (alt.length > 250) {
            _this9.warningCount++;
            $el.classList.add("jooa11y-warning-border");
            $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang._('LINK_ALT_TOO_LONG_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LINK_ALT_TOO_LONG_MESSAGE_INFO', altText, altLength), false));
          } else if (alt !== "") {
            //Figure element has same alt and caption text.
            if ($el.closest("figure")) {
              var _figcaption = $el.closest("figure").querySelector("figcaption");
              if (_figcaption !== null && _figcaption.textContent.trim().toLowerCase === altText.trim().toLowerCase) {
                _this9.warningCount++;
                $el.classList.add("jooa11y-warning-border");
                $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('WARNING'), Lang.sprintf('IMAGE_FIGURE_DUPLICATE_ALT', altText) + " <hr aria-hidden=\"true\"> " + Lang._('IMAGE_FIGURE_DECORATIVE_INFO'), false, true));
              }
            }
            //If image has alt text - pass!
            else {
              $el.insertAdjacentHTML('beforebegin', _this9.annotate(Lang._('GOOD'), "" + Lang.sprintf('LINK_PASS_ALT', altText), false, true));
            }
          }
        }
      });
    }
    // ============================================================
    // Rulesets: Labels
    // ============================================================
    ;
    _proto.checkLabels = function checkLabels() {
      var _this10 = this;
      var $inputs = Array.from(this.$root.querySelectorAll('input, select, textarea')).filter(function ($i) {
        return !_this10.$containerExclusions.includes($i) && !isElementHidden($i);
      });
      $inputs.forEach(function (el) {
        var ariaLabel = _this10.computeAriaLabel(el);
        var type = el.getAttribute('type');

        //If button type is submit or button: pass
        if (type === "submit" || type === "button" || type === "hidden") ;
        //Inputs where type="image".
        else if (type === "image") {
          var imgalt = el.getAttribute("alt");
          if (!imgalt || imgalt === ' ') {
            if (el.getAttribute("aria-label")) ;else {
              _this10.errorCount++;
              el.classList.add("jooa11y-error-border");
              el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('ERROR'), Lang._('LABELS_MISSING_IMAGE_INPUT_MESSAGE'), true));
            }
          }
        }
        //Recommendation to remove reset buttons.
        else if (type === "reset") {
          _this10.warningCount++;
          el.classList.add("jooa11y-warning-border");
          el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('WARNING'), Lang._('LABELS_INPUT_RESET_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang._('LABELS_INPUT_RESET_MESSAGE_TIP'), true));
        }
        //Uses ARIA. Warn them to ensure there's a visible label.
        else if (el.getAttribute("aria-label") || el.getAttribute("aria-labelledby") || el.getAttribute("title")) {
          if (el.getAttribute("title")) {
            var _ariaLabel = el.getAttribute("title");
            _this10.warningCount++;
            el.classList.add("jooa11y-warning-border");
            el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('WARNING'), Lang._('LABELS_ARIA_LABEL_INPUT_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LABELS_ARIA_LABEL_INPUT_MESSAGE_INFO', _ariaLabel), true));
          } else {
            _this10.warningCount++;
            el.classList.add("jooa11y-warning-border");
            el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('WARNING'), Lang._('LABELS_ARIA_LABEL_INPUT_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LABELS_ARIA_LABEL_INPUT_MESSAGE_INFO', ariaLabel), true));
          }
        }
        //Implicit labels.
        else if (el.closest('label') && el.closest('label').textContent.trim()) ;
        //Has an ID but doesn't have a matching FOR attribute.
        else if (el.getAttribute("id") && Array.from(el.parentElement.children).filter(function ($c) {
          return $c.nodeName === 'LABEL';
        }).length) {
          var $labels = Array.from(el.parentElement.children).filter(function ($c) {
            return $c.nodeName === 'LABEL';
          });
          var hasFor = false;
          $labels.forEach(function ($l) {
            if (hasFor) return;
            if ($l.getAttribute('for') === el.getAttribute('id')) {
              hasFor = true;
            }
          });
          if (!hasFor) {
            _this10.errorCount++;
            el.classList.add("jooa11y-error-border");
            el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('ERROR'), Lang._('LABELS_NO_FOR_ATTRIBUTE_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('LABELS_NO_FOR_ATTRIBUTE_MESSAGE_INFO', el.getAttribute('id')), true));
          }
        } else {
          _this10.errorCount++;
          el.classList.add("jooa11y-error-border");
          el.insertAdjacentHTML('afterend', _this10.annotate(Lang._('ERROR'), Lang._('LABELS_MISSING_LABEL_MESSAGE'), true));
        }
      });
    }
    // ============================================================
    // Rulesets: Embedded content.
    // ============================================================
    ;
    _proto.checkEmbeddedContent = function checkEmbeddedContent() {
      var _this11 = this;
      var $findiframes = Array.from(this.$root.querySelectorAll("iframe, audio, video"));
      var $iframes = $findiframes.filter(function ($el) {
        return !_this11.$containerExclusions.includes($el);
      });

      //Warning: Video content.
      var $videos = $iframes.filter(function ($el) {
        return $el.matches(_this11.options.videoContent);
      });
      $videos.forEach(function ($el) {
        var track = $el.getElementsByTagName('TRACK');
        if ($el.tagName === "VIDEO" && track.length) ;else {
          _this11.warningCount++;
          $el.classList.add("jooa11y-warning-border");
          $el.insertAdjacentHTML('beforebegin', _this11.annotate(Lang._('WARNING'), Lang._('EMBED_VIDEO')));
        }
      });

      //Warning: Audio content.
      var $audio = $iframes.filter(function ($el) {
        return $el.matches(_this11.options.audioContent);
      });
      $audio.forEach(function ($el) {
        _this11.warningCount++;
        $el.classList.add("jooa11y-warning-border");
        $el.insertAdjacentHTML('beforebegin', _this11.annotate(Lang._('WARNING'), Lang._('EMBED_AUDIO')));
      });

      //Error: iFrame is missing accessible name.
      $iframes.forEach(function ($el) {
        if ($el.tagName === "VIDEO" || $el.tagName === "AUDIO" || $el.getAttribute("aria-hidden") === "true" || $el.getAttribute("hidden") !== null || $el.style.display === 'none' || $el.getAttribute("role") === "presentation") ;else if ($el.getAttribute("title") === null || $el.getAttribute("title") === '') {
          if ($el.getAttribute("aria-label") === null || $el.getAttribute("aria-label") === '') {
            if ($el.getAttribute("aria-labelledby") === null) {
              //Make sure red error border takes precedence
              if ($el.classList.contains("jooa11y-warning-border")) {
                $el.classList.remove("jooa11y-warning-border");
              }
              _this11.errorCount++;
              $el.classList.add("jooa11y-error-border");
              $el.insertAdjacentHTML('beforebegin', _this11.annotate(Lang._('ERROR'), Lang._('EMBED_MISSING_TITLE')));
            }
          }
        } else ;
      });
      var $embeddedcontent = $iframes.filter(function ($el) {
        return !$el.matches(_this11.options.embeddedContent);
      });
      $embeddedcontent.forEach(function ($el) {
        if ($el.tagName === "VIDEO" || $el.tagName === "AUDIO" || $el.getAttribute("aria-hidden") === "true" || $el.getAttribute("hidden") !== null || $el.style.display === 'none' || $el.getAttribute("role") === "presentation" || $el.getAttribute("tabindex") === "-1") ;else {
          _this11.warningCount++;
          $el.classList.add("jooa11y-warning-border");
          $el.insertAdjacentHTML('beforebegin', _this11.annotate(Lang._('WARNING'), Lang._('EMBED_GENERAL_WARNING')));
        }
      });
    }

    // ============================================================
    // Rulesets: QA
    // ============================================================
    ;
    _proto.checkQA = function checkQA() {
      var _this12 = this;
      //Error: Find all links pointing to development environment.
      var $findbadDevLinks = this.options.linksToFlag ? Array.from(this.$root.querySelectorAll(this.options.linksToFlag)) : [];
      var $badDevLinks = $findbadDevLinks.filter(function ($el) {
        return !_this12.$containerExclusions.includes($el);
      });
      $badDevLinks.forEach(function ($el) {
        _this12.errorCount++;
        $el.classList.add("jooa11y-error-text");
        $el.insertAdjacentHTML('afterend', _this12.annotate(Lang._('ERROR'), Lang.sprintf('QA_BAD_LINK', $el.getAttribute('href')), true));
      });

      //Warning: Find all PDFs. Although only append warning icon to first PDF on page.
      var checkPDF = Array.from(this.$root.querySelectorAll('a[href$=".pdf"]')).filter(function (p) {
        return !_this12.$containerExclusions.includes(p);
      });
      var firstPDF = checkPDF[0];
      var pdfCount = checkPDF.length;
      if (checkPDF.length > 0) {
        this.warningCount++;
        checkPDF.forEach(function ($pdf) {
          $pdf.classList.add('jooa11y-warning-text');
          if ($pdf.querySelector('img')) {
            $pdf.classList.remove('jooa11y-warning-text');
          }
        });
        firstPDF.insertAdjacentHTML('afterend', this.annotate(Lang._('WARNING'), Lang.sprintf('QA_PDF_COUNT', pdfCount), true));
      }

      //Warning: Detect uppercase.
      var $findallcaps = Array.from(this.$root.querySelectorAll("h1, h2, h3, h4, h5, h6, p, li:not([class^='jooa11y']), blockquote"));
      var $allcaps = $findallcaps.filter(function ($el) {
        return !_this12.$containerExclusions.includes($el);
      });
      $allcaps.forEach(function ($el) {
        var uppercasePattern = /(?!<a[^>]*?>)(\b[A-Z][',!:A-Z\s]{15,}|\b[A-Z]{15,}\b)(?![^<]*?<\/a>)/g;
        var html = $el.innerHTML;
        $el.innerHTML = html.replace(uppercasePattern, "<span class='jooa11y-warning-uppercase'>$1</span>");
      });
      var $warningUppercase = document.querySelectorAll(".jooa11y-warning-uppercase");
      $warningUppercase.forEach(function ($el) {
        $el.insertAdjacentHTML('afterend', _this12.annotate(Lang._('WARNING'), Lang._('QA_UPPERCASE_WARNING'), true));
      });
      if ($warningUppercase.length > 0) {
        this.warningCount++;
      }

      //Tables check.
      var $findtables = Array.from(this.$root.querySelectorAll("table:not([role='presentation'])"));
      var $tables = $findtables.filter(function ($el) {
        return !_this12.$containerExclusions.includes($el);
      });
      $tables.forEach(function ($el) {
        var findTHeaders = $el.querySelectorAll("th");
        var findHeadingTags = $el.querySelectorAll("h1, h2, h3, h4, h5, h6");
        if (findTHeaders.length === 0) {
          _this12.errorCount++;
          $el.classList.add("jooa11y-error-border");
          $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('ERROR'), Lang._('TABLES_MISSING_HEADINGS')));
        }
        if (findHeadingTags.length > 0) {
          _this12.errorCount++;
          findHeadingTags.forEach(function ($el) {
            $el.classList.add("jooa11y-error-border");
            $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('ERROR'), Lang._('TABLES_SEMANTIC_HEADING') + " <hr aria-hidden=\"true\"> " + Lang._('TABLES_SEMANTIC_HEADING_INFO')));
          });
        }
        findTHeaders.forEach(function ($el) {
          if ($el.textContent.trim().length === 0) {
            _this12.errorCount++;
            $el.classList.add("jooa11y-error-border");
            $el.innerHTML = _this12.annotate(Lang._('ERROR'), Lang._('TABLES_EMPTY_HEADING') + " <hr aria-hidden=\"true\"> " + Lang._('TABLES_EMPTY_HEADING_INFO'));
          }
        });
      });

      //Error: Missing language tag. Lang should be at least 2 characters.
      var lang = document.querySelector("html").getAttribute("lang");
      if (!lang || lang.length < 2) {
        this.errorCount++;
        var jooa11yContainer = document.getElementById("jooa11y-container");
        jooa11yContainer.insertAdjacentHTML('afterend', this.annotateBanner(Lang._('ERROR'), Lang._('QA_PAGE_LANGUAGE_MESSAGE')));
      }

      //Excessive bolding or italics.
      var $findstrongitalics = Array.from(this.$root.querySelectorAll("strong, em"));
      var $strongitalics = $findstrongitalics.filter(function ($el) {
        return !_this12.$containerExclusions.includes($el);
      });
      $strongitalics.forEach(function ($el) {
        if ($el.textContent.trim().length > 200) {
          _this12.warningCount++;
          $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('WARNING'), Lang._('QA_BAD_ITALICS')));
        }
      });

      //Find blockquotes used as headers.
      var $findblockquotes = Array.from(this.$root.querySelectorAll("blockquote"));
      var $blockquotes = $findblockquotes.filter(function ($el) {
        return !_this12.$containerExclusions.includes($el);
      });
      $blockquotes.forEach(function ($el) {
        var bqHeadingText = $el.textContent;
        if (bqHeadingText.trim().length < 25) {
          _this12.warningCount++;
          $el.classList.add("jooa11y-warning-border");
          $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('WARNING'), Lang.sprintf('QA_BLOCKQUOTE_MESSAGE', bqHeadingText) + " <hr aria-hidden=\"true\"> " + Lang._('QA_BLOCKQUOTE_MESSAGE_TIP')));
        }
      });

      // Warning: Detect fake headings.
      this.$p.forEach(function ($el) {
        var brAfter = $el.innerHTML.indexOf("</strong><br>");
        var brBefore = $el.innerHTML.indexOf("<br></strong>");

        //Check paragraphs greater than x characters.
        if ($el && $el.textContent.trim().length >= 300) {
          var firstChild = $el.firstChild;

          //If paragraph starts with <strong> tag and ends with <br>.
          if (firstChild.tagName === "STRONG" && (brBefore !== -1 || brAfter !== -1)) {
            var boldtext = firstChild.textContent;
            if (!$el.closest("table") && boldtext.length <= 120) {
              firstChild.classList.add("jooa11y-fake-heading", "jooa11y-warning-border");
              $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('WARNING'), Lang.sprintf('QA_FAKE_HEADING', boldtext) + " <hr aria-hidden=\"true\"> " + Lang._('QA_FAKE_HEADING_INFO')));
            }
          }
        }

        // If paragraph only contains <p><strong>...</strong></p>.
        if (/^<(strong)>.+<\/\1>$/.test($el.innerHTML.trim())) {
          //Although only flag if it:
          // 1) Has less than 120 characters (typical heading length).
          // 2) The previous element is not a heading.
          var prevElement = $el.previousElementSibling;
          var tagName = "";
          if (prevElement !== null) {
            tagName = prevElement.tagName;
          }
          if (!$el.closest("table") && $el.textContent.length <= 120 && tagName.charAt(0) !== "H") {
            var _boldtext = $el.textContent;
            $el.classList.add("jooa11y-fake-heading", "jooa11y-warning-border");
            $el.firstChild.insertAdjacentHTML("afterend", _this12.annotate(Lang._('WARNING'), Lang.sprintf('QA_FAKE_HEADING', _boldtext) + " <hr aria-hidden=\"true\"> " + Lang._('QA_FAKE_HEADING_INFO')));
          }
        }
      });
      if (this.$root.querySelectorAll(".jooa11y-fake-heading").length > 0) {
        this.warningCount++;
      }

      // Check duplicate ID
      var ids = this.$root.querySelectorAll('[id]');
      var allIds = {};
      ids.forEach(function ($el) {
        var id = $el.id;
        if (id) {
          if (allIds[id] === undefined) {
            allIds[id] = 1;
          } else {
            $el.classList.add("sa11y-error-border");
            $el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('WARNING'), Lang._('QA_DUPLICATE_ID') + "\n\t\t\t\t\t\t\t\t<hr aria-hidden=\"true\">\n\t\t\t\t\t\t\t\t" + Lang.sprintf('QA_DUPLICATE_ID_TIP', id), true));
          }
        }
      });
      /* Thanks to John Jameson from PrincetonU for this ruleset! */
      // Detect paragraphs that should be lists.
      var activeMatch = "";
      var prefixDecrement = {
        b: "a",
        B: "A",
        2: "1"
      };
      var prefixMatch = /a\.|a\)|A\.|A\)|1\.|1\)|\*\s|-\s|--|•\s|→\s|✓\s|✔\s|✗\s|✖\s|✘\s|❯\s|›\s|»\s/;
      var decrement = function decrement(el) {
        return el.replace(/^b|^B|^2/, function (match) {
          return prefixDecrement[match];
        });
      };
      this.$p.forEach(function (el) {
        var hit = false;
        // Grab first two characters.
        var firstPrefix = el.textContent.substring(0, 2);
        if (firstPrefix.trim().length > 0 && firstPrefix !== activeMatch && firstPrefix.match(prefixMatch)) {
          // We have a prefix and a possible hit
          // Split p by carriage return if present and compare.
          var hasBreak = el.innerHTML.indexOf("<br>");
          if (hasBreak !== -1) {
            var subParagraph = el.innerHTML.substring(hasBreak + 4).trim();
            var subPrefix = subParagraph.substring(0, 2);
            if (firstPrefix === decrement(subPrefix)) {
              hit = true;
            }
          }
          // Decrement the second p prefix and compare .
          if (!hit) {
            var $second = el.nextElementSibling.nodeName === 'P' ? el.nextElementSibling : null;
            if ($second) {
              var secondPrefix = decrement(el.nextElementSibling.textContent.substring(0, 2));
              if (firstPrefix === secondPrefix) {
                hit = true;
              }
            }
          }
          if (hit) {
            _this12.warningCount++;
            el.insertAdjacentHTML('beforebegin', _this12.annotate(Lang._('WARNING'), Lang.sprintf('QA_SHOULD_BE_LIST', firstPrefix) + " <hr aria-hidden=\"true\"> " + Lang._('QA_SHOULD_BE_LIST_TIP')));
            el.classList.add("jooa11y-fake-list");
            activeMatch = firstPrefix;
          } else {
            activeMatch = "";
          }
        } else {
          activeMatch = "";
        }
      });
      if (this.$root.querySelectorAll('.jooa11y-fake-list').length > 0) {
        this.warningCount++;
      }
    }
    // ============================================================
    // Rulesets: Contrast
    // Color contrast plugin by jasonday: https://github.com/jasonday/color-contrast
    // ============================================================
    ;
    _proto.checkContrast = function checkContrast() {
      var _this13 = this;
      var $findcontrast = Array.from(this.$root.querySelectorAll("* > :not(.jooa11y-heading-label)"));
      var $contrast = $findcontrast.filter(function ($el) {
        return !_this13.$containerExclusions.includes($el);
      });
      var contrastErrors = {
        errors: [],
        warnings: []
      };
      var elements = $contrast;
      var contrast = {
        // Parse rgb(r, g, b) and rgba(r, g, b, a) strings into an array.
        // Adapted from https://github.com/gka/chroma.js
        parseRgb: function parseRgb(css) {
          var i, m, rgb, _i, _j;
          if (m = css.match(/rgb\(\s*(\-?\d+),\s*(\-?\d+)\s*,\s*(\-?\d+)\s*\)/)) {
            rgb = m.slice(1, 4);
            for (i = _i = 0; _i <= 2; i = ++_i) {
              rgb[i] = +rgb[i];
            }
            rgb[3] = 1;
          } else if (m = css.match(/rgba\(\s*(\-?\d+),\s*(\-?\d+)\s*,\s*(\-?\d+)\s*,\s*([01]|[01]?\.\d+)\)/)) {
            rgb = m.slice(1, 5);
            for (i = _j = 0; _j <= 3; i = ++_j) {
              rgb[i] = +rgb[i];
            }
          }
          return rgb;
        },
        // Based on http://www.w3.org/TR/WCAG20/#relativeluminancedef
        relativeLuminance: function relativeLuminance(c) {
          var lum = [];
          for (var i = 0; i < 3; i++) {
            var v = c[i] / 255;
            lum.push(v < 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4));
          }
          return 0.2126 * lum[0] + 0.7152 * lum[1] + 0.0722 * lum[2];
        },
        // Based on http://www.w3.org/TR/WCAG20/#contrast-ratiodef
        contrastRatio: function contrastRatio(x, y) {
          var l1 = contrast.relativeLuminance(contrast.parseRgb(x));
          var l2 = contrast.relativeLuminance(contrast.parseRgb(y));
          return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
        },
        getBackground: function getBackground(el) {
          var styles = getComputedStyle(el),
            bgColor = styles.backgroundColor,
            bgImage = styles.backgroundImage,
            rgb = contrast.parseRgb(bgColor) + '',
            alpha = rgb.split(',');

          // if background has alpha transparency, flag manual check
          if (alpha[3] < 1 && alpha[3] > 0) {
            return "alpha";
          }

          // if element has no background image, or transparent background (alpha == 0) return bgColor
          if (bgColor !== 'rgba(0, 0, 0, 0)' && bgColor !== 'transparent' && bgImage === "none" && alpha[3] !== '0') {
            return bgColor;
          } else if (bgImage !== "none") {
            return "image";
          }

          // retest if not returned above
          if (el.tagName === 'HTML') {
            return 'rgb(255, 255, 255)';
          } else {
            return contrast.getBackground(el.parentNode);
          }
        },
        // check visibility - based on jQuery method
        // isVisible: function (el) {
        //     return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
        // },
        check: function check() {
          // resets results
          contrastErrors = {
            errors: [],
            warnings: []
          };
          for (var i = 0; i < elements.length; i++) {
            (function (elem) {
              // Test if visible. Although we want invisible too.
              if (contrast /* .isVisible(elem) */) {
                var style = getComputedStyle(elem),
                  color = style.color,
                  fill = style.fill,
                  fontSize = parseInt(style.fontSize),
                  pointSize = fontSize * 3 / 4,
                  fontWeight = style.fontWeight,
                  htmlTag = elem.tagName,
                  background = contrast.getBackground(elem),
                  textString = [].reduce.call(elem.childNodes, function (a, b) {
                    return a + (b.nodeType === 3 ? b.textContent : '');
                  }, ''),
                  text = textString.trim(),
                  ratio,
                  error,
                  warning;
                if (htmlTag === "SVG") {
                  ratio = Math.round(contrast.contrastRatio(fill, background) * 100) / 100;
                  if (ratio < 3) {
                    error = {
                      elem: elem,
                      ratio: ratio + ':1'
                    };
                    contrastErrors.errors.push(error);
                  }
                } else if (text.length || htmlTag === "INPUT" || htmlTag === "SELECT" || htmlTag === "TEXTAREA") {
                  // does element have a background image - needs to be manually reviewed
                  if (background === "image") {
                    warning = {
                      elem: elem
                    };
                    contrastErrors.warnings.push(warning);
                  } else if (background === "alpha") {
                    warning = {
                      elem: elem
                    };
                    contrastErrors.warnings.push(warning);
                  } else {
                    ratio = Math.round(contrast.contrastRatio(color, background) * 100) / 100;
                    if (pointSize >= 18 || pointSize >= 14 && fontWeight >= 700) {
                      if (ratio < 3) {
                        error = {
                          elem: elem,
                          ratio: ratio + ':1'
                        };
                        contrastErrors.errors.push(error);
                      }
                    } else {
                      if (ratio < 4.5) {
                        error = {
                          elem: elem,
                          ratio: ratio + ':1'
                        };
                        contrastErrors.errors.push(error);
                      }
                    }
                  }
                }
              }
            })(elements[i]);
          }
          return contrastErrors;
        }
      };
      contrast.check();
      //const {errorMessage, warningMessage} = jooa11yIM["contrast"];

      contrastErrors.errors.forEach(function (item) {
        var name = item.elem;
        var cratio = item.ratio;
        var clone = name.cloneNode(true);
        var removeJooa11yHeadingLabel = clone.querySelectorAll('.jooa11y-heading-label');
        for (var i = 0; i < removeJooa11yHeadingLabel.length; i++) {
          clone.removeChild(removeJooa11yHeadingLabel[i]);
        }
        var nodetext = clone.textContent;
        _this13.errorCount++;
        if (name.tagName === "INPUT") {
          name.insertAdjacentHTML('beforebegin', _this13.annotate(Lang._('ERROR'), Lang._('CONTRAST_ERROR_INPUT_MESSAGE') + "\n                         <hr aria-hidden=\"true\">\n                         " + Lang.sprintf('CONTRAST_ERROR_INPUT_MESSAGE_INFO', cratio), true));
        } else {
          name.insertAdjacentHTML('beforebegin', _this13.annotate(Lang._('ERROR'), Lang.sprintf('CONTRAST_ERROR_MESSAGE', cratio, nodetext) + "\n                        <hr aria-hidden=\"true\">\n                        " + Lang.sprintf('CONTRAST_ERROR_MESSAGE_INFO', cratio, nodetext), true));
        }
      });
      contrastErrors.warnings.forEach(function (item) {
        var name = item.elem;
        var clone = name.cloneNode(true);
        var removeJooa11yHeadingLabel = clone.querySelectorAll('.jooa11y-heading-label');
        for (var i = 0; i < removeJooa11yHeadingLabel.length; i++) {
          clone.removeChild(removeJooa11yHeadingLabel[i]);
        }
        var nodetext = clone.textContent;
        _this13.warningCount++;
        name.insertAdjacentHTML('beforebegin', _this13.annotate(Lang._('WARNING'), Lang._('CONTRAST_WARNING_MESSAGE') + " <hr aria-hidden=\"true\"> " + Lang.sprintf('CONTRAST_WARNING_MESSAGE_INFO', nodetext), true));
      });
    }
    // ============================================================
    // Rulesets: Readability
    // Adapted from Greg Kraus' readability script: https://accessibility.oit.ncsu.edu/it-accessibility-at-nc-state/developers/tools/readability-bookmarklet/
    // ============================================================
    ;
    _proto.checkReadability = function checkReadability() {
      var _this14 = this;
      var container = document.querySelector(this.options.readabilityRoot);
      var $findreadability = Array.from(container.querySelectorAll("p, li"));
      var $readability = $findreadability.filter(function ($el) {
        return !_this14.$containerExclusions.includes($el);
      });

      //Crude hack to add a period to the end of list items to make a complete sentence.
      $readability.forEach(function ($el) {
        var listText = $el.textContent;
        if (listText.length >= 120) {
          if (listText.charAt(listText.length - 1) !== ".") {
            $el.insertAdjacentHTML("beforeend", "<span class='jooa11y-readability-period jooa11y-visually-hidden'>.</span>");
          }
        }
      });

      // Compute syllables: http://stackoverflow.com/questions/5686483/how-to-compute-number-of-syllables-in-a-word-in-javascript
      function number_of_syllables(wordCheck) {
        wordCheck = wordCheck.toLowerCase().replace('.', '').replace('\n', '');
        if (wordCheck.length <= 3) {
          return 1;
        }
        wordCheck = wordCheck.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
        wordCheck = wordCheck.replace(/^y/, '');
        var syllable_string = wordCheck.match(/[aeiouy]{1,2}/g);
        var syllables = 0;
        if (!!syllable_string) {
          syllables = syllable_string.length;
        }
        return syllables;
      }
      var readabilityarray = [];
      for (var i = 0; i < $readability.length; i++) {
        var current = $readability[i];
        if (current.textContent.replace(/ |\n/g, '') !== '') {
          readabilityarray.push(current.textContent);
        }
      }
      var paragraphtext = readabilityarray.join(' ').trim().toString();
      var words_raw = paragraphtext.replace(/[.!?-]+/g, ' ').split(' ');
      var words = 0;
      for (var _i5 = 0; _i5 < words_raw.length; _i5++) {
        if (words_raw[_i5] != 0) {
          words = words + 1;
        }
      }
      var sentences_raw = paragraphtext.split(/[.!?]+/);
      var sentences = 0;
      for (var _i6 = 0; _i6 < sentences_raw.length; _i6++) {
        if (sentences_raw[_i6] !== '') {
          sentences = sentences + 1;
        }
      }
      var total_syllables = 0;
      var syllables1 = 0;
      var syllables2 = 0;
      for (var _i7 = 0; _i7 < words_raw.length; _i7++) {
        if (words_raw[_i7] != 0) {
          var syllable_count = number_of_syllables(words_raw[_i7]);
          if (syllable_count === 1) {
            syllables1 = syllables1 + 1;
          }
          if (syllable_count === 2) {
            syllables2 = syllables2 + 1;
          }
          total_syllables = total_syllables + syllable_count;
        }
      }

      //var characters = paragraphtext.replace(/[.!?|\s]+/g, '').length;
      //Reference: https://core.ac.uk/download/pdf/6552422.pdf
      //Reference: https://github.com/Yoast/YoastSEO.js/issues/267

      var flesch_reading_ease;
      if (this.options.readabilityLang === 'en') {
        flesch_reading_ease = 206.835 - 1.015 * words / sentences - 84.6 * total_syllables / words;
      } else if (this.options.readabilityLang === 'fr') {
        //French (Kandel & Moles)
        flesch_reading_ease = 207 - 1.015 * words / sentences - 73.6 * total_syllables / words;
      } else if (this.options.readabilityLang === 'es') {
        flesch_reading_ease = 206.84 - 1.02 * words / sentences - 0.60 * (100 * total_syllables / words);
      }
      if (flesch_reading_ease > 100) {
        flesch_reading_ease = 100;
      } else if (flesch_reading_ease < 0) {
        flesch_reading_ease = 0;
      }
      var $readabilityinfo = document.getElementById("jooa11y-readability-info");
      if (paragraphtext.length === 0) {
        $readabilityinfo.innerHTML = Lang._('READABILITY_NO_P_OR_LI_MESSAGE');
      } else if (words > 30) {
        var fleschScore = flesch_reading_ease.toFixed(1);
        var avgWordsPerSentence = (words / sentences).toFixed(1);
        var complexWords = Math.round(100 * ((words - (syllables1 + syllables2)) / words));

        //WCAG AAA pass if greater than 60
        if (fleschScore >= 0 && fleschScore < 30) {
          $readabilityinfo.innerHTML = "<span>" + fleschScore + "</span> <span class=\"jooa11y-readability-score\">" + Lang._('VERY_DIFFICULT_READABILITY') + "</span>";
        } else if (fleschScore > 31 && fleschScore < 49) {
          $readabilityinfo.innerHTML = "<span>" + fleschScore + "</span> <span class=\"jooa11y-readability-score\">" + Lang._('DIFFICULT_READABILITY') + "</span>";
        } else if (fleschScore > 50 && fleschScore < 60) {
          $readabilityinfo.innerHTML = "<span>" + fleschScore + "</span> <span class=\"jooa11y-readability-score\">" + Lang._('FAIRLY_DIFFICULT_READABILITY') + "</span>";
        } else {
          $readabilityinfo.innerHTML = "<span>" + fleschScore + "</span> <span class=\"jooa11y-readability-score\">" + Lang._('GOOD_READABILITY') + "</span>";
        }
        document.getElementById("jooa11y-readability-details").innerHTML = "<li><span class='jooa11y-bold'>" + Lang._('AVG_WORD_PER_SENTENCE') + "</span> " + avgWordsPerSentence + "</li>\n                <li><span class='jooa11y-bold'>" + Lang._('COMPLEX_WORDS') + "</span> " + complexWords + "%</li>\n                <li><span class='jooa11y-bold'>" + Lang._('TOTAL_WORDS') + "</span> " + words + "</li>";
      } else {
        $readabilityinfo.textContent = Lang._('READABILITY_NOT_ENOUGH_CONTENT_MESSAGE');
      }
    }
    //----------------------------------------------------------------------
    // Templating for Error, Warning and Pass buttons.
    //----------------------------------------------------------------------
    ;
    _proto.annotate = function annotate(type, content, inline) {
      var _CSSName;
      if (inline === void 0) {
        inline = false;
      }
      var validTypes = [Lang._('ERROR'), Lang._('WARNING'), Lang._('GOOD')];
      if (validTypes.indexOf(type) === -1) {
        throw Error("Invalid type [" + type + "] for annotation");
      }
      var CSSName = (_CSSName = {}, _CSSName[validTypes[0]] = "error", _CSSName[validTypes[1]] = "warning", _CSSName[validTypes[2]] = "good", _CSSName);

      // Check if content is a function
      if (content && {}.toString.call(content) === "[object Function]") {
        // if it is, call it and get the value.
        content = content();
      }

      // Escape content, it is need because it used inside data-tippy-content=""
      content = escapeHTML(content);
      return "\n        <div class=" + (inline ? "jooa11y-instance-inline" : "jooa11y-instance") + ">\n            <button\n            type=\"button\"\n            aria-label=\"" + [type] + "\"\n            class=\"jooa11y-btn jooa11y-" + CSSName[type] + "-btn" + (inline ? "-text" : "") + "\"\n            data-tippy-content=\"<div lang='" + this.options.langCode + "'>\n                <div class='jooa11y-header-text'>" + [type] + "</div>\n                " + content + "\n            </div>\n        \">\n        </button>\n        </div>";
    }
    //----------------------------------------------------------------------
    // Templating for full-width banners.
    //----------------------------------------------------------------------
    ;
    _proto.annotateBanner = function annotateBanner(type, content) {
      var _CSSName2;
      var validTypes = [Lang._('ERROR'), Lang._('WARNING'), Lang._('GOOD')];
      if (validTypes.indexOf(type) === -1) {
        throw Error("Invalid type [" + type + "] for annotation");
      }
      var CSSName = (_CSSName2 = {}, _CSSName2[validTypes[0]] = "error", _CSSName2[validTypes[1]] = "warning", _CSSName2[validTypes[2]] = "good", _CSSName2);

      // Check if content is a function
      if (content && {}.toString.call(content) === "[object Function]") {
        // if it is, call it and get the value.
        content = content();
      }
      return "<div class=\"jooa11y-instance jooa11y-" + CSSName[type] + "-message-container\">\n      <div role=\"region\" aria-label=\"" + [type] + "\" class=\"jooa11y-" + CSSName[type] + "-message\" lang=\"" + this.options.langCode + "\">\n          " + content + "\n      </div>\n  </div>";
    };
    return Jooa11y;
  }();
  if (!Joomla) {
    throw new Error('Joomla API is not properly initialised');
  }
  var stringPrefix = 'PLG_SYSTEM_JOOA11Y_';
  Lang.translate = function (string) {
    return Joomla.Text._(stringPrefix + string, string);
  };
  var options = Joomla.getOptions('jooa11yOptions');
  window.addEventListener('load', function () {
    // Instantiate
    var checker = new Jooa11y(options);
    checker.doInitialCheck();
  });

})();
