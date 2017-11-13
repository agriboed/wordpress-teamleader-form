<?php

namespace Teamleader\Controllers;

use Teamleader\Helpers\Fields;
use Teamleader\Interfaces\DependencyInterface;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\Options;

/**
 * Class Frontend
 * @package Teamleader\Controllers
 */
class Frontend implements DependencyInterface, HooksInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $basename;

    /**
     * @var string
     */
    protected $plugin_dir;

    /**
     * @var string
     */
    protected $plugin_dir_url;

    /**
     * @var string
     */
    protected $template_dir;

    /**
     * Frontend constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->key = $container::getKey();
        $this->basename = $container::getBasename();
        $this->plugin_dir = $container::getPluginDir();
        $this->plugin_dir_url = $container::getPluginDirUrl();
        $this->template_dir = get_template_directory();
    }

    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_shortcode($this->key, [$this, 'processShortcode']);
    }

    /**
     * @param array $atts
     * @return string
     */
    public function processShortcode($atts = array())
    {
        $atts = shortcode_atts([], $atts);

        /**
         * @var $optionsHelper Options
         */
        $optionsHelper = $this->container->getContainer(Options::class);

        if (null === $optionsHelper->getWebhook()) {
            return '';
        }

        wp_enqueue_script('jquery');
        wp_enqueue_style($this->key . '-styles', $this->plugin_dir_url . 'assets/css/styles.css');

        return $this->renderForm();
    }

    protected function renderForm()
    {
        /**
         * @var $fieldsHelper Fields
         */
        $fieldsHelper = $this->container->getContainer(Fields::class);

        /**
         * @var $optionsHelper Options
         */
        $optionsHelper = $this->container->getContainer(Options::class);

        $form = $optionsHelper->getForm();

        if (!empty($form['recaptcha'])) {
            wp_enqueue_script('google-api', 'https://www.google.com/recaptcha/api.js', [], true);
        }

        $fields = $fieldsHelper->getFields();
        $fields_options = $optionsHelper->getFields();

        $form['submit'] = !empty($form['submit']) ? $form['submit'] : __('Submit', $this->key);
        $form['success'] = !empty($form['success']) ? $form['success'] : __('Thank you!', $this->key);

        $logo = $this->plugin_dir_url . 'assets/images/logo.png';

        if (file_exists($this->template_dir . '/teamleader/frontend.php')) {
            $path = $this->template_dir . '/teamleader/frontend.php';
        } else {
            $path = $this->plugin_dir . '/templates/frontend.php';
        }

        if (!file_exists($path)) {
            throw new \LogicException('Frontend template not found');
        }

        ob_start();
        include $path;

        return ob_get_clean();
    }
}
