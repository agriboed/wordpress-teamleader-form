<div class="teamleader">
    <form method="post" action="options.php">
        <h1><?php _e('Teamleader', $data['key']); ?></h1>

        <?php settings_fields($data['key']); ?>
        <?php do_settings_sections($key['key']); ?>

        <div class="table">
            <div class="heading">
                <?php _e('Plugin settings', $data['key']); ?>
            </div>
            <div class="body">
                <?php _e('In order to <strong>connect your form</strong> to Teamleader with WordPress, you need to:', $data['key']); ?>
                <div class="installation">
                    <div class="step">
                        <span class="circle">1</span>
                        <?php _e('Install the <a href="https://app.teamleader.eu/contact_detail.php?id=18128209" target="_blank">WordPress integration</a> on the Teamleader Marketplace', $data['key']); ?>
                    </div>
                    <div class="step">
                        <span class="circle">2</span>
                        <?php _e('<a href="https://marketplace.teamleader.eu/eu/en/manage/settings/eeb282" target="_blank">Go to the integrations settings</a> and <strong>connect a new form</strong>', $data['key']); ?>
                    </div>
                    <div class="step">
                        <span class="circle">3</span>
                        <?php _e('After filling in the form, you\'ll see a <strong>Webhook URL</strong>. Copy this URL and paste it in the field below', $data['key']); ?>

                        <div class="webhook-field">
                            <?php _e('Webhook URL', $data['key']); ?>
                            <input type="text" value="<?php echo $data['webhook']; ?>" placeholder=""
                                   name="<?php echo $data['key']; ?>_webhook">
                            <input type="submit" class="button button-primary"
                                   value="<?php _e('Save', $data['key']); ?>">
                        </div>
                    </div>
                    <div class="step">
                        <span class="circle">4</span>
                        <?php _e('Add your form in any Wordpress page with the following <strong>shortcode</strong>:', $data['key']); ?>
                        <div class="shortcode-container">
                        <span class="shortcode">
                            [teamleader]
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom">
                <span class="circle">?</span>
                <?php _e('Read more about this process in the <a href="http://support.teamleader.eu/" target="_blank">Teamleader Knowledge Base</a>', $data['key']); ?>
            </div>
        </div>

        <?php if (null !== $data['webhook']): ?>
            <div class="table">
                <div class="heading">
                    <?php _e('Form settings', $data['key']); ?>
                </div>
                <div class="body">
                    <div class="step">
                        <label>
                            <?php _e('Submit button text', $data['key']); ?>
                            <input type="text"
                                   name="<?php echo $data['form_name']; ?>[submit]"
                                   value="<?php echo !empty($data['form_options']['submit']) ? $data['form_options']['submit'] : '' ?>">
                        </label>
                        <div class="description">
                            <?php _e('Specify the label of the submit button', $data['key']); ?>
                        </div>
                    </div>
                    <div class="step">
                        <label>
                            <?php _e('After submission text', $data['key']); ?>
                            <input type="text"
                                   name="<?php echo $data['form_name']; ?>[success]"
                                   value="<?php echo !empty($data['form_options']['success']) ? $data['form_options']['success'] : '' ?>">
                        </label>
                        <div class="description">
                            <?php _e('This is the text that is shown after the form has been successfully submitted', $data['key']); ?>
                        </div>
                    </div>
                    <div class="step">
                        <label>
                            <?php _e('Show Teamleader logo in the footer', $data['key']); ?>
                            <input type="checkbox"
                                   name="<?php echo $data['form_name']; ?>[logo]"
                                   value="1" <?php echo !empty($data['form_options']['logo']) ? 'checked' : '' ?>>
                        </label>
                        <div class="description">
                        </div>
                    </div>
                    <div class="step">
                        <label>
                            <?php _e('Enable Google Invisible reCAPTCHA', $data['key']); ?>
                            <input type="checkbox"
                                   id="teamleader_recaptcha"
                                   name="<?php echo $data['form_name']; ?>[recaptcha]"
                                   value="1" <?php echo !empty($data['form_options']['recaptcha']) ? 'checked' : '' ?>>
                        </label>
                        <div class="description">
                            <?php _e('See more information and keys on <a href="https://www.google.com/recaptcha/admin#list" target=_blank">the official google website</a>.', $data['key']); ?>
                        </div>
                    </div>
                    <div class="step recaptcha-container"
                         <?php if (empty($data['form_options']['recaptcha'])): ?>style="display: none"<?php endif; ?>>
                        <label>
                            <?php _e('Site key', $data['key']); ?>
                            <input type="text"
                                   name="<?php echo $data['form_name']; ?>[recaptcha_site_key]"
                                   value="<?php echo !empty($data['form_options']['recaptcha_site_key']) ? $data['form_options']['recaptcha_site_key'] : '' ?>">
                        </label>
                        <div class="description">
                        </div>
                    </div>
                    <div class="step recaptcha-container"
                         <?php if (empty($data['form_options']['recaptcha'])): ?>style="display: none"<?php endif; ?>>
                        <label>
                            <?php _e('Secret key', $data['key']); ?>
                            <input type="text"
                                   name="<?php echo $data['form_name']; ?>[recaptcha_secret_key]"
                                   value="<?php echo !empty($data['form_options']['recaptcha_secret_key']) ? $data['form_options']['recaptcha_secret_key'] : '' ?>">
                        </label>
                        <div class="description">
                        </div>
                    </div>
                    <div class="step">
                        <input type="submit" class="button button-primary"
                               value="<?php _e('Publish changes', $data['key']); ?>">
                    </div>
                </div>
            </div>

            <div class="table">
                <div class="heading">
                    <?php _e('Form fields', $data['key']); ?>
                </div>
                <div class="body-fields">
                    <?php foreach ($data['fields'] as $key => $field): ?>
                        <div class="field <?php echo (isset($data['fields_options'][$key]['public']) || true === $field['required']) ? '' : 'disabled'; ?>">
                            <div class="active">
                                <input type="checkbox"
                                       name="<?php echo $data['fields_name'] . '[' . $key . ']'; ?>[public]"
                                    <?php echo isset($data['fields_options'][$key]['public']) ? 'checked' : ''; ?>
                                    <?php echo (true === $field['required']) ? 'checked disabled' : ''; ?>
                                       id="<?php echo $field['title'] ?>"
                                />
                            </div>
                            <div class="name">
                                <strong><label for="<?php echo $field['title'] ?>"><?php echo $field['title'] ?></label></strong>
                                <div class="description">
                                    <?php echo (true === $field['required'] || isset($data['fields_options'][$key]['required'])) ? __('Field is required', $data['key']) : ''; ?>
                                </div>
                            </div>
                            <div class="label">
                                <?php _e('Field label', $data['key']); ?>
                                <input type="text" placeholder="<?php echo $field['title'] ?>"
                                       name="<?php echo $data['fields_name'] . '[' . $key . ']'; ?>[label]"
                                       value="<?php echo isset($data['fields_options'][$key]['label']) ? $data['fields_options'][$key]['label'] : ''; ?>">
                            </div>
                            <div class="default">
                                <?php if (true !== $field['required']): ?>
                                    <?php _e('Default value', $data['key']); ?>
                                    <input type="text" placeholder=""
                                           name="<?php echo $data['fields_name'] . '[' . $key . ']'; ?>[default]"
                                           value="<?php echo isset($data['fields_options'][$key]['default']) ? $data['fields_options'][$key]['default'] : ''; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="required">
                                <?php if (true !== $field['required']): ?>
                                    <?php _e('Required?', $data['key']); ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio"
                                                   name="<?php echo $data['fields_name'] . '[' . $key . ']'; ?>[required]"
                                                <?php echo isset($data['fields_options'][$key]['required']) ? 'checked' : ''; ?>>
                                            <?php _e('Yes', $data['key']);?>
                                        </label>
                                        <label>
                                            <input type="radio"
                                                   name="<?php echo $data['fields_name'] . '[' . $key . ']'; ?>[required]"
                                                <?php echo isset($data['fields_options'][$key]['required']) ? '' : 'checked'; ?>>
                                            <?php _e('No', $data['key']);?>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="bottom">
                        <input type="submit" class="button button-primary"
                               value="<?php _e('Publish changes', $data['key']); ?>">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </form>
    <script>
        (function ($) {
            $(document).ready(function () {
                var active = $('.active input[type=checkbox]'),
                    recaptcha = $('#teamleader_recaptcha');

                active.click(function () {
                    if ($(this).attr('checked')) {
                        $(this).closest('.field').removeClass('disabled');
                    }
                    else {
                        $(this).closest('.field').addClass('disabled');
                    }
                });

                recaptcha.click(function () {
                    if ($(this).attr('checked')) {
                        $('.recaptcha-container').show();
                    }
                    else {
                        $('.recaptcha-container').hide();
                    }
                });
            });
        })(jQuery);
    </script>
</div>
