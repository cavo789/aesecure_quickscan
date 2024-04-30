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

  /** Highest positive signed 32-bit float value */
  var maxInt = 2147483647; // aka. 0x7FFFFFFF or 2^31-1

  /** Bootstring parameters */
  var base = 36;
  var tMin = 1;
  var tMax = 26;
  var skew = 38;
  var damp = 700;
  var initialBias = 72;
  var initialN = 128; // 0x80
  var delimiter = '-'; // '\x2D'

  /** Regular expressions */
  var regexPunycode = /^xn--/;
  var regexNonASCII = /[^\0-\x7F]/; // Note: U+007F DEL is excluded too.
  var regexSeparators = /[\x2E\u3002\uFF0E\uFF61]/g; // RFC 3490 separators

  /** Error messages */
  var errors = {
    'overflow': 'Overflow: input needs wider integers to process',
    'not-basic': 'Illegal input >= 0x80 (not a basic code point)',
    'invalid-input': 'Invalid input'
  };

  /** Convenience shortcuts */
  var baseMinusTMin = base - tMin;
  var floor = Math.floor;
  var stringFromCharCode = String.fromCharCode;

  /*--------------------------------------------------------------------------*/

  /**
   * A generic error utility function.
   * @private
   * @param {String} type The error type.
   * @returns {Error} Throws a `RangeError` with the applicable error message.
   */
  function error(type) {
    throw new RangeError(errors[type]);
  }

  /**
   * A generic `Array#map` utility function.
   * @private
   * @param {Array} array The array to iterate over.
   * @param {Function} callback The function that gets called for every array
   * item.
   * @returns {Array} A new array of values returned by the callback function.
   */
  function map(array, callback) {
    var result = [];
    var length = array.length;
    while (length--) {
      result[length] = callback(array[length]);
    }
    return result;
  }

  /**
   * A simple `Array#map`-like wrapper to work with domain name strings or email
   * addresses.
   * @private
   * @param {String} domain The domain name or email address.
   * @param {Function} callback The function that gets called for every
   * character.
   * @returns {String} A new string of characters returned by the callback
   * function.
   */
  function mapDomain(domain, callback) {
    var parts = domain.split('@');
    var result = '';
    if (parts.length > 1) {
      // In email addresses, only the domain name should be punycoded. Leave
      // the local part (i.e. everything up to `@`) intact.
      result = parts[0] + '@';
      domain = parts[1];
    }
    // Avoid `split(regex)` for IE8 compatibility. See #17.
    domain = domain.replace(regexSeparators, '\x2E');
    var labels = domain.split('.');
    var encoded = map(labels, callback).join('.');
    return result + encoded;
  }

  /**
   * Creates an array containing the numeric code points of each Unicode
   * character in the string. While JavaScript uses UCS-2 internally,
   * this function will convert a pair of surrogate halves (each of which
   * UCS-2 exposes as separate characters) into a single code point,
   * matching UTF-16.
   * @see `punycode.ucs2.encode`
   * @see <https://mathiasbynens.be/notes/javascript-encoding>
   * @memberOf punycode.ucs2
   * @name decode
   * @param {String} string The Unicode input string (UCS-2).
   * @returns {Array} The new array of code points.
   */
  function ucs2decode(string) {
    var output = [];
    var counter = 0;
    var length = string.length;
    while (counter < length) {
      var value = string.charCodeAt(counter++);
      if (value >= 0xD800 && value <= 0xDBFF && counter < length) {
        // It's a high surrogate, and there is a next character.
        var extra = string.charCodeAt(counter++);
        if ((extra & 0xFC00) == 0xDC00) {
          // Low surrogate.
          output.push(((value & 0x3FF) << 10) + (extra & 0x3FF) + 0x10000);
        } else {
          // It's an unmatched surrogate; only append this code unit, in case the
          // next code unit is the high surrogate of a surrogate pair.
          output.push(value);
          counter--;
        }
      } else {
        output.push(value);
      }
    }
    return output;
  }

  /**
   * Creates a string based on an array of numeric code points.
   * @see `punycode.ucs2.decode`
   * @memberOf punycode.ucs2
   * @name encode
   * @param {Array} codePoints The array of numeric code points.
   * @returns {String} The new Unicode string (UCS-2).
   */
  var ucs2encode = function ucs2encode(codePoints) {
    return String.fromCodePoint.apply(String, codePoints);
  };

  /**
   * Converts a basic code point into a digit/integer.
   * @see `digitToBasic()`
   * @private
   * @param {Number} codePoint The basic numeric code point value.
   * @returns {Number} The numeric value of a basic code point (for use in
   * representing integers) in the range `0` to `base - 1`, or `base` if
   * the code point does not represent a value.
   */
  var basicToDigit = function basicToDigit(codePoint) {
    if (codePoint >= 0x30 && codePoint < 0x3A) {
      return 26 + (codePoint - 0x30);
    }
    if (codePoint >= 0x41 && codePoint < 0x5B) {
      return codePoint - 0x41;
    }
    if (codePoint >= 0x61 && codePoint < 0x7B) {
      return codePoint - 0x61;
    }
    return base;
  };

  /**
   * Converts a digit/integer into a basic code point.
   * @see `basicToDigit()`
   * @private
   * @param {Number} digit The numeric value of a basic code point.
   * @returns {Number} The basic code point whose value (when used for
   * representing integers) is `digit`, which needs to be in the range
   * `0` to `base - 1`. If `flag` is non-zero, the uppercase form is
   * used; else, the lowercase form is used. The behavior is undefined
   * if `flag` is non-zero and `digit` has no uppercase form.
   */
  var digitToBasic = function digitToBasic(digit, flag) {
    //  0..25 map to ASCII a..z or A..Z
    // 26..35 map to ASCII 0..9
    return digit + 22 + 75 * (digit < 26) - ((flag != 0) << 5);
  };

  /**
   * Bias adaptation function as per section 3.4 of RFC 3492.
   * https://tools.ietf.org/html/rfc3492#section-3.4
   * @private
   */
  var adapt = function adapt(delta, numPoints, firstTime) {
    var k = 0;
    delta = firstTime ? floor(delta / damp) : delta >> 1;
    delta += floor(delta / numPoints);
    for /* no initialization */
    (; delta > baseMinusTMin * tMax >> 1; k += base) {
      delta = floor(delta / baseMinusTMin);
    }
    return floor(k + (baseMinusTMin + 1) * delta / (delta + skew));
  };

  /**
   * Converts a Punycode string of ASCII-only symbols to a string of Unicode
   * symbols.
   * @memberOf punycode
   * @param {String} input The Punycode string of ASCII-only symbols.
   * @returns {String} The resulting string of Unicode symbols.
   */
  var decode = function decode(input) {
    // Don't use UCS-2.
    var output = [];
    var inputLength = input.length;
    var i = 0;
    var n = initialN;
    var bias = initialBias;

    // Handle the basic code points: let `basic` be the number of input code
    // points before the last delimiter, or `0` if there is none, then copy
    // the first basic code points to the output.

    var basic = input.lastIndexOf(delimiter);
    if (basic < 0) {
      basic = 0;
    }
    for (var j = 0; j < basic; ++j) {
      // if it's not a basic code point
      if (input.charCodeAt(j) >= 0x80) {
        error('not-basic');
      }
      output.push(input.charCodeAt(j));
    }

    // Main decoding loop: start just after the last delimiter if any basic code
    // points were copied; start at the beginning otherwise.

    for /* no final expression */
    (var index = basic > 0 ? basic + 1 : 0; index < inputLength;) {
      // `index` is the index of the next character to be consumed.
      // Decode a generalized variable-length integer into `delta`,
      // which gets added to `i`. The overflow checking is easier
      // if we increase `i` as we go, then subtract off its starting
      // value at the end to obtain `delta`.
      var oldi = i;
      for /* no condition */
      (var w = 1, k = base;; k += base) {
        if (index >= inputLength) {
          error('invalid-input');
        }
        var digit = basicToDigit(input.charCodeAt(index++));
        if (digit >= base) {
          error('invalid-input');
        }
        if (digit > floor((maxInt - i) / w)) {
          error('overflow');
        }
        i += digit * w;
        var t = k <= bias ? tMin : k >= bias + tMax ? tMax : k - bias;
        if (digit < t) {
          break;
        }
        var baseMinusT = base - t;
        if (w > floor(maxInt / baseMinusT)) {
          error('overflow');
        }
        w *= baseMinusT;
      }
      var out = output.length + 1;
      bias = adapt(i - oldi, out, oldi == 0);

      // `i` was supposed to wrap around from `out` to `0`,
      // incrementing `n` each time, so we'll fix that now:
      if (floor(i / out) > maxInt - n) {
        error('overflow');
      }
      n += floor(i / out);
      i %= out;

      // Insert `n` at position `i` of the output.
      output.splice(i++, 0, n);
    }
    return String.fromCodePoint.apply(String, output);
  };

  /**
   * Converts a string of Unicode symbols (e.g. a domain name label) to a
   * Punycode string of ASCII-only symbols.
   * @memberOf punycode
   * @param {String} input The string of Unicode symbols.
   * @returns {String} The resulting Punycode string of ASCII-only symbols.
   */
  var encode = function encode(input) {
    var output = [];

    // Convert the input in UCS-2 to an array of Unicode code points.
    input = ucs2decode(input);

    // Cache the length.
    var inputLength = input.length;

    // Initialize the state.
    var n = initialN;
    var delta = 0;
    var bias = initialBias;

    // Handle the basic code points.
    for (var _iterator = _createForOfIteratorHelperLoose(input), _step; !(_step = _iterator()).done;) {
      var _currentValue2 = _step.value;
      if (_currentValue2 < 0x80) {
        output.push(stringFromCharCode(_currentValue2));
      }
    }
    var basicLength = output.length;
    var handledCPCount = basicLength;

    // `handledCPCount` is the number of code points that have been handled;
    // `basicLength` is the number of basic code points.

    // Finish the basic string with a delimiter unless it's empty.
    if (basicLength) {
      output.push(delimiter);
    }

    // Main encoding loop:
    while (handledCPCount < inputLength) {
      // All non-basic code points < n have been handled already. Find the next
      // larger one:
      var m = maxInt;
      for (var _iterator2 = _createForOfIteratorHelperLoose(input), _step2; !(_step2 = _iterator2()).done;) {
        var currentValue = _step2.value;
        if (currentValue >= n && currentValue < m) {
          m = currentValue;
        }
      }

      // Increase `delta` enough to advance the decoder's <n,i> state to <m,0>,
      // but guard against overflow.
      var handledCPCountPlusOne = handledCPCount + 1;
      if (m - n > floor((maxInt - delta) / handledCPCountPlusOne)) {
        error('overflow');
      }
      delta += (m - n) * handledCPCountPlusOne;
      n = m;
      for (var _iterator3 = _createForOfIteratorHelperLoose(input), _step3; !(_step3 = _iterator3()).done;) {
        var _currentValue = _step3.value;
        if (_currentValue < n && ++delta > maxInt) {
          error('overflow');
        }
        if (_currentValue === n) {
          // Represent delta as a generalized variable-length integer.
          var q = delta;
          for /* no condition */
          (var k = base;; k += base) {
            var t = k <= bias ? tMin : k >= bias + tMax ? tMax : k - bias;
            if (q < t) {
              break;
            }
            var qMinusT = q - t;
            var baseMinusT = base - t;
            output.push(stringFromCharCode(digitToBasic(t + qMinusT % baseMinusT, 0)));
            q = floor(qMinusT / baseMinusT);
          }
          output.push(stringFromCharCode(digitToBasic(q, 0)));
          bias = adapt(delta, handledCPCountPlusOne, handledCPCount === basicLength);
          delta = 0;
          ++handledCPCount;
        }
      }
      ++delta;
      ++n;
    }
    return output.join('');
  };

  /**
   * Converts a Punycode string representing a domain name or an email address
   * to Unicode. Only the Punycoded parts of the input will be converted, i.e.
   * it doesn't matter if you call it on a string that has already been
   * converted to Unicode.
   * @memberOf punycode
   * @param {String} input The Punycoded domain name or email address to
   * convert to Unicode.
   * @returns {String} The Unicode representation of the given Punycode
   * string.
   */
  var toUnicode = function toUnicode(input) {
    return mapDomain(input, function (string) {
      return regexPunycode.test(string) ? decode(string.slice(4).toLowerCase()) : string;
    });
  };

  /**
   * Converts a Unicode string representing a domain name or an email address to
   * Punycode. Only the non-ASCII parts of the domain name will be converted,
   * i.e. it doesn't matter if you call it with a domain that's already in
   * ASCII.
   * @memberOf punycode
   * @param {String} input The domain name or email address to convert, as a
   * Unicode string.
   * @returns {String} The Punycode representation of the given domain name or
   * email address.
   */
  var toASCII = function toASCII(input) {
    return mapDomain(input, function (string) {
      return regexNonASCII.test(string) ? 'xn--' + encode(string) : string;
    });
  };

  /*--------------------------------------------------------------------------*/

  /** Define the public API */
  var punycode = {
    /**
     * A string representing the current Punycode.js version number.
     * @memberOf punycode
     * @type String
     */
    'version': '2.1.0',
    /**
     * An object of methods to convert from JavaScript's internal character
     * representation (UCS-2) to Unicode code points, and back.
     * @see <https://mathiasbynens.be/notes/javascript-encoding>
     * @memberOf punycode
     * @type Object
     */
    'ucs2': {
      'decode': ucs2decode,
      'encode': ucs2encode
    },
    'decode': decode,
    'encode': encode,
    'toASCII': toASCII,
    'toUnicode': toUnicode
  };

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  var JFormValidator = /*#__PURE__*/function () {
    function JFormValidator() {
      var _this = this;
      this.customValidators = {};
      this.handlers = [];
      this.handlers = {};
      this.removeMarking = this.removeMarking.bind(this);
      this.inputEmail = function () {
        var input = document.createElement('input');
        input.setAttribute('type', 'email');
        return input.type !== 'text';
      };

      // Default handlers
      this.setHandler('username', function (value) {
        var regex = /[<|>|"|'|%|;|(|)|&]/i;
        return !regex.test(value);
      });
      this.setHandler('password', function (value) {
        var regex = /^\S[\S ]{2,98}\S$/;
        return regex.test(value);
      });
      this.setHandler('numeric', function (value) {
        var regex = /^(\d|-)?(\d|,)*\.?\d*$/;
        return regex.test(value);
      });
      this.setHandler('email', function (value) {
        var newValue = punycode.toASCII(value);
        var regex = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        return regex.test(newValue);
      });

      // Attach all forms with a class 'form-validate'
      var forms = [].slice.call(document.querySelectorAll('form'));
      forms.forEach(function (form) {
        if (form.classList.contains('form-validate')) {
          _this.attachToForm(form);
        }
      });
    }
    var _proto = JFormValidator.prototype;
    _proto.setHandler = function setHandler(name, func, en) {
      var isEnabled = en === '' ? true : en;
      this.handlers[name] = {
        enabled: isEnabled,
        exec: func
      };
    }

    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.markValid = function markValid(element) {
      // Get a label
      var label = element.form.querySelector("label[for=\"" + element.id + "\"]");
      var message;
      if (element.classList.contains('required') || element.getAttribute('required')) {
        if (label) {
          message = label.querySelector('span.form-control-feedback');
        }
      }
      element.classList.remove('form-control-danger');
      element.classList.remove('invalid');
      element.classList.add('form-control-success');
      element.parentNode.classList.remove('has-danger');
      element.parentNode.classList.add('has-success');
      element.setAttribute('aria-invalid', 'false');

      // Remove message
      if (message) {
        message.parentNode.removeChild(message);
      }

      // Restore Label
      if (label) {
        label.classList.remove('invalid');
      }
    }

    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.markInvalid = function markInvalid(element, empty) {
      // Get a label
      var label = element.form.querySelector("label[for=\"" + element.id + "\"]");
      element.classList.remove('form-control-success');
      element.classList.remove('valid');
      element.classList.add('form-control-danger');
      element.classList.add('invalid');
      element.parentNode.classList.remove('has-success');
      element.parentNode.classList.add('has-danger');
      element.setAttribute('aria-invalid', 'true');

      // Display custom message
      var mesgCont;
      var message = element.getAttribute('data-validation-text');
      if (label) {
        mesgCont = label.querySelector('span.form-control-feedback');
      }
      if (!mesgCont) {
        var elMsg = document.createElement('span');
        elMsg.classList.add('form-control-feedback');
        if (empty && empty === 'checkbox') {
          elMsg.innerHTML = message !== null ? Joomla.sanitizeHtml(message) : Joomla.sanitizeHtml(Joomla.Text._('JLIB_FORM_FIELD_REQUIRED_CHECK'));
        } else if (empty && empty === 'value') {
          elMsg.innerHTML = message !== null ? Joomla.sanitizeHtml(message) : Joomla.sanitizeHtml(Joomla.Text._('JLIB_FORM_FIELD_REQUIRED_VALUE'));
        } else {
          elMsg.innerHTML = message !== null ? Joomla.sanitizeHtml(message) : Joomla.sanitizeHtml(Joomla.Text._('JLIB_FORM_FIELD_INVALID_VALUE'));
        }
        if (label) {
          label.appendChild(elMsg);
        }
      }

      // Mark the Label as well
      if (label) {
        label.classList.add('invalid');
      }
    }

    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.removeMarking = function removeMarking(element) {
      // Get the associated label
      var message;
      var label = element.form.querySelector("label[for=\"" + element.id + "\"]");
      if (label) {
        message = label.querySelector('span.form-control-feedback');
      }
      element.classList.remove('form-control-danger');
      element.classList.remove('form-control-success');
      element.classList.remove('invalid');
      element.classList.add('valid');
      element.parentNode.classList.remove('has-danger');
      element.parentNode.classList.remove('has-success');

      // Remove message
      if (message) {
        if (label) {
          label.removeChild(message);
        }
      }

      // Restore Label
      if (label) {
        label.classList.remove('invalid');
      }
    };
    _proto.handleResponse = function handleResponse(state, element, empty) {
      var tagName = element.tagName.toLowerCase();

      // Set the element and its label (if exists) invalid state
      if (tagName !== 'button' && element.value !== undefined || tagName === 'fieldset') {
        if (state === false) {
          this.markInvalid(element, empty);
        } else {
          this.markValid(element);
        }
      }
    };
    _proto.validate = function validate(element) {
      var tagName;

      // Ignore the element if its currently disabled,
      // because are not submitted for the http-request.
      // For those case return always true.
      if (element.getAttribute('disabled') === 'disabled' || element.getAttribute('display') === 'none') {
        this.handleResponse(true, element);
        return true;
      }
      // If the field is required make sure it has a value
      if (element.getAttribute('required') || element.classList.contains('required')) {
        tagName = element.tagName.toLowerCase();
        if (tagName === 'fieldset' && (element.classList.contains('radio') || element.classList.contains('checkboxes'))) {
          // No options are checked.
          if (element.querySelector('input:checked') === null) {
            this.handleResponse(false, element, 'checkbox');
            return false;
          }
        } else if (element.getAttribute('type') === 'checkbox' && element.checked !== true || tagName === 'select' && !element.value.length) {
          this.handleResponse(false, element, 'checkbox');
          return false;
        } else if (!element.value || element.classList.contains('placeholder')) {
          // If element has class placeholder that means it is empty.
          this.handleResponse(false, element, 'value');
          return false;
        }
      }

      // Only validate the field if the validate class is set
      var handler = element.getAttribute('class') && element.getAttribute('class').match(/validate-([a-zA-Z0-9_-]+)/) ? element.getAttribute('class').match(/validate-([a-zA-Z0-9_-]+)/)[1] : '';
      if (element.getAttribute('pattern') && element.getAttribute('pattern') !== '') {
        if (element.value.length) {
          var isValid = new RegExp("^" + element.getAttribute('pattern') + "$").test(element.value);
          this.handleResponse(isValid, element, 'empty');
          return isValid;
        }
        if (element.hasAttribute('required') || element.classList.contains('required')) {
          this.handleResponse(false, element, 'empty');
          return false;
        }
        this.handleResponse(true, element);
        return true;
      }
      if (handler === '') {
        this.handleResponse(true, element);
        return true;
      }

      // Check the additional validation types
      if (handler && handler !== 'none' && this.handlers[handler] && element.value) {
        // Execute the validation handler and return result
        if (this.handlers[handler].exec(element.value, element) !== true) {
          this.handleResponse(false, element, 'invalid_value');
          return false;
        }
      }

      // Return validation state
      this.handleResponse(true, element);
      return true;
    };
    _proto.isValid = function isValid(form) {
      var _this2 = this;
      var valid = true;
      var message;
      var error;
      var fields;
      var invalid = [];

      // Validate form fields
      if (form.nodeName === 'FORM') {
        fields = [].slice.call(form.elements);
      } else {
        fields = [].slice.call(form.querySelectorAll('input, textarea, select, button, fieldset'));
      }
      fields.forEach(function (field) {
        if (_this2.validate(field) === false) {
          valid = false;
          invalid.push(field);
        }
      });

      // Run custom form validators if present
      if (Object.keys(this.customValidators).length) {
        Object.keys(this.customValidators).foreach(function (key) {
          if (_this2.customValidators[key].exec() !== true) {
            valid = false;
          }
        });
      }
      if (!valid && invalid.length > 0) {
        if (form.getAttribute('data-validation-text')) {
          message = form.getAttribute('data-validation-text');
        } else {
          message = Joomla.Text._('JLIB_FORM_CONTAINS_INVALID_FIELDS');
        }
        error = {
          error: [message]
        };
        Joomla.renderMessages(error);
      }
      return valid;
    };
    _proto.attachToForm = function attachToForm(form) {
      var _this3 = this;
      var elements;
      if (form.nodeName === 'FORM') {
        elements = [].slice.call(form.elements);
      } else {
        elements = [].slice.call(form.querySelectorAll('input, textarea, select, button, fieldset'));
      }

      // Iterate through the form object and attach the validate method to all input fields.
      elements.forEach(function (element) {
        var tagName = element.tagName.toLowerCase();
        if (['input', 'textarea', 'select', 'fieldset'].indexOf(tagName) > -1 && element.classList.contains('required')) {
          element.setAttribute('required', '');
        }

        // Attach isValid method to submit button
        if ((tagName === 'input' || tagName === 'button') && (element.getAttribute('type') === 'submit' || element.getAttribute('type') === 'image')) {
          if (element.classList.contains('validate')) {
            element.addEventListener('click', function () {
              return _this3.isValid(form);
            });
          }
        } else if (tagName !== 'button' && !(tagName === 'input' && element.getAttribute('type') === 'button')) {
          // Attach validate method only to fields
          if (tagName !== 'fieldset') {
            element.addEventListener('blur', function (_ref) {
              var target = _ref.target;
              return _this3.validate(target);
            });
            element.addEventListener('focus', function (_ref2) {
              var target = _ref2.target;
              return _this3.removeMarking(target);
            });
            if (element.classList.contains('validate-email') && _this3.inputEmail) {
              element.setAttribute('type', 'email');
            }
          }
        }
      });
    };
    _createClass(JFormValidator, [{
      key: "custom",
      get: function get() {
        return this.customValidators;
      },
      set: function set(value) {
        this.customValidators = value;
      }
    }]);
    return JFormValidator;
  }();
  var initialize = function initialize() {
    document.formvalidator = new JFormValidator();

    // Cleanup
    document.removeEventListener('DOMContentLoaded', initialize);
  };
  document.addEventListener('DOMContentLoaded', initialize);

})();
