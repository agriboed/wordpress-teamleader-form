<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;
use Teamleader\Interfaces\HooksInterface;

/**
 * Class Options
 * @package Teamleader\Helpers
 */
class OptionsHelper extends AbstractHelper implements HooksInterface
{
    /**
     * Set Wordpress hooks
     */
    public function initHooks()
    {
        register_setting(Container::key(), Container::key() . '_webhook');
        register_setting(Container::key(), Container::key() . '_form');
        register_setting(Container::key(), Container::key() . '_fields');
    }

    /**
     * @return string|null
     */
    public function getWebhook()
    {
        return get_option(Container::key() . '_webhook', null);
    }

    /**
     * @return string
     */
    public function getWebhookKey()
    {
        return Container::key() . '_webhook';
    }

    /**
     * @return array
     */
    public function getForm()
    {
        return get_option(Container::key() . '_form', []);
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return Container::key() . '_form';
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return get_option(Container::key() . '_fields', []);
    }

    /**
     * @return string
     */
    public function getFieldsKey()
    {
        return Container::key() . '_fields';
    }
}