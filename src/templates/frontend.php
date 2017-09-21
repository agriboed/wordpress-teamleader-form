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
        <div class="powered">Powered by <a href="https://www.teamleader.eu/" target="_blank">Teamleader</a><img
                    src="<?php echo $logo; ?>" alt="Teamleader"></div>
        <button type="submit" class="btn btn-primary"><?php echo $form['submit']; ?></button>
    </form>
    <div data-result></div>
</div>
<script>
    (function ($) {
        $(document).ready(function () {
            let container = $('.teamleader-container'),
                form = container.find('form'),
                result = container.find('[data-result]'),
                data;

            form.submit(function (e) {
                e.preventDefault();
                data = 'action=teamleader&' + form.serialize();

                form.fadeOut();
                result.html('<div data-success><?php echo addslashes($form['success']); ?></div>').fadeIn();

                $.ajax({
                    method: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php');?>',
                    data: data,
                    success: function () {
                        setTimeout(function () {
                            result.html('').fadeOut();
                            form.fadeIn();
                        }, 10000);
                    }
                });
            });
        });
    })(jQuery);
</script>