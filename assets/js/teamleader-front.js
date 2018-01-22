/* eslint-disable no-undef,no-console */
/**
 * global document, $
 */
class Front {
  constructor(options) {
    this.url = options.url;
    this.nonce = options.nonce;
    this.container = options.container;
    this.form = this.container.find('form');
    this.success = this.container.find('[data-success]');
    this.error = this.container.find('[data-error]');
    this.timeout = 10000;

    try {
      this.init();
    } catch (e) {
      console.log(`Error:${e}`);
    }
  }
  send() {
    const data = `action=teamleader&nonce=${this.nonce}&${this.form.serialize()}`;
    let hasError = false;

    this.form.find('input').each((i, el) => {
      const input = jQuery(el);

      if (input.prop('required') && input.val().length === 0) {
        input.addClass('invalid');
        hasError = true;
      } else {
        input.removeClass('invalid');
      }
    });

    if (hasError) {
      return;
    }

    jQuery.ajax({
      method: 'POST',
      url: this.url,
      data,
      success: (result) => {
        const response = jQuery.parseJSON(result);

        if (response.success === true) {
          this.form.hide();
          this.error.hide();
          this.success.fadeIn();

          setTimeout(() => {
            this.success.fadeOut();
            this.form.fadeIn();
          }, this.timeout);

          return;
        }

        this.form.hide();
        this.success.hide();
        this.error.fadeIn();

        if (response.message) {
          this.error.html(response.message);
        }

        setTimeout(() => {
          this.error.fadeOut();
          this.form.fadeIn();
        }, this.timeout);
      },
    });
  }
  init() {
    this.form.submit((e) => {
      e.preventDefault();
      this.send();
    });
  }
}

const TeamleaderFront = options => new Front(options);
