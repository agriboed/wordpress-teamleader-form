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

        <div class="powered">Powered by Teamleader <img src="<?php echo $logo; ?>" alt="Teamleader"></div>

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
<style>
    .teamleader-container label {
        font-weight: 400;
    }

    .teamleader-container .form-control {
        padding: 5px 10px;
        margin-top: 5px;
        border: 2px solid #e9e9e9;
    }

    .teamleader-container .form-group {
        margin-bottom: 20px;
    }

    .teamleader-container button[type=submit] {
        padding: 5px 20px;
        text-transform: uppercase;
        font-weight: 500;
        background: #18aaa7;
        margin-top: 5px;
    }

    .teamleader-container .powered {
        text-align: right;
        font-size: 0.7em;
        color: #c3c3c3;
    }

    .teamleader-container .powered img {
        top: 2px;
        position: relative;
    }
</style>