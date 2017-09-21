<?php

namespace Teamleader;

class Teamleader
{
    protected static $plugin_key = 'teamleader';
    protected $basename;
    protected $fields = [];

    public function __construct($plugin = __FILE__)
    {
        $this->basename = plugin_basename($plugin);
        $this->init();
    }

    /**
     * Init hooks
     */
    protected function init()
    {
        add_shortcode(self::$plugin_key, [$this, 'shortcodeHandler']);

        add_action('wp_ajax_' . self::$plugin_key, [$this, 'ajaxHandler']);
        add_action('wp_ajax_nopriv_' . self::$plugin_key, [$this, 'ajaxHandler']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', function () {
            add_submenu_page('options-general.php', __('Teamleader'), __('Teamleader'),
                'manage_options', self::$plugin_key, [$this, 'renderOptionsPage']);
        });

        add_filter('plugin_action_links_' . $this->basename, function ($links) {
            $link = [
                '<a href="' . admin_url('options-general.php?page=' . self::$plugin_key) . '">' . __('Settings') . '</a>',
            ];
            return array_merge($links, $link);
        });
    }

    /**
     * Add settings to the CMS
     */
    public function registerSettings()
    {
        register_setting(self::$plugin_key, self::$plugin_key . '_webhook');
        register_setting(self::$plugin_key, self::$plugin_key . '_form');
        register_setting(self::$plugin_key, self::$plugin_key . '_fields');
    }

    /**
     * Render admin options page
     */
    public function renderOptionsPage()
    {
        wp_enqueue_style(self::$plugin_key, plugin_dir_url($this->basename) . '/src/assets/css/styles.css');

        $form = $this->getOptionForm();
        $data = $this->getOptionFields();
        $form_name = self::$plugin_key . '_form';
        $fields_name = self::$plugin_key . '_fields';

        require __DIR__ . '/templates/options.php';
    }

    /**
     * @return string|null
     */
    protected function getWebhook()
    {
        return get_option(self::$plugin_key . '_webhook', null);
    }

    /**
     * @return string|null
     */
    protected function getOptionForm()
    {
        return get_option(self::$plugin_key . '_form', array());
    }

    /**
     * @return array
     */
    protected function getOptionFields()
    {
        return get_option(self::$plugin_key . '_fields', array());
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        $this->fields = require __DIR__ . '/fields/fields.php';

        return $this->fields;
    }

    /**
     * Process shortcode
     *
     * @param mixed $atts
     * @return string
     */
    public function shortcodeHandler($atts = [])
    {
        $atts = shortcode_atts([], $atts);

        if (null === $this->getWebhook()) {
            return '';
        }

        wp_enqueue_style(self::$plugin_key, plugin_dir_url($this->basename) . 'src/assets/css/styles.css');

        $form = $this->getOptionForm();
        $fields = $this->getFields();
        $fields_options = $this->getOptionFields();

        $form['submit'] = !empty($form['submit']) ? $form['submit'] : __('Submit');
        $form['success'] = !empty($form['success']) ? $form['success'] : __('Thank you!');

        $logo = plugin_dir_url($this->basename) . 'src/assets/images/logo.png';

        if (file_exists(get_template_directory() . '/teamleader/frontend.php')) {
            $path = get_template_directory() . '/teamleader/frontend.php';
        } else {
            $path = __DIR__ . '/templates/frontend.php';
        }

        ob_start();
        include $path;

        return ob_get_clean();
    }

    /**
     * Process ajax call
     *
     * @throws \Exception
     * @return string|null
     */
    public function ajaxHandler()
    {
        $fields = $this->getFields();
        $fields_options = $this->getOptionFields();

        $data = [];

        foreach ($fields as $key => $field) {
            $value = isset($_POST['data'][$key]) ? $_POST['data'][$key] : null;
            $data[$key] = !empty($fields_options[$key]['default']) ? $fields_options[$key]['default'] : $value;
        }

        try {
            $this->request($data);

        } catch (\RuntimeException $exception) {

            echo json_encode([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
            wp_die();
        }

        echo json_encode([
            'success' => true
        ]);

        wp_die();

        return null;
    }

    /**
     * @param array $fields
     * @return mixed
     * @throws \RuntimeException
     */
    protected function request(array $fields = array())
    {
        $webhook = $this->getWebhook();

        if (null === $webhook) {
            throw new \RuntimeException('Webhook url is empty');
        }

        $webhook .= '&' . http_build_query($fields);

        $options[CURLOPT_URL] = $webhook;
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

        $json = json_decode($response, true);

        return (false !== $json) ? $json : $response;
    }
}
