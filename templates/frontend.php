<div class="teamleader-container">
    <form>
        <?php
        foreach ($fields as $key => $field) {
            if (!isset($fields_options[$key]['public']) && (true !== $field['required'])) {
                continue;
            }

            $label = !empty($fields_options[$key]['label']) ? $fields_options[$key]['label'] : $field['title'];
            $required = (!empty($fields_options[$key]['required']) || $fields[$key]['required']) ? 'required="required"' : null;
            $value = !empty($fields_options[$key]['default']) ? $fields_options[$key]['default'] : null;
            ?>
            <div class="form-group">
                <label>
                    <?php echo $label; ?>
                    <?php
                    switch ($field['type']) {
                        case 'textarea':
                            echo '<textarea name="data[' . $key . ']" placeholder="" ' . $required . ' class="form-control">' . $value . '</textarea>';
                            break;
                        case 'number':
                            echo '<input type="number" name="data[' . $key . ']" value="' . $value . '" placeholder="" ' . $required . ' class="form-control">';
                            break;
                        case 'boolean':
                            $checked = !empty($value) ? 'checked' : '';
                            echo '<input type="checkbox" name="data[' . $key . ']" ' . $required . ' ' . $checked . ' class="form-control">';
                            break;
                        default:
                            echo '<input type="text" name="data[' . $key . ']" value="' . $value . '" placeholder="" ' . $required . ' class="form-control">';
                            break;
                    }
                    ?>
                </label>
            </div>
            <?php
        }
        ?>
        <?php if (!empty($form['logo'])): ?>
            <div class="teamleader-powered"><?php _e('Powered by <a href="https://www.teamleader.eu/" target="_blank">Teamleader</a>', 'teamleader'); ?>
                <img src="<?php echo $logo; ?>" alt="<?php _e('Teamleader', 'teamleader'); ?>"></div>
        <?php endif; ?>

        <?php if ($form['recaptcha']): ?>
            <button type="submit" class="btn btn-primary g-recaptcha teamleader-submit"
                    data-sitekey="<?php echo $form['recaptcha_site_key']; ?>"
                    data-callback="checkRecaptcha"
                    data-size="invisible"><?php echo $form['submit']; ?></button>
        <?php else: ?>
            <button type="submit" class="btn btn-primary teamleader-submit"><?php echo $form['submit']; ?></button>
        <?php endif; ?>
    </form>
    <div class="teamleader-success" data-success
         style="display: none;"><?php echo addslashes($form['success']); ?></div>
    <div class="teamleader-error" data-error style="display: none;">Error sending form. Please contact system
        administrator.
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
                submit = container.find('input[type=submit]');

            form.submit(function (e) {
                e.preventDefault();

                <?php if (empty($form['recaptcha'])): ?>
                submitData();
                <?php endif;?>
            });
        },
        form_success = function () {
            form.hide();
            error.hide();
            success.fadeIn();

            setTimeout(function () {
                success.fadeOut();
                form.fadeIn();
            }, 10000);
        },
        form_error = function (message) {
            form.hide();
            success.hide();
            error.fadeIn();

            if (message.length > 0) {
                error.html(message);
            }

            setTimeout(function () {
                error.fadeOut();
                form.fadeIn();
            }, 10000);
        },
        submitData = function () {
            var data = 'action=teamleader&' + form.serialize();

            jQuery.ajax({
                method: 'POST',
                url: '<?php echo admin_url('admin-ajax.php');?>',
                data: data,
                success: function (data) {
                    var response = jQuery.parseJSON(data);

                    if (true === response.success) {
                        form_success();
                        return;
                    }

                    form_error(response.message);
                }
            });
        },
        checkRecaptcha = function (token) {
            submitData();
        };

    jQuery(document).ready(function () {
        init();
    });
</script>