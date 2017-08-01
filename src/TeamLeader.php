<?php
/**
 * Author AGriboed (c) 2017
 *
 * alexv1rs@gmail.com
 * http://v1rus.ru
 */

namespace TeamLeader;

class TeamLeader
{
    const PLUGIN_KEY = 'teamleader';
    const API_URL = 'https://app.teamleader.eu/api';

    private $contact_fields = [];
    private $deal_fields = [];

    public function __construct()
    {
        $this->init();
    }

    /**
     * Init hooks
     */
    private function init()
    {
        add_shortcode(self::PLUGIN_KEY, array($this, 'shortcodeHandler'));
        add_action('wp_ajax_' . self::PLUGIN_KEY, array($this, 'ajaxHandler'));
        add_action('wp_ajax_nopriv_' . self::PLUGIN_KEY, array($this, 'ajaxHandler'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('admin_menu', function () {
            add_submenu_page('options-general.php', 'Team Leader', 'Team Leader',
                'manage_options', self::PLUGIN_KEY, array($this, 'renderOptionsPage'));
        }
        );
    }

    /**
     * Add settings to the CMS
     */
    public function registerSettings()
    {
        register_setting(self::PLUGIN_KEY, self::PLUGIN_KEY . '_api_group');
        register_setting(self::PLUGIN_KEY, self::PLUGIN_KEY . '_api_key');
        register_setting(self::PLUGIN_KEY, self::PLUGIN_KEY . '_form');
        register_setting(self::PLUGIN_KEY, self::PLUGIN_KEY . '_contact_fields');
        register_setting(self::PLUGIN_KEY, self::PLUGIN_KEY . '_deal_fields');
    }

    /**
     * See http://apidocs.teamleader.eu/crm.php
     *
     * @return array
     */
    private function getContactCrmFields()
    {
        if (empty($this->contact_fields)
            && file_exists(__DIR__ . '/fields/contact.php')
        ) {
            $this->contact_fields = require __DIR__ . '/fields/contact.php';
        }

        return $this->contact_fields;
    }

    /**
     * See http://apidocs.teamleader.eu/opportunities.php
     *
     * @return array
     */
    private function getDealCrmFields()
    {
        if (empty($this->deal_fields)
            && file_exists(__DIR__ . '/fields/deal.php')
        ) {
            $this->deal_fields = require __DIR__ . '/fields/deal.php';
        }

        return $this->deal_fields;
    }

    /**
     * Render admin options page
     */
    public function renderOptionsPage()
    {
        $contact_data = $this->getOptionContactFields();
        $deal_data = $this->getOptionDealFields();
        $form = $this->getOptionForm();
        $api_group_name = self::PLUGIN_KEY . '_api_group';
        $api_secret_name = self::PLUGIN_KEY . '_api_key';
        $form_name = self::PLUGIN_KEY . '_form';
        $contact_fields_name = self::PLUGIN_KEY . '_contact_fields';
        $deal_fields_name = self::PLUGIN_KEY . '_deal_fields';
        ?>
        <div>
            <form method="post" action="options.php">
                <h1>TeamLeader</h1>

                <?php settings_fields(self::PLUGIN_KEY); ?>
                <?php do_settings_sections(self::PLUGIN_KEY); ?>
                <table class="wp-list-table widefat fixed striped">
                    <tr>
                        <th scope="row"><strong>Your API group</strong></th>
                        <td style="width:40%"><input type="text" name="<?php echo $api_group_name; ?>"
                                                     value="<?php echo $this->getOptionApiGroup(); ?>"></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><strong>Your API key</strong></th>
                        <td><textarea
                                    name="<?php echo $api_secret_name; ?>"><?php echo $this->getOptionApiSecret() ?></textarea>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row"><strong>Form settings</strong></th>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Form title</th>
                        <td><input type="text"
                                   name="<?php echo $form_name; ?>[title]"
                                   value="<?php echo !empty($form['title']) ? $form['title'] : '' ?>">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Submit button text</th>
                        <td><input type="text"
                                   name="<?php echo $form_name; ?>[submit]"
                                   value="<?php echo !empty($form['submit']) ? $form['submit'] : '' ?>">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">After submission text</th>
                        <td><input type="text"
                                   name="<?php echo $form_name; ?>[text]"
                                   value="<?php echo !empty($form['text']) ? $form['text'] : '' ?>">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row"><strong>Contact fields</strong></th>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php foreach ($this->getContactCrmFields() as $key => $field) {
                        ?>
                        <tr>
                            <th scope="row"><?php echo $field['title'] ?></th>
                            <td>
                                <label>
                                    Public?
                                    <input type="checkbox"
                                           name="<?php echo $contact_fields_name . '[' . $key . ']'; ?>[public]"
                                        <?php
                                        if (isset($contact_data[$key]['public'])) {
                                            echo 'checked';
                                        }
                                        ?> />
                                </label>
                                <br>
                                <label>
                                    Required
                                    <input type="checkbox" placeholder="Required"
                                           name="<?php echo $contact_fields_name . '[' . $key . ']'; ?>[required]" <?php
                                    if ($field['required'] === true || isset($contact_data[$key]['required'])) {
                                        echo 'checked';
                                    }

                                    if ($field['required'] === true) {
                                        echo ' disabled';
                                    }
                                    ?>>
                                </label>
                                <br>
                                <label>
                                    Default value
                                    <input type="text" placeholder="Default value"
                                           name="<?php echo $contact_fields_name . '[' . $key . ']'; ?>[default]"
                                           value="<?php
                                           if (isset($contact_data[$key]['default'])) {
                                               echo $contact_data[$key]['default'];
                                           }
                                           ?>">
                                </label>
                                <br>
                                <label>
                                    Public label
                                    <input type="text" placeholder="Public label"
                                           name="<?php echo $contact_fields_name . '[' . $key . ']'; ?>[label]"
                                           value="<?php
                                           if (isset($contact_data[$key]['label'])) {
                                               echo $contact_data[$key]['label'];
                                           }
                                           ?>">
                                </label>
                            </td>
                            <td>
                                <small><?php echo $field['description']; ?></small>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th scope="row"><strong>Deal fields</strong></th>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php foreach ($this->getDealCrmFields() as $key => $field) {
                        ?>
                        <tr>
                            <th scope="row"><?php echo $field['title'] ?></th>
                            <td>
                                <label>
                                    Public?
                                    <input type="checkbox"
                                           name="<?php echo $deal_fields_name . '[' . $key . ']'; ?>[public]"
                                        <?php
                                        if (isset($deal_data[$key]['public'])) {
                                            echo 'checked';
                                        }
                                        ?> />
                                </label>
                                <br>
                                <label>
                                    Required
                                    <input type="checkbox" placeholder="Required"
                                           name="<?php echo $deal_fields_name . '[' . $key . ']'; ?>[required]" <?php
                                    if ($field['required'] === true || isset($deal_data[$key]['required'])) {
                                        echo 'checked';
                                    }

                                    if ($field['required'] === true) {
                                        echo ' disabled';
                                    }
                                    ?>>
                                </label>
                                <br>
                                <label>
                                    Default value
                                    <input type="text" placeholder="Default value"
                                           name="<?php echo $deal_fields_name . '[' . $key . ']'; ?>[default]"
                                           value="<?php
                                           if (isset($deal_data[$key]['default'])) {
                                               echo $deal_data[$key]['default'];
                                           }
                                           ?>">
                                </label>
                                <br>
                                <label>
                                    Public label
                                    <input type="text" placeholder="Public label"
                                           name="<?php echo $deal_fields_name . '[' . $key . ']'; ?>[label]"
                                           value="<?php
                                           if (isset($deal_data[$key]['label'])) {
                                               echo $deal_data[$key]['label'];
                                           }
                                           ?>">
                                </label>
                            </td>
                            <td>
                                <small><?php echo $field['description']; ?></small>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php submit_button(); ?>
            </form>
            <div class="">For showing form use shortcode <strong>[teamleader]</strong></div>
        </div>
        <?php
    }

    /**
     * Process shortcode
     *
     * @param array $atts
     * @return string
     */
    public function shortcodeHandler($atts = [])
    {
        $atts = shortcode_atts(array(), $atts);
        $ajax_url = admin_url('admin-ajax.php');

        $form = $this->getOptionForm();
        $form_title = !empty($form['title']) ? $form['title'] : '';
        $form_submit = !empty($form['submit']) ? $form['submit'] : '';
        $form_text = !empty($form['text']) ? $form['text'] : '';

        $contact_options = $this->getOptionContactFields();
        $contact_fields = $this->getContactCrmFields();

        $deal_options = $this->getOptionDealFields();
        $deal_fields = $this->getDealCrmFields();
        $fields_html = '';

        foreach ($contact_fields as $key => $field) {
            if (!empty($contact_options[$key]['public'])) {
                $label = !empty($contact_options[$key]['label']) ? $contact_options[$key]['label'] : $field['title'];
                $required = !empty($contact_options[$key]['required']) ? 'required="required"' : '';
                $value = !empty($contact_options[$key]['default']) ? $contact_options[$key]['default'] : '';

                $fields_html .= '<label>' . $label;

                if ('text' === $field['type']) {
                    $fields_html .= '<input type="text" name="contact[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ('textarea' === $field['type']) {
                    $fields_html .= '<textarea name="contact[' . $key . ']" placeholder="' . $label . '" ' . $required . '>' . $value . '</textarea>';
                }

                if ('number' === $field['type']) {
                    $fields_html .= '<input type="number" name="contact[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ('boolean' === $field['type']) {
                    $checked = !empty($value) ? 'checked' : '';
                    $fields_html .= '<input type="checkbox" name="contact[' . $key . ']" ' . $required . ' ' . $checked . '>';
                }

                $fields_html .= '</label>';
            }
        }

        foreach ($deal_fields as $key => $field) {
            if (!empty($deal_options[$key]['public'])) {
                $label = !empty($deal_options[$key]['label']) ? $deal_options[$key]['label'] : $field['title'];
                $required = !empty($deal_options[$key]['required']) ? 'required="required"' : '';
                $value = !empty($deal_options[$key]['default']) ? $deal_options[$key]['default'] : '';

                $fields_html .= '<label>' . $label;

                if ('text' === $field['type']) {
                    $fields_html .= '<input type="text" name="deal[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ('textarea' === $field['type']) {
                    $fields_html .= '<textarea name="deal[' . $key . ']" placeholder="' . $label . '" ' . $required . '>' . $value . '</textarea>';
                }

                if ('number' === $field['type']) {
                    $fields_html .= '<input type="number" name="deal[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ('boolean' === $field['type']) {
                    $checked = !empty($value) ? 'checked' : '';
                    $fields_html .= '<input type="checkbox" name="deal[' . $key . ']" ' . $required . ' ' . $checked . '>';
                }

                $fields_html .= '</label>';
            }
        }

        $key = self::PLUGIN_KEY;
        $html = '<div class="' . $key . '-container"><form><div class="' . $key . '-title">' . $form_title . '</div>';
        $html .= $fields_html;
        $html .= '<button type="submit" class="btn btn-primary">' . $form_submit . '</button></form><div class="teamleader-result"></div></div>';
        $html .= <<<HTML
<script>
(function($) {
$(document).ready(function()
{
   var form = $('.{$key}-container').find('form'),
       result = $('.{$key}-container').find('.{$key}-result');
   
   form.submit(function(e){
    e.preventDefault();
    var data = 'action={$key}&' + form.serialize();
    
    form.fadeOut();
 
     $.ajax({
      method: "POST",
      url: '{$ajax_url}',
      data: data,
      success: function( response ) {
        result.html('<div class="{$key}-success">{$form_text}</div>').fadeIn();
        setTimeout(function() {
            result.html('').fadeOut();
            form.fadeIn();
        }, 10000);
      }
    });
   });
});
})( jQuery );
</script>
HTML;
        return $html;
    }

    /**
     * @return string|null
     */
    private function getOptionApiGroup()
    {
        return get_option(self::PLUGIN_KEY . '_api_group', null);
    }

    /**
     * @return string|null
     */
    private function getOptionApiSecret()
    {
        return get_option(self::PLUGIN_KEY . '_api_key', null);
    }

    /**
     * @return string|null
     */
    private function getOptionForm()
    {
        return get_option(self::PLUGIN_KEY . '_form', array());
    }

    /**
     * @return array
     */
    private function getOptionContactFields()
    {
        return get_option(self::PLUGIN_KEY . '_contact_fields', array());
    }

    /**
     * @return array
     */
    private function getOptionDealFields()
    {
        return get_option(self::PLUGIN_KEY . '_deal_fields', array());
    }

    /**
     * Process ajax call
     *
     * @throws \Exception
     * @return string|null
     */
    public function ajaxHandler()
    {
        $contact_options = $this->getOptionContactFields();
        $contact_fields = $this->getContactCrmFields();

        $deal_options = $this->getOptionDealFields();
        $deal_fields = $this->getDealCrmFields();

        $contact_post = array();
        $deal_post = array();

        foreach ($contact_fields as $key => $field) {
            $value = isset($_POST['contact'][$key]) ? $_POST['contact'][$key] : null;

            if (isset($contact_options[$key]['default'])) {
                $value = $contact_options[$key]['default'];
            }

            $contact_post[$key] = $value;
        }

        foreach ($deal_fields as $key => $field) {
            $value = isset($_POST['deal'][$key]) ? $_POST['deal'][$key] : null;

            if (isset($deal_options[$key]['default'])) {
                $value = $deal_options[$key]['default'];
            }

            $deal_post[$key] = $value;
        }

        try {
            $contact_id = $this->request('addContact.php', $contact_post);

            $deal_post['contact_or_company'] = 'contact';
            $deal_post['contact_or_company_id'] = (int)$contact_id;

            $deal_id = $this->request('addDeal.php', $deal_post);

        } catch (\RuntimeException $exception) {
            echo json_encode(['success' => false, 'message' => $exception->getMessage()]);
            wp_die();
        }

        echo json_encode(['success' => true]);
        wp_die();
    }

    /**
     *
     *
     * @param string $endPoint
     * @param array $fields
     * @return mixed
     * @throws \Exception
     */
    private function request($endPoint = '', array $fields = array())
    {
        $fields['api_group'] = $this->getOptionApiGroup();
        $fields['api_secret'] = $this->getOptionApiSecret();

        $options[CURLOPT_URL] = self::API_URL . '/' . $endPoint;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $fields;

        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);

        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        if ($errorNumber) {
            throw new \RuntimeException($errorMessage, $errorNumber);
        }

        if ($headers['http_code'] === 400) {
            $json = @json_decode($response, true);
            if ($json !== false && isset($json['reason'])) {
                throw new \RuntimeException('Teamleader ' . $endPoint . ' API returned status code 400 Bad Request. Reason: ' . $json['reason']);
            } else {
                throw new \RuntimeException('Teamleader ' . $endPoint . ' API returned status code 400 Bad Request. Data returned: ' . $response);
            }
        }

        if ($endPoint === 'downloadInvoicePDF.php') {
            return $response;
        }

        $json = @json_decode($response, true);

        if ($json !== false) {
            return $json;
        }

        return $response;
    }
}