<?php

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Helpers\Fields;
use Teamleader\Interfaces\DependencyInterface;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Helpers\Options;

/**
 * Class Admin
 * @package Teamleader\Controllers
 */
class Admin implements DependencyInterface, HooksInterface
{
    /**
     * @var Container
     */
    protected $container;
    protected $key;
    protected $basename;
    protected $plugin_dir;
    protected $plugin_dir_url;

    /**
     * Admin constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->key = $container::getKey();
        $this->basename = $container::getBasename();
        $this->plugin_dir = $container::getPluginDir();
        $this->plugin_dir_url = $container::getPluginDirUrl();
    }

    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_action('admin_menu', function () {
            add_submenu_page('options-general.php', __('Teamleader', $this->key), __('Teamleader', $this->key),
                'manage_options', $this->key, [$this, 'renderOptionsPage']);
        });

        add_filter('plugin_action_links_' . $this->basename, function ($links) {
            $link = [
                '<a href="' . admin_url('options-general.php?page=' . $this->key) . '">' . __('Settings', $this->key) . '</a>',
            ];

            return array_merge($links, $link);
        });
    }

    /**
     * Render page
     *
     * @throws \LogicException
     */
    public function renderOptionsPage()
    {
        wp_enqueue_style($this->key . '-styles', $this->plugin_dir_url . 'assets/css/styles.css');
        wp_enqueue_style($this->key . '-admin', $this->plugin_dir_url . 'assets/css/admin.css');

        /**
         * @var $optionsHelper Options
         */
        $optionsHelper = $this->container->getContainer(Options::class);

        /**
         * @var $fieldsHelper Fields
         */
        $fieldsHelper = $this->container->getContainer(Fields::class);

        $data =
            [
                'key' => $this->key,
                'form_options' => $optionsHelper->getForm(),
                'webhook' => $optionsHelper->getWebhook(),
                'fields_options' => $optionsHelper->getFields(),
                'form_name' => $optionsHelper->getFormKey(),
                'fields_name' => $optionsHelper->getFieldsKey(),
                'fields' => $fieldsHelper->getFields()
            ];

        if (!file_exists($this->plugin_dir . '/templates/options.php')) {
            throw new \LogicException('Options template not found');
        }

        require $this->plugin_dir . '/templates/options.php';
    }
}
