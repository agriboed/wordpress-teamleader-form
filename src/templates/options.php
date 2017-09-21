<div class="teamleader">
    <form method="post" action="options.php">
        <h1><?php _e('Teamleader', 'teamleader'); ?></h1>

        <?php settings_fields(self::$plugin_key); ?>
        <?php do_settings_sections(self::$plugin_key); ?>

        <div class="table">
            <div class="heading">
                <?php _e('Plugin settings', 'teamleader'); ?>
            </div>
            <div class="body">
                <?php _e('In order to <strong>connect your form</strong> to Teamleader with WordPress, you need to:', 'teamleader'); ?>
                <div class="installation">
                    <div class="step">
                        <span class="circle">1</span>
                        <?php _e('Install the <a href="https://marketplace.teamleader.eu/eu/en/detail/teamleader/custom-form/eeb282" target="_blank">WordPress integration</a> on the Teamleader Marketplace', 'teamleader'); ?>
                    </div>
                    <div class="step">
                        <span class="circle">2</span>
                        <?php _e('<a href="https://marketplace.teamleader.eu/eu/en/manage/settings/eeb282" target="_blank">Go to the integrations settings</a> and <strong>connect a new form</strong>', 'teamleader'); ?>
                    </div>
                    <div class="step">
                        <span class="circle">3</span>
                        <?php _e('After filling in the form, you\'ll see a <strong>Webhook URL</strong>. Copy this URL and paste it in the field below', 'teamleader'); ?>

                        <div class="webhook-field">
                            <?php _e('Webhook URL', 'teamleader'); ?>
                            <input type="text" value="<?php echo $this->getWebhook(); ?>" placeholder=""
                                   name="<?php echo self::$plugin_key; ?>_webhook">
                            <input type="submit" class="button button-primary"
                                   value="<?php _e('Save', 'teamleader'); ?>">
                        </div>
                    </div>
                    <div class="step">
                        <span class="circle">4</span>
                        <?php _e('Add your form in any Wordpress page with the following <strong>shortcode</strong>:', 'teamleader'); ?>
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
                <?php _e('Read more about this process in the <a href="http://support.teamleader.eu/" target="_blank">Teamleader Knowledge Base</a>', 'teamleader'); ?>
            </div>
        </div>

        <?php if (null !== $this->getWebhook()): ?>
            <div class="table">
                <div class="heading">
                    <?php _e('Form settings', 'teamleader'); ?>
                </div>
                <div class="body">
                    <div class="step">
                        <label>
                            <?php _e('Submit button text', 'teamleader'); ?>
                            <input type="text"
                                   name="<?php echo $form_name; ?>[submit]"
                                   value="<?php echo !empty($form['submit']) ? $form['submit'] : '' ?>">
                        </label>
                        <div class="description">
                            <?php _e('Specify the label of the submit button', 'teamleader'); ?>
                        </div>
                    </div>
                    <div class="step">
                        <label>
                            <?php _e('After submission text'); ?>
                            <input type="text"
                                   name="<?php echo $form_name; ?>[success]"
                                   value="<?php echo !empty($form['success']) ? $form['success'] : '' ?>">
                        </label>
                        <div class="description">
                            <?php _e('This is the text that is shown after the form has been successfully submitted', 'teamleader'); ?>
                        </div>
                    </div>
                    <div class="step">
                        <input type="submit" class="button button-primary"
                               value="<?php _e('Publish changes', 'teamleader'); ?>">
                    </div>
                </div>
            </div>

            <div class="table">
                <div class="heading">
                    <?php _e('Form fields'); ?>
                </div>
                <div class="body-fields">
                    <?php foreach ($this->getFields() as $key => $field): ?>
                        <div class="field <?php echo (isset($data[$key]['public']) || true === $field['required']) ? '' : 'disabled'; ?>">
                            <div class="active">
                                <input type="checkbox"
                                       name="<?php echo $fields_name . '[' . $key . ']'; ?>[public]"
                                    <?php echo isset($data[$key]['public']) ? 'checked' : ''; ?>
                                    <?php echo (true === $field['required']) ? 'checked disabled' : ''; ?>
                                       id="<?php echo $field['title'] ?>"
                                />
                            </div>
                            <div class="name">
                                <strong><label for="<?php echo $field['title'] ?>"><?php echo $field['title'] ?></label></strong>
                                <div class="description">
                                    <?php echo (true === $field['required'] || isset($data[$key]['required'])) ? __('Field is required') : ''; ?>
                                </div>
                            </div>
                            <div class="label">
                                <?php _e('Field label', 'teamleader'); ?>
                                <input type="text" placeholder="<?php echo $field['title'] ?>"
                                       name="<?php echo $fields_name . '[' . $key . ']'; ?>[label]"
                                       value="<?php echo isset($data[$key]['label']) ? $data[$key]['label'] : ''; ?>">
                            </div>
                            <div class="default">
                                <?php if (true !== $field['required']): ?>
                                    <?php _e('Default value', 'teamleader'); ?>
                                    <input type="text" placeholder=""
                                           name="<?php echo $fields_name . '[' . $key . ']'; ?>[default]"
                                           value="<?php echo isset($data[$key]['default']) ? $data[$key]['default'] : ''; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="required">
                                <?php if (true !== $field['required']): ?>
                                    <?php _e('Required?', 'teamleader'); ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio"
                                                   name="<?php echo $fields_name . '[' . $key . ']'; ?>[required]"
                                                <?php echo isset($data[$key]['required']) ? 'checked' : ''; ?>> Yes
                                        </label>
                                        <label>
                                            <input type="radio"
                                                   name="<?php echo $fields_name . '[' . $key . ']'; ?>[required]"
                                                <?php echo isset($data[$key]['required']) ? '' : 'checked'; ?>> No
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="bottom">
                        <input type="submit" class="button button-primary"
                               value="<?php _e('Publish changes', 'teamleader'); ?>">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </form>
    <script>
        (function ($) {
            $(document).ready(function () {
                let active = $('.active input[type=checkbox]');
                active.click(function () {
                    if ($(this).attr('checked')) {
                        $(this).closest('.field').removeClass('disabled');
                    }
                    else {
                        $(this).closest('.field').addClass('disabled');
                    }
                });
            });
        })(jQuery);
    </script>
</div>
