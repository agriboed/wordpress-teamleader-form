<div class="teamleader-container">
    <form>
        <?php foreach ($fields as $key => $field):
            ?>
            <div class="form-group">
                <label>
                    <?php if ($field['type'] !== 'hidden') {
                        echo $field['label'];
                    }

                    $required = $field['required'] ? 'required=required' : '';

                    switch ($field['type']) {
                        case 'textarea':
                            echo '<textarea name="' . $key . '" placeholder="" ' . $required . ' class="form-control">' . $field['value'] . '</textarea>';
                            break;
                        case 'number':
                            echo '<input type="number" name="' . $key . '" value="' . $field['value'] . '" placeholder="" ' . $required . ' class="form-control">';
                            break;
                        case 'boolean':
                            $checked = !empty($field['value']) ? 'checked' : '';
                            echo '<input type="checkbox" name="' . $key . '" ' . $required . ' ' . $checked . ' class="form-control">';
                            break;
                        case 'hidden':
                            echo '<input type="hidden" name="' . $key . '" value=" ' . $field['value'] . '">';
                            break;
                        default:
                            echo '<input type="text" name="' . $key . '" value="' . $field['value'] . '" placeholder="" ' . $required . ' class="form-control">';
                            break;
                    }
                    ?>
                </label>
            </div>
        <?php endforeach; ?>
        <?php if (true === $options['logo']): ?>
            <div class="teamleader-powered"><?php _e('Powered by', 'teamleader'); ?>
                <a href="http://referral.teamleader.eu/en?token=<?php echo (!empty($options['referral_token'])) ? $options['referral_token'] : 'refferal'; ?>"
                   target="_blank">Teamleader</a>
                <img src="<?php echo $logo; ?>" alt="<?php _e('Teamleader', $key); ?>"></div>
        <?php endif; ?>

        <?php if (true === $options['recaptcha']): ?>
            <button type="submit" class="btn btn-primary g-recaptcha teamleader-submit"
                    data-sitekey="<?php echo $options['recaptcha_site_key']; ?>"
                    data-callback="checkRecaptcha"
                    data-size="invisible"><?php echo $form['form']['submit']; ?></button>
        <?php else: ?>
            <button type="submit"
                    class="btn btn-primary teamleader-submit"><?php echo $form['form']['submit']; ?></button>
        <?php endif; ?>
    </form>
    <div class="teamleader-success" data-success
         style="display: none;"><?php echo addslashes($form['form']['success']); ?></div>
    <div class="teamleader-error" data-error style="display: none;"><?php _e('Error sending form. Please contact system
        administrator.', 'teamleader'); ?>
    </div>
</div>
<script>
  var container,
    form,
    success,
    error,
    submit,
    init = function () {
      container = jQuery('.teamleader-container'),
        form = container.find('form'),
        success = container.find('[data-success]'),
        error = container.find('[data-error]'),
        submit = container.find('button[type=submit]')

      form.submit(function (e) {
        e.preventDefault()
          <?php if (true !== $options['recaptcha']): ?>
        submitData()
          <?php endif;?>
      })
    },
    form_success = function () {
      form.hide()
      error.hide()
      success.fadeIn()

      setTimeout(function () {
        success.fadeOut()
        form.fadeIn()
      }, 10000)
    },
    form_error = function (message) {
      form.hide()
      success.hide()
      error.fadeIn()

      if (message.length > 0) {
        error.html(message)
      }

      setTimeout(function () {
        error.fadeOut()
        form.fadeIn()
      }, 10000)
    },
    submitData = function () {
      var data = 'action=teamleader&' + form.serialize(),
        has_error = false

      form.find('input').each(function () {
        var input = jQuery(this)

        if (input.prop('required') && input.val().length === 0) {
          input.addClass('invalid')
          has_error = true
        } else {
          input.removeClass('invalid')
        }
      })

      if (has_error) {
        return
      }

      jQuery.ajax({
        method: 'POST',
        url: '<?php echo admin_url('admin-ajax.php');?>',
        data: data,
        success: function (data) {
          var response = jQuery.parseJSON(data)

          if (true === response.success) {
            form_success()
            return
          }
          form_error(response.message)
        },
      })
    },
    checkRecaptcha = function (token) {
      submitData()
    }
  jQuery(document).ready(function () {
    init()
  })
</script>