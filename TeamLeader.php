<?php
/**
 * Author AGriboed 2017
 *
 * alexv1rs@gmail.com
 * http://v1rus.ru
 */

namespace TeamLeader;

class TeamLeader
{
    /**
     * @var string Plugin's key
     */
    private $key = 'teamleader';
    const API_URL = 'https://app.teamleader.eu/api';

    public function __construct()
    {
        $this->init();
    }

    /**
     * Init hooks
     */
    private function init()
    {
        add_shortcode($this->key, [$this, 'shortcodeHandler']);
        add_action('wp_ajax_' . $this->key, [$this, 'ajaxHandler']);
        add_action('wp_ajax_nopriv_' . $this->key, [$this, 'ajaxHandler']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', function () {
            add_submenu_page('options-general.php', 'Team Leader', 'Team Leader',
                'manage_options', $this->key, [$this, 'renderOptionsPage']);
        }
        );
    }

    /**
     * Add settings to the CMS
     */
    public function registerSettings()
    {
        register_setting($this->key, $this->key . '_api_group');
        register_setting($this->key, $this->key . '_api_key');
        register_setting($this->key, $this->key . '_contact_fields');
        register_setting($this->key, $this->key . '_deal_fields');
    }

    /**
     * See http://apidocs.teamleader.eu/crm.php
     *
     * @return array
     */
    private function getContactCrmFields()
    {
        $crm_fields = [
            'forename' => [
                'title' => 'First name',
                'required' => true,
                'type' => 'text',
                'description' => 'Required',
            ],
            'surname' => [
                'title' => 'Last name',
                'required' => true,
                'type' => 'text',
                'description' => 'Required',
            ],
            'email' => [
                'title' => 'Email',
                'required' => true,
                'type' => 'text',
                'description' => 'Required',
            ],
            'salutation' => [
                'title' => 'Salutation',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'telephone' => [
                'title' => 'Phone',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'gsm' => [
                'title' => 'Mobile',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'website' => [
                'title' => 'Website',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'country' => [
                'title' => 'Country',
                'required' => false,
                'type' => 'text',
                'description' => 'country code according to ISO 3166-1 alpha-2. For Belgium: "BE"',
            ],
            'zipcode' => [
                'title' => 'Zip Code',
                'required' => false,
                'type' => 'number',
                'description' => '',
            ],
            'city' => [
                'title' => 'City',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'street' => [
                'title' => 'Street',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'number' => [
                'title' => 'Number',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],

            'address_name_delivery' => [
                'title' => 'Delivery Address',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'zipcode_delivery' => [
                'title' => 'Delivery Zip Code',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'city_delivery' => [
                'title' => 'Delivery City',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'street_delivery' => [
                'title' => 'Delivery Street',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'number_delivery' => [
                'title' => 'Delivery Number',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'address_name_visiting' => [
                'title' => 'Visiting Address',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'zipcode_visiting' => [
                'title' => 'Visiting Zip Code',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'city_visiting' => [
                'title' => 'Visiting City',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'street_visiting' => [
                'title' => 'Visiting Street',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'number_visiting' => [
                'title' => 'Visiting Number',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'address_name_invoicing' => [
                'title' => 'Invoicing Address',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'zipcode_invoicing' => [
                'title' => 'Invoicing Zip Code',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'city_invoicing' => [
                'title' => 'Invoicing City',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'street_invoicing' => [
                'title' => 'Invoicing Street',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'number_invoicing' => [
                'title' => 'Invoicing Number',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'invoice_email_address' => [
                'title' => 'Invoice Email',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'language' => [
                'title' => 'Language',
                'required' => false,
                'type' => 'text',
                'description' => 'language code according to ISO 639-1. For Dutch: "NL"',
            ],
            'gender' => [
                'title' => 'Gender',
                'required' => false,
                'type' => 'text',
                'description' => 'M/F/U',
            ],
            'dob' => [
                'title' => 'Date Of Birth',
                'required' => false,
                'type' => 'text',
                'description' => 'unix timestamp',
            ],
            'description' => [
                'title' => 'Description',
                'required' => false,
                'type' => 'text',
                'description' => 'background information on the contact',
            ],
            'newsletter' => [
                'title' => 'Newsletter',
                'required' => false,
                'type' => 'boolean',
                'description' => '',
            ],
            'add_tag_by_string' => [
                'title' => 'Add tag by string',
                'required' => false,
                'type' => 'text',
                'description' => 'string: pass one or more tags, comma-separated. Existing tags will be reused, other tags will be automatically created for you.',
            ],
            'automerge_by_name' => [
                'title' => 'Auto merge by name',
                'required' => false,
                'type' => 'boolean',
                'description' => 'If this flag is set to 1, Teamleader will merge this info into an existing contact with the same forename and surname, if it finds any. Default: 0',
            ],
            'automerge_by_email' => [
                'title' => 'Auto merge by email',
                'required' => false,
                'type' => 'boolean',
                'description' => 'If this flag is set to 1, Teamleader will merge this info into an existing contact with the same email address, if it finds any.',
            ],
            'tracking' => [
                'title' => 'Tracking',
                'required' => false,
                'type' => 'text',
                'description' => 'title of the activity',
            ],
            'tracking_long' => [
                'title' => 'Tracking Long',
                'required' => false,
                'type' => 'text',
                'description' => 'description of the activity',
            ],
        ];

        return $crm_fields;
    }

    /**
     * See http://apidocs.teamleader.eu/opportunities.php
     *
     * @return array
     */
    private function getDealCrmFields()
    {
        $crm_fields = [
            'title' => [
                'title' => 'Title',
                'required' => true,
                'type' => 'text',
                'description' => 'Required',
            ],
            'source' => [
                'title' => 'Source',
                'required' => true,
                'type' => 'text',
                'description' => 'Required',
            ],
            'description_1' => [
                'title' => 'Description 1',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'price_1' => [
                'title' => 'Price 1',
                'required' => false,
                'type' => 'number',
                'description' => '',
            ],
            'amount_1' => [
                'title' => 'Amount 1',
                'required' => false,
                'type' => 'number',
                'description' => '',
            ],
            'vat_1' => [
                'title' => 'Vat 1',
                'required' => false,
                'type' => 'number',
                'description' => '00/ 06 / 12 / 21 / CM / EX / MC / VCMD: the vat tariff for this line',
            ],
            'product_id_1' => [
                'title' => 'Product id 1',
                'required' => false,
                'type' => 'text',
                'description' => 'id of the product (optional)',
            ],
            'account_1' => [
                'title' => 'Account 1',
                'required' => false,
                'type' => 'text',
                'description' => 'id of the bookkeeping account (optional)',
            ],
            'subtitle_1' => [
                'title' => 'Subtitle 1',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'description_2' => [
                'title' => 'Description 2',
                'required' => false,
                'type' => 'text',
                'description' => '',
            ],
            'price_2' => [
                'title' => 'Price 2',
                'required' => false,
                'type' => 'number',
                'description' => '',
            ],
            'sys_department_id' => [
                'title' => 'Sys Department Id',
                'required' => false,
                'type' => 'text',
                'description' => 'ID of the department the deal will be added to',
            ],
            'responsible_sys_client_id' => [
                'title' => 'Responsible Sys Client Id',
                'required' => false,
                'type' => 'text',
                'description' => 'id of the user that is responsible for this deal',
            ],
            'phase_id' => [
                'title' => 'Phase Id',
                'required' => false,
                'type' => 'number',
                'description' => 'new phase id this deal should be moved to',
            ],
            'description' => [
                'title' => 'Description',
                'required' => false,
                'type' => 'text',
                'description' => ' If this parameter is filled out a quotation (PDF) is generated by Teamleader using the text provided and the items. The PDF is accessible via the web interface.',
            ],
            'budget_estimation' => [
                'title' => 'Budget Estimation',
                'required' => false,
                'type' => 'number',
                'description' => 'budget estimation for this deal. This value will be overwritten once a quotation is made for this deal',
            ],
            'optional_contact_id' => [
                'title' => 'Optional Contact Id',
                'required' => false,
                'type' => 'number',
                'description' => 'if this deal is related to a company, you can specify the ID of a related contact within that company via this parameter',
            ],
            'filter_double_sales' => [
                'title' => 'Filter Double Sales',
                'required' => false,
                'type' => 'text',
                'description' => 'if set to 1, an extra check added to make sure the deal isn\'t a double of the previous one.',
            ],
        ];

        return $crm_fields;
    }

    /**
     * Render admin options page
     */
    public function renderOptionsPage()
    {
        $contact_data = $this->getOptionContactFields();
        $deal_data = $this->getOptionDealFields();
        $api_group_name = $this->key . '_api_group';
        $api_secret_name = $this->key . '_api_key';
        $contact_fields_name = $this->key . '_contact_fields';
        $deal_fields_name = $this->key . '_deal_fields';
        ?>
        <div>
            <form method="post" action="options.php">
                <h1>TeamLeader</h1>
                <?php settings_fields($this->key); ?>
                <?php do_settings_sections($this->key); ?>
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
        $atts = shortcode_atts([], $atts);
        $ajax_url = admin_url('admin-ajax.php');

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

                if ($field['type'] === 'text') {
                    $fields_html .= '<input type="text" name="contact[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ($field['type'] === 'number') {
                    $fields_html .= '<input type="number" name="contact[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ($field['type'] === 'boolean') {
                    $checked = !empty($value) ? 'checked' : '';
                    $fields_html .= '<input type="checkbox" name="contact[' . $key . ']" ' . $required . ' ' . $checked . '>';
                }

                $fields_html .= '</label>';
            }
        }

        foreach ($deal_fields as $key => $field) {
            if (!empty($deal_options[$key]['public'])) {
                $label = isset($deal_options[$key]['label']) ? $deal_options[$key]['label'] : $field['title'];
                $required = !empty($deal_options[$key]['required']) ? 'required="required"' : '';
                $value = isset($deal_options[$key]['default']) ? $deal_options[$key]['default'] : '';

                $fields_html .= '<label>' . $label;

                if ($field['type'] === 'text') {
                    $fields_html .= '<input type="text" name="deal[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ($field['type'] === 'number') {
                    $fields_html .= '<input type="number" name="deal[' . $key . ']" value="' . $value . '" placeholder="' . $label . '" ' . $required . '>';
                }

                if ($field['type'] === 'boolean') {
                    $checked = !empty($value) ? 'checked' : '';
                    $fields_html .= '<input type="checkbox" name="deal[' . $key . ']" ' . $required . ' ' . $checked . '>';
                }

                $fields_html .= '</label>';
            }
        }

        $html = '<div class="teamleader-container"><form>';
        $html .= $fields_html;
        $html .= '<button type="submit" class="btn btn-primary">Add contact to CRM</button></form><div class="teamleader-result"></div></div>';

        $html .= <<<HTML
<script>
(function($) {
$(document).ready(function()
{
   var form = $('.teamleader-container').find('form'),
       result = $('.teamleader-container').find('.teamleader-result');
   
   form.submit(function(e){
    e.preventDefault();
    var data = 'action={$this->key}&' + form.serialize();
    
    form.fadeOut();
    
     $.ajax({
      method: "POST",
      url: '{$ajax_url}',
      data: data,
      success: function( response ) {
        result.html('<h2>Thank you!</h2>').fadeIn();
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
        return get_option($this->key . '_api_group', null);
    }

    /**
     * @return string|null
     */
    private function getOptionApiSecret()
    {
        return get_option($this->key . '_api_key', null);
    }

    /**
     * @return array
     */
    private function getOptionContactFields()
    {
        return get_option($this->key . '_contact_fields', array());
    }

    /**
     * @return array
     */
    private function getOptionDealFields()
    {
        return get_option($this->key . '_deal_fields', array());
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

        $contact_post = [];
        $deal_post = [];

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
