(function () {
  'use strict';

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
   * A NodeIterator with iframes support and a method to check if an element is
   * matching a specified selector
   * @example
   * const iterator = new DOMIterator(
   *     document.querySelector("#context"), true
   * );
   * iterator.forEachNode(NodeFilter.SHOW_TEXT, node => {
   *     console.log(node);
   * }, node => {
   *     if(DOMIterator.matches(node.parentNode, ".ignore")){
   *         return NodeFilter.FILTER_REJECT;
   *     } else {
   *         return NodeFilter.FILTER_ACCEPT;
   *     }
   * }, () => {
   *     console.log("DONE");
   * });
   * @todo Outsource into separate repository
   */
  var DOMIterator = /*#__PURE__*/function () {
    /**
     * @param {HTMLElement|HTMLElement[]|NodeList|string} ctx - The context DOM
     * element, an array of DOM elements, a NodeList or a selector
     * @param {boolean} [iframes=true] - A boolean indicating if iframes should
     * be handled
     * @param {string[]} [exclude=[]] - An array containing exclusion selectors
     * for iframes
     * @param {number} [iframesTimeout=5000] - A number indicating the ms to
     * wait before an iframe should be skipped, in case the load event isn't
     * fired. This also applies if the user is offline and the resource of the
     * iframe is online (either by the browsers "offline" mode or because
     * there's no internet connection)
     */
    function DOMIterator(ctx, iframes, exclude, iframesTimeout) {
      if (iframes === void 0) {
        iframes = true;
      }
      if (exclude === void 0) {
        exclude = [];
      }
      if (iframesTimeout === void 0) {
        iframesTimeout = 5000;
      }
      /**
       * The context of the instance. Either a DOM element, an array of DOM
       * elements, a NodeList or a selector
       * @type {HTMLElement|HTMLElement[]|NodeList|string}
       * @access protected
       */
      this.ctx = ctx;
      /**
       * Boolean indicating if iframe support is enabled
       * @type {boolean}
       * @access protected
       */
      this.iframes = iframes;
      /**
       * An array containing exclusion selectors for iframes
       * @type {string[]}
       */
      this.exclude = exclude;
      /**
       * The maximum ms to wait for a load event before skipping an iframe
       * @type {number}
       */
      this.iframesTimeout = iframesTimeout;
    }

    /**
     * Checks if the specified DOM element matches the selector
     * @param  {HTMLElement} element - The DOM element
     * @param  {string|string[]} selector - The selector or an array with
     * selectors
     * @return {boolean}
     * @access public
     */
    DOMIterator.matches = function matches(element, selector) {
      var selectors = typeof selector === 'string' ? [selector] : selector,
        fn = element.matches || element.matchesSelector || element.msMatchesSelector || element.mozMatchesSelector || element.oMatchesSelector || element.webkitMatchesSelector;
      if (fn) {
        var match = false;
        selectors.every(function (sel) {
          if (fn.call(element, sel)) {
            match = true;
            return false;
          }
          return true;
        });
        return match;
      } else {
        // may be false e.g. when el is a textNode
        return false;
      }
    }

    /**
     * Returns all contexts filtered by duplicates (even nested)
     * @return {HTMLElement[]} - An array containing DOM contexts
     * @access protected
     */;
    var _proto = DOMIterator.prototype;
    _proto.getContexts = function getContexts() {
      var ctx,
        filteredCtx = [];
      if (typeof this.ctx === 'undefined' || !this.ctx) {
        // e.g. null
        ctx = [];
      } else if (NodeList.prototype.isPrototypeOf(this.ctx)) {
        ctx = Array.prototype.slice.call(this.ctx);
      } else if (Array.isArray(this.ctx)) {
        ctx = this.ctx;
      } else if (typeof this.ctx === 'string') {
        ctx = Array.prototype.slice.call(document.querySelectorAll(this.ctx));
      } else {
        // e.g. HTMLElement or element inside iframe
        ctx = [this.ctx];
      }
      // filter duplicate text nodes
      ctx.forEach(function (ctx) {
        var isDescendant = filteredCtx.filter(function (contexts) {
          return contexts.contains(ctx);
        }).length > 0;
        if (filteredCtx.indexOf(ctx) === -1 && !isDescendant) {
          filteredCtx.push(ctx);
        }
      });
      return filteredCtx;
    }

    /**
     * @callback DOMIterator~getIframeContentsSuccessCallback
     * @param {HTMLDocument} contents - The contentDocument of the iframe
     */
    /**
     * Calls the success callback function with the iframe document. If it can't
     * be accessed it calls the error callback function
     * @param {HTMLElement} ifr - The iframe DOM element
     * @param {DOMIterator~getIframeContentsSuccessCallback} successFn
     * @param {function} [errorFn]
     * @access protected
     */;
    _proto.getIframeContents = function getIframeContents(ifr, successFn, errorFn) {
      if (errorFn === void 0) {
        errorFn = function errorFn() {};
      }
      var doc;
      try {
        var ifrWin = ifr.contentWindow;
        doc = ifrWin.document;
        if (!ifrWin || !doc) {
          // no permission = null. Undefined in Phantom
          throw new Error('iframe inaccessible');
        }
      } catch (e) {
        errorFn();
      }
      if (doc) {
        successFn(doc);
      }
    }

    /**
     * Checks if an iframe is empty (if about:blank is the shown page)
     * @param {HTMLElement} ifr - The iframe DOM element
     * @return {boolean}
     * @access protected
     */;
    _proto.isIframeBlank = function isIframeBlank(ifr) {
      var bl = 'about:blank',
        src = ifr.getAttribute('src').trim(),
        href = ifr.contentWindow.location.href;
      return href === bl && src !== bl && src;
    }

    /**
     * Observes the onload event of an iframe and calls the success callback or
     * the error callback if the iframe is inaccessible. If the event isn't
     * fired within the specified {@link DOMIterator#iframesTimeout}, then it'll
     * call the error callback too
     * @param {HTMLElement} ifr - The iframe DOM element
     * @param {DOMIterator~getIframeContentsSuccessCallback} successFn
     * @param {function} errorFn
     * @access protected
     */;
    _proto.observeIframeLoad = function observeIframeLoad(ifr, successFn, errorFn) {
      var _this = this;
      var called = false,
        tout = null;
      var listener = function listener() {
        if (called) {
          return;
        }
        called = true;
        clearTimeout(tout);
        try {
          if (!_this.isIframeBlank(ifr)) {
            ifr.removeEventListener('load', listener);
            _this.getIframeContents(ifr, successFn, errorFn);
          }
        } catch (e) {
          // isIframeBlank maybe throws throws an error
          errorFn();
        }
      };
      ifr.addEventListener('load', listener);
      tout = setTimeout(listener, this.iframesTimeout);
    }

    /**
     * Callback when the iframe is ready
     * @callback DOMIterator~onIframeReadySuccessCallback
     * @param {HTMLDocument} contents - The contentDocument of the iframe
     */
    /**
     * Callback if the iframe can't be accessed
     * @callback DOMIterator~onIframeReadyErrorCallback
     */
    /**
     * Calls the callback if the specified iframe is ready for DOM access
     * @param  {HTMLElement} ifr - The iframe DOM element
     * @param  {DOMIterator~onIframeReadySuccessCallback} successFn - Success
     * callback
     * @param {DOMIterator~onIframeReadyErrorCallback} errorFn - Error callback
     * @see {@link http://stackoverflow.com/a/36155560/3894981} for
     * background information
     * @access protected
     */;
    _proto.onIframeReady = function onIframeReady(ifr, successFn, errorFn) {
      try {
        if (ifr.contentWindow.document.readyState === 'complete') {
          if (this.isIframeBlank(ifr)) {
            this.observeIframeLoad(ifr, successFn, errorFn);
          } else {
            this.getIframeContents(ifr, successFn, errorFn);
          }
        } else {
          this.observeIframeLoad(ifr, successFn, errorFn);
        }
      } catch (e) {
        // accessing document failed
        errorFn();
      }
    }

    /**
     * Callback when all iframes are ready for DOM access
     * @callback DOMIterator~waitForIframesDoneCallback
     */
    /**
     * Iterates over all iframes and calls the done callback when all of them
     * are ready for DOM access (including nested ones)
     * @param {HTMLElement} ctx - The context DOM element
     * @param {DOMIterator~waitForIframesDoneCallback} done - Done callback
     */;
    _proto.waitForIframes = function waitForIframes(ctx, done) {
      var _this2 = this;
      var eachCalled = 0;
      this.forEachIframe(ctx, function () {
        return true;
      }, function (ifr) {
        eachCalled++;
        _this2.waitForIframes(ifr.querySelector('html'), function () {
          if (! --eachCalled) {
            done();
          }
        });
      }, function (handled) {
        if (!handled) {
          done();
        }
      });
    }

    /**
     * Callback allowing to filter an iframe. Must return true when the element
     * should remain, otherwise false
     * @callback DOMIterator~forEachIframeFilterCallback
     * @param {HTMLElement} iframe - The iframe DOM element
     */
    /**
     * Callback for each iframe content
     * @callback DOMIterator~forEachIframeEachCallback
     * @param {HTMLElement} content - The iframe document
     */
    /**
     * Callback if all iframes inside the context were handled
     * @callback DOMIterator~forEachIframeEndCallback
     * @param {number} handled - The number of handled iframes (those who
     * wheren't filtered)
     */
    /**
     * Iterates over all iframes inside the specified context and calls the
     * callbacks when they're ready. Filters iframes based on the instance
     * exclusion selectors
     * @param {HTMLElement} ctx - The context DOM element
     * @param {DOMIterator~forEachIframeFilterCallback} filter - Filter callback
     * @param {DOMIterator~forEachIframeEachCallback} each - Each callback
     * @param {DOMIterator~forEachIframeEndCallback} [end] - End callback
     * @access protected
     */;
    _proto.forEachIframe = function forEachIframe(ctx, filter, each, end) {
      var _this3 = this;
      if (end === void 0) {
        end = function end() {};
      }
      var ifr = ctx.querySelectorAll('iframe'),
        open = ifr.length,
        handled = 0;
      ifr = Array.prototype.slice.call(ifr);
      var checkEnd = function checkEnd() {
        if (--open <= 0) {
          end(handled);
        }
      };
      if (!open) {
        checkEnd();
      }
      ifr.forEach(function (ifr) {
        if (DOMIterator.matches(ifr, _this3.exclude)) {
          checkEnd();
        } else {
          _this3.onIframeReady(ifr, function (con) {
            if (filter(ifr)) {
              handled++;
              each(con);
            }
            checkEnd();
          }, checkEnd);
        }
      });
    }

    /**
     * Creates a NodeIterator on the specified context
     * @see {@link https://developer.mozilla.org/en/docs/Web/API/NodeIterator}
     * @param {HTMLElement} ctx - The context DOM element
     * @param {DOMIterator~whatToShow} whatToShow
     * @param {DOMIterator~filterCb} filter
     * @return {NodeIterator}
     * @access protected
     */;
    _proto.createIterator = function createIterator(ctx, whatToShow, filter) {
      return document.createNodeIterator(ctx, whatToShow, filter, false);
    }

    /**
     * Creates an instance of DOMIterator in an iframe
     * @param {HTMLDocument} contents - Iframe document
     * @return {DOMIterator}
     * @access protected
     */;
    _proto.createInstanceOnIframe = function createInstanceOnIframe(contents) {
      return new DOMIterator(contents.querySelector('html'), this.iframes);
    }

    /**
     * Checks if an iframe occurs between two nodes, more specifically if an
     * iframe occurs before the specified node and after the specified prevNode
     * @param {HTMLElement} node - The node that should occur after the iframe
     * @param {HTMLElement} prevNode - The node that should occur before the
     * iframe
     * @param {HTMLElement} ifr - The iframe to check against
     * @return {boolean}
     * @access protected
     */;
    _proto.compareNodeIframe = function compareNodeIframe(node, prevNode, ifr) {
      var compCurr = node.compareDocumentPosition(ifr),
        prev = Node.DOCUMENT_POSITION_PRECEDING;
      if (compCurr & prev) {
        if (prevNode !== null) {
          var compPrev = prevNode.compareDocumentPosition(ifr),
            after = Node.DOCUMENT_POSITION_FOLLOWING;
          if (compPrev & after) {
            return true;
          }
        } else {
          return true;
        }
      }
      return false;
    }

    /**
     * @typedef {DOMIterator~getIteratorNodeReturn}
     * @type {object.<string>}
     * @property {HTMLElement} prevNode - The previous node or null if there is
     * no
     * @property {HTMLElement} node - The current node
     */
    /**
     * Returns the previous and current node of the specified iterator
     * @param {NodeIterator} itr - The iterator
     * @return {DOMIterator~getIteratorNodeReturn}
     * @access protected
     */;
    _proto.getIteratorNode = function getIteratorNode(itr) {
      var prevNode = itr.previousNode();
      var node;
      if (prevNode === null) {
        node = itr.nextNode();
      } else {
        node = itr.nextNode() && itr.nextNode();
      }
      return {
        prevNode: prevNode,
        node: node
      };
    }

    /**
     * An array containing objects. The object key "val" contains an iframe
     * DOM element. The object key "handled" contains a boolean indicating if
     * the iframe was handled already.
     * It wouldn't be enough to save all open or all already handled iframes.
     * The information of open iframes is necessary because they may occur after
     * all other text nodes (and compareNodeIframe would never be true). The
     * information of already handled iframes is necessary as otherwise they may
     * be handled multiple times
     * @typedef DOMIterator~checkIframeFilterIfr
     * @type {object[]}
     */
    /**
     * Checks if an iframe wasn't handled already and if so, calls
     * {@link DOMIterator#compareNodeIframe} to check if it should be handled.
     * Information wheter an iframe was or wasn't handled is given within the
     * <code>ifr</code> dictionary
     * @param {HTMLElement} node - The node that should occur after the iframe
     * @param {HTMLElement} prevNode - The node that should occur before the
     * iframe
     * @param {HTMLElement} currIfr - The iframe to check
     * @param {DOMIterator~checkIframeFilterIfr} ifr - The iframe dictionary.
     * Will be manipulated (by reference)
     * @return {boolean} Returns true when it should be handled, otherwise false
     * @access protected
     */;
    _proto.checkIframeFilter = function checkIframeFilter(node, prevNode, currIfr, ifr) {
      var key = false,
        // false === doesn't exist
        handled = false;
      ifr.forEach(function (ifrDict, i) {
        if (ifrDict.val === currIfr) {
          key = i;
          handled = ifrDict.handled;
        }
      });
      if (this.compareNodeIframe(node, prevNode, currIfr)) {
        if (key === false && !handled) {
          ifr.push({
            val: currIfr,
            handled: true
          });
        } else if (key !== false && !handled) {
          ifr[key].handled = true;
        }
        return true;
      }
      if (key === false) {
        ifr.push({
          val: currIfr,
          handled: false
        });
      }
      return false;
    }

    /**
     * Creates an iterator on all open iframes in the specified array and calls
     * the end callback when finished
     * @param {DOMIterator~checkIframeFilterIfr} ifr
     * @param {DOMIterator~whatToShow} whatToShow
     * @param  {DOMIterator~forEachNodeCallback} eCb - Each callback
     * @param {DOMIterator~filterCb} fCb
     * @access protected
     */;
    _proto.handleOpenIframes = function handleOpenIframes(ifr, whatToShow, eCb, fCb) {
      var _this4 = this;
      ifr.forEach(function (ifrDict) {
        if (!ifrDict.handled) {
          _this4.getIframeContents(ifrDict.val, function (con) {
            _this4.createInstanceOnIframe(con).forEachNode(whatToShow, eCb, fCb);
          });
        }
      });
    }

    /**
     * Iterates through all nodes in the specified context and handles iframe
     * nodes at the correct position
     * @param {DOMIterator~whatToShow} whatToShow
     * @param {HTMLElement} ctx - The context
     * @param  {DOMIterator~forEachNodeCallback} eachCb - Each callback
     * @param {DOMIterator~filterCb} filterCb - Filter callback
     * @param {DOMIterator~forEachNodeEndCallback} doneCb - End callback
     * @access protected
     */;
    _proto.iterateThroughNodes = function iterateThroughNodes(whatToShow, ctx, eachCb, filterCb, doneCb) {
      var _this5 = this;
      var itr = this.createIterator(ctx, whatToShow, filterCb);
      var ifr = [],
        elements = [],
        node,
        prevNode,
        retrieveNodes = function retrieveNodes() {
          var _this5$getIteratorNod = _this5.getIteratorNode(itr);
          prevNode = _this5$getIteratorNod.prevNode;
          node = _this5$getIteratorNod.node;
          return node;
        };
      while (retrieveNodes()) {
        if (this.iframes) {
          this.forEachIframe(ctx, function (currIfr) {
            // note that ifr will be manipulated here
            return _this5.checkIframeFilter(node, prevNode, currIfr, ifr);
          }, function (con) {
            _this5.createInstanceOnIframe(con).forEachNode(whatToShow, function (ifrNode) {
              return elements.push(ifrNode);
            }, filterCb);
          });
        }
        // it's faster to call the each callback in an array loop
        // than in this while loop
        elements.push(node);
      }
      elements.forEach(function (node) {
        eachCb(node);
      });
      if (this.iframes) {
        this.handleOpenIframes(ifr, whatToShow, eachCb, filterCb);
      }
      doneCb();
    }

    /**
     * Callback for each node
     * @callback DOMIterator~forEachNodeCallback
     * @param {HTMLElement} node - The DOM text node element
     */
    /**
     * Callback if all contexts were handled
     * @callback DOMIterator~forEachNodeEndCallback
     */
    /**
     * Iterates over all contexts and initializes
     * {@link DOMIterator#iterateThroughNodes iterateThroughNodes} on them
     * @param {DOMIterator~whatToShow} whatToShow
     * @param  {DOMIterator~forEachNodeCallback} each - Each callback
     * @param {DOMIterator~filterCb} filter - Filter callback
     * @param {DOMIterator~forEachNodeEndCallback} done - End callback
     * @access public
     */;
    _proto.forEachNode = function forEachNode(whatToShow, each, filter, done) {
      var _this6 = this;
      if (done === void 0) {
        done = function done() {};
      }
      var contexts = this.getContexts();
      var open = contexts.length;
      if (!open) {
        done();
      }
      contexts.forEach(function (ctx) {
        var ready = function ready() {
          _this6.iterateThroughNodes(whatToShow, ctx, each, filter, function () {
            if (--open <= 0) {
              // call end all contexts were handled
              done();
            }
          });
        };
        // wait for iframes to avoid recursive calls, otherwise this would
        // perhaps reach the recursive function call limit with many nodes
        if (_this6.iframes) {
          _this6.waitForIframes(ctx, ready);
        } else {
          ready();
        }
      });
    }

    /**
     * Callback to filter nodes. Can return e.g. NodeFilter.FILTER_ACCEPT or
     * NodeFilter.FILTER_REJECT
     * @see {@link http://tinyurl.com/zdczmm2}
     * @callback DOMIterator~filterCb
     * @param {HTMLElement} node - The node to filter
     */
    /**
     * @typedef DOMIterator~whatToShow
     * @see {@link http://tinyurl.com/zfqqkx2}
     * @type {number}
     */;
    return DOMIterator;
  }();
  /**
   * Marks search terms in DOM elements
   * @example
   * new Mark(document.querySelector(".context")).mark("lorem ipsum");
   * @example
   * new Mark(document.querySelector(".context")).markRegExp(/lorem/gmi);
   */
  var Mark$1 = /*#__PURE__*/function () {
    // eslint-disable-line no-unused-vars

    /**
     * @param {HTMLElement|HTMLElement[]|NodeList|string} ctx - The context DOM
     * element, an array of DOM elements, a NodeList or a selector
     */
    function Mark$1(ctx) {
      /**
       * The context of the instance. Either a DOM element, an array of DOM
       * elements, a NodeList or a selector
       * @type {HTMLElement|HTMLElement[]|NodeList|string}
       * @access protected
       */
      this.ctx = ctx;
      /**
       * Specifies if the current browser is a IE (necessary for the node
       * normalization bug workaround). See {@link Mark#unwrapMatches}
       * @type {boolean}
       * @access protected
       */
      this.ie = false;
      var ua = window.navigator.userAgent;
      if (ua.indexOf('MSIE') > -1 || ua.indexOf('Trident') > -1) {
        this.ie = true;
      }
    }

    /**
     * Options defined by the user. They will be initialized from one of the
     * public methods. See {@link Mark#mark}, {@link Mark#markRegExp},
     * {@link Mark#markRanges} and {@link Mark#unmark} for option properties.
     * @type {object}
     * @param {object} [val] - An object that will be merged with defaults
     * @access protected
     */
    var _proto2 = Mark$1.prototype;
    /**
     * Logs a message if log is enabled
     * @param {string} msg - The message to log
     * @param {string} [level="debug"] - The log level, e.g. <code>warn</code>
     * <code>error</code>, <code>debug</code>
     * @access protected
     */
    _proto2.log = function log(msg, level) {
      if (level === void 0) {
        level = 'debug';
      }
      var log = this.opt.log;
      if (!this.opt.debug) {
        return;
      }
      if (typeof log === 'object' && typeof log[level] === 'function') {
        log[level]("mark.js: " + msg);
      }
    }

    /**
     * Escapes a string for usage within a regular expression
     * @param {string} str - The string to escape
     * @return {string}
     * @access protected
     */;
    _proto2.escapeStr = function escapeStr(str) {
      // eslint-disable-next-line no-useless-escape
      return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
    }

    /**
     * Creates a regular expression string to match the specified search
     * term including synonyms, diacritics and accuracy if defined
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createRegExp = function createRegExp(str) {
      if (this.opt.wildcards !== 'disabled') {
        str = this.setupWildcardsRegExp(str);
      }
      str = this.escapeStr(str);
      if (Object.keys(this.opt.synonyms).length) {
        str = this.createSynonymsRegExp(str);
      }
      if (this.opt.ignoreJoiners || this.opt.ignorePunctuation.length) {
        str = this.setupIgnoreJoinersRegExp(str);
      }
      if (this.opt.diacritics) {
        str = this.createDiacriticsRegExp(str);
      }
      str = this.createMergedBlanksRegExp(str);
      if (this.opt.ignoreJoiners || this.opt.ignorePunctuation.length) {
        str = this.createJoinersRegExp(str);
      }
      if (this.opt.wildcards !== 'disabled') {
        str = this.createWildcardsRegExp(str);
      }
      str = this.createAccuracyRegExp(str);
      return str;
    }

    /**
     * Creates a regular expression string to match the defined synonyms
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createSynonymsRegExp = function createSynonymsRegExp(str) {
      var syn = this.opt.synonyms,
        sens = this.opt.caseSensitive ? '' : 'i',
        // add replacement character placeholder before and after the
        // synonym group
        joinerPlaceholder = this.opt.ignoreJoiners || this.opt.ignorePunctuation.length ? "\0" : '';
      for (var index in syn) {
        if (syn.hasOwnProperty(index)) {
          var value = syn[index],
            k1 = this.opt.wildcards !== 'disabled' ? this.setupWildcardsRegExp(index) : this.escapeStr(index),
            k2 = this.opt.wildcards !== 'disabled' ? this.setupWildcardsRegExp(value) : this.escapeStr(value);
          if (k1 !== '' && k2 !== '') {
            str = str.replace(new RegExp("(" + this.escapeStr(k1) + "|" + this.escapeStr(k2) + ")", "gm" + sens), joinerPlaceholder + ("(" + this.processSynomyms(k1) + "|") + (this.processSynomyms(k2) + ")") + joinerPlaceholder);
          }
        }
      }
      return str;
    }

    /**
     * Setup synonyms to work with ignoreJoiners and or ignorePunctuation
     * @param {string} str - synonym key or value to process
     * @return {string} - processed synonym string
     */;
    _proto2.processSynomyms = function processSynomyms(str) {
      if (this.opt.ignoreJoiners || this.opt.ignorePunctuation.length) {
        str = this.setupIgnoreJoinersRegExp(str);
      }
      return str;
    }

    /**
     * Sets up the regular expression string to allow later insertion of
     * wildcard regular expression matches
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.setupWildcardsRegExp = function setupWildcardsRegExp(str) {
      // replace single character wildcard with unicode 0001
      str = str.replace(/(?:\\)*\?/g, function (val) {
        return val.charAt(0) === '\\' ? '?' : "\x01";
      });
      // replace multiple character wildcard with unicode 0002
      return str.replace(/(?:\\)*\*/g, function (val) {
        return val.charAt(0) === '\\' ? '*' : "\x02";
      });
    }

    /**
     * Sets up the regular expression string to allow later insertion of
     * wildcard regular expression matches
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createWildcardsRegExp = function createWildcardsRegExp(str) {
      // default to "enable" (i.e. to not include spaces)
      // "withSpaces" uses `[\\S\\s]` instead of `.` because the latter
      // does not match new line characters
      var spaces = this.opt.wildcards === 'withSpaces';
      return str
      // replace unicode 0001 with a RegExp class to match any single
      // character, or any single non-whitespace character depending
      // on the setting
      .replace(/\u0001/g, spaces ? '[\\S\\s]?' : '\\S?')
      // replace unicode 0002 with a RegExp class to match zero or
      // more characters, or zero or more non-whitespace characters
      // depending on the setting
      .replace(/\u0002/g, spaces ? '[\\S\\s]*?' : '\\S*');
    }

    /**
     * Sets up the regular expression string to allow later insertion of
     * designated characters (soft hyphens & zero width characters)
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.setupIgnoreJoinersRegExp = function setupIgnoreJoinersRegExp(str) {
      // adding a "null" unicode character as it will not be modified by the
      // other "create" regular expression functions
      return str.replace(/[^(|)\\]/g, function (val, indx, original) {
        // don't add a null after an opening "(", around a "|" or before
        // a closing "(", or between an escapement (e.g. \+)
        var nextChar = original.charAt(indx + 1);
        if (/[(|)\\]/.test(nextChar) || nextChar === '') {
          return val;
        } else {
          return val + "\0";
        }
      });
    }

    /**
     * Creates a regular expression string to allow ignoring of designated
     * characters (soft hyphens, zero width characters & punctuation) based on
     * the specified option values of <code>ignorePunctuation</code> and
     * <code>ignoreJoiners</code>
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createJoinersRegExp = function createJoinersRegExp(str) {
      var joiner = [];
      var ignorePunctuation = this.opt.ignorePunctuation;
      if (Array.isArray(ignorePunctuation) && ignorePunctuation.length) {
        joiner.push(this.escapeStr(ignorePunctuation.join('')));
      }
      if (this.opt.ignoreJoiners) {
        // u+00ad = soft hyphen
        // u+200b = zero-width space
        // u+200c = zero-width non-joiner
        // u+200d = zero-width joiner
        joiner.push("\\u00ad\\u200b\\u200c\\u200d");
      }
      return joiner.length ? str.split(/\u0000+/).join("[" + joiner.join('') + "]*") : str;
    }

    /**
     * Creates a regular expression string to match diacritics
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createDiacriticsRegExp = function createDiacriticsRegExp(str) {
      var sens = this.opt.caseSensitive ? '' : 'i',
        dct = this.opt.caseSensitive ? ['aàáảãạăằắẳẵặâầấẩẫậäåāą', 'AÀÁẢÃẠĂẰẮẲẴẶÂẦẤẨẪẬÄÅĀĄ', 'cçćč', 'CÇĆČ', 'dđď', 'DĐĎ', 'eèéẻẽẹêềếểễệëěēę', 'EÈÉẺẼẸÊỀẾỂỄỆËĚĒĘ', 'iìíỉĩịîïī', 'IÌÍỈĨỊÎÏĪ', 'lł', 'LŁ', 'nñňń', 'NÑŇŃ', 'oòóỏõọôồốổỗộơởỡớờợöøō', 'OÒÓỎÕỌÔỒỐỔỖỘƠỞỠỚỜỢÖØŌ', 'rř', 'RŘ', 'sšśșş', 'SŠŚȘŞ', 'tťțţ', 'TŤȚŢ', 'uùúủũụưừứửữựûüůū', 'UÙÚỦŨỤƯỪỨỬỮỰÛÜŮŪ', 'yýỳỷỹỵÿ', 'YÝỲỶỸỴŸ', 'zžżź', 'ZŽŻŹ'] : ['aàáảãạăằắẳẵặâầấẩẫậäåāąAÀÁẢÃẠĂẰẮẲẴẶÂẦẤẨẪẬÄÅĀĄ', 'cçćčCÇĆČ', 'dđďDĐĎ', 'eèéẻẽẹêềếểễệëěēęEÈÉẺẼẸÊỀẾỂỄỆËĚĒĘ', 'iìíỉĩịîïīIÌÍỈĨỊÎÏĪ', 'lłLŁ', 'nñňńNÑŇŃ', 'oòóỏõọôồốổỗộơởỡớờợöøōOÒÓỎÕỌÔỒỐỔỖỘƠỞỠỚỜỢÖØŌ', 'rřRŘ', 'sšśșşSŠŚȘŞ', 'tťțţTŤȚŢ', 'uùúủũụưừứửữựûüůūUÙÚỦŨỤƯỪỨỬỮỰÛÜŮŪ', 'yýỳỷỹỵÿYÝỲỶỸỴŸ', 'zžżźZŽŻŹ'];
      var handled = [];
      str.split('').forEach(function (ch) {
        dct.every(function (dct) {
          // Check if the character is inside a diacritics list
          if (dct.indexOf(ch) !== -1) {
            // Check if the related diacritics list was not
            // handled yet
            if (handled.indexOf(dct) > -1) {
              return false;
            }
            // Make sure that the character OR any other
            // character in the diacritics list will be matched
            str = str.replace(new RegExp("[" + dct + "]", "gm" + sens), "[" + dct + "]");
            handled.push(dct);
          }
          return true;
        });
      });
      return str;
    }

    /**
     * Creates a regular expression string that merges whitespace characters
     * including subsequent ones into a single pattern, one or multiple
     * whitespaces
     * @param  {string} str - The search term to be used
     * @return {string}
     * @access protected
     */;
    _proto2.createMergedBlanksRegExp = function createMergedBlanksRegExp(str) {
      return str.replace(/[\s]+/gmi, '[\\s]+');
    }

    /**
     * Creates a regular expression string to match the specified string with
     * the defined accuracy. As in the regular expression of "exactly" can be
     * a group containing a blank at the beginning, all regular expressions will
     * be created with two groups. The first group can be ignored (may contain
     * the said blank), the second contains the actual match
     * @param  {string} str - The searm term to be used
     * @return {str}
     * @access protected
     */;
    _proto2.createAccuracyRegExp = function createAccuracyRegExp(str) {
      var _this7 = this;
      var chars = '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~¡¿';
      var acc = this.opt.accuracy,
        val = typeof acc === 'string' ? acc : acc.value,
        ls = typeof acc === 'string' ? [] : acc.limiters,
        lsJoin = '';
      ls.forEach(function (limiter) {
        lsJoin += "|" + _this7.escapeStr(limiter);
      });
      switch (val) {
        case 'partially':
        default:
          return "()(" + str + ")";
        case 'complementary':
          lsJoin = '\\s' + (lsJoin ? lsJoin : this.escapeStr(chars));
          return "()([^" + lsJoin + "]*" + str + "[^" + lsJoin + "]*)";
        case 'exactly':
          return "(^|\\s" + lsJoin + ")(" + str + ")(?=$|\\s" + lsJoin + ")";
      }
    }

    /**
     * @typedef Mark~separatedKeywords
     * @type {object.<string>}
     * @property {array.<string>} keywords - The list of keywords
     * @property {number} length - The length
     */
    /**
     * Returns a list of keywords dependent on whether separate word search
     * was defined. Also it filters empty keywords
     * @param {array} sv - The array of keywords
     * @return {Mark~separatedKeywords}
     * @access protected
     */;
    _proto2.getSeparatedKeywords = function getSeparatedKeywords(sv) {
      var _this8 = this;
      var stack = [];
      sv.forEach(function (kw) {
        if (!_this8.opt.separateWordSearch) {
          if (kw.trim() && stack.indexOf(kw) === -1) {
            stack.push(kw);
          }
        } else {
          kw.split(' ').forEach(function (kwSplitted) {
            if (kwSplitted.trim() && stack.indexOf(kwSplitted) === -1) {
              stack.push(kwSplitted);
            }
          });
        }
      });
      return {
        // sort because of https://git.io/v6USg
        'keywords': stack.sort(function (a, b) {
          return b.length - a.length;
        }),
        'length': stack.length
      };
    }

    /**
     * Check if a value is a number
     * @param {number|string} value - the value to check;
     * numeric strings allowed
     * @return {boolean}
     * @access protected
     */;
    _proto2.isNumeric = function isNumeric(value) {
      // http://stackoverflow.com/a/16655847/145346
      // eslint-disable-next-line eqeqeq
      return Number(parseFloat(value)) == value;
    }

    /**
     * @typedef Mark~rangeObject
     * @type {object}
     * @property {number} start - The start position within the composite value
     * @property {number} length - The length of the string to mark within the
     * composite value.
     */
    /**
     * @typedef Mark~setOfRanges
     * @type {object[]}
     * @property {Mark~rangeObject}
     */
    /**
     * Returns a processed list of integer offset indexes that do not overlap
     * each other, and remove any string values or additional elements
     * @param {Mark~setOfRanges} array - unprocessed raw array
     * @return {Mark~setOfRanges} - processed array with any invalid entries
     * removed
     * @throws Will throw an error if an array of objects is not passed
     * @access protected
     */;
    _proto2.checkRanges = function checkRanges(array) {
      var _this9 = this;
      // start and length indexes are included in an array of objects
      // [{start: 0, length: 1}, {start: 4, length: 5}]
      // quick validity check of the first entry only
      if (!Array.isArray(array) || Object.prototype.toString.call(array[0]) !== '[object Object]') {
        this.log('markRanges() will only accept an array of objects');
        this.opt.noMatch(array);
        return [];
      }
      var stack = [];
      var last = 0;
      array
      // acending sort to ensure there is no overlap in start & end
      // offsets
      .sort(function (a, b) {
        return a.start - b.start;
      }).forEach(function (item) {
        var _this9$callNoMatchOnI = _this9.callNoMatchOnInvalidRanges(item, last),
          start = _this9$callNoMatchOnI.start,
          end = _this9$callNoMatchOnI.end,
          valid = _this9$callNoMatchOnI.valid;
        if (valid) {
          // preserve item in case there are extra key:values within
          item.start = start;
          item.length = end - start;
          stack.push(item);
          last = end;
        }
      });
      return stack;
    }

    /**
     * @typedef Mark~validObject
     * @type {object}
     * @property {number} start - The start position within the composite value
     * @property {number} end - The calculated end position within the composite
     * value.
     * @property {boolean} valid - boolean value indicating that the start and
     * calculated end range is valid
     */
    /**
      * Initial validation of ranges for markRanges. Preliminary checks are done
      * to ensure the start and length values exist and are not zero or non-
      * numeric
      * @param {Mark~rangeObject} range - the current range object
      * @param {number} last - last index of range
      * @return {Mark~validObject}
      * @access protected
      */;
    _proto2.callNoMatchOnInvalidRanges = function callNoMatchOnInvalidRanges(range, last) {
      var start,
        end,
        valid = false;
      if (range && typeof range.start !== 'undefined') {
        start = parseInt(range.start, 10);
        end = start + parseInt(range.length, 10);
        // ignore overlapping values & non-numeric entries
        if (this.isNumeric(range.start) && this.isNumeric(range.length) && end - last > 0 && end - start > 0) {
          valid = true;
        } else {
          this.log('Ignoring invalid or overlapping range: ' + ("" + JSON.stringify(range)));
          this.opt.noMatch(range);
        }
      } else {
        this.log("Ignoring invalid range: " + JSON.stringify(range));
        this.opt.noMatch(range);
      }
      return {
        start: start,
        end: end,
        valid: valid
      };
    }

    /**
     * Check valid range for markRanges. Check ranges with access to the context
     * string. Range values are double checked, lengths that extend the mark
     * beyond the string length are limitied and ranges containing only
     * whitespace are ignored
     * @param {Mark~rangeObject} range - the current range object
     * @param {number} originalLength - original length of the context string
     * @param {string} string - current content string
     * @return {Mark~validObject}
     * @access protected
     */;
    _proto2.checkWhitespaceRanges = function checkWhitespaceRanges(range, originalLength, string) {
      var end,
        valid = true,
        // the max value changes after the DOM is manipulated
        max = string.length,
        // adjust offset to account for wrapped text node
        offset = originalLength - max,
        start = parseInt(range.start, 10) - offset;
      // make sure to stop at max
      start = start > max ? max : start;
      end = start + parseInt(range.length, 10);
      if (end > max) {
        end = max;
        this.log("End range automatically set to the max value of " + max);
      }
      if (start < 0 || end - start < 0 || start > max || end > max) {
        valid = false;
        this.log("Invalid range: " + JSON.stringify(range));
        this.opt.noMatch(range);
      } else if (string.substring(start, end).replace(/\s+/g, '') === '') {
        valid = false;
        // whitespace only; even if wrapped it is not visible
        this.log('Skipping whitespace only range: ' + JSON.stringify(range));
        this.opt.noMatch(range);
      }
      return {
        start: start,
        end: end,
        valid: valid
      };
    }

    /**
     * @typedef Mark~getTextNodesDict
     * @type {object.<string>}
     * @property {string} value - The composite value of all text nodes
     * @property {object[]} nodes - An array of objects
     * @property {number} nodes.start - The start position within the composite
     * value
     * @property {number} nodes.end - The end position within the composite
     * value
     * @property {HTMLElement} nodes.node - The DOM text node element
     */
    /**
     * Callback
     * @callback Mark~getTextNodesCallback
     * @param {Mark~getTextNodesDict}
     */
    /**
     * Calls the callback with an object containing all text nodes (including
     * iframe text nodes) with start and end positions and the composite value
     * of them (string)
     * @param {Mark~getTextNodesCallback} cb - Callback
     * @access protected
     */;
    _proto2.getTextNodes = function getTextNodes(cb) {
      var _this10 = this;
      var val = '',
        nodes = [];
      this.iterator.forEachNode(NodeFilter.SHOW_TEXT, function (node) {
        nodes.push({
          start: val.length,
          end: (val += node.textContent).length,
          node: node
        });
      }, function (node) {
        if (_this10.matchesExclude(node.parentNode)) {
          return NodeFilter.FILTER_REJECT;
        } else {
          return NodeFilter.FILTER_ACCEPT;
        }
      }, function () {
        cb({
          value: val,
          nodes: nodes
        });
      });
    }

    /**
     * Checks if an element matches any of the specified exclude selectors. Also
     * it checks for elements in which no marks should be performed (e.g.
     * script and style tags) and optionally already marked elements
     * @param  {HTMLElement} el - The element to check
     * @return {boolean}
     * @access protected
     */;
    _proto2.matchesExclude = function matchesExclude(el) {
      return DOMIterator.matches(el, this.opt.exclude.concat([
      // ignores the elements itself, not their childrens (selector *)
      'script', 'style', 'title', 'head', 'html']));
    }

    /**
     * Wraps the instance element and class around matches that fit the start
     * and end positions within the node
     * @param  {HTMLElement} node - The DOM text node
     * @param  {number} start - The position where to start wrapping
     * @param  {number} end - The position where to end wrapping
     * @return {HTMLElement} Returns the splitted text node that will appear
     * after the wrapped text node
     * @access protected
     */;
    _proto2.wrapRangeInTextNode = function wrapRangeInTextNode(node, start, end) {
      var hEl = !this.opt.element ? 'mark' : this.opt.element,
        startNode = node.splitText(start),
        ret = startNode.splitText(end - start);
      var repl = document.createElement(hEl);
      repl.setAttribute('data-markjs', 'true');
      if (this.opt.className) {
        repl.setAttribute('class', this.opt.className);
      }
      repl.textContent = startNode.textContent;
      startNode.parentNode.replaceChild(repl, startNode);
      return ret;
    }

    /**
     * @typedef Mark~wrapRangeInMappedTextNodeDict
     * @type {object.<string>}
     * @property {string} value - The composite value of all text nodes
     * @property {object[]} nodes - An array of objects
     * @property {number} nodes.start - The start position within the composite
     * value
     * @property {number} nodes.end - The end position within the composite
     * value
     * @property {HTMLElement} nodes.node - The DOM text node element
     */
    /**
     * Each callback
     * @callback Mark~wrapMatchesEachCallback
     * @param {HTMLElement} node - The wrapped DOM element
     * @param {number} lastIndex - The last matching position within the
     * composite value of text nodes
     */
    /**
     * Filter callback
     * @callback Mark~wrapMatchesFilterCallback
     * @param {HTMLElement} node - The matching text node DOM element
     */
    /**
     * Determines matches by start and end positions using the text node
     * dictionary even across text nodes and calls
     * {@link Mark#wrapRangeInTextNode} to wrap them
     * @param  {Mark~wrapRangeInMappedTextNodeDict} dict - The dictionary
     * @param  {number} start - The start position of the match
     * @param  {number} end - The end position of the match
     * @param  {Mark~wrapMatchesFilterCallback} filterCb - Filter callback
     * @param  {Mark~wrapMatchesEachCallback} eachCb - Each callback
     * @access protected
     */;
    _proto2.wrapRangeInMappedTextNode = function wrapRangeInMappedTextNode(dict, start, end, filterCb, eachCb) {
      var _this11 = this;
      // iterate over all text nodes to find the one matching the positions
      dict.nodes.every(function (n, i) {
        var sibl = dict.nodes[i + 1];
        if (typeof sibl === 'undefined' || sibl.start > start) {
          if (!filterCb(n.node)) {
            return false;
          }
          // map range from dict.value to text node
          var s = start - n.start,
            e = (end > n.end ? n.end : end) - n.start,
            startStr = dict.value.substr(0, n.start),
            endStr = dict.value.substr(e + n.start);
          n.node = _this11.wrapRangeInTextNode(n.node, s, e);
          // recalculate positions to also find subsequent matches in the
          // same text node. Necessary as the text node in dict now only
          // contains the splitted part after the wrapped one
          dict.value = startStr + endStr;
          dict.nodes.forEach(function (k, j) {
            if (j >= i) {
              if (dict.nodes[j].start > 0 && j !== i) {
                dict.nodes[j].start -= e;
              }
              dict.nodes[j].end -= e;
            }
          });
          end -= e;
          eachCb(n.node.previousSibling, n.start);
          if (end > n.end) {
            start = n.end;
          } else {
            return false;
          }
        }
        return true;
      });
    }

    /**
     * Filter callback before each wrapping
     * @callback Mark~wrapMatchesFilterCallback
     * @param {string} match - The matching string
     * @param {HTMLElement} node - The text node where the match occurs
     */
    /**
     * Callback for each wrapped element
     * @callback Mark~wrapMatchesEachCallback
     * @param {HTMLElement} element - The marked DOM element
     */
    /**
     * Callback on end
     * @callback Mark~wrapMatchesEndCallback
     */
    /**
     * Wraps the instance element and class around matches within single HTML
     * elements in all contexts
     * @param {RegExp} regex - The regular expression to be searched for
     * @param {number} ignoreGroups - A number indicating the amount of RegExp
     * matching groups to ignore
     * @param {Mark~wrapMatchesFilterCallback} filterCb
     * @param {Mark~wrapMatchesEachCallback} eachCb
     * @param {Mark~wrapMatchesEndCallback} endCb
     * @access protected
     */;
    _proto2.wrapMatches = function wrapMatches(regex, ignoreGroups, filterCb, eachCb, endCb) {
      var _this12 = this;
      var matchIdx = ignoreGroups === 0 ? 0 : ignoreGroups + 1;
      this.getTextNodes(function (dict) {
        dict.nodes.forEach(function (node) {
          node = node.node;
          var match;
          while ((match = regex.exec(node.textContent)) !== null && match[matchIdx] !== '') {
            if (!filterCb(match[matchIdx], node)) {
              continue;
            }
            var pos = match.index;
            if (matchIdx !== 0) {
              for (var i = 1; i < matchIdx; i++) {
                pos += match[i].length;
              }
            }
            node = _this12.wrapRangeInTextNode(node, pos, pos + match[matchIdx].length);
            eachCb(node.previousSibling);
            // reset index of last match as the node changed and the
            // index isn't valid anymore http://tinyurl.com/htsudjd
            regex.lastIndex = 0;
          }
        });
        endCb();
      });
    }

    /**
     * Callback for each wrapped element
     * @callback Mark~wrapMatchesAcrossElementsEachCallback
     * @param {HTMLElement} element - The marked DOM element
     */
    /**
     * Filter callback before each wrapping
     * @callback Mark~wrapMatchesAcrossElementsFilterCallback
     * @param {string} match - The matching string
     * @param {HTMLElement} node - The text node where the match occurs
     */
    /**
     * Callback on end
     * @callback Mark~wrapMatchesAcrossElementsEndCallback
     */
    /**
     * Wraps the instance element and class around matches across all HTML
     * elements in all contexts
     * @param {RegExp} regex - The regular expression to be searched for
     * @param {number} ignoreGroups - A number indicating the amount of RegExp
     * matching groups to ignore
     * @param {Mark~wrapMatchesAcrossElementsFilterCallback} filterCb
     * @param {Mark~wrapMatchesAcrossElementsEachCallback} eachCb
     * @param {Mark~wrapMatchesAcrossElementsEndCallback} endCb
     * @access protected
     */;
    _proto2.wrapMatchesAcrossElements = function wrapMatchesAcrossElements(regex, ignoreGroups, filterCb, eachCb, endCb) {
      var _this13 = this;
      var matchIdx = ignoreGroups === 0 ? 0 : ignoreGroups + 1;
      this.getTextNodes(function (dict) {
        var match;
        while ((match = regex.exec(dict.value)) !== null && match[matchIdx] !== '') {
          // calculate range inside dict.value
          var start = match.index;
          if (matchIdx !== 0) {
            for (var i = 1; i < matchIdx; i++) {
              start += match[i].length;
            }
          }
          var end = start + match[matchIdx].length;
          // note that dict will be updated automatically, as it'll change
          // in the wrapping process, due to the fact that text
          // nodes will be splitted
          _this13.wrapRangeInMappedTextNode(dict, start, end, function (node) {
            return filterCb(match[matchIdx], node);
          }, function (node, lastIndex) {
            regex.lastIndex = lastIndex;
            eachCb(node);
          });
        }
        endCb();
      });
    }

    /**
     * Callback for each wrapped element
     * @callback Mark~wrapRangeFromIndexEachCallback
     * @param {HTMLElement} element - The marked DOM element
     * @param {Mark~rangeObject} range - the current range object; provided
     * start and length values will be numeric integers modified from the
     * provided original ranges.
     */
    /**
     * Filter callback before each wrapping
     * @callback Mark~wrapRangeFromIndexFilterCallback
     * @param {HTMLElement} node - The text node which includes the range
     * @param {Mark~rangeObject} range - the current range object
     * @param {string} match - string extracted from the matching range
     * @param {number} counter - A counter indicating the number of all marks
     */
    /**
     * Callback on end
     * @callback Mark~wrapRangeFromIndexEndCallback
     */
    /**
     * Wraps the indicated ranges across all HTML elements in all contexts
     * @param {Mark~setOfRanges} ranges
     * @param {Mark~wrapRangeFromIndexFilterCallback} filterCb
     * @param {Mark~wrapRangeFromIndexEachCallback} eachCb
     * @param {Mark~wrapRangeFromIndexEndCallback} endCb
     * @access protected
     */;
    _proto2.wrapRangeFromIndex = function wrapRangeFromIndex(ranges, filterCb, eachCb, endCb) {
      var _this14 = this;
      this.getTextNodes(function (dict) {
        var originalLength = dict.value.length;
        ranges.forEach(function (range, counter) {
          var _this14$checkWhitespa = _this14.checkWhitespaceRanges(range, originalLength, dict.value),
            start = _this14$checkWhitespa.start,
            end = _this14$checkWhitespa.end,
            valid = _this14$checkWhitespa.valid;
          if (valid) {
            _this14.wrapRangeInMappedTextNode(dict, start, end, function (node) {
              return filterCb(node, range, dict.value.substring(start, end), counter);
            }, function (node) {
              eachCb(node, range);
            });
          }
        });
        endCb();
      });
    }

    /**
     * Unwraps the specified DOM node with its content (text nodes or HTML)
     * without destroying possibly present events (using innerHTML) and
     * normalizes the parent at the end (merge splitted text nodes)
     * @param  {HTMLElement} node - The DOM node to unwrap
     * @access protected
     */;
    _proto2.unwrapMatches = function unwrapMatches(node) {
      var parent = node.parentNode;
      var docFrag = document.createDocumentFragment();
      while (node.firstChild) {
        docFrag.appendChild(node.removeChild(node.firstChild));
      }
      parent.replaceChild(docFrag, node);
      if (!this.ie) {
        // use browser's normalize method
        parent.normalize();
      } else {
        // custom method (needs more time)
        this.normalizeTextNode(parent);
      }
    }

    /**
     * Normalizes text nodes. It's a workaround for the native normalize method
     * that has a bug in IE (see attached link). Should only be used in IE
     * browsers as it's slower than the native method.
     * @see {@link http://tinyurl.com/z5asa8c}
     * @param {HTMLElement} node - The DOM node to normalize
     * @access protected
     */;
    _proto2.normalizeTextNode = function normalizeTextNode(node) {
      if (!node) {
        return;
      }
      if (node.nodeType === 3) {
        while (node.nextSibling && node.nextSibling.nodeType === 3) {
          node.nodeValue += node.nextSibling.nodeValue;
          node.parentNode.removeChild(node.nextSibling);
        }
      } else {
        this.normalizeTextNode(node.firstChild);
      }
      this.normalizeTextNode(node.nextSibling);
    }

    /**
     * Callback when finished
     * @callback Mark~commonDoneCallback
     * @param {number} totalMatches - The number of marked elements
     */
    /**
     * @typedef Mark~commonOptions
     * @type {object.<string>}
     * @property {string} [element="mark"] - HTML element tag name
     * @property {string} [className] - An optional class name
     * @property {string[]} [exclude] - An array with exclusion selectors.
     * Elements matching those selectors will be ignored
     * @property {boolean} [iframes=false] - Whether to search inside iframes
     * @property {Mark~commonDoneCallback} [done]
     * @property {boolean} [debug=false] - Wheter to log messages
     * @property {object} [log=window.console] - Where to log messages (only if
     * debug is true)
     */
    /**
     * Callback for each marked element
     * @callback Mark~markRegExpEachCallback
     * @param {HTMLElement} element - The marked DOM element
     */
    /**
     * Callback if there were no matches
     * @callback Mark~markRegExpNoMatchCallback
     * @param {RegExp} regexp - The regular expression
     */
    /**
     * Callback to filter matches
     * @callback Mark~markRegExpFilterCallback
     * @param {HTMLElement} textNode - The text node which includes the match
     * @param {string} match - The matching string for the RegExp
     * @param {number} counter - A counter indicating the number of all marks
     */
    /**
     * These options also include the common options from
     * {@link Mark~commonOptions}
     * @typedef Mark~markRegExpOptions
     * @type {object.<string>}
     * @property {Mark~markRegExpEachCallback} [each]
     * @property {Mark~markRegExpNoMatchCallback} [noMatch]
     * @property {Mark~markRegExpFilterCallback} [filter]
     */
    /**
     * Marks a custom regular expression
     * @param  {RegExp} regexp - The regular expression
     * @param  {Mark~markRegExpOptions} [opt] - Optional options object
     * @access public
     */;
    _proto2.markRegExp = function markRegExp(regexp, opt) {
      var _this15 = this;
      this.opt = opt;
      this.log("Searching with expression \"" + regexp + "\"");
      var totalMatches = 0,
        fn = 'wrapMatches';
      var eachCb = function eachCb(element) {
        totalMatches++;
        _this15.opt.each(element);
      };
      if (this.opt.acrossElements) {
        fn = 'wrapMatchesAcrossElements';
      }
      this[fn](regexp, this.opt.ignoreGroups, function (match, node) {
        return _this15.opt.filter(node, match, totalMatches);
      }, eachCb, function () {
        if (totalMatches === 0) {
          _this15.opt.noMatch(regexp);
        }
        _this15.opt.done(totalMatches);
      });
    }

    /**
     * Callback for each marked element
     * @callback Mark~markEachCallback
     * @param {HTMLElement} element - The marked DOM element
     */
    /**
     * Callback if there were no matches
     * @callback Mark~markNoMatchCallback
     * @param {RegExp} term - The search term that was not found
     */
    /**
     * Callback to filter matches
     * @callback Mark~markFilterCallback
     * @param {HTMLElement} textNode - The text node which includes the match
     * @param {string} match - The matching term
     * @param {number} totalCounter - A counter indicating the number of all
     * marks
     * @param {number} termCounter - A counter indicating the number of marks
     * for the specific match
     */
    /**
     * @typedef Mark~markAccuracyObject
     * @type {object.<string>}
     * @property {string} value - A accuracy string value
     * @property {string[]} limiters - A custom array of limiters. For example
     * <code>["-", ","]</code>
     */
    /**
     * @typedef Mark~markAccuracySetting
     * @type {string}
     * @property {"partially"|"complementary"|"exactly"|Mark~markAccuracyObject}
     * [accuracy="partially"] - Either one of the following string values:
     * <ul>
     *   <li><i>partially</i>: When searching for "lor" only "lor" inside
     *   "lorem" will be marked</li>
     *   <li><i>complementary</i>: When searching for "lor" the whole word
     *   "lorem" will be marked</li>
     *   <li><i>exactly</i>: When searching for "lor" only those exact words
     *   will be marked. In this example nothing inside "lorem". This value
     *   is equivalent to the previous option <i>wordBoundary</i></li>
     * </ul>
     * Or an object containing two properties:
     * <ul>
     *   <li><i>value</i>: One of the above named string values</li>
     *   <li><i>limiters</i>: A custom array of string limiters for accuracy
     *   "exactly" or "complementary"</li>
     * </ul>
     */
    /**
     * @typedef Mark~markWildcardsSetting
     * @type {string}
     * @property {"disabled"|"enabled"|"withSpaces"}
     * [wildcards="disabled"] - Set to any of the following string values:
     * <ul>
     *   <li><i>disabled</i>: Disable wildcard usage</li>
     *   <li><i>enabled</i>: When searching for "lor?m", the "?" will match zero
     *   or one non-space character (e.g. "lorm", "loram", "lor3m", etc). When
     *   searching for "lor*m", the "*" will match zero or more non-space
     *   characters (e.g. "lorm", "loram", "lor123m", etc).</li>
     *   <li><i>withSpaces</i>: When searching for "lor?m", the "?" will
     *   match zero or one space or non-space character (e.g. "lor m", "loram",
     *   etc). When searching for "lor*m", the "*" will match zero or more space
     *   or non-space characters (e.g. "lorm", "lore et dolor ipsum", "lor: m",
     *   etc).</li>
     * </ul>
     */
    /**
     * @typedef Mark~markIgnorePunctuationSetting
     * @type {string[]}
     * @property {string} The strings in this setting will contain punctuation
     * marks that will be ignored:
     * <ul>
     *   <li>These punctuation marks can be between any characters, e.g. setting
     *   this option to <code>["'"]</code> would match "Worlds", "World's" and
     *   "Wo'rlds"</li>
     *   <li>One or more apostrophes between the letters would still produce a
     *   match (e.g. "W'o''r'l'd's").</li>
     *   <li>A typical setting for this option could be as follows:
     *   <pre>ignorePunctuation: ":;.,-–—‒_(){}[]!'\"+=".split(""),</pre> This
     *   setting includes common punctuation as well as a minus, en-dash,
     *   em-dash and figure-dash
     *   ({@link https://en.wikipedia.org/wiki/Dash#Figure_dash ref}), as well
     *   as an underscore.</li>
     * </ul>
     */
    /**
     * These options also include the common options from
     * {@link Mark~commonOptions}
     * @typedef Mark~markOptions
     * @type {object.<string>}
     * @property {boolean} [separateWordSearch=true] - Whether to search for
     * each word separated by a blank instead of the complete term
     * @property {boolean} [diacritics=true] - If diacritic characters should be
     * matched. ({@link https://en.wikipedia.org/wiki/Diacritic Diacritics})
     * @property {object} [synonyms] - An object with synonyms. The key will be
     * a synonym for the value and the value for the key
     * @property {Mark~markAccuracySetting} [accuracy]
     * @property {Mark~markWildcardsSetting} [wildcards]
     * @property {boolean} [acrossElements=false] - Whether to find matches
     * across HTML elements. By default, only matches within single HTML
     * elements will be found
     * @property {boolean} [ignoreJoiners=false] - Whether to ignore word
     * joiners inside of key words. These include soft-hyphens, zero-width
     * space, zero-width non-joiners and zero-width joiners.
     * @property {Mark~markIgnorePunctuationSetting} [ignorePunctuation]
     * @property {Mark~markEachCallback} [each]
     * @property {Mark~markNoMatchCallback} [noMatch]
     * @property {Mark~markFilterCallback} [filter]
     */
    /**
     * Marks the specified search terms
     * @param {string|string[]} [sv] - Search value, either a search string or
     * an array containing multiple search strings
     * @param  {Mark~markOptions} [opt] - Optional options object
     * @access public
     */;
    _proto2.mark = function mark(sv, opt) {
      var _this16 = this;
      this.opt = opt;
      var totalMatches = 0,
        fn = 'wrapMatches';
      var _this$getSeparatedKey = this.getSeparatedKeywords(typeof sv === 'string' ? [sv] : sv),
        kwArr = _this$getSeparatedKey.keywords,
        kwArrLen = _this$getSeparatedKey.length,
        sens = this.opt.caseSensitive ? '' : 'i',
        handler = function handler(kw) {
          // async function calls as iframes are async too
          var regex = new RegExp(_this16.createRegExp(kw), "gm" + sens),
            matches = 0;
          _this16.log("Searching with expression \"" + regex + "\"");
          _this16[fn](regex, 1, function (term, node) {
            return _this16.opt.filter(node, kw, totalMatches, matches);
          }, function (element) {
            matches++;
            totalMatches++;
            _this16.opt.each(element);
          }, function () {
            if (matches === 0) {
              _this16.opt.noMatch(kw);
            }
            if (kwArr[kwArrLen - 1] === kw) {
              _this16.opt.done(totalMatches);
            } else {
              handler(kwArr[kwArr.indexOf(kw) + 1]);
            }
          });
        };
      if (this.opt.acrossElements) {
        fn = 'wrapMatchesAcrossElements';
      }
      if (kwArrLen === 0) {
        this.opt.done(totalMatches);
      } else {
        handler(kwArr[0]);
      }
    }

    /**
     * Callback for each marked element
     * @callback Mark~markRangesEachCallback
     * @param {HTMLElement} element - The marked DOM element
     * @param {array} range - array of range start and end points
     */
    /**
     * Callback if a processed range is invalid, out-of-bounds, overlaps another
     * range, or only matches whitespace
     * @callback Mark~markRangesNoMatchCallback
     * @param {Mark~rangeObject} range - a range object
     */
    /**
     * Callback to filter matches
     * @callback Mark~markRangesFilterCallback
     * @param {HTMLElement} node - The text node which includes the range
     * @param {array} range - array of range start and end points
     * @param {string} match - string extracted from the matching range
     * @param {number} counter - A counter indicating the number of all marks
     */
    /**
     * These options also include the common options from
     * {@link Mark~commonOptions}
     * @typedef Mark~markRangesOptions
     * @type {object.<string>}
     * @property {Mark~markRangesEachCallback} [each]
     * @property {Mark~markRangesNoMatchCallback} [noMatch]
     * @property {Mark~markRangesFilterCallback} [filter]
     */
    /**
     * Marks an array of objects containing a start with an end or length of the
     * string to mark
     * @param  {Mark~setOfRanges} rawRanges - The original (preprocessed)
     * array of objects
     * @param  {Mark~markRangesOptions} [opt] - Optional options object
     * @access public
     */;
    _proto2.markRanges = function markRanges(rawRanges, opt) {
      var _this17 = this;
      this.opt = opt;
      var totalMatches = 0,
        ranges = this.checkRanges(rawRanges);
      if (ranges && ranges.length) {
        this.log('Starting to mark with the following ranges: ' + JSON.stringify(ranges));
        this.wrapRangeFromIndex(ranges, function (node, range, match, counter) {
          return _this17.opt.filter(node, range, match, counter);
        }, function (element, range) {
          totalMatches++;
          _this17.opt.each(element, range);
        }, function () {
          _this17.opt.done(totalMatches);
        });
      } else {
        this.opt.done(totalMatches);
      }
    }

    /**
     * Removes all marked elements inside the context with their HTML and
     * normalizes the parent at the end
     * @param  {Mark~commonOptions} [opt] - Optional options object
     * @access public
     */;
    _proto2.unmark = function unmark(opt) {
      var _this18 = this;
      this.opt = opt;
      var sel = this.opt.element ? this.opt.element : '*';
      sel += '[data-markjs]';
      if (this.opt.className) {
        sel += "." + this.opt.className;
      }
      this.log("Removal selector \"" + sel + "\"");
      this.iterator.forEachNode(NodeFilter.SHOW_ELEMENT, function (node) {
        _this18.unwrapMatches(node);
      }, function (node) {
        var matchesSel = DOMIterator.matches(node, sel),
          matchesExclude = _this18.matchesExclude(node);
        if (!matchesSel || matchesExclude) {
          return NodeFilter.FILTER_REJECT;
        } else {
          return NodeFilter.FILTER_ACCEPT;
        }
      }, this.opt.done);
    };
    _createClass(Mark$1, [{
      key: "opt",
      get: function get() {
        return this._opt;
      }

      /**
       * An instance of DOMIterator
       * @type {DOMIterator}
       * @access protected
       */,
      set: function set(val) {
        this._opt = Object.assign({}, {
          'element': '',
          'className': '',
          'exclude': [],
          'iframes': false,
          'iframesTimeout': 5000,
          'separateWordSearch': true,
          'diacritics': true,
          'synonyms': {},
          'accuracy': 'partially',
          'acrossElements': false,
          'caseSensitive': false,
          'ignoreJoiners': false,
          'ignoreGroups': 0,
          'ignorePunctuation': [],
          'wildcards': 'disabled',
          'each': function each() {},
          'noMatch': function noMatch() {},
          'filter': function filter() {
            return true;
          },
          'done': function done() {},
          'debug': false,
          'log': window.console
        }, val);
      }
    }, {
      key: "iterator",
      get: function get() {
        // always return new instance in case there were option changes
        return new DOMIterator(this.ctx, this.opt.iframes, this.opt.exclude, this.opt.iframesTimeout);
      }
    }]);
    return Mark$1;
  }();
  function Mark(ctx) {
    var _this19 = this;
    var instance = new Mark$1(ctx);
    this.mark = function (sv, opt) {
      instance.mark(sv, opt);
      return _this19;
    };
    this.markRegExp = function (sv, opt) {
      instance.markRegExp(sv, opt);
      return _this19;
    };
    this.markRanges = function (sv, opt) {
      instance.markRanges(sv, opt);
      return _this19;
    };
    this.unmark = function (opt) {
      instance.unmark(opt);
      return _this19;
    };
    return this;
  }

  // mark.js defaults
  var defaultOptions = {
    exclude: [],
    separateWordSearch: true,
    accuracy: 'partially',
    diacritics: true,
    synonyms: {},
    iframes: false,
    iframesTimeout: 5000,
    acrossElements: true,
    caseSensitive: false,
    ignoreJoiners: false,
    wildcards: 'disabled',
    compatibility: false
  };
  if (Joomla.getOptions && typeof Joomla.getOptions === 'function' && Joomla.getOptions('highlight')) {
    var scriptOptions = Joomla.getOptions('highlight');
    scriptOptions.forEach(function (currentOpts) {
      var options = Object.assign({}, defaultOptions, currentOpts);

      // Continue only if the element exists
      if (!options.compatibility) {
        var element = document.querySelector("." + options.class);
        if (element) {
          var instance = new Mark(element);

          // Loop through the terms
          options.highLight.forEach(function (term) {
            instance.mark(term, options);
          });
        }
      } else {
        var start = document.querySelector("#" + options.start);
        document.querySelector("#" + options.end);
        var parent = start.parentNode;
        var targetNodes = [];
        var allElems = Array.from(parent.childNodes);

        // Remove all elements till start element
        allElems.forEach(function (element) {
          {
            return;
          }
        });
        targetNodes.forEach(function (node) {
          var instance = new Mark(node);
          // Loop through the terms
          options.highLight.map(function (term) {
            return instance.mark(term, options);
          });
        });
      }
    });
  }

})();
