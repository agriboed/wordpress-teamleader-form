/* eslint-disable no-undef,no-console */
/**
 * global document, $
 */
class App {
  /**
   *
   */
  constructor(options) {
    this.key = options.key;
    this.url = options.url;
    this.nonce = options.nonce;
    this.container = options.container;
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
    this.bindActive()
      .bindCreateForm()
      .bindReCaptcha()
      .bindLogo()
      .bindSaveOptions();
    return this;
  }

  /**
   * @return App
   */
  bindActive() {
    const el = this.getElement('.active input[type=checkbox]');
    el.click(() => {
      if (el.attr('checked')) {
        el.closest('.field').removeClass('disabled');
      } else {
        el.closest('.field').addClass('disabled');
      }
    });
    return this;
  }

  /**
   * @return App
   */
  bindLogo() {
    const el = this.getElement('[data-action=logo]');
    const container = this.getElement('[data-container=logo]');

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
    const el = this.getElement('[data-action=recaptcha]');
    const container = this.getElement('[data-container=recaptcha]');

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
    const button = this.getElement('[data-action=createForm]');

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
    const form = jQuery(document.createElement('div'));
    const template = this.getElement('#template').html();

    form.html(template);
    form.hide();

    this.forms.push(form);

    this.getElement('[data-container=create]').html(form);

    this.bindSaveNewForm(form.find('[data-action=addForm]'));
    this.bindDiscardNewForm(form.find('[data-action=discardForm]'));
    this.bindActivateField(form.find('[data-action=activateField]'));
    this.bindRequiredField(form.find('[data-action=requiredField]'));
    this.bindHiddenField(form.find('[data-action=hiddenField]'));

    form.fadeIn();
  }

  getFormData(form) {
    const unindexedArray = form.serializeArray();
    const indexedArray = {};

    jQuery.map(unindexedArray, (n, i) => {
      indexedArray[n.name] = n.value;
    });

    return indexedArray;
  }

  /**
   *
   * @param el
   */
  bindSaveNewForm(el) {
    el.click(() => {
      const form = el.closest('form');
      const title = form.find('[data-element=formTitle]');

      if (title.val() === '') {
        this.showMessage('Form title is empty', false);
        title.addClass('error');
        return;
      }

      title.removeClass('error');

      const data = this.getFormData(form);
      data.action = 'teamleader_create';
      data.nonce = this.nonce;

      jQuery.ajax({
        url: this.url,
        method: 'post',
        data,
        dataType: 'json',
        success: (response) => {
          this.showMessage(response.message);
        },
        error: () => {
          this.showMessage('Server error. Please, try again');
        },
      });

      this.getElement('[data-container=create]').html('');
      this.getElement('[data-action=createForm]').fadeIn('normal');
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
    });
  }

  /**
   *
   */
  bindSaveOptions() {
    const button = this.getElement('[data-action=save-options]');

    button.click((e) => {
      e.preventDefault();

      const form = button.closest('form');
      const data = {
        action: 'teamleader_options',
        nonce: this.nonce,
      };

      jQuery(form.serializeArray()).each((i, el) => {
        data[el.name] = el.value;
      });

      jQuery.ajax({
        url: this.url,
        method: 'post',
        data,
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

  bindActivateField(el) {
    el.click((e) => {
      const el = jQuery(e.target);
      const field = el.closest('.tl__field');

      if (el.attr('checked') === 'checked') {
        field.removeClass('tl__disabled');
        field.find('input').removeAttr('disabled');
      } else {
        field.addClass('tl__disabled');
        field.find('input').attr('disabled', 'disabled');
      }
    });
  }

  bindHiddenField(el) {
    el.click((e) => {
      const hiddenRadio = jQuery(e.target);
      const requiredContainer = hiddenRadio.closest('.tl__field')
        .find('.tl__required');
      const requiredRadio = requiredContainer.find('input');

      requiredContainer.removeClass('tl__disabled');
      requiredRadio.removeAttr('disabled');

      if (hiddenRadio.val() === '1') {
        requiredRadio.attr('disabled', 'disabled');
        requiredContainer.addClass('tl__disabled');
      }
    });
  }

  bindRequiredField(el) {
    el.click((e) => {
      const requiredRadio = jQuery(e.target);
      const hiddenContainer = requiredRadio.closest('.tl__field')
        .find('.tl__hidden');
      const hiddenRadio = hiddenContainer.find('input');

      hiddenContainer.removeClass('tl__disabled');
      hiddenRadio.removeAttr('disabled');

      if (requiredRadio.val() === '1') {
        hiddenContainer.addClass('tl__disabled');
        hiddenRadio.attr('disabled', 'disabled');
      }
    });
  }

  /**
   *
   * @param message
   * @param success
   */
  showMessage(message = '', success) {
    const container = this.getElement('[data-action=message]');
    container.html(message);

    container.removeClass('success error');

    if (success === true) {
      container.addClass('success');
    } else if (success === false) {
      container.addClass('failed');
    }
    container.addClass('status').fadeIn('normal');

    setTimeout(() => {
      container.fadeOut('normal').html('');
    }, 5000);
  }
}

// eslint-disable-next-line no-unused-vars
const TeamLeader = (options) => {
  new App(options);
};
