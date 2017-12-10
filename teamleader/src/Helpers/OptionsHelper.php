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
    public static function setForms($forms)
    {
        return update_option(Container::key() . '_forms', $forms);
    }

    /**
     * @return int
     */
    public static function getLastFromId()
    {
        return get_option(Container::key() . '_forms_id', 0);
    }

    /**
     * @param $id
     * @return int
     */
    public static function setLastFromId($id)
    {
        return update_option(Container::key() . '_forms_id', (int)$id);
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function setOptions(array $data)
    {
        $options = [
            'webhook' => sanitize_text_field($data['webhook']),
            'logo' => isset($data['logo']) ? true : false,
            'referral' => !empty($data['referral']) ? sanitize_text_field($data['referral']) : null,
            'recaptcha' => [
                'enable' => isset($data['recaptcha']['enable']) ? true : false,
                'key' => !empty($data['recaptcha']['key']) ? sanitize_text_field($data['recaptcha']['key']) : null,
                'secret' => !empty($data['recaptcha']['secret']) ? sanitize_text_field($data['recaptcha']['secret']) : null,
            ]
        ];

        if (empty($options['recaptcha']['key']) || empty($options['recaptcha']['secret'])) {
            $options['recaptcha']['enabled'] = false;
        }

        return update_option(Container::key() . '_options', $options);
    }
}