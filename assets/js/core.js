/**
 * global document, $
 */
class App {
  /**
   *
   */
  constructor() {
    this.key = 'teamleader';
    this.container = jQuery('#teamleader');
    this.forms = [];
    //  try {
    this.init();
    // } catch (e) {
    //    console.log('Error:' + e);
    //   }
  }

  /**
   * @param selector
   * @return object
   */
  getElement(selector) {
    return this.container.find(selector);
  }

  /**
   * @return App
   */
  init() {
    this.bindActive().bindCreateForm().bindReCaptcha().bindLogo();
    return this;
  }

  /**
   * @return App
   */
  bindActive() {
    let active = this.getElement('.active input[type=checkbox]');
    active.click(function() {
      if (active.attr('checked')) {
        active.closest('.field').removeClass('disabled');
      } else {
        active.closest('.field').addClass('disabled');
      }
    });
    return this;
  }

  /**
   * @return App
   */
  bindLogo() {
    let logo = this.getElement('#teamleader_logo');
    let container = this.getElement('.teamleader_logo-container');
    logo.click(() => {
      container.toggle();
    });
    return this;
  }

  /**
   * @return App
   */
  bindReCaptcha() {
    let reCaptcha = this.getElement('#teamleader_recaptcha');
    let container = this.getElement('.teamleader_recaptcha-container');
    reCaptcha.click(() => {
      container.toggle();
    });
    return this;
  }

  /**
   * @return App
   */
  bindCreateForm() {
    let button = this.getElement('.create-form');
    let self = this;

    button.click((e) => {
      e.preventDefault();
      self.createForm();
    });
    return this;
  }

  /**
   *
   */
  createForm() {
    let form = jQuery(document.createElement('div'));
    let template = this.getElement('#template').html();
    form.html(template);
    this.forms.push(form);
    form.hide();
    this.getElement('.create-form-container').append(form);
    form.fadeIn();
  }
}

(function($) {
  $(document).ready(() => {
    new App();
  });
}(jQuery));
