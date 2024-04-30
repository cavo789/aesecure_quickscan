(function () {
  'use strict';

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

  (function (customElements, Joomla) {
    var JoomlaFieldSendTestMail = /*#__PURE__*/function (_HTMLElement) {
      _inheritsLoose(JoomlaFieldSendTestMail, _HTMLElement);
      // attributeChangedCallback(attr, oldValue, newValue) {}
      function JoomlaFieldSendTestMail() {
        var _this;
        _this = _HTMLElement.call(this) || this;
        if (!Joomla) {
          throw new Error('Joomla API is not properly initiated');
        }
        if (!_this.getAttribute('uri')) {
          throw new Error('No valid url for validation');
        }
        return _this;
      }
      var _proto = JoomlaFieldSendTestMail.prototype;
      _proto.connectedCallback = function connectedCallback() {
        var self = this;
        var button = document.getElementById('sendtestmail');
        if (button) {
          button.addEventListener('click', function () {
            self.sendTestMail(self);
          });
        }
      };
      _proto.sendTestMail = function sendTestMail() {
        var emailData = {
          smtpauth: document.getElementById('jform_smtpauth1').checked ? 1 : 0,
          smtpuser: this.querySelector('[name="jform[smtpuser]"]').value,
          smtphost: this.querySelector('[name="jform[smtphost]"]').value,
          smtpsecure: this.querySelector('[name="jform[smtpsecure]"]').value,
          smtpport: this.querySelector('[name="jform[smtpport]"]').value,
          mailfrom: this.querySelector('[name="jform[mailfrom]"]').value,
          fromname: this.querySelector('[name="jform[fromname]"]').value,
          mailer: this.querySelector('[name="jform[mailer]"]').value,
          mailonline: document.getElementById('jform_mailonline1').checked ? 1 : 0
        };
        var smtppass = this.querySelector('[name="jform[smtppass]"]');
        if (smtppass.disabled === false) {
          emailData.smtppass = smtppass.value;
        }

        // Remove js messages, if they exist.
        Joomla.removeMessages();
        Joomla.request({
          url: this.getAttribute('uri'),
          method: 'POST',
          data: JSON.stringify(emailData),
          perform: true,
          headers: {
            'Content-Type': 'application/json'
          },
          onSuccess: function onSuccess(resp) {
            var response;
            try {
              response = JSON.parse(resp);
            } catch (e) {
              // eslint-disable-next-line no-console
              console.error(e);
            }
            if (typeof response.messages === 'object' && response.messages !== null) {
              Joomla.renderMessages(response.messages);
            }
            document.body.scrollIntoView({
              behavior: 'smooth'
            });
          },
          onError: function onError(xhr) {
            Joomla.renderMessages(Joomla.ajaxErrorsMessages(xhr));
            document.body.scrollIntoView({
              behavior: 'smooth'
            });
          }
        });
      };
      return JoomlaFieldSendTestMail;
    }( /*#__PURE__*/_wrapNativeSuper(HTMLElement));
    customElements.define('joomla-field-send-test-mail', JoomlaFieldSendTestMail);
  })(customElements, Joomla);

})();
