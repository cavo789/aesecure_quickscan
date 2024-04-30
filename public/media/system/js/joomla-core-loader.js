(customElements => {
  'strict';

  /**
   * Creates a custom element with the default spinner of the Joomla logo
   */
  class JoomlaCoreLoader extends HTMLElement {
    constructor() {
      super();
      const template = document.createElement('template');
      template.innerHTML = `<style>:host{-webkit-box-align:center;-ms-flex-align:center;align-items:center;display:-webkit-box;display:-ms-flexbox;display:flex;height:100%;left:0;opacity:.8;overflow:hidden;position:fixed;top:0;width:100%;z-index:10000}.box{height:345px;margin:0 auto;position:relative;width:345px}.box p{color:#999;float:right;font:normal 1.25em/1em sans-serif;margin:95px 0 0}.box>span{-webkit-animation:jspinner 2s ease-in-out infinite;animation:jspinner 2s ease-in-out infinite}.box .red{-webkit-animation-delay:-1.5s;animation-delay:-1.5s}.box .blue{-webkit-animation-delay:-1s;animation-delay:-1s}.box .green{-webkit-animation-delay:-.5s;animation-delay:-.5s}.yellow{background:#f9a541;border-radius:90px;height:90px;width:90px}.yellow,.yellow:after,.yellow:before{content:"";left:0;position:absolute;top:0}.yellow:after,.yellow:before{background:transparent;border:50px solid #f9a541;-webkit-box-sizing:content-box;box-sizing:content-box;width:50px}.yellow:before{border-radius:75px 75px 0 0;border-width:50px 50px 0;height:35px;margin:60px 0 0 -30px}.yellow:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.red{background:#f44321;border-radius:90px;height:90px;width:90px}.red,.red:after,.red:before{content:"";left:0;position:absolute;top:0}.red:after,.red:before{background:transparent;border:50px solid #f44321;-webkit-box-sizing:content-box;box-sizing:content-box;width:50px}.red:before{border-radius:75px 75px 0 0;border-width:50px 50px 0;height:35px;margin:60px 0 0 -30px}.red:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.blue{background:#5091cd;border-radius:90px;height:90px;width:90px}.blue,.blue:after,.blue:before{content:"";left:0;position:absolute;top:0}.blue:after,.blue:before{background:transparent;border:50px solid #5091cd;-webkit-box-sizing:content-box;box-sizing:content-box;width:50px}.blue:before{border-radius:75px 75px 0 0;border-width:50px 50px 0;height:35px;margin:60px 0 0 -30px}.blue:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.green{background:#7ac143;border-radius:90px;height:90px;width:90px}.green,.green:after,.green:before{content:"";left:0;position:absolute;top:0}.green:after,.green:before{background:transparent;border:50px solid #7ac143;-webkit-box-sizing:content-box;box-sizing:content-box;width:50px}.green:before{border-radius:75px 75px 0 0;border-width:50px 50px 0;height:35px;margin:60px 0 0 -30px}.green:after{border-width:0 0 0 50px;height:105px;margin:140px 0 0 -30px}.yellow{margin:0 0 0 255px;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.red{margin:255px 0 0 255px;-webkit-transform:rotate(135deg);transform:rotate(135deg)}.blue{margin:255px 0 0;-webkit-transform:rotate(225deg);transform:rotate(225deg)}.green{-webkit-transform:rotate(315deg);transform:rotate(315deg)}@-webkit-keyframes jspinner{0%,40%,to{opacity:.3}20%{opacity:1}}@keyframes jspinner{0%,40%,to{opacity:.3}20%{opacity:1}}@media (prefers-reduced-motion:reduce){.box>span{-webkit-animation:none;animation:none}}</style>
<div>
    <span class="yellow"></span>
    <span class="red"></span>
    <span class="blue"></span>
    <span class="green"></span>
    <p>&trade;</p>
</div>`;

      // Patch the shadow DOM
      if (window.ShadyCSS) {
        window.ShadyCSS.prepareTemplate(template, 'joomla-core-loader');
      }
      this.attachShadow({
        mode: 'open'
      });
      this.shadowRoot.appendChild(template.content.cloneNode(true));

      // Patch the shadow DOM
      if (window.ShadyCSS) {
        window.ShadyCSS.styleElement(this);
      }
    }
    connectedCallback() {
      this.style.backgroundColor = this.color;
      this.style.opacity = 0.8;
      this.shadowRoot.querySelector('div').classList.add('box');
    }
    static get observedAttributes() {
      return ['color'];
    }
    get color() {
      return this.getAttribute('color') || '#fff';
    }
    set color(value) {
      this.setAttribute('color', value);
    }
    attributeChangedCallback(attr, oldValue, newValue) {
      switch (attr) {
        case 'color':
          if (newValue && newValue !== oldValue) {
            this.style.backgroundColor = this.color;
          }
          break;
        // Do nothing
      }
    }
  }

  if (!customElements.get('joomla-core-loader')) {
    customElements.define('joomla-core-loader', JoomlaCoreLoader);
  }
})(customElements);
