<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Controllers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;
use Teamleader\Helpers\OptionsHelper;
use Teamleader\Helpers\FieldsHelper;

/**
 * Class Admin
 * @package Teamleader\Controllers
 */
class AdminController extends AbstractController implements HooksInterface
{
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
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        add_action('admin_menu', function () {
            add_submenu_page('options-general.php', __('Teamleader', Container::key()),
                __('Teamleader', Container::key()),
                'manage_options', Container::key(), [$this, 'renderOptionsPage']);
        });

        add_filter('plugin_action_links_' . Container::basename(), function ($links) {
            $link = [
                '<a href="' . admin_url('options-general.php?page=' . Container::key()) . '">' . __('Settings',
                    Container::key()) . '</a>',
            ];

            return array_merge($links, $link);
        });

        add_action('plugins_loaded', function () {
            load_plugin_textdomain(Container::key(), false, Container::pluginDir() . '/languages');
        });
    }

    /**
     * Render page
     *
     * @throws \LogicException
     * @throws \Exception
     */
    public function renderOptionsPage()
    {
        wp_enqueue_style(Container::key() . '-styles', Container::pluginUrl() . 'assets/css/styles.css', null, Container::version());
        wp_enqueue_style(Container::key() . '-admin', Container::pluginUrl() . 'assets/css/admin.css');
        wp_enqueue_script(Container::key() . '-admin', Container::pluginUrl() . 'assets/js/app.js', array('jquery'), Container::version());

        /**
         * @var $optionsHelper OptionsHelper
         */
        $optionsHelper = $this->container->get(OptionsHelper::class);

        /**
         * @var $fieldsHelper FieldsHelper
         */
        $fieldsHelper = $this->container->get(FieldsHelper::class);

        $data = [
            'key' => Container::key(),
            'form_options' => $optionsHelper->getForm(),
            'webhook' => $optionsHelper->getWebhook(),
            'fields_options' => $optionsHelper->getFields(),
            'form_name' => $optionsHelper->getFormKey(),
            'fields_name' => $optionsHelper->getFieldsKey(),
            'fields' => $fieldsHelper->getFields(),
        ];

        if (!file_exists(Container::pluginDir() . '/templates/options.php')) {
            throw new \LogicException('Options template not found');
        }

        require Container::pluginDir() . '/templates/options.php';
    }
}