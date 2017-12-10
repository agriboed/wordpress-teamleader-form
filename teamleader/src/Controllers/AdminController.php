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
     * @throws \ReflectionException
     * @throws \LogicException
     * @throws \Exception
     */
    public function renderOptionsPage()
    {
        wp_enqueue_style(Container::key(), Container::pluginUrl() . 'assets/css/admin.css', false, mt_rand(0, 22222));
        wp_enqueue_script(Container::key(), Container::pluginUrl() . 'assets/js/app.js', ['jquery'], mt_rand(0, 22222),
            Container::version());

        /**
         * @var $fieldsHelper FieldsHelper
         */
        $fieldsHelper = $this->container->get(FieldsHelper::class);

        $data = [
            'key' => Container::key(),
            'options' => OptionsHelper::getOptions(),
            'forms' => OptionsHelper::getForms(),
            'fields' => $fieldsHelper->getFields(),
        ];

        if (!file_exists(Container::pluginDir() . '/templates/options.php')) {
            throw new \LogicException('Options template not found');
        }

        require Container::pluginDir() . '/templates/options.php';
    }
}