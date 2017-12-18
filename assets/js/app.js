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
      .bindDeleteForm()
      .bindEditForm()
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
    form.find('[data-element=titleEdit]').hide();
    form.find('[data-action=saveForm]').hide();

    this.forms.push(form);

    this.getElement('[data-container=create]').html(form).fadeIn();

    this.bindSaveNewForm(form.find('[data-action=addForm]'));
    this.bindDiscardNewForm(form.find('[data-action=discardForm]'));
    this.bindActivateField(form.find('[data-action=activateField]'));
    this.bindRequiredField(form.find('[data-action=requiredField]'));
    this.bindHiddenField(form.find('[data-action=hiddenField]'));

    form.fadeIn();
  }

  /**
   *
   */
  editForm(id) {
    const form = jQuery(document.createElement('div'));
    const template = this.getElement('#template').html();

    form.html(template);
    form.hide();
    form.find('[data-element=titleCreate]').hide();
    form.find('[data-action=addForm]').hide();

    jQuery.ajax({
      url: this.url,
      method: 'post',
      data: {
        action: 'teamleader_get',
        nonce: this.nonce,
        id,
      },
      dataType: 'json',
      success: (response) => {
        if ((response.success !== true || !response.form) && response.message) {
          return this.showMessage(response.message);
        }

        this.fillForm(form, response.form);
        return true;
      },
      error: () => {
        this.showMessage('Server error. Please, try again');
      },
    });

    this.getElement('[data-container=edit]').html(form).fadeIn();

    this.bindSaveForm(form.find('[data-action=saveForm]'), id);
    this.bindDiscardNewForm(form.find('[data-action=discardForm]'));
    this.bindActivateField(form.find('[data-action=activateField]'));
    this.bindRequiredField(form.find('[data-action=requiredField]'));
    this.bindHiddenField(form.find('[data-action=hiddenField]'));

    form.fadeIn();
  }

  /**
   * @param form
   * @param data
   */
  fillForm(form, data) {
    const fields = form.find('.tl__field');

    if (!data.form) {
      return;
    }

    if (data.form.title) {
      form.find('[data-element=formTitle]').val(data.form.title);
    }

    if (data.form.submit) {
      form.find('[data-element=formSubmit]').val(data.form.submit);
    }

    if (data.form.success) {
      form.find('[data-element=formSuccess]').val(data.form.success);
    }

    jQuery.each(fields, (i, el) => {
      const field = jQuery(el);
      const key = field.data('param');
      const fieldData = data[key];

      if (fieldData) {
        if (fieldData.active) {
          field.removeClass('tl__disabled');
          field.find('[data-element=active]').attr('checked', 'checked');
          field.find('input').removeAttr('disabled');
          field.find('textarea').removeAttr('disabled');
        }

        if (fieldData.label) {
          field.find('[data-element=label]').val(fieldData.label);
        }

        if (fieldData.default) {
          field.find('[data-element=default]').val(fieldData.default);
        }

        if (fieldData.required) {
          field.find('.tl__hidden').addClass('tl__disabled');
          field.find('[data-element=requiredTrue]').attr('checked', 'checked');
        }

        if (fieldData.hidden) {
          field.find('.tl__required').addClass('tl__disabled');
          field.find('[data-element=hiddenTrue]').attr('checked', 'checked');
        }
      }
    });
  }

  /**
   *
   * @param form
   * @returns {{}}
   */
  getFormData(form) {
    const unindexedArray = form.serializeArray();
    const indexedArray = {};

    jQuery.map(unindexedArray, (n) => {
      indexedArray[n.name] = n.value;
    });

    return indexedArray;
  }

  /**
   *
   * @param el
   * @param id
   */
  bindSaveForm(el, id) {
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
      data.action = 'teamleader_save';
      data.nonce = this.nonce;
      data.id = id;

      const success = () => {
        this.getElement('[data-container=edit]').fadeOut();
        this.showMessage('Form saved');
        this.getElement(`[data-element=form${id}]`)
          .find('[data-element=formTitle]').text(title.val());
      };

      jQuery.ajax({
        url: this.url,
        method: 'post',
        data,
        dataType: 'json',
        success: (response) => {
          this.showMessage(response.message);
          if (response.success === true) {
            success();
          }
        },
        error: () => {
          this.showMessage('Server error. Please, try again');
        },
      });
    });
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

      const success = (id) => {
        this.getElement('[data-container=create]').fadeOut();
        this.getElement('[data-action=createForm]').fadeIn('normal');
        this.showMessage('Form added');

        this.displayNewForm(id, title.val());
      };

      jQuery.ajax({
        url: this.url,
        method: 'post',
        data,
        dataType: 'json',
        success: (response) => {
          this.showMessage(response.message);
          if (response.success === true) {
            success(response.id);
          }
        },
        error: () => {
          this.showMessage('Server error. Please, try again');
        },
      });
    });
  }

  /**
   *
   * @param id
   * @param title
   */
  displayNewForm(id, title) {
    const form = jQuery(document.createElement('div'));
    const template = this.getElement('#templateForm').html();

    form.html(template);
    form.hide();

    form.find('[data-element=title]').html(title);
    form.find('[data-element=id]').html(id);
    form.find('[data-action=editForm]').data('param', id);
    form.find('[data-action=deleteForm]').data('param', id);

    this.forms.push(form);

    this.getElement('[data-container=forms]').append(form);
    form.fadeIn('normal');
    this.bindDeleteForm();
    this.bindEditForm();
  }

  /**
   *
   * @param el
   */
  bindDiscardNewForm(el) {
    el.click(() => {
      this.getElement('[data-container=create]').fadeOut();
    });
  }

  /**
   *
   * @param el
   */
  bindDiscardForm(el) {
    el.click(() => {
      this.getElement('[data-container=edit]').fadeOut();
    });
  }

  /**
   *
   */
  bindSaveOptions() {
    const button = this.getElement('[data-action=save-options]');

    button.click((e) => {
      e.preventDefault();
      this.saveOptions(button);
    });
  }

  saveOptions(button) {
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
  }

  bindActivateField(el) {
    el.click((e) => {
      const checkbox = jQuery(e.target);
      const field = checkbox.closest('.tl__field');

      if (checkbox.attr('checked') === 'checked') {
        field.removeClass('tl__disabled');
        field.find('input').removeAttr('disabled');
        field.find('textarea').removeAttr('disabled');
      } else {
        field.addClass('tl__disabled');
        field.find('input').attr('disabled', 'disabled');
        field.find('textarea').attr('disabled', 'disabled');
      }
    });
  }

  /**
   *
   * @param el
   */
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

  /**
   *
   * @param el
   */
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
   * @returns {App}
   */
  bindDeleteForm() {
    const buttons = this.getElement('[data-action=deleteForm]');
    buttons.unbind();
    buttons.click((e) => {
      const button = jQuery(e.target);
      this.deleteForm(button);
    });
    return this;
  }

  /**
   *
   * @param button
   */
  deleteForm(button) {
    jQuery.ajax({
      url: this.url,
      method: 'post',
      data: {
        action: 'teamleader_delete',
        nonce: this.nonce,
        id: button.data('param'),
      },
      dataType: 'json',
      success: (response) => {
        this.showMessage(response.message);

        if (response.success === true) {
          button.closest('[data-element=form]').fadeOut('normal');
        }
      },
      error: (response) => {
        console.log(response);
        this.showMessage('Server error. Please, try again');
      },
    });
  }

  bindEditForm() {
    const buttons = this.getElement('[data-action=editForm]');

    buttons.click((e) => {
      const button = jQuery(e.target);
      this.editForm(button.data('param'));
    });

    return this;
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

const TeamLeaderAdmin = (options) => {
  new App(options);
};
