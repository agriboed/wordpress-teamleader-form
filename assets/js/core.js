/**
 * global document, $
 */
class App {
  /**
   *
   */
  constructor() {
    this.key = 'teamleader';
    this.url = window.TeamLeader.url;
    this.nonce = window.TeamLeader.nonce;
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
    this.bindActive().
        bindCreateForm().
        bindReCaptcha().
        bindLogo().
        bindSaveOptions();
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
    let el = this.getElement('[data-action=logo]');
    let container = this.getElement('[data-container=logo]');

    if (el.attr('checked')) {
      container.show();
    }

    el.click(() => {
      container.toggle();
    });

    return this;
  }

  /**
   * @return App
   */
  bindReCaptcha() {
    let el = this.getElement('[data-action=recaptcha]');
    let container = this.getElement('[data-container=recaptcha]');

    if (el.attr('checked')) {
      container.show();
    }

    el.click(() => {
      container.toggle('normal');
    });

    return this;
  }

  /**
   * @return App
   */
  bindCreateForm() {
    let button = this.getElement('[data-action=create]');

    button.click((e) => {
      e.preventDefault();
      button.hide();
      this.createForm();
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
    form.hide();

    this.forms.push(form);

    this.bindSaveNewForm(form.find('[data-action=save]'), form);
    this.bindDiscardNewForm(form.find('[data-action=discard]'));

    this.getElement('[data-container=create]').html(form);
    form.fadeIn();
  }

  /**
   *
   * @param el
   * @param form
   */
  bindSaveNewForm(el, form) {
    let data = form.serializeArray();

    el.onclick(() => {
      this.getElement('[data-container=create]').html('');
      this.getElement('[data-action=create]').fadeIn('normal');

      this.showMessage('Form added');
    });
  }

  /**
   *
   * @param el
   */
  bindDiscardNewForm(el) {
    el.click(() => {
      this.getElement('[data-container=create]').html('');
      this.getElement('[data-action=create]').fadeIn('normal');
    });
  }

  /**
   *
   */
  bindSaveOptions() {
    let button = this.getElement('[data-action=save-options]');

    button.click((e) => {
      e.preventDefault();

      let form = button.closest('form');
      let data = {
        action: 'teamleader_options',
        nonce: this.nonce,
      };

      jQuery(form.serializeArray()).each(function(i, el) {
        data[el.name] = el.value;
      });

      jQuery.ajax({
        url: this.url,
        method: 'post',
        data: data,
        dataType: 'json',
        success: (response) => {
          this.showMessage(response.message);
        },
        error: (response) => {
          console.log(response);
          this.showMessage('Server error. Please, try again');
        },
      });
    });
  }

  /**
   *
   * @param message
   * @param success
   */
  showMessage(message = '', success) {
    let container = this.getElement('[data-action=message]');
    container.html(message);

    container.removeClass('success error');

    if (success === true) {
      container.addClass('success');
    } else if (success === false) {
      container.addClass('error');
    }
    container.addClass('status').fadeIn('normal');

    setTimeout(() => {
      container.fadeOut('normal').html('');
    }, 5000);
  }
}

(function($) {
  $(document).ready(() => {
    new App();
  });
}(jQuery));
