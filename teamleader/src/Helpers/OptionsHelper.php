<?php
/**
 * Copyright (c) 2017 by AGriboed <alexv1rs@gmail.com>
 * https://v1rus.ru/
 */

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;

/**
 * Class Options
 * @package Teamleader\Helpers
 */
class OptionsHelper
{
    /**
     * @return string|null
     */
    public static function getOptions()
    {
        return get_option(Container::key() . '_options', []);
    }

    /**
     * @return array
     */
    public static function getForms()
    {
        return get_option(Container::key() . '_forms', []);
    }

    /**
     * @param $forms
     * @return bool
     */
    public static function setForms($forms){
        return update_option(Container::key() . '_forms', $forms);
    }

    /**
     * @return int
     */
    public static function getLastFromId()
    {
        return get_option(Container::key().'_forms_id', 0);
    }

    /**
     * @param array $options
     * @return bool
     */
    public static function setOptions(array $options)
    {
        return update_option(Container::key() . '_options', $options);
    }
}